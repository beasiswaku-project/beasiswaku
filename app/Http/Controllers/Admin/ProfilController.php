<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Display all student profiles, searchable and filterable by account status.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();

        $profil = Mahasiswa::query()
            ->with(['user', 'wilayah', 'jenjang'])
            ->search($search)
            ->statusAkun($status)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.profil.index', [
            'profil' => $profil,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Display a single student profile.
     */
    public function show(Mahasiswa $profil): View
    {
        $profil->load(['user', 'wilayah', 'jenjang']);

        return view('admin.profil.show', ['profil' => $profil]);
    }

    /**
     * Deactivate the account that owns the profile. The data is kept; the
     * student is logged out on their next request and can no longer sign in.
     */
    public function nonaktif(Mahasiswa $profil): RedirectResponse
    {
        $user = $profil->user;

        if (! $user->is_active) {
            return back()->with('error', "Akun \"{$user->nama}\" sudah nonaktif.");
        }

        $user->forceFill(['is_active' => false])->save();

        return back()->with('success', "Akun \"{$user->nama}\" berhasil dinonaktifkan.");
    }
}

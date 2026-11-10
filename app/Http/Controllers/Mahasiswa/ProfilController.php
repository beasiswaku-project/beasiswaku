<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\ProfilRequest;
use App\Models\Jenjang;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Display the student's own profile with its completeness indicator,
     * or an empty form when the profile has not been filled in yet.
     */
    public function show(Request $request): View
    {
        $profil = $request->user()->mahasiswa()->with(['wilayah', 'jenjang'])->first();

        return view('mahasiswa.profil.show', [
            'user' => $request->user(),
            'profil' => $profil,
            'daftarJenjang' => Jenjang::query()->orderBy('nama_jenjang')->get(['id', 'nama_jenjang']),
            'daftarWilayah' => Wilayah::query()->orderBy('provinsi')->orderBy('kabupaten')->get(['id', 'provinsi', 'kabupaten'])->groupBy('provinsi'),
        ]);
    }

    /**
     * Create the student's profile for the first time.
     */
    public function store(ProfilRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->mahasiswa()->exists()) {
            return to_route('mahasiswa.profil.show')->with('error', 'Profil sudah dibuat. Gunakan formulir untuk memperbaruinya.');
        }

        DB::transaction(function () use ($request, $user): void {
            $user->update($request->identitas());
            $user->mahasiswa()->create($request->profil());
        });

        return to_route('mahasiswa.profil.show')->with('success', 'Profil berhasil disimpan.');
    }

    /**
     * Update the student's existing profile.
     */
    public function update(ProfilRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profil = $user->mahasiswa()->firstOrFail();

        DB::transaction(function () use ($request, $user, $profil): void {
            $user->update($request->identitas());
            $profil->update($request->profil());
        });

        return to_route('mahasiswa.profil.show')->with('success', 'Profil berhasil diperbarui.');
    }
}

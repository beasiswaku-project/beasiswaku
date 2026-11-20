<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\RiwayatPendidikanRequest;
use App\Models\Jenjang;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RiwayatPendidikanController extends Controller
{
    /**
     * Display the student's GPA history, newest semester first.
     */
    public function index(Request $request): View
    {
        $profil = $request->user()->mahasiswa;
        $profil?->load('riwayatPendidikan.jenjang');

        return view('mahasiswa.riwayat-pendidikan.index', [
            'profil' => $profil,
            'daftarJenjang' => Jenjang::query()->orderBy('nama_jenjang')->get(['id', 'nama_jenjang']),
        ]);
    }

    /**
     * Record the GPA of a semester.
     */
    public function store(RiwayatPendidikanRequest $request): RedirectResponse
    {
        $profil = $request->user()->mahasiswa;

        if (! $profil) {
            return to_route('mahasiswa.profil.show')->with('error', 'Lengkapi profil terlebih dahulu sebelum mengisi riwayat IPK.');
        }

        $riwayat = $profil->riwayatPendidikan()->create($request->validated());

        return to_route('mahasiswa.riwayat-pendidikan.index')
            ->with('success', "IPK semester {$riwayat->semester} berhasil ditambahkan.");
    }

    /**
     * Correct the GPA of a semester.
     */
    public function update(RiwayatPendidikanRequest $request, RiwayatPendidikan $riwayatPendidikan): RedirectResponse
    {
        Gate::authorize('update', $riwayatPendidikan);

        $riwayatPendidikan->update($request->validated());

        return to_route('mahasiswa.riwayat-pendidikan.index')
            ->with('success', "IPK semester {$riwayatPendidikan->semester} berhasil diperbarui.");
    }

    /**
     * Delete a wrongly entered GPA.
     */
    public function destroy(Request $request, RiwayatPendidikan $riwayatPendidikan): RedirectResponse
    {
        Gate::authorize('delete', $riwayatPendidikan);

        $riwayatPendidikan->delete();

        return to_route('mahasiswa.riwayat-pendidikan.index')
            ->with('success', "IPK semester {$riwayatPendidikan->semester} berhasil dihapus.");
    }
}

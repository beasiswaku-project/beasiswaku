<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\JenisPrestasi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\PrestasiRequest;
use App\Models\Prestasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PrestasiController extends Controller
{
    /**
     * Display the student's achievements and organisation memberships.
     */
    public function index(Request $request): View
    {
        $profil = $request->user()->mahasiswa;
        $profil?->load('prestasi');

        return view('mahasiswa.prestasi.index', [
            'profil' => $profil,
            'daftarJenis' => JenisPrestasi::cases(),
            'daftarTingkat' => Prestasi::DAFTAR_TINGKAT,
        ]);
    }

    /**
     * Record a new achievement, storing its proof file on the private disk.
     */
    public function store(PrestasiRequest $request): RedirectResponse
    {
        $profil = $request->user()->mahasiswa;

        if (! $profil) {
            return to_route('mahasiswa.profil.show')->with('error', 'Lengkapi profil terlebih dahulu sebelum menambah prestasi.');
        }

        $prestasi = $profil->prestasi()->make($request->safe()->except('bukti'));
        $prestasi->bukti_path = $request->file('bukti')?->store(Prestasi::FOLDER_BUKTI);
        $prestasi->save();

        return to_route('mahasiswa.prestasi.index')
            ->with('success', "Prestasi \"{$prestasi->nama_kegiatan}\" berhasil ditambahkan.");
    }

    /**
     * Update an achievement; a newly uploaded proof replaces the old file.
     */
    public function update(PrestasiRequest $request, Prestasi $prestasi): RedirectResponse
    {
        Gate::authorize('update', $prestasi);

        $prestasi->fill($request->safe()->except('bukti'));

        if ($request->hasFile('bukti')) {
            $buktiLama = $prestasi->bukti_path;
            $prestasi->bukti_path = $request->file('bukti')->store(Prestasi::FOLDER_BUKTI);

            if ($buktiLama) {
                Storage::delete($buktiLama);
            }
        }

        $prestasi->save();

        return to_route('mahasiswa.prestasi.index')
            ->with('success', "Prestasi \"{$prestasi->nama_kegiatan}\" berhasil diperbarui.");
    }

    /**
     * Delete a wrongly entered achievement together with its proof file.
     */
    public function destroy(Prestasi $prestasi): RedirectResponse
    {
        Gate::authorize('delete', $prestasi);

        $prestasi->delete();

        if ($prestasi->bukti_path) {
            Storage::delete($prestasi->bukti_path);
        }

        return to_route('mahasiswa.prestasi.index')
            ->with('success', "Prestasi \"{$prestasi->nama_kegiatan}\" berhasil dihapus.");
    }
}

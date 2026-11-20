<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\EsaiRequest;
use App\Models\Esai;
use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EsaiController extends Controller
{
    /**
     * Display the essay editor for the registration (empty when not written yet).
     */
    public function index(Pendaftaran $pendaftaran): View
    {
        Gate::authorize('view', $pendaftaran);

        $pendaftaran->load(['program', 'esai']);

        return view('mahasiswa.esai.index', ['pendaftaran' => $pendaftaran, 'esai' => $pendaftaran->esai]);
    }

    /**
     * Write the essay for a draft registration.
     */
    public function store(EsaiRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        if (! $pendaftaran->isDraft()) {
            return back()->with('error', 'Esai tidak dapat diubah karena pendaftaran sudah dikirim.');
        }

        if ($pendaftaran->esai()->exists()) {
            return to_route('mahasiswa.pendaftaran.esai.index', $pendaftaran)->with('error', 'Esai sudah ditulis. Simpan perubahan dari formulir yang ada.');
        }

        $pendaftaran->esai()->create($request->validated());

        return to_route('mahasiswa.pendaftaran.esai.index', $pendaftaran)->with('success', 'Draf esai berhasil disimpan.');
    }

    /**
     * Edit the essay while the registration has not been submitted.
     */
    public function update(EsaiRequest $request, Esai $esai): RedirectResponse
    {
        if (! $esai->isDraft() || ! $esai->pendaftaran->isDraft()) {
            return back()->with('error', 'Esai tidak dapat diubah karena pendaftaran sudah dikirim.');
        }

        $esai->update($request->validated());

        return to_route('mahasiswa.pendaftaran.esai.index', $esai->pendaftaran)->with('success', 'Draf esai berhasil diperbarui.');
    }

    /**
     * Delete a draft essay.
     */
    public function destroy(Esai $esai): RedirectResponse
    {
        Gate::authorize('delete', $esai);

        if (! $esai->isDraft() || ! $esai->pendaftaran->isDraft()) {
            return back()->with('error', 'Esai yang sudah dikirim tidak dapat dihapus.');
        }

        $esai->delete();

        return to_route('mahasiswa.pendaftaran.esai.index', $esai->pendaftaran)->with('success', 'Draf esai berhasil dihapus.');
    }
}

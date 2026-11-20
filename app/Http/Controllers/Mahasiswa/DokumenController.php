<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\DokumenRequest;
use App\Models\Dokumen;
use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * Documents are listed on the registration detail page.
 */
class DokumenController extends Controller
{
    /**
     * Upload a mandatory or optional document for the registration.
     */
    public function store(DokumenRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        Gate::authorize('update', $pendaftaran);

        $pendaftaran->load(['program.persyaratan', 'mahasiswa']);

        if (! $pendaftaran->isMenerimaDokumen()) {
            return back()->with('error', 'Dokumen tidak dapat diunggah karena pendaftaran sudah melewati tahap verifikasi.');
        }

        $dokumen = new Dokumen;
        $dokumen->jenis_dokumen = $request->validated('jenis_dokumen');
        $dokumen->opsional = ! in_array($dokumen->jenis_dokumen, $pendaftaran->dokumenWajib(), true);
        $dokumen->file_path = $request->file('file')->store(Dokumen::FOLDER);
        $pendaftaran->dokumen()->save($dokumen);

        return to_route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', "Dokumen \"{$dokumen->jenis_dokumen}\" berhasil diunggah.");
    }

    /**
     * Replace the file of a document that has not been accepted yet; the
     * document goes back to waiting for verification.
     */
    public function update(DokumenRequest $request, Dokumen $dokumen): RedirectResponse
    {
        Gate::authorize('update', $dokumen);

        if (! $dokumen->isDapatDiganti() || ! $dokumen->pendaftaran->isMenerimaDokumen()) {
            return back()->with('error', 'Dokumen yang sudah valid atau pendaftaran yang sudah diproses tidak dapat diganti.');
        }

        $fileLama = $dokumen->file_path;

        $dokumen->file_path = $request->file('file')->store(Dokumen::FOLDER);
        $dokumen->status_verifikasi = StatusVerifikasi::Menunggu;
        $dokumen->catatan_admin = null;
        $dokumen->save();

        if ($fileLama) {
            Storage::delete($fileLama);
        }

        return to_route('mahasiswa.pendaftaran.show', $dokumen->pendaftaran)
            ->with('success', "Dokumen \"{$dokumen->jenis_dokumen}\" berhasil diganti dan menunggu verifikasi ulang.");
    }

    /**
     * Delete an own document that has not been verified yet.
     */
    public function destroy(Dokumen $dokumen): RedirectResponse
    {
        Gate::authorize('delete', $dokumen);

        if (! $dokumen->isDapatDihapus()) {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat dihapus.');
        }

        $dokumen->delete();

        if ($dokumen->file_path) {
            Storage::delete($dokumen->file_path);
        }

        return to_route('mahasiswa.pendaftaran.show', $dokumen->pendaftaran)
            ->with('success', "Dokumen \"{$dokumen->jenis_dokumen}\" berhasil dihapus.");
    }
}

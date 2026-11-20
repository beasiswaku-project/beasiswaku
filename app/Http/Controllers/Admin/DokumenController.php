<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifikasiDokumenRequest;
use App\Models\Dokumen;
use Illuminate\Http\RedirectResponse;

class DokumenController extends Controller
{
    /**
     * Mark a document as waiting, valid, or rejected with a note.
     */
    public function verifikasi(VerifikasiDokumenRequest $request, Dokumen $dokumen): RedirectResponse
    {
        $dokumen->status_verifikasi = $request->validated('status_verifikasi');
        $dokumen->catatan_admin = $request->validated('catatan_admin');
        $dokumen->save();

        return back()->with('success', "Dokumen \"{$dokumen->jenis_dokumen}\" ditandai {$dokumen->status_verifikasi->label()}.");
    }
}

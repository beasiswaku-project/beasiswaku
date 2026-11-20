<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenUnduhController extends Controller
{
    /**
     * Stream a registration document to its owner or an admin.
     */
    public function __invoke(Dokumen $dokumen): StreamedResponse
    {
        Gate::authorize('view', $dokumen);

        abort_unless($dokumen->file_path && Storage::exists($dokumen->file_path), 404);

        $ekstensi = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);

        return Storage::download($dokumen->file_path, Str::slug($dokumen->jenis_dokumen)."-{$dokumen->pendaftaran_id}.{$ekstensi}");
    }
}

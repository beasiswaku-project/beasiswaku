<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrestasiBuktiController extends Controller
{
    /**
     * Stream an achievement's proof file to its owner or an admin.
     */
    public function __invoke(Prestasi $prestasi): StreamedResponse
    {
        Gate::authorize('view', $prestasi);

        abort_unless($prestasi->bukti_path && Storage::exists($prestasi->bukti_path), 404);

        $ekstensi = pathinfo($prestasi->bukti_path, PATHINFO_EXTENSION);

        return Storage::download($prestasi->bukti_path, "bukti-prestasi-{$prestasi->id}.{$ekstensi}");
    }
}

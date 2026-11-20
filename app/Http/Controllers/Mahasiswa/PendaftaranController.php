<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\PendaftaranRequest;
use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Display the student's own registrations.
     */
    public function index(Request $request): View
    {
        $pendaftaran = $request->user()->mahasiswa
            ?->pendaftaran()
            ->with('program.penyedia')
            ->get() ?? collect();

        return view('mahasiswa.pendaftaran.index', ['pendaftaran' => $pendaftaran]);
    }

    /**
     * Start a draft registration for an open program.
     */
    public function store(PendaftaranRequest $request): RedirectResponse
    {
        $pendaftaran = new Pendaftaran;
        $pendaftaran->mahasiswa()->associate($request->user()->mahasiswa);
        $pendaftaran->program()->associate($request->integer('program_id'));
        $pendaftaran->save();

        return to_route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Draf pendaftaran dibuat. Lengkapi berkasnya lalu kirim sebelum deadline.');
    }

    /**
     * Display a registration with what is still needed before submitting.
     */
    public function show(Pendaftaran $pendaftaran): View
    {
        Gate::authorize('view', $pendaftaran);

        $pendaftaran->load(['program.penyedia', 'program.jenjang', 'program.persyaratan', 'mahasiswa.wilayah', 'mahasiswa.riwayatPendidikan', 'dokumen']);

        return view('mahasiswa.pendaftaran.show', [
            'pendaftaran' => $pendaftaran,
            'kendala' => $pendaftaran->isDraft() ? $pendaftaran->kendalaPengiriman() : [],
        ]);
    }

    /**
     * Submit the draft, saving a snapshot of the economic data and GPA.
     */
    public function kirim(Pendaftaran $pendaftaran): RedirectResponse
    {
        Gate::authorize('update', $pendaftaran);

        $pendaftaran->load(['program.persyaratan', 'mahasiswa.wilayah', 'mahasiswa.riwayatPendidikan', 'dokumen']);

        if ($kendala = $pendaftaran->kendalaPengiriman()) {
            return back()->with('error', 'Pendaftaran belum dapat dikirim: '.implode(' ', $kendala));
        }

        $pendaftaran->kirim();

        return to_route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Pendaftaran berhasil dikirim. Data ekonomi dan IPK saat ini sudah disimpan untuk seleksi.');
    }

    /**
     * Cancel the registration before the deadline.
     */
    public function destroy(Pendaftaran $pendaftaran): RedirectResponse
    {
        Gate::authorize('delete', $pendaftaran);

        if (! $pendaftaran->isDapatDibatalkan()) {
            return back()->with('error', 'Pendaftaran tidak dapat dibatalkan karena sudah diproses atau melewati deadline.');
        }

        $fileDokumen = $pendaftaran->dokumen()->pluck('file_path')->filter()->all();

        $pendaftaran->delete();

        Storage::delete($fileDokumen);

        return to_route('mahasiswa.pendaftaran.index')
            ->with('success', "Pendaftaran pada \"{$pendaftaran->program->judul}\" berhasil dibatalkan.");
    }
}

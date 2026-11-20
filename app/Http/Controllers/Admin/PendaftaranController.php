<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StatusPendaftaranRequest;
use App\Models\Pendaftaran;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Display every submitted registration, searchable and filterable.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $filter = $request->only(['program', 'status']);

        $pendaftaran = Pendaftaran::query()
            ->terkirim()
            ->with(['mahasiswa.user', 'program'])
            ->search($search)
            ->filter($filter)
            ->latest('tgl_kirim')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pendaftaran.index', [
            'pendaftaran' => $pendaftaran,
            'search' => $search,
            'filter' => $filter,
            'daftarProgram' => Program::query()->orderBy('judul')->get(['id', 'judul']),
            'daftarStatus' => StatusPendaftaran::untukAdmin(),
        ]);
    }

    /**
     * Display a registration with the snapshot taken when it was submitted.
     */
    public function show(Pendaftaran $pendaftaran): View
    {
        abort_if($pendaftaran->isDraft(), 404);

        $pendaftaran->load(['mahasiswa.user', 'program.penyedia']);

        return view('admin.pendaftaran.show', [
            'pendaftaran' => $pendaftaran,
            'daftarStatus' => StatusPendaftaran::untukAdmin(),
        ]);
    }

    /**
     * Display the applicant's achievements, read when scoring the achievement component.
     */
    public function prestasi(Pendaftaran $pendaftaran): View
    {
        abort_if($pendaftaran->isDraft(), 404);

        $pendaftaran->load(['mahasiswa.user', 'mahasiswa.prestasi', 'program']);

        return view('admin.pendaftaran.prestasi', ['pendaftaran' => $pendaftaran]);
    }

    /**
     * Change the status of a submitted registration.
     */
    public function status(StatusPendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $pendaftaran->status = $request->enum('status', StatusPendaftaran::class);
        $pendaftaran->save();

        return back()->with('success', "Status pendaftaran diubah menjadi {$pendaftaran->status->label()}.");
    }
}

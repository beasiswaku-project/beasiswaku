<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusProgram;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Http\Requests\Admin\StatusProgramRequest;
use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\KonfigurasiPenilaian;
use App\Models\Penyedia;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Display every program, drafts included, searchable and filterable.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $filter = $request->only(['kategori', 'jenjang', 'status']);

        $program = Program::query()
            ->with(['penyedia', 'kategori', 'jenjang'])
            ->withCount('pendaftaran')
            ->search($search)
            ->filter($filter)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.program.index', [
            'program' => $program,
            'search' => $search,
            'filter' => $filter,
            ...$this->pilihan(),
        ]);
    }

    /**
     * Store a new program as a draft linked to the newest scoring configuration.
     */
    public function store(ProgramRequest $request): RedirectResponse
    {
        $program = new Program($request->validated());
        $program->konfigurasi()->associate(KonfigurasiPenilaian::versiTerbaru($request->user()));
        $program->save();

        return to_route('admin.program.show', $program)
            ->with('success', "Program \"{$program->judul}\" berhasil dibuat sebagai draf.");
    }

    /**
     * Display a program with its registration count.
     */
    public function show(Program $program): View
    {
        $program->load(['penyedia', 'kategori', 'jenjang', 'konfigurasi'])->loadCount('pendaftaran');

        return view('admin.program.show', [
            'program' => $program,
            'daftarStatus' => StatusProgram::cases(),
            ...$this->pilihan(),
        ]);
    }

    /**
     * Update the program's data.
     */
    public function update(ProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->validated());

        return back()->with('success', "Program \"{$program->judul}\" berhasil diperbarui.");
    }

    /**
     * Change the program's status (draft, open, closed, finished).
     */
    public function status(StatusProgramRequest $request, Program $program): RedirectResponse
    {
        $program->status = $request->enum('status', StatusProgram::class);
        $program->save();

        return back()->with('success', "Status program diubah menjadi {$program->status->label()}.");
    }

    /**
     * Delete the program when nobody has registered for it.
     */
    public function destroy(Program $program): RedirectResponse
    {
        if (! $program->isDeletable()) {
            return back()->with('error', "Program \"{$program->judul}\" tidak dapat dihapus karena sudah memiliki pendaftar.");
        }

        $program->delete();

        return to_route('admin.program.index')
            ->with('success', "Program \"{$program->judul}\" berhasil dihapus.");
    }

    /**
     * Options for the program form and filters.
     *
     * @return array<string, mixed>
     */
    private function pilihan(): array
    {
        return [
            'daftarPenyedia' => Penyedia::query()->orderBy('nama_penyedia')->get(['id', 'nama_penyedia']),
            'daftarKategori' => Kategori::query()->orderBy('nama_kategori')->get(['id', 'nama_kategori']),
            'daftarJenjang' => Jenjang::query()->orderBy('nama_jenjang')->get(['id', 'nama_jenjang']),
            'daftarStatus' => StatusProgram::cases(),
        ];
    }
}

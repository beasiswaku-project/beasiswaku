<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeasiswaController extends Controller
{
    /**
     * Display the catalogue of open programs, filterable by category and level.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $filter = $request->only(['kategori', 'jenjang']);

        $program = Program::query()
            ->terbuka()
            ->with(['penyedia', 'kategori', 'jenjang'])
            ->search($search)
            ->filter($filter)
            ->orderBy('deadline')
            ->paginate(9)
            ->withQueryString();

        return view('mahasiswa.beasiswa.index', [
            'program' => $program,
            'search' => $search,
            'filter' => $filter,
            'daftarKategori' => Kategori::query()->orderBy('nama_kategori')->get(['id', 'nama_kategori']),
            'daftarJenjang' => Jenjang::query()->orderBy('nama_jenjang')->get(['id', 'nama_jenjang']),
        ]);
    }

    /**
     * Display an open program. Drafts and closed programs are not visible to students.
     */
    public function show(Program $program): View
    {
        abort_unless($program->isTerbuka(), 404);

        $program->load(['penyedia', 'kategori', 'jenjang']);

        return view('mahasiswa.beasiswa.show', ['program' => $program]);
    }
}

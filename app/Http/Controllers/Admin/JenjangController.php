<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JenjangRequest;
use App\Models\Jenjang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenjangController extends Controller
{
    /**
     * Display a searchable list of education levels.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $jenjang = Jenjang::query()
            ->search($search)
            ->withCount(['mahasiswa', 'riwayatPendidikan', 'programs'])
            ->orderBy('nama_jenjang')
            ->paginate(10)
            ->withQueryString();

        return view('admin.jenjang.index', [
            'jenjang' => $jenjang,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created education level.
     */
    public function store(JenjangRequest $request): RedirectResponse
    {
        $jenjang = Jenjang::create($request->validated());

        return to_route('admin.jenjang.index')
            ->with('success', "Jenjang \"{$jenjang->nama_jenjang}\" berhasil ditambahkan.");
    }

    /**
     * Display an education level together with the programs that target it.
     */
    public function show(Jenjang $jenjang): View
    {
        $jenjang->loadCount(['mahasiswa', 'riwayatPendidikan', 'programs'])
            ->load(['programs' => fn ($query) => $query->orderBy('judul')]);

        return view('admin.jenjang.show', [
            'jenjang' => $jenjang,
        ]);
    }

    /**
     * Update the given education level.
     */
    public function update(JenjangRequest $request, Jenjang $jenjang): RedirectResponse
    {
        $jenjang->update($request->validated());

        return back()->with('success', "Jenjang \"{$jenjang->nama_jenjang}\" berhasil diperbarui.");
    }

    /**
     * Delete the given education level when no profile, education history, or program uses it.
     */
    public function destroy(Jenjang $jenjang): RedirectResponse
    {
        if (! $jenjang->isDeletable()) {
            return back()->with('error', "Jenjang \"{$jenjang->nama_jenjang}\" tidak dapat dihapus karena masih dipakai profil, riwayat pendidikan, atau program.");
        }

        $jenjang->delete();

        return to_route('admin.jenjang.index')
            ->with('success', "Jenjang \"{$jenjang->nama_jenjang}\" berhasil dihapus.");
    }
}

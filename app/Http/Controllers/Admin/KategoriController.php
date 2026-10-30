<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /**
     * Display a searchable list of scholarship categories.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $kategori = Kategori::query()
            ->search($search)
            ->withCount('programs')
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori.index', [
            'kategori' => $kategori,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(KategoriRequest $request): RedirectResponse
    {
        $kategori = Kategori::create($request->validated());

        return to_route('admin.kategori.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil ditambahkan.");
    }

    /**
     * Display a category together with the programs that use it.
     */
    public function show(Kategori $kategori): View
    {
        $kategori->load(['programs' => fn ($query) => $query->orderBy('judul')]);

        return view('admin.kategori.show', [
            'kategori' => $kategori,
        ]);
    }

    /**
     * Update the given category.
     */
    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return back()->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil diperbarui.");
    }

    /**
     * Delete the given category when no program uses it.
     */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        if (! $kategori->isDeletable()) {
            return back()->with('error', "Kategori \"{$kategori->nama_kategori}\" tidak dapat dihapus karena masih dipakai program.");
        }

        $kategori->delete();

        return to_route('admin.kategori.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil dihapus.");
    }
}

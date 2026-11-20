<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisPenyedia;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PenyediaRequest;
use App\Models\Penyedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenyediaController extends Controller
{
    /**
     * Display a list of providers, searchable and filterable by type.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $jenis = $request->string('jenis')->toString();

        $penyedia = Penyedia::query()
            ->search($search)
            ->ofJenis($jenis)
            ->withCount('programs')
            ->orderBy('nama_penyedia')
            ->paginate(10)
            ->withQueryString();

        return view('admin.penyedia.index', [
            'penyedia' => $penyedia,
            'search' => $search,
            'jenis' => $jenis,
            'daftarJenis' => JenisPenyedia::cases(),
        ]);
    }

    /**
     * Store a newly created provider.
     */
    public function store(PenyediaRequest $request): RedirectResponse
    {
        $penyedia = Penyedia::create($request->validated());

        return to_route('admin.penyedia.index')
            ->with('success', "Penyedia \"{$penyedia->nama_penyedia}\" berhasil ditambahkan.");
    }

    /**
     * Display a provider together with how many programs it offers.
     */
    public function show(Penyedia $penyedia): View
    {
        $penyedia->loadCount('programs');

        return view('admin.penyedia.show', [
            'penyedia' => $penyedia,
            'daftarJenis' => JenisPenyedia::cases(),
        ]);
    }

    /**
     * Update the given provider.
     */
    public function update(PenyediaRequest $request, Penyedia $penyedia): RedirectResponse
    {
        $penyedia->update($request->validated());

        return back()->with('success', "Penyedia \"{$penyedia->nama_penyedia}\" berhasil diperbarui.");
    }

    /**
     * Delete the given provider when it has no program.
     */
    public function destroy(Penyedia $penyedia): RedirectResponse
    {
        if (! $penyedia->isDeletable()) {
            return back()->with('error', "Penyedia \"{$penyedia->nama_penyedia}\" tidak dapat dihapus karena masih memiliki program beasiswa.");
        }

        $penyedia->delete();

        return to_route('admin.penyedia.index')
            ->with('success', "Penyedia \"{$penyedia->nama_penyedia}\" berhasil dihapus.");
    }
}

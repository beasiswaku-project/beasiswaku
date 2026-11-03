<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WilayahRequest;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WilayahController extends Controller
{
    /**
     * Display a list of regions, searchable and filterable by province and 3T status.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $provinsi = $request->string('provinsi')->trim()->toString();
        $status = $request->string('status')->toString();

        $wilayah = Wilayah::query()
            ->search($search)
            ->inProvinsi($provinsi)
            ->status3t($status)
            ->withCount('mahasiswa')
            ->orderBy('provinsi')
            ->orderBy('kabupaten')
            ->paginate(10)
            ->withQueryString();

        return view('admin.wilayah.index', [
            'wilayah' => $wilayah,
            'search' => $search,
            'provinsi' => $provinsi,
            'status' => $status,
            'daftarProvinsi' => Wilayah::query()->distinct()->orderBy('provinsi')->pluck('provinsi'),
        ]);
    }

    /**
     * Store a newly created region.
     */
    public function store(WilayahRequest $request): RedirectResponse
    {
        $wilayah = Wilayah::create($request->validated());

        return to_route('admin.wilayah.index')
            ->with('success', "Wilayah \"{$wilayah->kabupaten}\" berhasil ditambahkan.");
    }

    /**
     * Display a region together with how many student profiles use it.
     */
    public function show(Wilayah $wilayah): View
    {
        $wilayah->loadCount('mahasiswa');

        return view('admin.wilayah.show', [
            'wilayah' => $wilayah,
            'daftarProvinsi' => Wilayah::query()->distinct()->orderBy('provinsi')->pluck('provinsi'),
        ]);
    }

    /**
     * Update the given region.
     */
    public function update(WilayahRequest $request, Wilayah $wilayah): RedirectResponse
    {
        $wilayah->update($request->validated());

        return back()->with('success', "Wilayah \"{$wilayah->kabupaten}\" berhasil diperbarui.");
    }

    /**
     * Delete the given region when no student profile uses it.
     */
    public function destroy(Wilayah $wilayah): RedirectResponse
    {
        if (! $wilayah->isDeletable()) {
            return back()->with('error', "Wilayah \"{$wilayah->kabupaten}\" tidak dapat dihapus karena masih dipakai profil mahasiswa.");
        }

        $wilayah->delete();

        return to_route('admin.wilayah.index')
            ->with('success', "Wilayah \"{$wilayah->kabupaten}\" berhasil dihapus.");
    }
}

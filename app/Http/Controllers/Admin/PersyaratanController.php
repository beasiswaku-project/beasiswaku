<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PersyaratanRequest;
use App\Models\Persyaratan;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;

/**
 * Requirements are listed on the program detail page; they can only change
 * while the program has no registrations, so the rules stay fair to applicants.
 */
class PersyaratanController extends Controller
{
    /**
     * Add a requirement to the program.
     */
    public function store(PersyaratanRequest $request, Program $program): RedirectResponse
    {
        if ($program->isPersyaratanTerkunci()) {
            return $this->terkunci($program);
        }

        $persyaratan = $program->persyaratan()->create($request->validated());

        return to_route('admin.program.show', $program)
            ->with('success', "Syarat \"{$persyaratan->deskripsi()}\" berhasil ditambahkan.");
    }

    /**
     * Change a requirement's value or whether it is mandatory.
     */
    public function update(PersyaratanRequest $request, Persyaratan $persyaratan): RedirectResponse
    {
        if ($persyaratan->program->isPersyaratanTerkunci()) {
            return $this->terkunci($persyaratan->program);
        }

        $persyaratan->update($request->validated());

        return to_route('admin.program.show', $persyaratan->program)
            ->with('success', "Syarat \"{$persyaratan->deskripsi()}\" berhasil diperbarui.");
    }

    /**
     * Remove a requirement.
     */
    public function destroy(Persyaratan $persyaratan): RedirectResponse
    {
        if ($persyaratan->program->isPersyaratanTerkunci()) {
            return $this->terkunci($persyaratan->program);
        }

        $persyaratan->delete();

        return to_route('admin.program.show', $persyaratan->program)
            ->with('success', "Syarat \"{$persyaratan->deskripsi()}\" berhasil dihapus.");
    }

    private function terkunci(Program $program): RedirectResponse
    {
        return to_route('admin.program.show', $program)
            ->with('error', 'Persyaratan tidak dapat diubah karena program sudah memiliki pendaftar.');
    }
}

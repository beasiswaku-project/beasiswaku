<?php

namespace App\Policies;

use App\Models\Dokumen;
use App\Models\User;

/**
 * Only the registration's owner and admins may download a document, which
 * prevents reading another student's files by guessing ids (IDOR).
 */
class DokumenPolicy
{
    /**
     * Determine whether the user can view or download the document.
     */
    public function view(User $user, Dokumen $dokumen): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $dokumen);
    }

    /**
     * Determine whether the user can replace the document.
     */
    public function update(User $user, Dokumen $dokumen): bool
    {
        return $this->isOwner($user, $dokumen);
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Dokumen $dokumen): bool
    {
        return $this->isOwner($user, $dokumen);
    }

    private function isOwner(User $user, Dokumen $dokumen): bool
    {
        return $user->mahasiswa !== null && $user->mahasiswa->id === $dokumen->pendaftaran->mahasiswa_id;
    }
}

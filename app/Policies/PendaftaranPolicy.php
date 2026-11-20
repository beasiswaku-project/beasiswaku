<?php

namespace App\Policies;

use App\Models\Pendaftaran;
use App\Models\User;

/**
 * Students may only see and change their own registrations; admins may read
 * any submitted registration.
 */
class PendaftaranPolicy
{
    /**
     * Determine whether the user can view the registration.
     */
    public function view(User $user, Pendaftaran $pendaftaran): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $pendaftaran);
    }

    /**
     * Determine whether the user can change the registration (submit it or edit its parts).
     */
    public function update(User $user, Pendaftaran $pendaftaran): bool
    {
        return $this->isOwner($user, $pendaftaran);
    }

    /**
     * Determine whether the user can cancel the registration.
     */
    public function delete(User $user, Pendaftaran $pendaftaran): bool
    {
        return $this->isOwner($user, $pendaftaran);
    }

    private function isOwner(User $user, Pendaftaran $pendaftaran): bool
    {
        return $user->mahasiswa?->id === $pendaftaran->mahasiswa_id;
    }
}

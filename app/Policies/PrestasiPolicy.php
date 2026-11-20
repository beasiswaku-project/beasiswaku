<?php

namespace App\Policies;

use App\Models\Prestasi;
use App\Models\User;

/**
 * Students may only manage their own achievements; admins may read any of them
 * (including the proof file) when scoring an application.
 */
class PrestasiPolicy
{
    /**
     * Determine whether the user can view the achievement and its proof file.
     */
    public function view(User $user, Prestasi $prestasi): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $prestasi);
    }

    /**
     * Determine whether the user can update the achievement.
     */
    public function update(User $user, Prestasi $prestasi): bool
    {
        return $this->isOwner($user, $prestasi);
    }

    /**
     * Determine whether the user can delete the achievement.
     */
    public function delete(User $user, Prestasi $prestasi): bool
    {
        return $this->isOwner($user, $prestasi);
    }

    private function isOwner(User $user, Prestasi $prestasi): bool
    {
        return $user->mahasiswa?->id === $prestasi->mahasiswa_id;
    }
}

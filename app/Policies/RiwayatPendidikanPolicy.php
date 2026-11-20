<?php

namespace App\Policies;

use App\Models\RiwayatPendidikan;
use App\Models\User;

/**
 * Students may only change education history entries on their own profile.
 */
class RiwayatPendidikanPolicy
{
    /**
     * Determine whether the user can update the entry.
     */
    public function update(User $user, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $user->mahasiswa?->id === $riwayatPendidikan->mahasiswa_id;
    }

    /**
     * Determine whether the user can delete the entry.
     */
    public function delete(User $user, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $this->update($user, $riwayatPendidikan);
    }
}

<?php

namespace App\Policies;

use App\Models\Esai;
use App\Models\User;

/**
 * Students may only change the essay of their own registration.
 */
class EsaiPolicy
{
    /**
     * Determine whether the user can update the essay.
     */
    public function update(User $user, Esai $esai): bool
    {
        return $user->mahasiswa !== null && $user->mahasiswa->id === $esai->pendaftaran->mahasiswa_id;
    }

    /**
     * Determine whether the user can delete the essay.
     */
    public function delete(User $user, Esai $esai): bool
    {
        return $this->update($user, $esai);
    }
}

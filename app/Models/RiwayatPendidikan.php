<?php

namespace App\Models;

use Database\Factories\RiwayatPendidikanFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Minimal model for the `riwayat_pendidikan` table so other modules can reference it.
 * The education history module builds on top of this class.
 */
#[Table('riwayat_pendidikan', timestamps: false)]
class RiwayatPendidikan extends Model
{
    /** @use HasFactory<RiwayatPendidikanFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Mahasiswa, $this>
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    /**
     * @return BelongsTo<Jenjang, $this>
     */
    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class);
    }
}

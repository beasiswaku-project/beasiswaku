<?php

namespace App\Models;

use Database\Factories\KonfigurasiPenilaianFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Minimal model; the scoring configuration feature (KF-36) builds on top of this class.
 * Rows are append-only, so the table only has a `created_at` column.
 */
#[Table('konfigurasi_penilaian')]
class KonfigurasiPenilaian extends Model
{
    /** @use HasFactory<KonfigurasiPenilaianFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bobot' => 'array',
            'ambang' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}

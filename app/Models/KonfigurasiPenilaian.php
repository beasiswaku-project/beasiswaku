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
     * Final score weights from the proposal (subbab 4.6.2), used for the first version.
     *
     * @var array<string, float>
     */
    public const array BOBOT_AWAL = [
        'ekonomi' => 0.40,
        'ipk' => 0.20,
        'prestasi' => 0.15,
        'esai' => 0.10,
        'wawancara' => 0.15,
    ];

    /**
     * Thresholds from the proposal (subbab 4.6.1), used for the first version.
     *
     * @var array<string, int>
     */
    public const array AMBANG_AWAL = [
        'desil_dtsen_maksimum' => 4,
    ];

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

    /**
     * The newest configuration version, creating version 1 with the proposal's
     * defaults when none exists yet. New programs are linked to this version.
     */
    public static function versiTerbaru(User $pembuat): self
    {
        return static::query()->orderByDesc('versi')->first()
            ?? static::query()->forceCreate([
                'versi' => 1,
                'bobot' => self::BOBOT_AWAL,
                'ambang' => self::AMBANG_AWAL,
                'dibuat_oleh' => $pembuat->id,
            ]);
    }
}

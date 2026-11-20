<?php

namespace App\Models;

use Database\Factories\RiwayatPendidikanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GPA (IPK) recorded per semester and education level. The most recent entry
 * is used for scholarship matching and the registration snapshot.
 * `mahasiswa_id` is set from the owner's profile, so it is not fillable.
 */
#[Table('riwayat_pendidikan', timestamps: false)]
#[Fillable(['jenjang_id', 'semester', 'ipk'])]
class RiwayatPendidikan extends Model
{
    /** @use HasFactory<RiwayatPendidikanFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'ipk' => 'decimal:2',
        ];
    }

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

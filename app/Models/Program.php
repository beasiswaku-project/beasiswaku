<?php

namespace App\Models;

use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Minimal model for the `program` table so other modules can reference it.
 * The full program module (PBI-08) builds on top of this class.
 */
#[Table('program', timestamps: false)]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tgl_buka' => 'date',
            'deadline' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Kategori, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * @return BelongsTo<Penyedia, $this>
     */
    public function penyedia(): BelongsTo
    {
        return $this->belongsTo(Penyedia::class);
    }

    /**
     * @return BelongsTo<Jenjang, $this>
     */
    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class);
    }

    /**
     * @return BelongsTo<KonfigurasiPenilaian, $this>
     */
    public function konfigurasi(): BelongsTo
    {
        return $this->belongsTo(KonfigurasiPenilaian::class, 'konfigurasi_id');
    }
}

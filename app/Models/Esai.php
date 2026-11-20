<?php

namespace App\Models;

use App\Enums\StatusEsai;
use Database\Factories\EsaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The motivation essay of a registration, read by an admin when scoring.
 * One essay per registration; `pendaftaran_id` and `status` are set by the system.
 */
#[Table('esai', timestamps: false)]
#[Fillable(['judul', 'isi'])]
class Esai extends Model
{
    /** @use HasFactory<EsaiFactory> */
    use HasFactory;

    /**
     * Minimum essay length in characters before a registration can be submitted.
     */
    public const int PANJANG_MINIMUM = 200;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => StatusEsai::Draft->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusEsai::class,
        ];
    }

    /**
     * @return BelongsTo<Pendaftaran, $this>
     */
    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function isDraft(): bool
    {
        return $this->status === StatusEsai::Draft;
    }

    /**
     * Number of words in the essay body.
     */
    public function jumlahKata(): int
    {
        return str_word_count(strip_tags((string) $this->isi));
    }
}

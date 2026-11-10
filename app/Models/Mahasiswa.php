<?php

namespace App\Models;

use Database\Factories\MahasiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A student's academic and economic profile, used for scholarship matching
 * and the economic need score. `user_id` is set from the authenticated user,
 * never from request input, so it is intentionally not fillable.
 */
#[Table('mahasiswa', timestamps: false)]
#[Fillable([
    'wilayah_id',
    'jenjang_id',
    'kampus',
    'penghasilan_ortu',
    'jml_tanggungan',
    'punya_kipk',
    'terdaftar_dtsen',
    'desil_dtsen',
    'kondisi_khusus',
])]
class Mahasiswa extends Model
{
    /** @use HasFactory<MahasiswaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'penghasilan_ortu' => 'decimal:2',
            'jml_tanggungan' => 'integer',
            'punya_kipk' => 'boolean',
            'terdaftar_dtsen' => 'boolean',
            'desil_dtsen' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Wilayah, $this>
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    /**
     * @return BelongsTo<Jenjang, $this>
     */
    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class);
    }

    /**
     * Checklist behind the profile completeness indicator, keyed by item label.
     *
     * @return array<string, bool>
     */
    public function kelengkapan(): array
    {
        return [
            'Kampus dan jenjang' => filled($this->kampus) && filled($this->jenjang_id),
            'Wilayah asal' => filled($this->wilayah_id),
            'Data ekonomi keluarga' => $this->penghasilan_ortu !== null && $this->jml_tanggungan >= 1,
            'Status DTSEN' => ! $this->terdaftar_dtsen || filled($this->desil_dtsen),
        ];
    }

    /**
     * Percentage (0–100) of completed checklist items.
     */
    public function persentaseKelengkapan(): int
    {
        $items = $this->kelengkapan();

        return (int) round(count(array_filter($items)) / count($items) * 100);
    }

    /**
     * Whether every checklist item is complete, which registration requires.
     */
    public function isLengkap(): bool
    {
        return ! in_array(false, $this->kelengkapan(), true);
    }

    /**
     * Filter profiles whose owner name, email, or campus contains the given keyword.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->whereLike('kampus', "%{$keyword}%")
                ->orWhereHas('user', fn (Builder $query) => $query
                    ->whereLike('nama', "%{$keyword}%")
                    ->orWhereLike('email', "%{$keyword}%"))
        ));
    }

    /**
     * Filter profiles by account status: `aktif`, `nonaktif`, or anything else for all.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function statusAkun(Builder $query, ?string $status): void
    {
        $query->when(
            in_array($status, ['aktif', 'nonaktif'], true),
            fn (Builder $query) => $query->whereHas('user', fn (Builder $query) => $query->where('is_active', $status === 'aktif')),
        );
    }
}

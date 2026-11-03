<?php

namespace App\Models;

use Database\Factories\WilayahFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A regency/city together with its province, 3T flag, and priority score (0–100)
 * used as S_wilayah in the economic score.
 */
#[Table('wilayah', timestamps: false)]
#[Fillable(['provinsi', 'kabupaten', 'is_3t', 'skor_prioritas'])]
class Wilayah extends Model
{
    /** @use HasFactory<WilayahFactory> */
    use HasFactory;

    /**
     * Default priority score for a 3T regency when the admin leaves it empty.
     */
    public const int DEFAULT_SKOR_3T = 100;

    /**
     * Default priority score for a non-3T regency when the admin leaves it empty.
     */
    public const int DEFAULT_SKOR_NON_3T = 0;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_3t' => 'boolean',
            'skor_prioritas' => 'integer',
        ];
    }

    /**
     * Student profiles whose home region is this regency.
     *
     * @return HasMany<Mahasiswa, $this>
     */
    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    /**
     * A region may only be deleted while no student profile uses it.
     */
    public function isDeletable(): bool
    {
        return ! $this->mahasiswa()->exists();
    }

    /**
     * Filter regions whose regency or province contains the given keyword.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->whereLike('kabupaten', "%{$keyword}%")
                ->orWhereLike('provinsi', "%{$keyword}%")
        ));
    }

    /**
     * Filter regions in the given province.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function inProvinsi(Builder $query, ?string $provinsi): void
    {
        $query->when($provinsi, fn (Builder $query) => $query->where('provinsi', $provinsi));
    }

    /**
     * Filter regions by 3T status: `3t`, `non_3t`, or anything else for all regions.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function status3t(Builder $query, ?string $status): void
    {
        $query->when(in_array($status, ['3t', 'non_3t'], true), fn (Builder $query) => $query->where('is_3t', $status === '3t'));
    }
}

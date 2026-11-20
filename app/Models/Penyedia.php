<?php

namespace App\Models;

use App\Enums\JenisPenyedia;
use Database\Factories\PenyediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A scholarship provider (government, company, campus, or foundation).
 */
#[Table('penyedia', timestamps: false)]
#[Fillable(['nama_penyedia', 'jenis', 'kontak', 'alamat'])]
class Penyedia extends Model
{
    /** @use HasFactory<PenyediaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis' => JenisPenyedia::class,
        ];
    }

    /**
     * Scholarship programs offered by this provider.
     *
     * @return HasMany<Program, $this>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * A provider may only be deleted while it has no program.
     */
    public function isDeletable(): bool
    {
        return ! $this->programs()->exists();
    }

    /**
     * Filter providers whose name or contact contains the given keyword.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->whereLike('nama_penyedia', "%{$keyword}%")
                ->orWhereLike('kontak', "%{$keyword}%")
        ));
    }

    /**
     * Filter providers by type; unknown values show every provider.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ofJenis(Builder $query, ?string $jenis): void
    {
        $query->when(JenisPenyedia::tryFrom((string) $jenis), fn (Builder $query, JenisPenyedia $jenis) => $query->where('jenis', $jenis));
    }
}

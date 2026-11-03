<?php

namespace App\Models;

use Database\Factories\JenjangFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('jenjang', timestamps: false)]
#[Fillable(['nama_jenjang', 'keterangan'])]
class Jenjang extends Model
{
    /** @use HasFactory<JenjangFactory> */
    use HasFactory;

    /**
     * Student profiles currently at this education level.
     *
     * @return HasMany<Mahasiswa, $this>
     */
    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    /**
     * Education history entries recorded at this level.
     *
     * @return HasMany<RiwayatPendidikan, $this>
     */
    public function riwayatPendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    /**
     * Scholarship programs targeting this level.
     *
     * @return HasMany<Program, $this>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * A level may only be deleted while no profile, education history, or program uses it.
     */
    public function isDeletable(): bool
    {
        return ! $this->mahasiswa()->exists()
            && ! $this->riwayatPendidikan()->exists()
            && ! $this->programs()->exists();
    }

    /**
     * Filter levels whose name or description contains the given keyword.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->whereLike('nama_jenjang', "%{$keyword}%")
                ->orWhereLike('keterangan', "%{$keyword}%")
        ));
    }
}

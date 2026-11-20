<?php

namespace App\Models;

use App\Enums\StatusProgram;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A scholarship program offered by a provider. `status` is changed through its
 * own endpoint and `konfigurasi_id` is assigned by the system, so neither is fillable.
 */
#[Table('program', timestamps: false)]
#[Fillable(['penyedia_id', 'kategori_id', 'jenjang_id', 'judul', 'kuota', 'tgl_buka', 'deadline'])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => StatusProgram::Draft->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kuota' => 'integer',
            'tgl_buka' => 'date',
            'deadline' => 'date',
            'status' => StatusProgram::class,
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

    /**
     * @return HasMany<Pendaftaran, $this>
     */
    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    /**
     * A program may only be deleted while nobody has registered for it.
     */
    public function isDeletable(): bool
    {
        return ! $this->pendaftaran()->exists();
    }

    /**
     * Whether students can currently see and apply to the program: status
     * `dibuka`, already opened, and the deadline has not passed.
     */
    public function isTerbuka(): bool
    {
        return $this->status === StatusProgram::Dibuka
            && ($this->tgl_buka === null || $this->tgl_buka->lte(today()))
            && ($this->deadline === null || $this->deadline->gte(today()));
    }

    /**
     * Programs students can currently see and apply to; see isTerbuka().
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function terbuka(Builder $query): void
    {
        $query->where('status', StatusProgram::Dibuka)
            ->where(fn (Builder $query) => $query->whereNull('tgl_buka')->orWhereDate('tgl_buka', '<=', today()))
            ->where(fn (Builder $query) => $query->whereNull('deadline')->orWhereDate('deadline', '>=', today()));
    }

    /**
     * Filter programs whose title contains the given keyword.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, fn (Builder $query) => $query->whereLike('judul', "%{$keyword}%"));
    }

    /**
     * Filter by category, level, and status; empty or unknown values are ignored.
     *
     * @param  Builder<self>  $query
     * @param  array{kategori?: int|string|null, jenjang?: int|string|null, status?: string|null}  $filter
     */
    #[Scope]
    protected function filter(Builder $query, array $filter): void
    {
        $query
            ->when($filter['kategori'] ?? null, fn (Builder $query, int|string $id) => $query->where('kategori_id', $id))
            ->when($filter['jenjang'] ?? null, fn (Builder $query, int|string $id) => $query->where('jenjang_id', $id))
            ->when(StatusProgram::tryFrom((string) ($filter['status'] ?? '')), fn (Builder $query, StatusProgram $status) => $query->where('status', $status));
    }
}

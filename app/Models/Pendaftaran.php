<?php

namespace App\Models;

use App\Enums\StatusPendaftaran;
use Database\Factories\PendaftaranFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A student's registration for a program. Every column is set by the system
 * (owner, status, submission snapshot, scores), so nothing is mass assignable.
 */
#[Table('pendaftaran', timestamps: false)]
class Pendaftaran extends Model
{
    /** @use HasFactory<PendaftaranFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => StatusPendaftaran::Draft->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusPendaftaran::class,
            'tgl_kirim' => 'datetime',
            'snapshot_ekonomi' => 'array',
            'skor_ekonomi' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
            'peringkat' => 'integer',
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
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Uploaded documents, mandatory ones first.
     *
     * @return HasMany<Dokumen, $this>
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class)->orderBy('opsional')->orderBy('id');
    }

    /**
     * Names of the documents that must be uploaded: the program's document
     * requirements plus proof of every assistance status the student declared.
     *
     * @return list<string>
     */
    public function dokumenWajib(): array
    {
        $dokumen = $this->program->persyaratan
            ->filter(fn (Persyaratan $persyaratan): bool => $persyaratan->jenis->isDokumen())
            ->pluck('nilai')
            ->all();

        if ($this->mahasiswa->punya_kipk) {
            $dokumen[] = Dokumen::BUKTI_KIPK;
        }

        if ($this->mahasiswa->terdaftar_dtsen) {
            $dokumen[] = Dokumen::BUKTI_DTSEN;
        }

        return array_values(array_unique($dokumen));
    }

    /**
     * Mandatory documents that have not been uploaded yet.
     *
     * @return list<string>
     */
    public function dokumenWajibBelumDiunggah(): array
    {
        return array_values(array_diff($this->dokumenWajib(), $this->dokumen->pluck('jenis_dokumen')->all()));
    }

    /**
     * Documents can be uploaded or replaced until the administrative check ends,
     * so a student can fix a rejected proof after submitting.
     */
    public function isMenerimaDokumen(): bool
    {
        return in_array($this->status, [StatusPendaftaran::Draft, StatusPendaftaran::Dikirim, StatusPendaftaran::Verifikasi], true);
    }

    public function isDraft(): bool
    {
        return $this->status === StatusPendaftaran::Draft;
    }

    /**
     * A registration can be cancelled until the deadline, as long as the
     * administrative check has not started.
     */
    public function isDapatDibatalkan(): bool
    {
        return in_array($this->status, [StatusPendaftaran::Draft, StatusPendaftaran::Dikirim], true)
            && ($this->program->deadline === null || $this->program->deadline->gte(today()));
    }

    /**
     * Reasons the draft cannot be submitted yet; empty when it is ready.
     *
     * @return list<string>
     */
    public function kendalaPengiriman(): array
    {
        $kendala = [];

        if (! $this->isDraft()) {
            $kendala[] = 'Pendaftaran sudah dikirim.';
        }

        if (! $this->program->isTerbuka()) {
            $kendala[] = 'Program sudah ditutup atau melewati deadline.';
        }

        if (! $this->mahasiswa->isLengkap()) {
            $kendala[] = 'Profil belum lengkap.';
        }

        if ($belumDiunggah = $this->dokumenWajibBelumDiunggah()) {
            $kendala[] = 'Dokumen wajib belum diunggah: '.implode(', ', $belumDiunggah).'.';
        }

        return $kendala;
    }

    /**
     * Submit the draft: lock in a snapshot of the student's economic data and
     * latest GPA so later profile changes do not affect selection (KNF-14).
     */
    public function kirim(): void
    {
        $mahasiswa = $this->mahasiswa;

        $this->snapshot_ekonomi = [
            'penghasilan_ortu' => (float) $mahasiswa->penghasilan_ortu,
            'jml_tanggungan' => $mahasiswa->jml_tanggungan,
            'punya_kipk' => $mahasiswa->punya_kipk,
            'terdaftar_dtsen' => $mahasiswa->terdaftar_dtsen,
            'desil_dtsen' => $mahasiswa->desil_dtsen,
            'kondisi_khusus' => $mahasiswa->kondisi_khusus,
            'wilayah_id' => $mahasiswa->wilayah_id,
            'wilayah' => "{$mahasiswa->wilayah->kabupaten}, {$mahasiswa->wilayah->provinsi}",
            'is_3t' => $mahasiswa->wilayah->is_3t,
            'skor_prioritas_wilayah' => $mahasiswa->wilayah->skor_prioritas,
            'jenjang_id' => $mahasiswa->jenjang_id,
            'ipk_terakhir' => $mahasiswa->ipkTerakhir(),
            'semester_terakhir' => $mahasiswa->semesterTerakhir(),
        ];
        $this->status = StatusPendaftaran::Dikirim;
        $this->tgl_kirim = now();
        $this->save();
    }

    /**
     * Filter registrations whose student name or program title contains the keyword.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->whereHas('mahasiswa.user', fn (Builder $query) => $query->whereLike('nama', "%{$keyword}%"))
                ->orWhereHas('program', fn (Builder $query) => $query->whereLike('judul', "%{$keyword}%"))
        ));
    }

    /**
     * Filter by program and status; empty or unknown values are ignored.
     *
     * @param  Builder<self>  $query
     * @param  array{program?: int|string|null, status?: string|null}  $filter
     */
    #[Scope]
    protected function filter(Builder $query, array $filter): void
    {
        $query
            ->when($filter['program'] ?? null, fn (Builder $query, int|string $id) => $query->where('program_id', $id))
            ->when(StatusPendaftaran::tryFrom((string) ($filter['status'] ?? '')), fn (Builder $query, StatusPendaftaran $status) => $query->where('status', $status));
    }

    /**
     * Registrations that have been submitted (anything past draft).
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function terkirim(Builder $query): void
    {
        $query->where('status', '!=', StatusPendaftaran::Draft);
    }
}

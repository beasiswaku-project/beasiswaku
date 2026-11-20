<?php

namespace App\Models;

use App\Enums\JenisPersyaratan;
use Database\Factories\PersyaratanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single requirement of a program, either mandatory (`wajib`) or a preference.
 * `program_id` comes from the route, so it is not fillable.
 */
#[Table('persyaratan', timestamps: false)]
#[Fillable(['jenis', 'nilai', 'wajib'])]
class Persyaratan extends Model
{
    /** @use HasFactory<PersyaratanFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis' => JenisPersyaratan::class,
            'wajib' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Human readable description, e.g. "IPK minimum 3,00".
     */
    public function deskripsi(): string
    {
        return $this->jenis->deskripsi($this->nilai);
    }

    /**
     * Whether the student's current profile meets this requirement. DTSEN uses
     * the status declared on the profile, since proof is verified only after
     * registering. Document requirements always count as met here because they
     * are fulfilled by uploading a file.
     */
    public function isTerpenuhiOleh(Mahasiswa $mahasiswa): bool
    {
        return match ($this->jenis) {
            JenisPersyaratan::IpkMinimum => ($mahasiswa->ipkTerakhir() ?? -1) >= (float) $this->nilai,
            JenisPersyaratan::PenghasilanMaksimum => (float) $mahasiswa->penghasilan_ortu <= (float) $this->nilai,
            JenisPersyaratan::SemesterMinimum => ($mahasiswa->semesterTerakhir() ?? 0) >= (int) $this->nilai,
            JenisPersyaratan::Kipk => $mahasiswa->punya_kipk,
            JenisPersyaratan::Dtsen => $mahasiswa->terdaftar_dtsen
                && $mahasiswa->desil_dtsen !== null
                && $mahasiswa->desil_dtsen <= (int) ($this->nilai ?: KonfigurasiPenilaian::AMBANG_AWAL['desil_dtsen_maksimum']),
            JenisPersyaratan::Wilayah3t => $mahasiswa->wilayah->is_3t,
            JenisPersyaratan::Dokumen => true,
        };
    }
}

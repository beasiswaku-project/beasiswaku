<?php

namespace App\Models;

use App\Enums\JenisPrestasi;
use Database\Factories\PrestasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A non-GPA achievement or organisation membership, with an optional proof file
 * stored on the private disk. `mahasiswa_id` and `bukti_path` are set by the
 * controller, never from request input.
 */
#[Table('prestasi', timestamps: false)]
#[Fillable(['jenis', 'nama_kegiatan', 'tingkat'])]
class Prestasi extends Model
{
    /** @use HasFactory<PrestasiFactory> */
    use HasFactory;

    /**
     * Allowed achievement levels, from lowest to highest.
     *
     * @var list<string>
     */
    public const array DAFTAR_TINGKAT = ['Kampus', 'Kabupaten/kota', 'Provinsi', 'Nasional', 'Internasional'];

    /**
     * Folder on the private disk that holds proof files.
     */
    public const string FOLDER_BUKTI = 'prestasi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis' => JenisPrestasi::class,
        ];
    }

    /**
     * @return BelongsTo<Mahasiswa, $this>
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}

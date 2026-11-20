<?php

namespace App\Models;

use App\Enums\StatusVerifikasi;
use Database\Factories\DokumenFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A file uploaded for a registration, stored on the private disk and verified
 * by an admin. Columns are set explicitly by the controllers (owner, file path,
 * verification result), so nothing is mass assignable.
 */
#[Table('dokumen', timestamps: false)]
class Dokumen extends Model
{
    /** @use HasFactory<DokumenFactory> */
    use HasFactory;

    /**
     * Folder on the private disk that holds registration documents.
     */
    public const string FOLDER = 'dokumen';

    /**
     * Name of the KIP-K proof a KIP-K recipient must upload.
     */
    public const string BUKTI_KIPK = 'Bukti KIP Kuliah';

    /**
     * Name of the DTSEN proof (showing the decile) a DTSEN registrant must upload.
     */
    public const string BUKTI_DTSEN = 'Bukti status DTSEN';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status_verifikasi' => StatusVerifikasi::Menunggu->value,
        'opsional' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opsional' => 'boolean',
            'status_verifikasi' => StatusVerifikasi::class,
        ];
    }

    /**
     * @return BelongsTo<Pendaftaran, $this>
     */
    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    /**
     * A document can be replaced as long as it has not been accepted.
     */
    public function isDapatDiganti(): bool
    {
        return $this->status_verifikasi !== StatusVerifikasi::Valid;
    }

    /**
     * A document can be deleted by its owner until an admin has verified it.
     */
    public function isDapatDihapus(): bool
    {
        return $this->status_verifikasi === StatusVerifikasi::Menunggu;
    }
}

<?php

namespace Database\Factories;

use App\Enums\StatusVerifikasi;
use App\Models\Dokumen;
use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dokumen>
 */
class DokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pendaftaran_id' => Pendaftaran::factory(),
            'jenis_dokumen' => 'SKTM',
            'opsional' => false,
            'file_path' => Dokumen::FOLDER.'/'.fake()->uuid().'.pdf',
            'status_verifikasi' => StatusVerifikasi::Menunggu,
            'catatan_admin' => null,
        ];
    }

    /**
     * Indicate that an admin accepted the document.
     */
    public function valid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => StatusVerifikasi::Valid,
        ]);
    }

    /**
     * Indicate that an admin rejected the document with a note.
     */
    public function ditolak(string $catatan = 'Dokumen tidak terbaca.'): static
    {
        return $this->state(fn (array $attributes) => [
            'status_verifikasi' => StatusVerifikasi::TidakValid,
            'catatan_admin' => $catatan,
        ]);
    }
}

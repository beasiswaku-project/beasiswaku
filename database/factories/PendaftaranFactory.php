<?php

namespace Database\Factories;

use App\Enums\StatusPendaftaran;
use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pendaftaran>
 */
class PendaftaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'program_id' => Program::factory(),
            'status' => StatusPendaftaran::Draft,
        ];
    }

    /**
     * Indicate that the registration has been submitted with a snapshot.
     */
    public function dikirim(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusPendaftaran::Dikirim,
            'tgl_kirim' => now()->subDay(),
            'snapshot_ekonomi' => ['penghasilan_ortu' => 2_000_000, 'jml_tanggungan' => 4, 'ipk_terakhir' => 3.5],
        ]);
    }
}

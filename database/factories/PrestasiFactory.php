<?php

namespace Database\Factories;

use App\Enums\JenisPrestasi;
use App\Models\Mahasiswa;
use App\Models\Prestasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prestasi>
 */
class PrestasiFactory extends Factory
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
            'jenis' => fake()->randomElement(JenisPrestasi::cases()),
            'nama_kegiatan' => 'Lomba '.fake()->words(3, true),
            'tingkat' => fake()->randomElement(Prestasi::DAFTAR_TINGKAT),
            'bukti_path' => null,
        ];
    }
}

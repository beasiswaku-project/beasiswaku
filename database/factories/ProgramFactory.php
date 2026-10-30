<?php

namespace Database\Factories;

use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\KonfigurasiPenilaian;
use App\Models\Penyedia;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tglBuka = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'penyedia_id' => Penyedia::factory(),
            'kategori_id' => Kategori::factory(),
            'jenjang_id' => Jenjang::factory(),
            'konfigurasi_id' => KonfigurasiPenilaian::factory(),
            'judul' => 'Beasiswa '.fake()->words(3, true),
            'kuota' => fake()->numberBetween(10, 200),
            'tgl_buka' => $tglBuka,
            'deadline' => fake()->dateTimeBetween($tglBuka, '+3 months'),
            'status' => 'draft',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'wilayah_id' => Wilayah::factory(),
            'jenjang_id' => Jenjang::factory(),
            'kampus' => 'Universitas '.fake()->city(),
            'penghasilan_ortu' => fake()->numberBetween(500_000, 10_000_000),
            'jml_tanggungan' => fake()->numberBetween(1, 6),
            'punya_kipk' => false,
            'terdaftar_dtsen' => false,
            'desil_dtsen' => null,
            'kondisi_khusus' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\RiwayatPendidikan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiwayatPendidikan>
 */
class RiwayatPendidikanFactory extends Factory
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
            'jenjang_id' => Jenjang::factory(),
            'semester' => fake()->numberBetween(1, 8),
            'ipk' => fake()->randomFloat(2, 2, 4),
        ];
    }
}

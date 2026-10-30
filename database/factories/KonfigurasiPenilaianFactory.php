<?php

namespace Database\Factories;

use App\Models\KonfigurasiPenilaian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KonfigurasiPenilaian>
 */
class KonfigurasiPenilaianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'versi' => fake()->unique()->numberBetween(1, 10_000),
            'bobot' => [],
            'ambang' => [],
            'dibuat_oleh' => User::factory()->admin(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Jenjang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jenjang>
 */
class JenjangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_jenjang' => fake()->randomElement(['D3', 'D4', 'S1', 'S2']),
            'keterangan' => fake()->sentence(),
        ];
    }
}

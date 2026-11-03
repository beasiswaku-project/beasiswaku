<?php

namespace Database\Factories;

use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wilayah>
 */
class WilayahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provinsi' => fake()->state(),
            'kabupaten' => fake()->city(),
            'is_3t' => false,
            'skor_prioritas' => fake()->numberBetween(0, 100),
        ];
    }
}

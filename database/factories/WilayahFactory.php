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
            'kabupaten' => 'Kabupaten '.fake()->unique()->city(),
            'is_3t' => false,
            'skor_prioritas' => Wilayah::DEFAULT_SKOR_NON_3T,
        ];
    }

    /**
     * Indicate that the region is a 3T (tertinggal, terdepan, terluar) regency.
     */
    public function tertinggal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_3t' => true,
            'skor_prioritas' => Wilayah::DEFAULT_SKOR_3T,
        ]);
    }
}

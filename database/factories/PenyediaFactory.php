<?php

namespace Database\Factories;

use App\Enums\JenisPenyedia;
use App\Models\Penyedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penyedia>
 */
class PenyediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_penyedia' => fake()->company(),
            'jenis' => fake()->randomElement(JenisPenyedia::cases()),
            'kontak' => fake()->numerify('08##########'),
            'alamat' => fake()->address(),
        ];
    }
}

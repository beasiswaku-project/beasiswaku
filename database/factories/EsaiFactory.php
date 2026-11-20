<?php

namespace Database\Factories;

use App\Enums\StatusEsai;
use App\Models\Esai;
use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Esai>
 */
class EsaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pendaftaran_id' => Pendaftaran::factory(),
            'judul' => 'Motivasi saya melanjutkan kuliah',
            'isi' => fake()->paragraphs(4, true),
            'status' => StatusEsai::Draft,
        ];
    }

    /**
     * Indicate that the essay was submitted with its registration.
     */
    public function dikirim(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEsai::Dikirim,
        ]);
    }
}

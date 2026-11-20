<?php

namespace Database\Factories;

use App\Enums\JenisPersyaratan;
use App\Models\Persyaratan;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Persyaratan>
 */
class PersyaratanFactory extends Factory
{
    /**
     * Define the model's default state: a mandatory minimum GPA of 3.00.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'jenis' => JenisPersyaratan::IpkMinimum,
            'nilai' => '3.00',
            'wajib' => true,
        ];
    }

    /**
     * Indicate that the requirement is a preference rather than mandatory.
     */
    public function preferensi(): static
    {
        return $this->state(fn (array $attributes) => [
            'wajib' => false,
        ]);
    }

    /**
     * A mandatory document requirement with the given document name.
     */
    public function dokumen(string $nama = 'SKTM'): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => JenisPersyaratan::Dokumen,
            'nilai' => $nama,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pendaftaran>
 */
class PendaftaranFactory extends Factory
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
            'program_id' => Program::factory(),
            'status' => 'draft',
        ];
    }
}

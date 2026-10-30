<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with development accounts.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'nama' => 'Alya Rahma',
            'email' => 'admin@beasiswaku.test',
        ]);

        User::factory()->create([
            'nama' => 'Nadia Putri',
            'email' => 'mahasiswa@beasiswaku.test',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Jenjang;
use Illuminate\Database\Seeder;

class JenjangSeeder extends Seeder
{
    /**
     * Seed the initial education levels.
     */
    public function run(): void
    {
        $jenjang = [
            'D3' => 'Diploma Tiga',
            'D4' => 'Diploma Empat / Sarjana Terapan',
            'S1' => 'Sarjana',
            'S2' => 'Magister',
        ];

        foreach ($jenjang as $nama => $keterangan) {
            Jenjang::firstOrCreate(['nama_jenjang' => $nama], ['keterangan' => $keterangan]);
        }
    }
}

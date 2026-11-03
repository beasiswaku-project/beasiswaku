<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Seed a starter set of regencies. 3T regencies start with priority score 100, others with 0.
     */
    public function run(): void
    {
        /** @var array<int, array{provinsi: string, kabupaten: string, is_3t: bool}> $wilayah */
        $wilayah = [
            ['provinsi' => 'Papua Pegunungan', 'kabupaten' => 'Kabupaten Nduga', 'is_3t' => true],
            ['provinsi' => 'Papua Pegunungan', 'kabupaten' => 'Kabupaten Lanny Jaya', 'is_3t' => true],
            ['provinsi' => 'Nusa Tenggara Timur', 'kabupaten' => 'Kabupaten Sumba Barat Daya', 'is_3t' => true],
            ['provinsi' => 'Nusa Tenggara Timur', 'kabupaten' => 'Kota Kupang', 'is_3t' => false],
            ['provinsi' => 'Maluku', 'kabupaten' => 'Kabupaten Maluku Barat Daya', 'is_3t' => true],
            ['provinsi' => 'Sumatera Barat', 'kabupaten' => 'Kabupaten Kepulauan Mentawai', 'is_3t' => true],
            ['provinsi' => 'Jawa Barat', 'kabupaten' => 'Kota Bandung', 'is_3t' => false],
            ['provinsi' => 'Jawa Timur', 'kabupaten' => 'Kota Surabaya', 'is_3t' => false],
            ['provinsi' => 'DI Yogyakarta', 'kabupaten' => 'Kota Yogyakarta', 'is_3t' => false],
            ['provinsi' => 'DKI Jakarta', 'kabupaten' => 'Kota Jakarta Selatan', 'is_3t' => false],
        ];

        foreach ($wilayah as $item) {
            Wilayah::firstOrCreate(
                ['provinsi' => $item['provinsi'], 'kabupaten' => $item['kabupaten']],
                [
                    'is_3t' => $item['is_3t'],
                    'skor_prioritas' => $item['is_3t'] ? Wilayah::DEFAULT_SKOR_3T : Wilayah::DEFAULT_SKOR_NON_3T,
                ],
            );
        }
    }
}

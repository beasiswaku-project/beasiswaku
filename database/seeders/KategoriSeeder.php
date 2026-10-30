<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Seed the initial scholarship categories.
     */
    public function run(): void
    {
        $kategori = [
            'Prestasi Akademik' => 'Beasiswa berdasarkan prestasi akademik',
            'Ekonomi' => 'Dukungan untuk kondisi ekonomi',
            'KIP-K' => 'Lanjutan dukungan bagi penerima Kartu Indonesia Pintar Kuliah',
            'Afirmasi 3T' => 'Kesempatan untuk mahasiswa dari wilayah tertinggal, terdepan, dan terluar',
            'Difabel' => 'Dukungan bagi mahasiswa penyandang disabilitas',
            'Organisasi' => 'Prestasi dalam organisasi mahasiswa',
            'Keagamaan' => 'Dukungan berdasarkan kegiatan keagamaan',
        ];

        foreach ($kategori as $nama => $deskripsi) {
            Kategori::firstOrCreate(['nama_kategori' => $nama], ['deskripsi' => $deskripsi]);
        }
    }
}

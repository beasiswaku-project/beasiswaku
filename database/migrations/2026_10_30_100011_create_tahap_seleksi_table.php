<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tahap_seleksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('program')->cascadeOnDelete();
            $table->enum('nama_tahap', ['administrasi', 'esai', 'wawancara', 'penetapan']);
            $table->integer('urutan');
            $table->enum('status', ['belum_mulai', 'berjalan', 'selesai'])->default('belum_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahap_seleksi');
    }
};

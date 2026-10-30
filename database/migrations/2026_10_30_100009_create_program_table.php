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
        Schema::create('program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyedia_id')->constrained('penyedia');
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->foreignId('jenjang_id')->constrained('jenjang');
            $table->foreignId('konfigurasi_id')->constrained('konfigurasi_penilaian');
            $table->string('judul', 200);
            $table->integer('kuota')->default(0);
            $table->date('tgl_buka')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['draft', 'dibuka', 'ditutup', 'selesai'])->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program');
    }
};

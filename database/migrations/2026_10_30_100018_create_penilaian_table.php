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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('tahap_id')->constrained('tahap_seleksi');
            $table->enum('komponen', ['ekonomi', 'akademik', 'prestasi', 'esai', 'wawancara']);
            $table->decimal('nilai', 6, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->text('alasan_hapus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};

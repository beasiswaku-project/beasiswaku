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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa');
            $table->foreignId('program_id')->constrained('program');
            $table->enum('status', ['draft', 'dikirim', 'verifikasi', 'seleksi', 'lolos', 'tidak_lolos', 'diterima'])->default('draft');
            $table->dateTime('tgl_kirim')->nullable();
            $table->json('snapshot_ekonomi')->nullable();
            $table->decimal('skor_ekonomi', 6, 2)->nullable();
            $table->decimal('nilai_akhir', 6, 2)->nullable();
            $table->integer('peringkat')->nullable();

            $table->unique(['mahasiswa_id', 'program_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};

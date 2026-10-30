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
        Schema::create('laporan_studi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerima_id')->constrained('penerima')->cascadeOnDelete();
            $table->integer('semester');
            $table->decimal('ipk_semester', 3, 2)->nullable();
            $table->text('kendala')->nullable();
            $table->string('bukti_path')->nullable();
            $table->enum('status_review', ['menunggu', 'disetujui', 'revisi'])->default('menunggu');
            $table->text('catatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_studi');
    }
};

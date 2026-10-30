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
        Schema::create('konfigurasi_penilaian', function (Blueprint $table) {
            $table->id();
            $table->integer('versi')->unique();
            $table->json('bobot')->nullable();
            $table->json('ambang')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_penilaian');
    }
};

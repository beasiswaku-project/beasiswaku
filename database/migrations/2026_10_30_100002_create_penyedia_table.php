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
        Schema::create('penyedia', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penyedia', 150);
            $table->enum('jenis', ['pemerintah', 'swasta', 'kampus', 'yayasan']);
            $table->string('kontak', 100)->nullable();
            $table->string('alamat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyedia');
    }
};

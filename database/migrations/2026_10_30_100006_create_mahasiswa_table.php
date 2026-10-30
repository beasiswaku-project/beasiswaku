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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('wilayah_id')->constrained('wilayah');
            $table->foreignId('jenjang_id')->constrained('jenjang');
            $table->string('kampus', 150)->nullable();
            $table->decimal('penghasilan_ortu', 15, 2)->default(0);
            $table->integer('jml_tanggungan')->default(0);
            $table->boolean('punya_kipk')->default(false);
            $table->boolean('terdaftar_dtsen')->default(false);
            $table->integer('desil_dtsen')->nullable();
            $table->string('kondisi_khusus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};

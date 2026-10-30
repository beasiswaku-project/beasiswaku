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
        Schema::create('pencairan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerima_id')->constrained('penerima')->cascadeOnDelete();
            $table->string('periode', 50)->nullable();
            $table->decimal('jumlah', 15, 2)->nullable();
            $table->enum('status', ['menunggu', 'dicairkan', 'gagal'])->default('menunggu');
            $table->date('tgl_cair')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan');
    }
};

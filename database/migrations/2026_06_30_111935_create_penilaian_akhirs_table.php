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
        Schema::create('penilaian_akhirs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftaran_id')->unique()->constrained('pendaftaran_praktikums')->onDelete('cascade');
            $table->json('nilai_dosen')->nullable();
            $table->integer('nilai_laporan')->nullable();
            $table->integer('nilai_tugas_akhir')->nullable();
            $table->decimal('total_praktikum', 5, 2)->nullable();
            $table->decimal('total_asistensi', 5, 2)->nullable();
            $table->decimal('total_praktikum_asistensi', 5, 2)->nullable();
            $table->decimal('total_dosen', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('nilai_huruf', 3)->nullable();
            $table->string('status_kelulusan', 20)->nullable();
            $table->boolean('is_gugur')->default(false);
            $table->text('alasan_gugur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_akhirs');
    }
};

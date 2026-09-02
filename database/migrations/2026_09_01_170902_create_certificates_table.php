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
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('praktikum_id')->unique()->constrained('praktikums')->onDelete('cascade');
            $table->string('nomor_surat_prefix')->nullable();
            $table->string('bg_template')->nullable();
            $table->string('nama_kepala_lab')->nullable();
            $table->string('nip_kepala_lab')->nullable();
            $table->string('ttd_kepala_lab')->nullable();
            $table->string('nama_kaprodi')->nullable();
            $table->string('nip_kaprodi')->nullable();
            $table->string('ttd_kaprodi')->nullable();
            $table->date('tanggal_sertifikat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

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
        Schema::create('lab_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kepala_lab')->nullable();
            $table->string('nip_kepala_lab')->nullable();
            $table->string('ttd_kepala_lab')->nullable();
            $table->string('nama_kaprodi')->nullable();
            $table->string('nip_kaprodi')->nullable();
            $table->string('ttd_kaprodi')->nullable();
            $table->string('nomor_surat_prefix')->nullable()->default('LAB-RPL/SERT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_settings');
    }
};

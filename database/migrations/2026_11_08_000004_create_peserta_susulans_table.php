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
        Schema::create('peserta_susulans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('jadwal_id')->constrained('jadwal_praktikums')->onDelete('cascade');
            $table->foreignUuid('pendaftaran_id')->constrained('pendaftaran_praktikums')->onDelete('cascade');
            $table->enum('status', ['terdaftar', 'hadir', 'batal'])->default('terdaftar');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_id', 'pendaftaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_susulans');
    }
};


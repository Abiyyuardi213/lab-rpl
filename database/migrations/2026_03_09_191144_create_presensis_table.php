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
        Schema::create('presensis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('jadwal_id')->constrained('jadwal_praktikums')->onDelete('cascade');
            $table->foreignUuid('pendaftaran_id')->constrained('pendaftaran_praktikums')->onDelete('cascade');
            $table->dateTime('jam_masuk');
            $table->enum('status', ['hadir', 'terlambat', 'alfa'])->default('hadir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};

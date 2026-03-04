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
        Schema::create('praktikums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_praktikum')->unique();
            $table->string('nama_praktikum');
            $table->string('periode_praktikum');
            $table->integer('kuota_praktikan');
            $table->enum('status_praktikum', ['open_registration', 'on_progress', 'finished'])->default('open_registration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('praktikums');
    }
};

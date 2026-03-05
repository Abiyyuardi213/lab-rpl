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
        Schema::create('tugas_asistensis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftaran_id')->constrained('pendaftaran_praktikums')->onDelete('cascade');
            $table->foreignUuid('aslab_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->enum('status', ['pending', 'submitted', 'reviewed'])->default('pending');
            $table->string('file_mahasiswa')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('catatan_aslab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_asistensis');
    }
};

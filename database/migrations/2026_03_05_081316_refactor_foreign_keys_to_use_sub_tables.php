<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable FK checks to avoid issues during table drops/moves
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Refactor pendaftaran_praktikums
        // We drop and recreate it because renaming columns with FKs in MySQL can be painful
        Schema::dropIfExists('pendaftaran_praktikums');
        Schema::create('pendaftaran_praktikums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('praktikan_id')->constrained('praktikans')->onDelete('cascade');
            $table->foreignUuid('praktikum_id')->constrained('praktikums')->onDelete('cascade');
            $table->foreignUuid('sesi_id')->constrained('sesi_praktikums')->onDelete('cascade');
            $table->foreignUuid('aslab_id')->nullable()->constrained('aslabs')->onDelete('set null');
            $table->string('no_hp');
            $table->string('dosen_pengampu');
            $table->enum('kelas', ['pagi', 'malam']);
            $table->string('asal_kelas_mata_kuliah');
            $table->string('bukti_krs');
            $table->string('bukti_pembayaran');
            $table->string('foto_almamater');
            $table->boolean('is_mengulang')->default(false);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 2. Refactor aslab_praktikums
        Schema::dropIfExists('aslab_praktikums');
        Schema::create('aslab_praktikums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('aslab_id')->constrained('aslabs')->onDelete('cascade');
            $table->foreignUuid('praktikum_id')->constrained('praktikums')->onDelete('cascade');
            $table->integer('kuota')->default(0);
            $table->timestamps();
        });

        // 3. Refactor tugas_asistensis
        Schema::dropIfExists('tugas_asistensis');
        Schema::create('tugas_asistensis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftaran_id')->constrained('pendaftaran_praktikums')->onDelete('cascade');
            $table->foreignUuid('aslab_id')->constrained('aslabs')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->enum('status', ['pending', 'submitted', 'reviewed'])->default('pending');
            $table->string('file_mahasiswa')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('catatan_aslab')->nullable();
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not easily reversible without data loss, but we can try to restore the old structure
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tugas_asistensis');
        Schema::dropIfExists('aslab_praktikums');
        Schema::dropIfExists('pendaftaran_praktikums');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

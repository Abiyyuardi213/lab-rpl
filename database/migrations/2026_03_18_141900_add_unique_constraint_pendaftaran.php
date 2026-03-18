<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mencegah race condition pendaftaran ganda via unique constraint di level database.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->unique(['praktikan_id', 'praktikum_id'], 'unique_praktikan_praktikum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->dropUnique('unique_praktikan_praktikum');
        });
    }
};

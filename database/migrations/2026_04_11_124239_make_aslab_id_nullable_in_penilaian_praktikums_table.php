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
        Schema::table('penilaian_praktikums', function (Blueprint $table) {
            $table->uuid('aslab_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_praktikums', function (Blueprint $table) {
            $table->uuid('aslab_id')->nullable(false)->change();
        });
    }
};

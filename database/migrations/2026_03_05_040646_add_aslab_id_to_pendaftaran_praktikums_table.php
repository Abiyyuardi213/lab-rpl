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
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->foreignUuid('aslab_id')->nullable()->after('sesi_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->dropForeign(['aslab_id']);
            $table->dropColumn('aslab_id');
        });
    }
};

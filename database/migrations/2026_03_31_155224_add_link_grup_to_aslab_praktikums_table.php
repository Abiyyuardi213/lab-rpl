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
        Schema::table('aslab_praktikums', function (Blueprint $table) {
            $table->string('link_grup')->nullable()->after('kuota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aslab_praktikums', function (Blueprint $table) {
            $table->dropColumn('link_grup');
        });
    }
};

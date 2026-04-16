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
        Schema::table('aslabs', function (Blueprint $table) {
            $table->dropColumn(['achievements', 'experience', 'activities']);
        });

        Schema::table('aslabs', function (Blueprint $table) {
            $table->json('achievements')->nullable();
            $table->json('experience')->nullable();
            $table->json('activities')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aslabs', function (Blueprint $table) {
            //
        });
    }
};

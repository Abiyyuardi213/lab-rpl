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
        Schema::table('aslab_achievements', function (Blueprint $table) {
            $table->string('start_year')->nullable()->after('name');
            $table->string('end_year')->nullable()->after('start_year');
        });

        Schema::table('aslab_experiences', function (Blueprint $table) {
            $table->string('start_year')->nullable()->after('name');
            $table->string('end_year')->nullable()->after('start_year');
        });

        Schema::table('aslab_activities', function (Blueprint $table) {
            $table->string('start_year')->nullable()->after('name');
            $table->string('end_year')->nullable()->after('start_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aslab_achievements', function (Blueprint $table) {
            $table->dropColumn(['start_year', 'end_year']);
        });

        Schema::table('aslab_experiences', function (Blueprint $table) {
            $table->dropColumn(['start_year', 'end_year']);
        });

        Schema::table('aslab_activities', function (Blueprint $table) {
            $table->dropColumn(['start_year', 'end_year']);
        });
    }
};

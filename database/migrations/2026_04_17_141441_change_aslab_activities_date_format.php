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
        Schema::table('aslab_activities', function (Blueprint $table) {
            // Check if columns exist before dropping to avoid errors if partially run
            if (Schema::hasColumn('aslab_activities', 'start_year')) {
                $table->dropColumn('start_year');
            }
            if (Schema::hasColumn('aslab_activities', 'end_year')) {
                $table->dropColumn('end_year');
            }
            
            // Add month if not exists
            if (!Schema::hasColumn('aslab_activities', 'month')) {
                $table->string('month')->nullable()->after('name');
            }
            
            // Year already exists from initial migration, so we don't need to add it again
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aslab_activities', function (Blueprint $table) {
            if (Schema::hasColumn('aslab_activities', 'month')) {
                $table->dropColumn('month');
            }
            $table->string('start_year')->nullable()->after('name');
            $table->string('end_year')->nullable()->after('start_year');
        });
    }
};

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
        Schema::table('recruitment_periods', function (Blueprint $table) {
            $table->string('whatsapp_link')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_periods', function (Blueprint $table) {
            $table->dropColumn('whatsapp_link');
        });
    }
};

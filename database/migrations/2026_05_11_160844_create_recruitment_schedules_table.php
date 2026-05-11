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
        Schema::create('recruitment_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recruitment_period_id')->constrained('recruitment_periods')->onDelete('cascade');
            $table->string('name'); 
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('aslab_application_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('aslab_application_id')->constrained('aslab_applications')->onDelete('cascade');
            $table->foreignUuid('recruitment_schedule_id')->constrained('recruitment_schedules')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aslab_application_schedule');
        Schema::dropIfExists('recruitment_schedules');
    }
};

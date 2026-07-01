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
        Schema::create('guest_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('visit_date');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->text('activity_purpose');
            $table->string('guest_name');
            $table->unsignedSmallInteger('guest_count')->default(1);
            $table->string('lab_condition');
            $table->text('additional_note')->nullable();
            $table->timestamps();

            $table->index(['visit_date', 'ended_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_visits');
    }
};

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
        Schema::create('activity_logs', function (Blueprint $row) {
            $row->id();
            $row->uuid('user_id')->nullable();
            $row->string('activity');
            $row->text('description')->nullable();
            $row->json('data')->nullable(); // For metadata like old/new values
            $row->string('role')->nullable(); // Role of the user at that time
            $row->string('ip_address')->nullable();
            $row->string('user_agent')->nullable();
            $row->timestamps();

            $row->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

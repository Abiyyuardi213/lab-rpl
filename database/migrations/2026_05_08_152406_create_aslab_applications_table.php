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
        Schema::create('aslab_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recruitment_period_id')->constrained('recruitment_periods')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            
            // Uploaded Documents
            $table->string('cv_path');
            $table->string('khs_path');
            $table->string('portfolio_url')->nullable();
            $table->text('motivation_letter')->nullable();
            
            // Status Management
            $table->enum('status', ['pending', 'shortlisted', 'rejected', 'accepted'])->default('pending');
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aslab_applications');
    }
};

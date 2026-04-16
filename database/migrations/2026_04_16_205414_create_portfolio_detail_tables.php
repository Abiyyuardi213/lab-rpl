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
        Schema::dropIfExists('aslab_achievements');
        Schema::dropIfExists('aslab_experiences');
        Schema::dropIfExists('aslab_activities');

        Schema::create('aslab_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('aslab_id')->constrained('aslabs')->onDelete('cascade');
            $table->string('name');
            $table->string('year')->nullable();
            $table->timestamps();
        });

        Schema::create('aslab_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('aslab_id')->constrained('aslabs')->onDelete('cascade');
            $table->string('name');
            $table->string('year')->nullable();
            $table->timestamps();
        });

        Schema::create('aslab_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('aslab_id')->constrained('aslabs')->onDelete('cascade');
            $table->string('name');
            $table->string('year')->nullable();
            $table->timestamps();
        });

        Schema::table('aslabs', function (Blueprint $table) {
            $table->dropColumn(['achievements', 'experience', 'activities']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aslabs', function (Blueprint $table) {
            $table->json('achievements')->nullable();
            $table->json('experience')->nullable();
            $table->json('activities')->nullable();
        });

        Schema::dropIfExists('aslab_achievements');
        Schema::dropIfExists('aslab_experiences');
        Schema::dropIfExists('aslab_activities');
    }
};

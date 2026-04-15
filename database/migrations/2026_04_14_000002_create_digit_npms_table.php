<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('digit_npms', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('digit')->unique();
            $table->string('label');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $digits = [];

        for ($digit = 0; $digit <= 9; $digit++) {
            $digits[] = [
                'digit' => $digit,
                'label' => 'Digit Akhir: ' . $digit,
                'is_active' => true,
                'sort_order' => $digit,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('digit_npms')->insert($digits);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digit_npms');
    }
};

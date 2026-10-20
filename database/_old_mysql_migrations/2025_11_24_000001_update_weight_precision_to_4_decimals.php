<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update weight column precision from 2 decimals to 4 decimals
     * This allows for more precise weight distributions (e.g., 33.3333%)
     */
    public function up(): void
    {
        Schema::table('subject_grade_criteria', function (Blueprint $table) {
            // Change weight from decimal(5,2) to decimal(7,4)
            // This allows values like 33.3333 instead of just 33.33
            $table->decimal('weight', 7, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_grade_criteria', function (Blueprint $table) {
            // Revert back to decimal(5,2)
            $table->decimal('weight', 5, 2)->change();
        });
    }
};

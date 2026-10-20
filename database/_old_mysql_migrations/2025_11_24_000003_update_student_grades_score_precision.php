<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update score column precision from 2 decimals to 4 decimals in student_grades table
     * This allows for more precise grade scores
     */
    public function up(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            // Change score from decimal(5,2) to decimal(7,4)
            $table->decimal('score', 7, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            // Revert back to decimal(5,2)
            $table->decimal('score', 5, 2)->change();
        });
    }
};

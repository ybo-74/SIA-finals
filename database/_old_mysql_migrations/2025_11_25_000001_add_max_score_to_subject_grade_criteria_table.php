<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds max_score field to allow professors to set total items per criteria
     * Example: Quiz 1 has max_score of 15 items
     */
    public function up(): void
    {
        Schema::table('subject_grade_criteria', function (Blueprint $table) {
            // Add max_score field (nullable for backward compatibility)
            $table->decimal('max_score', 7, 2)->nullable()->after('weight');
            
            // Add index for faster queries
            $table->index('max_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_grade_criteria', function (Blueprint $table) {
            $table->dropIndex(['max_score']);
            $table->dropColumn('max_score');
        });
    }
};

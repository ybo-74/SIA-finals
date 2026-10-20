<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Changes student_grades to use subject_grade_criteria instead of grade_types
     */
    public function up(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            // Drop old foreign key and column
            $table->dropForeign(['grade_type_id']);
            $table->dropColumn('grade_type_id');
            
            // Add new foreign key to subject_grade_criteria
            $table->foreignId('grade_criteria_id')
                  ->after('subject_id')
                  ->constrained('subject_grade_criteria')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['grade_criteria_id']);
            $table->dropColumn('grade_criteria_id');
            
            // Restore old foreign key
            $table->foreignId('grade_type_id')
                  ->after('subject_id')
                  ->constrained('grade_types')
                  ->onDelete('cascade');
        });
    }
};

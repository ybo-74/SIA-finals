<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Final shape: uses grade_criteria_id, score decimal(7,4). The old grade_types table is unused and dropped.
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->noActionOnDelete();
            $table->foreignId('subject_id')->constrained()->noActionOnDelete();
            $table->foreignId('grade_criteria_id')->constrained('subject_grade_criteria')->cascadeOnDelete();
            $table->decimal('score', 7, 4);
            $table->foreignId('entered_by')->constrained('users')->noActionOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};

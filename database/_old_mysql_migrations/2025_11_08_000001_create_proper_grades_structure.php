<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create grade_types table
        Schema::create('grade_types', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // 'lecture', 'laboratory', 'exams'
            $table->string('name'); // 'Quizzes', 'Lab Activity', etc.
            $table->decimal('weight', 5, 2); // Percentage weight
            $table->boolean('admin_only')->default(false); // For Term Exam and all Exams
            $table->timestamps();
        });

        // Create student_grades table (replaces lab_grades, lecture_grades, exam_grades)
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('grade_type_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2);
            $table->foreignId('entered_by')->constrained('users'); // Track who entered
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('grade_types');
    }
};

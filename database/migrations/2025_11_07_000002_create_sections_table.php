<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('year_level');
            $table->string('academic_year')->default('2024-2025');
            $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer'])->default('1st Semester');
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            // NO ACTION to avoid multiple cascade paths on SQL Server (handled in User::deleting)
            $table->foreignId('professor_id')->constrained('users')->noActionOnDelete();
            $table->timestamps();

            $table->index('academic_year');
            $table->index('semester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};

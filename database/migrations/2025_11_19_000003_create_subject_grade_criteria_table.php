<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Final shape: includes weight decimal(7,4) and max_score from the later migrations
        Schema::create('subject_grade_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->enum('term', ['Prelim', 'Midterm', 'Finals']);
            $table->enum('category', ['lecture', 'laboratory']);
            $table->string('name');
            $table->decimal('weight', 7, 4);
            $table->decimal('max_score', 7, 2)->nullable();
            $table->boolean('admin_only')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['subject_id', 'term', 'category']);
            $table->index('max_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_grade_criteria');
    }
};

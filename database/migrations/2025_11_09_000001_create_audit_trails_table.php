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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            
            // Who performed the action
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_name'); // Store name for historical record
            $table->string('user_role'); // admin, professor, student
            
            // What action was performed
            $table->string('action'); // create, update, delete
            $table->string('model_type'); // StudentGrade, Section, User, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected record
            
            // Details of the change
            $table->text('description'); // Human-readable description
            $table->json('old_values')->nullable(); // Before state
            $table->json('new_values')->nullable(); // After state
            
            // Additional context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('user_id');
            $table->index('model_type');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};

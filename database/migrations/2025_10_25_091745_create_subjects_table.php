<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            // SQL Server: NO ACTION (not SET NULL) to avoid "multiple cascade paths" (error 1785).
            // The User model's deleting event clears this column before a professor is deleted.
            $table->foreignId('professor_id')->nullable()->constrained('users')->noActionOnDelete();
            $table->boolean('has_lab')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

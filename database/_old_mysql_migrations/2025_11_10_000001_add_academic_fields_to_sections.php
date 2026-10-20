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
        Schema::table('sections', function (Blueprint $table) {
            $table->string('academic_year')->after('year_level')->default('2024-2025'); // e.g., "2024-2025"
            $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer'])->after('academic_year')->default('1st Semester');
            
            // Add indexes for faster filtering
            $table->index('academic_year');
            $table->index('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex(['academic_year']);
            $table->dropIndex(['semester']);
            $table->dropColumn(['academic_year', 'semester']);
        });
    }
};

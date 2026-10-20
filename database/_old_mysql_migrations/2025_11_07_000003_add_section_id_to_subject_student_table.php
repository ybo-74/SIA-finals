<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_student', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('subject_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('subject_student', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
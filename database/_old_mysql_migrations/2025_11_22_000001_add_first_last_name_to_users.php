<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Splits the 'name' field into 'first_name' and 'last_name'
     * This migration will attempt to migrate existing data
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('middle_name')->nullable()->after('last_name'); // Optional middle name
        });

        // Migrate existing data: split 'name' into first_name and last_name
        DB::statement('
            UPDATE users 
            SET 
                first_name = SUBSTRING_INDEX(name, " ", 1),
                last_name = CASE 
                    WHEN LOCATE(" ", name) > 0 THEN SUBSTRING(name, LOCATE(" ", name) + 1)
                    ELSE ""
                END
            WHERE first_name IS NULL
        ');

        // After migration, you can optionally drop the old 'name' column
        // Uncomment the line below if you want to remove the 'name' column
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('name');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'middle_name']);
        });
    }
};

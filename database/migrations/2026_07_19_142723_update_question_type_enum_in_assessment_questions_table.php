<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL doesn't support modifying enum directly in Laravel, so we use raw SQL
        DB::statement("ALTER TABLE assessment_questions MODIFY COLUMN question_type ENUM('multiple_choice', 'scale', 'likert') DEFAULT 'multiple_choice'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE assessment_questions MODIFY COLUMN question_type ENUM('multiple_choice', 'scale') DEFAULT 'multiple_choice'");
    }
};

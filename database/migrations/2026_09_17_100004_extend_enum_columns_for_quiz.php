<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'quiz' ke ENUM assessment_type (MySQL only)
        // SQLite tidak ada ENUM jadi ini aman di-skip otomatis
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE assessments MODIFY COLUMN assessment_type 
                ENUM('vark','diagnostic','quiz') DEFAULT 'vark'");

            DB::statement("ALTER TABLE assessment_questions MODIFY COLUMN question_type
                ENUM('multiple_choice','scale','likert','true_false','matching','essay','file_upload')
                DEFAULT 'multiple_choice'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE assessments MODIFY COLUMN assessment_type 
                ENUM('vark','diagnostic') DEFAULT 'vark'");

            DB::statement("ALTER TABLE assessment_questions MODIFY COLUMN question_type
                ENUM('multiple_choice','scale','likert','true_false')
                DEFAULT 'multiple_choice'");
        }
    }
};

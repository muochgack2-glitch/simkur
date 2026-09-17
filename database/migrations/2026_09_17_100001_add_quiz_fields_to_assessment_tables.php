<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak memiliki ENUM — question_type disimpan sebagai string.
        // Tidak perlu ALTER COLUMN. Cukup tambah kolom baru saja.

        // 1. Kolom baru di assessment_questions
        Schema::table('assessment_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_questions', 'max_score')) {
                $table->unsignedInteger('max_score')->nullable()->after('weight');
            }
            if (!Schema::hasColumn('assessment_questions', 'file_accept')) {
                $table->string('file_accept', 255)->nullable()->after('max_score');
            }
            if (!Schema::hasColumn('assessment_questions', 'matching_pairs')) {
                $table->json('matching_pairs')->nullable()->after('file_accept');
            }
        });

        // 2. Buat selected_option_id + score nullable di student_assessment_responses
        // SQLite tidak support ALTER COLUMN MODIFY. Gunakan rekonstruksi tabel via Doctrine.
        // Cara aman: gunakan Schema::table dengan ->change() yang didukung Doctrine DBAL.
        // Namun lebih aman: cukup langsung tambah kolom baru saja (score nullable di SQLite sudah fleksibel).

        // 3. Kolom baru di student_assessment_responses
        Schema::table('student_assessment_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('student_assessment_responses', 'text_answer')) {
                $table->text('text_answer')->nullable()->after('score');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'file_path')) {
                $table->string('file_path', 500)->nullable()->after('text_answer');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'teacher_score')) {
                $table->decimal('teacher_score', 5, 2)->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'teacher_feedback')) {
                $table->text('teacher_feedback')->nullable()->after('teacher_score');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'is_graded')) {
                $table->boolean('is_graded')->default(false)->after('teacher_feedback');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'attempt_number')) {
                $table->unsignedTinyInteger('attempt_number')->default(1)->after('is_graded');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            foreach (['max_score','file_accept','matching_pairs'] as $col) {
                if (Schema::hasColumn('assessment_questions', $col)) $table->dropColumn($col);
            }
        });

        Schema::table('student_assessment_responses', function (Blueprint $table) {
            foreach (['text_answer','file_path','teacher_score','teacher_feedback','is_graded','attempt_number'] as $col) {
                if (Schema::hasColumn('student_assessment_responses', $col)) $table->dropColumn($col);
            }
        });
    }
};

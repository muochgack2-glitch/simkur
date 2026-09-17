<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek FK yang ada di production sebelum drop
        $fks = DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'student_assessment_responses'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        $fkNames = collect($fks)->pluck('CONSTRAINT_NAME')->toArray();

        Schema::table('student_assessment_responses', function (Blueprint $table) use ($fkNames) {
            // Drop FK jika ada (skip jika sudah tidak ada dari run sebelumnya)
            $prefix = 'student_assessment_responses_';
            foreach (['assessment_id', 'user_id', 'assessment_question_id', 'selected_option_id'] as $col) {
                if (in_array($prefix . $col . '_foreign', $fkNames)) {
                    $table->dropForeign([$col]);
                }
            }

            // Cek & drop unique lama
            $indexes = DB::select("SHOW INDEX FROM student_assessment_responses WHERE Key_name = 'unique_student_question_response'");
            if (count($indexes) > 0) {
                $table->dropUnique('unique_student_question_response');
            }

            // Ubah kolom nullable (hanya jika perlu — change() idempotent di doctrine)
            $table->unsignedBigInteger('selected_option_id')->nullable()->change();
            $table->integer('score')->nullable()->change();

            // Tambah kolom baru hanya jika belum ada
            if (!Schema::hasColumn('student_assessment_responses', 'attempt_number')) {
                $table->unsignedTinyInteger('attempt_number')->default(1)
                      ->after('assessment_question_id')
                      ->comment('Percobaan ke-berapa, sinkron dengan session');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'text_answer')) {
                $table->text('text_answer')->nullable()->after('selected_option_id')
                      ->comment('Jawaban teks untuk soal esai');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'file_path')) {
                $table->string('file_path')->nullable()->after('text_answer')
                      ->comment('Path file untuk soal upload file');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'teacher_score')) {
                $table->decimal('teacher_score', 8, 2)->nullable()->after('score')
                      ->comment('Nilai yang diberikan guru untuk soal esai/file');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'teacher_feedback')) {
                $table->text('teacher_feedback')->nullable()->after('teacher_score')
                      ->comment('Komentar/feedback guru untuk jawaban siswa');
            }
            if (!Schema::hasColumn('student_assessment_responses', 'is_graded')) {
                $table->boolean('is_graded')->default(false)->after('teacher_feedback')
                      ->comment('Apakah sudah dinilai guru');
            }

            // Tambah unique baru jika belum ada
            $newIdx = DB::select("SHOW INDEX FROM student_assessment_responses WHERE Key_name = 'unique_student_question_attempt'");
            if (count($newIdx) === 0) {
                $table->unique(
                    ['assessment_id', 'user_id', 'assessment_question_id', 'attempt_number'],
                    'unique_student_question_attempt'
                );
            }

            // Re-add FK
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('assessment_question_options')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_assessment_responses', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assessment_question_id']);
            $table->dropForeign(['selected_option_id']);
            $table->dropUnique('unique_student_question_attempt');
            $table->dropColumn(['attempt_number', 'text_answer', 'file_path', 'teacher_score', 'teacher_feedback', 'is_graded']);
            $table->unsignedBigInteger('selected_option_id')->nullable(false)->change();
            $table->integer('score')->nullable(false)->change();
            $table->unique(['assessment_id', 'user_id', 'assessment_question_id'], 'unique_student_question_response');
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('assessment_question_options')->onDelete('cascade');
        });
    }
};
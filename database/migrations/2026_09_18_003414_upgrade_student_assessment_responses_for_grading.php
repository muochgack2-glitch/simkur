<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_assessment_responses', function (Blueprint $table) {
            // 1. Drop FK yang memakai unique index sebagai backing index di MySQL
            $table->dropForeign(['assessment_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assessment_question_id']);
            $table->dropForeign(['selected_option_id']);

            // 2. Sekarang aman drop unique constraint lama
            $table->dropUnique('unique_student_question_response');

            // 3. Ubah kolom yang perlu nullable
            $table->unsignedBigInteger('selected_option_id')->nullable()->change();
            $table->integer('score')->nullable()->change();

            // 4. Tambah kolom baru
            $table->unsignedTinyInteger('attempt_number')->default(1)
                  ->after('assessment_question_id')
                  ->comment('Percobaan ke-berapa, sinkron dengan session');
            $table->text('text_answer')->nullable()->after('selected_option_id')
                  ->comment('Jawaban teks untuk soal esai');
            $table->string('file_path')->nullable()->after('text_answer')
                  ->comment('Path file untuk soal upload file');
            $table->decimal('teacher_score', 8, 2)->nullable()->after('score')
                  ->comment('Nilai yang diberikan guru untuk soal esai/file');
            $table->text('teacher_feedback')->nullable()->after('teacher_score')
                  ->comment('Komentar/feedback guru untuk jawaban siswa');
            $table->boolean('is_graded')->default(false)->after('teacher_feedback')
                  ->comment('Apakah sudah dinilai guru');

            // 5. Tambah unique constraint baru (include attempt_number)
            $table->unique(
                ['assessment_id', 'user_id', 'assessment_question_id', 'attempt_number'],
                'unique_student_question_attempt'
            );

            // 6. Re-add FK constraints
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

            $table->unique(
                ['assessment_id', 'user_id', 'assessment_question_id'],
                'unique_student_question_response'
            );

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('assessment_question_options')->onDelete('cascade');
        });
    }
};
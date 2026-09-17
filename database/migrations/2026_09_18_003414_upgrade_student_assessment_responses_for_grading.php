<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_assessment_responses', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique('unique_student_question_response');

            // Buat selected_option_id nullable (untuk esai/file yang tidak punya option)
            $table->unsignedBigInteger('selected_option_id')->nullable()->change();

            // Buat score nullable (esai belum punya skor saat submit)
            $table->integer('score')->nullable()->change();

            // Kolom baru
            $table->unsignedTinyInteger('attempt_number')->default(1)->after('assessment_question_id')
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

            // Unique constraint baru: satu jawaban per soal per siswa per attempt
            $table->unique(
                ['assessment_id', 'user_id', 'assessment_question_id', 'attempt_number'],
                'unique_student_question_attempt'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_assessment_responses', function (Blueprint $table) {
            $table->dropUnique('unique_student_question_attempt');
            $table->dropColumn(['attempt_number', 'text_answer', 'file_path', 'teacher_score', 'teacher_feedback', 'is_graded']);
            $table->unsignedBigInteger('selected_option_id')->nullable(false)->change();
            $table->integer('score')->nullable(false)->change();
            $table->unique(
                ['assessment_id', 'user_id', 'assessment_question_id'],
                'unique_student_question_response'
            );
        });
    }
};
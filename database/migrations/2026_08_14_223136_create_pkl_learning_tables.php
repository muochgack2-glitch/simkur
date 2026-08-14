<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PKL Courses - Materi pembelajaran per guru per mapel
        Schema::create('pkl_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('competency')->nullable();
            $table->json('target_classes');
            $table->integer('order')->default(0);
            $table->date('start_date');
            $table->date('deadline');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['activity_id', 'teacher_id']);
            $table->index(['academic_year_id', 'is_published']);
        });

        // 2. PKL Materials - File materi (bisa banyak per course)
        Schema::create('pkl_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_course_id')->constrained('pkl_courses')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['pdf', 'video', 'link', 'document', 'image']);
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. PKL Assignments - Tugas
        Schema::create('pkl_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_course_id')->constrained('pkl_courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('deadline');
            $table->integer('max_score')->default(100);
            $table->boolean('allow_late')->default(false);
            $table->boolean('allow_file_upload')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 4. PKL Submissions - Pengumpulan tugas siswa
        Schema::create('pkl_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_assignment_id')->constrained('pkl_assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->timestamps();

            $table->unique(['pkl_assignment_id', 'student_id']);
        });

        // 5. PKL Quizzes - Kuis online
        Schema::create('pkl_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_course_id')->constrained('pkl_courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('max_score')->default(100);
            $table->datetime('deadline');
            $table->boolean('is_published')->default(false);
            $table->boolean('shuffle_questions')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 6. PKL Quiz Questions - Soal kuis
        Schema::create('pkl_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_quiz_id')->constrained('pkl_quizzes')->cascadeOnDelete();
            $table->enum('question_type', ['multiple_choice', 'essay', 'true_false']);
            $table->text('question');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->integer('score')->default(10);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 7. PKL Quiz Responses - Jawaban siswa
        Schema::create('pkl_quiz_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_quiz_id')->constrained('pkl_quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->json('answers')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_graded')->default(false);
            $table->timestamps();

            $table->unique(['pkl_quiz_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_quiz_responses');
        Schema::dropIfExists('pkl_quiz_questions');
        Schema::dropIfExists('pkl_quizzes');
        Schema::dropIfExists('pkl_submissions');
        Schema::dropIfExists('pkl_assignments');
        Schema::dropIfExists('pkl_materials');
        Schema::dropIfExists('pkl_courses');
    }
};

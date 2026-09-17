<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_student_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('attempt_number')->default(1)
                  ->comment('Percobaan ke-berapa (retry naik 1)');
            $table->json('question_order')->nullable()
                  ->comment('Array question_id setelah diacak untuk siswa ini');
            $table->json('option_orders')->nullable()
                  ->comment('Map question_id => [option_id,...] setelah diacak');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('auto_score', 8, 2)->nullable()
                  ->comment('Skor otomatis: PG + T/F + Menjodohkan');
            $table->decimal('manual_score', 8, 2)->nullable()
                  ->comment('Skor manual guru: Esai + File. Diisi guru setelah grading');
            $table->decimal('total_score', 8, 2)->nullable()
                  ->comment('auto_score + manual_score');
            $table->decimal('max_possible_score', 8, 2)->nullable()
                  ->comment('Total max score semua soal');
            $table->timestamps();

            $table->unique(['assessment_id', 'user_id', 'attempt_number'], 'unique_session_attempt');
            $table->index(['assessment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_student_sessions');
    }
};

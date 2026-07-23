<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siswa
            $table->foreignId('assessment_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('selected_option_id')->constrained('assessment_question_options')->onDelete('cascade');
            $table->integer('score'); // Skor yang didapat
            $table->timestamp('answered_at')->useCurrent();
            $table->timestamps();

            // Ensure unique response per question per student
            $table->unique(['assessment_id', 'user_id', 'assessment_question_id'], 'unique_student_question_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessment_responses');
    }
};

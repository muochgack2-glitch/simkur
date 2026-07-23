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
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'scale'])->default('multiple_choice');
            $table->integer('order_number');
            $table->enum('learning_style_indicator', ['visual', 'auditory', 'kinesthetic', 'reading_writing']);
            $table->integer('weight')->default(1); // Bobot soal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};

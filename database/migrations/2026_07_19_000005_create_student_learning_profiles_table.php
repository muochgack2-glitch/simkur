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
        Schema::create('student_learning_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siswa
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->enum('dominant_style', ['visual', 'auditory', 'kinesthetic', 'reading_writing']);
            $table->integer('visual_score')->default(0);
            $table->integer('auditory_score')->default(0);
            $table->integer('kinesthetic_score')->default(0);
            $table->integer('reading_writing_score')->default(0);
            $table->integer('total_score')->default(0);
            $table->json('recommendations')->nullable(); // Tips belajar
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            // Ensure one profile per student per semester
            $table->unique(['user_id', 'semester_id'], 'unique_student_semester_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_learning_profiles');
    }
};

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
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            
            $table->date('date');
            $table->string('time_slot', 50); // Jam ke-1, Jam ke-2, dst
            $table->string('competence')->nullable(); // Kompetensi Dasar
            $table->text('topic'); // Materi Pokok
            $table->string('teaching_method')->nullable(); // Metode mengajar
            $table->text('notes')->nullable(); // Catatan khusus
            
            // Stats
            $table->integer('total_students')->default(0);
            $table->integer('present_count')->default(0);
            $table->integer('sick_count')->default(0);
            $table->integer('permission_count')->default(0);
            $table->integer('absent_count')->default(0);
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['teacher_id', 'date']);
            $table->index(['class_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_journals');
    }
};

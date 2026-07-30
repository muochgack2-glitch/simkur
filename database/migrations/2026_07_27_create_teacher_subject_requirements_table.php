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
        Schema::create('teacher_subject_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            
            // Tracking per kategori dokumen (7 dokumen perencanaan wajib)
            $table->boolean('has_cp')->default(false)->comment('Capaian Pembelajaran');
            $table->boolean('has_atp')->default(false)->comment('Alur Tujuan Pembelajaran');
            $table->boolean('has_kktp')->default(false)->comment('Kriteria Ketercapaian');
            $table->boolean('has_prota')->default(false)->comment('Program Tahunan');
            $table->boolean('has_prosem')->default(false)->comment('Program Semester');
            $table->boolean('has_modul_ajar')->default(false)->comment('Modul Ajar');
            $table->boolean('has_modul_projek')->default(false)->comment('Modul Projek');
            
            // Metadata
            $table->integer('completion_percentage')->default(0);
            $table->timestamp('last_upload_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['teacher_id', 'subject_id', 'academic_year_id'], 'teacher_subject_year_unique');
            
            // Indexes
            $table->index('teacher_id');
            $table->index('subject_id');
            $table->index('academic_year_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject_requirements');
    }
};

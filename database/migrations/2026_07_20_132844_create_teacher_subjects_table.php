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
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Guru
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); // Mata Pelajaran
            $table->timestamps();
            
            // Unique constraint: satu guru tidak bisa mengajar mata pelajaran yang sama 2x
            $table->unique(['user_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};

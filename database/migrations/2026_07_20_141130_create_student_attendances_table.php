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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_journal_id')->constrained('teaching_journals')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('hadir');
            $table->text('notes')->nullable(); // Catatan khusus untuk siswa (opsional)
            
            $table->timestamps();
            
            // Unique constraint: satu siswa hanya bisa punya 1 record per journal
            $table->unique(['teaching_journal_id', 'student_id']);
            
            // Index
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};

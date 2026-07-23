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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Kelas (e.g., X MPLB, XI AKL)
            $table->enum('grade', ['X', 'XI', 'XII']); // Tingkat kelas
            $table->enum('major', ['MPLB', 'AKL', 'BUSANA']); // Jurusan
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // Tahun Ajaran
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->onDelete('set null'); // Wali Kelas (guru)
            $table->integer('capacity')->default(36); // Kapasitas siswa
            $table->string('room')->nullable(); // Ruangan kelas
            $table->boolean('is_active')->default(true); // Status aktif
            $table->timestamps();
            
            // Index untuk query performance
            $table->index(['grade', 'major']);
            $table->index('academic_year_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};

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
        Schema::create('effective_days_by_grade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effective_day_id')->constrained()->cascadeOnDelete();
            $table->enum('grade', ['X', 'XI', 'XII'])->comment('Tingkat kelas');
            
            // Date range for this grade (dapat berbeda per grade)
            $table->date('start_date')->comment('Tanggal mulai KBM untuk grade ini');
            $table->date('end_date')->comment('Tanggal selesai KBM untuk grade ini');
            
            // Calculation results
            $table->integer('total_days')->default(0)->comment('Total hari dalam periode');
            $table->integer('weekend_days')->default(0)->comment('Jumlah hari weekend');
            $table->integer('holiday_days')->default(0)->comment('Jumlah hari libur (weekdays only)');
            $table->integer('exam_days')->default(0)->comment('Jumlah hari ujian (weekdays only)');
            $table->integer('study_days')->default(0)->comment('Jumlah hari belajar efektif');
            $table->decimal('effective_weeks', 5, 2)->default(0)->comment('Jumlah minggu efektif');
            $table->decimal('percentage', 5, 2)->default(0)->comment('Persentase hari efektif');
            
            // Exam breakdown (optional, untuk detail)
            $table->text('exam_notes')->nullable()->comment('Catatan ujian (UTS, UAS, Ujian Sekolah, dll)');
            
            // Metadata
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('effective_day_id');
            $table->index('grade');
            $table->unique(['effective_day_id', 'grade'], 'effective_day_grade_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effective_days_by_grade');
    }
};

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
        Schema::create('effective_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('total_days')->default(0)->comment('Total hari dalam semester');
            $table->integer('study_days')->default(0)->comment('Hari belajar efektif');
            $table->integer('holiday_days')->default(0)->comment('Hari libur');
            $table->integer('exam_days')->default(0)->comment('Hari ujian');
            $table->decimal('effective_weeks', 5, 2)->default(0)->comment('Minggu efektif');
            $table->timestamp('calculated_at')->nullable()->comment('Terakhir dihitung');
            $table->timestamps();

            $table->index('semester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effective_days');
    }
};

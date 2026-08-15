<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_assessment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', ['school', 'company']);
            $table->decimal('weight', 5, 2);
            $table->integer('max_score')->default(100);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pkl_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained('pkl_placements')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('pkl_assessment_components')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->foreignId('assessor_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['pkl_placement_id', 'component_id'], 'pkl_assessment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_assessments');
        Schema::dropIfExists('pkl_assessment_components');
    }
};
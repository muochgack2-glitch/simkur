<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained('pkl_placements')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('journal_date');
            $table->text('activities');
            $table->text('learnings')->nullable();
            $table->text('challenges')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'revision'])->default('draft');
            $table->text('supervisor_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['pkl_placement_id', 'journal_date'], 'pkl_journal_unique');
        });

        Schema::create('pkl_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pkl_company_id')->constrained('pkl_companies')->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->date('actual_date')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'missed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_visits');
        Schema::dropIfExists('pkl_journals');
    }
};
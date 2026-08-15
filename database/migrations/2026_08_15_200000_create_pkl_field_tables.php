<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->integer('capacity')->default(5);
            $table->string('business_field')->nullable();
            $table->json('suitable_departments')->nullable();
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pkl_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pkl_company_id')->constrained('pkl_companies')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled', 'moved'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['academic_year_id', 'student_id'], 'pkl_place_ay_stu_unique');
        });

        Schema::create('pkl_placement_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained('pkl_placements')->cascadeOnDelete();
            $table->foreignId('from_company_id')->constrained('pkl_companies');
            $table->foreignId('to_company_id')->constrained('pkl_companies');
            $table->text('reason');
            $table->foreignId('moved_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('pkl_company_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pkl_company_id')->constrained('pkl_companies')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['academic_year_id', 'teacher_id', 'pkl_company_id'], 'pkl_sup_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_company_supervisors');
        Schema::dropIfExists('pkl_placement_moves');
        Schema::dropIfExists('pkl_placements');
        Schema::dropIfExists('pkl_companies');
    }
};
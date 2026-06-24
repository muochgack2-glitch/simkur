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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('restrict');
            $table->foreignId('semester_id')->constrained()->onDelete('restrict');
            $table->foreignId('activity_type_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('color', 7)->default('#3B82F6')->comment('Override color dari activity_type');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('academic_year_id');
            $table->index('semester_id');
            $table->index('activity_type_id');
            $table->index('created_by');
            $table->index(['start_date', 'end_date']);
            $table->index(['academic_year_id', 'start_date', 'end_date']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

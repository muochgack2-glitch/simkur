<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->integer('period_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_year_id', 'period_number']);
        });

        Schema::table('pkl_courses', function (Blueprint $table) {
            $table->foreignId('pkl_period_id')->nullable()->after('academic_year_id')->constrained('pkl_periods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pkl_courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pkl_period_id');
        });
        Schema::dropIfExists('pkl_periods');
    }
};
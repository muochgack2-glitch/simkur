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
        Schema::table('assessments', function (Blueprint $table) {
            // Add target_majors field for targeting specific majors
            $table->json('target_majors')
                ->nullable()
                ->after('target_grades')
                ->comment('Target jurusan: ["MPLB", "AKL", "BUSANA"] atau null untuk semua');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('target_majors');
        });
    }
};

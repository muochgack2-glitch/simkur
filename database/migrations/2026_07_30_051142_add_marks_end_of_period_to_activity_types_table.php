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
        Schema::table('activity_types', function (Blueprint $table) {
            // Flag untuk menandai kegiatan ini sebagai "akhir periode KBM"
            $table->boolean('marks_end_of_period')->default(false)->after('is_exam');
            
            // JSON untuk menyimpan jenjang mana yang terpengaruh
            // Contoh: ["X"], ["XI"], ["XII"], ["X", "XI"], ["X", "XI", "XII"]
            $table->json('affects_grades')->nullable()->after('marks_end_of_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_types', function (Blueprint $table) {
            $table->dropColumn(['marks_end_of_period', 'affects_grades']);
        });
    }
};

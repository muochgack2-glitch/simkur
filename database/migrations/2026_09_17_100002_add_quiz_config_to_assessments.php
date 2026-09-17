<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak ada ENUM — assessment_type disimpan string, tidak perlu MODIFY.
        // Cukup tambah kolom konfigurasi quiz.

        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('semester_id');
            }
            if (!Schema::hasColumn('assessments', 'target_class_ids')) {
                $table->json('target_class_ids')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'allow_retry')) {
                $table->boolean('allow_retry')->default(false);
            }
            if (!Schema::hasColumn('assessments', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(true);
            }
            if (!Schema::hasColumn('assessments', 'shuffle_options')) {
                $table->boolean('shuffle_options')->default(true);
            }
            if (!Schema::hasColumn('assessments', 'start_time')) {
                $table->string('start_time', 8)->nullable()
                      ->comment('Format HH:MM:SS. Kombinasikan dengan start_date');
            }
            if (!Schema::hasColumn('assessments', 'end_time')) {
                $table->string('end_time', 8)->nullable()
                      ->comment('Format HH:MM:SS. Kombinasikan dengan end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            foreach (['subject_id','target_class_ids','allow_retry','shuffle_questions','shuffle_options','start_time','end_time'] as $col) {
                if (Schema::hasColumn('assessments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

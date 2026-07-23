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
        // Add assessment_type to assessments table (if not exists)
        if (!Schema::hasColumn('assessments', 'assessment_type')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->enum('assessment_type', ['vark', 'diagnostic'])->default('vark')->after('description');
            });
        }

        // Add aspect fields to assessment_questions table
        if (!Schema::hasColumn('assessment_questions', 'aspect')) {
            Schema::table('assessment_questions', function (Blueprint $table) {
                $table->string('aspect')->nullable()->after('learning_style_indicator')
                      ->comment('For diagnostic: kesiapan, motivasi, kemandirian, kolaborasi, dunia_kerja');
                $table->integer('aspect_weight')->nullable()->after('aspect')
                      ->comment('Weight percentage for diagnostic aspects: 30, 20, 15, etc');
            });
        }

        // Add diagnostic fields to student_learning_profiles table
        if (!Schema::hasColumn('student_learning_profiles', 'aspect_scores')) {
            Schema::table('student_learning_profiles', function (Blueprint $table) {
                $table->json('aspect_scores')->nullable()->after('total_score')
                      ->comment('Diagnostic aspect scores: {kesiapan: 84, motivasi: 90, ...}');
                $table->json('needs_support_in')->nullable()->after('aspect_scores')
                      ->comment('Areas needing support: [kemandirian, motivasi]');
                $table->json('diagnostic_recommendations')->nullable()->after('recommendations')
                      ->comment('Diagnostic-specific recommendations');
                $table->enum('diagnostic_category', ['sangat_baik', 'baik', 'cukup', 'perlu_pendampingan'])->nullable()->after('diagnostic_recommendations');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('assessment_type');
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn(['aspect', 'aspect_weight']);
        });

        Schema::table('student_learning_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'aspect_scores',
                'needs_support_in',
                'diagnostic_recommendations',
                'diagnostic_category'
            ]);
        });
    }
};

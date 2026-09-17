<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_student_sessions', function (Blueprint $table) {
            // Buat manual_score dan total_score nullable tanpa default
            // sehingga null = belum dinilai, 0 = dinilai dengan skor nol
            $table->decimal('manual_score', 8, 2)->nullable()->default(null)->change();
            $table->decimal('total_score', 8, 2)->nullable()->default(null)->change();
        });

        // Reset session yang manual_score-nya 0 padahal belum dinilai
        // (session sudah submit tapi manual_score = 0.00 karena default kolom)
        // Hanya reset jika assessment punya soal esai/file_upload
        DB::statement("
            UPDATE assessment_student_sessions ass
            SET ass.manual_score = NULL,
                ass.total_score  = NULL
            WHERE ass.submitted_at IS NOT NULL
              AND ass.manual_score = 0
              AND ass.total_score  = 0
              AND EXISTS (
                  SELECT 1 FROM assessment_questions aq
                  WHERE aq.assessment_id = ass.assessment_id
                    AND aq.question_type IN ('essay', 'file_upload')
              )
        ");
    }

    public function down(): void
    {
        Schema::table('assessment_student_sessions', function (Blueprint $table) {
            $table->decimal('manual_score', 8, 2)->nullable(false)->default(0)->change();
            $table->decimal('total_score', 8, 2)->nullable(false)->default(0)->change();
        });
    }
};
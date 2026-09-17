<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_student_sessions', function (Blueprint $table) {
            $table->json('answers_data')
                  ->nullable()
                  ->after('option_orders')
                  ->comment('Jawaban sementara siswa {question_id: value} — autosave mid-quiz');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_student_sessions', function (Blueprint $table) {
            $table->dropColumn('answers_data');
        });
    }
};
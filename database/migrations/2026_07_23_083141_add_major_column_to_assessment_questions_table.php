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
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->string('major', 20)->nullable()->after('aspect_weight')
                  ->comment('Specific major for the question: MPLB, AKL, BUSANA. NULL for common questions');
            $table->index('major');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropIndex(['major']);
            $table->dropColumn('major');
        });
    }
};

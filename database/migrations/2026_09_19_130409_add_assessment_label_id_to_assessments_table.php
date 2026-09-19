<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'assessment_label_id')) {
                $table->unsignedBigInteger('assessment_label_id')->nullable()->after('id');
                $table->foreign('assessment_label_id')->references('id')->on('assessment_labels')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['assessment_label_id']);
            $table->dropColumn('assessment_label_id');
        });
    }
};
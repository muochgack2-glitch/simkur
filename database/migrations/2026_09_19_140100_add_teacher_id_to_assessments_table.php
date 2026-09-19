<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('created_by');
                $table->foreign('teacher_id')->references('id')->on('users')->nullOnDelete();
            }
        });

        // Isi data lama: teacher_id = created_by
        DB::statement('UPDATE assessments SET teacher_id = created_by WHERE teacher_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
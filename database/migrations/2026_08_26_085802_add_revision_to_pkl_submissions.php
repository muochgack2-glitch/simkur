<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pkl_submissions', function (Blueprint $table) {
            $table->boolean('revision_requested')->default(false)->after('graded_at');
            $table->text('revision_note')->nullable()->after('revision_requested');
            $table->timestamp('revision_requested_at')->nullable()->after('revision_note');
        });
    }

    public function down(): void
    {
        Schema::table('pkl_submissions', function (Blueprint $table) {
            $table->dropColumn(['revision_requested', 'revision_note', 'revision_requested_at']);
        });
    }
};

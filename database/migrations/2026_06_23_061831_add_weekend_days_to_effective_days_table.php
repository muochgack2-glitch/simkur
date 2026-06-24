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
        Schema::table('effective_days', function (Blueprint $table) {
            $table->integer('weekend_days')->default(0)->after('total_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('effective_days', function (Blueprint $table) {
            $table->dropColumn('weekend_days');
        });
    }
};

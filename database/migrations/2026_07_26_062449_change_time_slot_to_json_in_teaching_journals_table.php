<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, backup old data by converting single time_slot to JSON array
        DB::statement("UPDATE teaching_journals SET time_slot = JSON_ARRAY(time_slot) WHERE time_slot IS NOT NULL AND JSON_VALID(time_slot) = 0");
        
        // Then change column type to JSON
        Schema::table('teaching_journals', function (Blueprint $table) {
            $table->json('time_slot')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to string - take first element of array
        DB::statement("UPDATE teaching_journals SET time_slot = JSON_UNQUOTE(JSON_EXTRACT(time_slot, '$[0]')) WHERE JSON_VALID(time_slot) = 1");
        
        Schema::table('teaching_journals', function (Blueprint $table) {
            $table->string('time_slot')->nullable()->change();
        });
    }
};

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
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('is_assessable')->default(true)->after('is_active')
                  ->comment('Apakah mapel ini ikut dalam rekapitulasi asesmen (ASTS/ASAS)');
        });

        // Set BK dan Ke PGRI an sebagai non-assessable
        DB::table('subjects')
            ->whereIn('code', ['BK', 'Bimbingan Konseling'])
            ->orWhere('name', 'like', '%Bimbingan Konseling%')
            ->update(['is_assessable' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('is_assessable');
        });
    }
};

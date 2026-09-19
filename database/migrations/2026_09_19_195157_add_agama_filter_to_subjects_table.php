<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('agama_filter')->nullable()->after('description');
            // Contoh nilai: 'Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'
            // NULL = berlaku untuk semua siswa
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('agama_filter');
        });
    }
};
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
        Schema::table('users', function (Blueprint $table) {
            // Add major/jurusan field for SMK students
            $table->enum('major', ['MPLB', 'AKL', 'BUSANA'])
                ->nullable()
                ->after('grade')
                ->comment('Jurusan SMK: MPLB, AKL, BUSANA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('major');
        });
    }
};

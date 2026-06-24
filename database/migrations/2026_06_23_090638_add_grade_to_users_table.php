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
            // Add grade column only if it doesn't exist
            if (!Schema::hasColumn('users', 'grade')) {
                $table->enum('grade', ['X', 'XI', 'XII'])
                      ->nullable()
                      ->after('role')
                      ->comment('Tingkat kelas untuk siswa: X, XI, XII');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'grade')) {
                $table->dropColumn('grade');
            }
        });
    }
};

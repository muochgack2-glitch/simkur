<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter the ENUM to add 'kepala_sekolah' role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'waka_kurikulum', 'kepala_sekolah', 'guru', 'siswa') NOT NULL DEFAULT 'guru'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'kepala_sekolah' from ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'waka_kurikulum', 'guru', 'siswa') NOT NULL DEFAULT 'guru'");
    }
};

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
        // Modify ENUM to add 'siswa' role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'waka_kurikulum', 'guru', 'siswa') DEFAULT 'guru'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'siswa' from ENUM (only if no users have siswa role)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'waka_kurikulum', 'guru') DEFAULT 'guru'");
    }
};

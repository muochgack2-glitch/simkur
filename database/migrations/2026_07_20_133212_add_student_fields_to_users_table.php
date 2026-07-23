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
            // Field untuk Siswa
            $table->string('nisn', 10)->nullable()->after('nip_nuptk'); // NISN (Nomor Induk Siswa Nasional)
            $table->string('nis', 10)->nullable()->after('nisn'); // NIS (Nomor Induk Sekolah)
            $table->string('no_hp', 15)->nullable()->after('email'); // No HP Siswa
            $table->string('parent_name')->nullable()->after('no_hp'); // Nama Orang Tua
            $table->string('parent_phone', 15)->nullable()->after('parent_name'); // No HP Orang Tua
            $table->boolean('is_pkl')->default(false)->after('taught_majors'); // Status PKL
            $table->boolean('is_teaching_factory')->default(false)->after('is_pkl'); // Status Teaching Factory
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nisn', 
                'nis', 
                'no_hp', 
                'parent_name', 
                'parent_phone',
                'is_pkl',
                'is_teaching_factory'
            ]);
        });
    }
};

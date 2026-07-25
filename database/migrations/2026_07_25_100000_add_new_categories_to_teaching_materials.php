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
        // Alter column category to add new categories
        // We need to use raw SQL because Laravel doesn't support modifying enum directly
        
        DB::statement("ALTER TABLE teaching_materials MODIFY COLUMN category ENUM(
            'atp',
            'cp',
            'kktp',
            'prota',
            'prosem',
            'modul_ajar',
            'modul_projek',
            'buku_teks',
            'video_pembelajaran',
            'presentasi_infografis',
            'bahan_bacaan',
            'bank_soal',
            'rubrik_penilaian_umum',
            'asesmen_diagnostik',
            'instrumen_uji_kompetensi',
            'program_remedial',
            'program_pengayaan',
            'job_sheet',
            'teaching_factory',
            'pkl'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback to original 12 categories
        DB::statement("ALTER TABLE teaching_materials MODIFY COLUMN category ENUM(
            'atp',
            'cp',
            'modul_ajar',
            'buku_teks',
            'video_pembelajaran',
            'presentasi_infografis',
            'bahan_bacaan',
            'bank_soal',
            'rubrik_penilaian_umum',
            'job_sheet',
            'teaching_factory',
            'pkl'
        ) NOT NULL");
    }
};

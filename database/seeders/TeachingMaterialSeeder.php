<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\TeachingMaterial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeachingMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample data
        $academicYear = AcademicYear::where('year', 'like', '%2025/2026%')->first() 
                        ?? AcademicYear::first();
        
        $matematika = Subject::where('name', 'like', '%Matematika%')->first();
        $bahasaIndonesia = Subject::where('name', 'like', '%Bahasa Indonesia%')->first();
        $akuntansi = Subject::where('name', 'like', '%Akuntansi%')->first();
        
        $guru = User::where('role', 'guru')->first();
        
        if (!$academicYear || !$guru) {
            $this->command->warn('⚠️ Academic Year atau Guru tidak ditemukan. Skip seeding.');
            return;
        }

        $this->command->info('📚 Seeding Teaching Materials...');

        // Sample 1: ATP Matematika
        TeachingMaterial::create([
            'title' => 'ATP Matematika Fase F (Kelas XI-XII)',
            'description' => 'Alur Tujuan Pembelajaran Matematika untuk Fase F yang mencakup materi Kalkulus, Statistika, dan Aljabar Lanjut.',
            'category' => 'atp',
            'subject_id' => $matematika?->id,
            'academic_year_id' => $academicYear->id,
            'grade' => 'XI',
            'phase' => 'F',
            'semester' => '1',
            'file_type' => 'link',
            'external_link' => 'https://docs.google.com/document/d/sample-atp-matematika',
            'dimension_5_bernalar_kritis' => true,
            'dimension_6_kreatif' => true,
            'dimension_7_numerasi' => true,
            'tags' => ['diferensiasi', 'kurikulum_merdeka'],
            'is_public' => true,
            'status' => 'approved',
            'approved_by' => User::where('role', 'waka_kurikulum')->first()?->id ?? $guru->id,
            'approved_at' => now(),
            'created_by' => $guru->id,
            'view_count' => 45,
            'download_count' => 12,
        ]);

        // Sample 2: Modul Ajar Matematika
        TeachingMaterial::create([
            'title' => 'Modul Ajar: Sistem Persamaan Linear Pertemuan 1',
            'description' => 'Modul Ajar lengkap dengan LKPD, Instrumen Asesmen, dan Rubrik Penilaian untuk materi Sistem Persamaan Linear.',
            'category' => 'modul_ajar',
            'subject_id' => $matematika?->id,
            'academic_year_id' => $academicYear->id,
            'grade' => 'X',
            'phase' => 'E',
            'semester' => '2',
            'file_type' => 'link',
            'external_link' => 'https://docs.google.com/document/d/sample-modul-ajar',
            'dimension_1_beriman' => true,
            'dimension_3_gotong_royong' => true,
            'dimension_5_bernalar_kritis' => true,
            'dimension_6_kreatif' => true,
            'dimension_7_numerasi' => true,
            'dimension_8_literasi' => true,
            'tags' => ['aljabar', 'diferensiasi', 'praktik'],
            'is_public' => true,
            'status' => 'pending_approval',
            'created_by' => $guru->id,
            'view_count' => 8,
        ]);

        // Sample 3: Video Pembelajaran Akuntansi
        if ($akuntansi) {
            TeachingMaterial::create([
                'title' => 'Video: Pengenalan Akuntansi Keuangan',
                'description' => 'Video tutorial pengenalan dasar-dasar akuntansi keuangan untuk siswa kelas X AKL.',
                'category' => 'video_pembelajaran',
                'subject_id' => $akuntansi->id,
                'academic_year_id' => $academicYear->id,
                'grade' => 'X',
                'phase' => 'E',
                'semester' => '1',
                'file_type' => 'link',
                'external_link' => 'https://youtu.be/sample-akuntansi-video',
                'dimension_4_mandiri' => true,
                'dimension_8_literasi' => true,
                'dimension_7_numerasi' => true,
                'tags' => ['akuntansi', 'video', 'dasar'],
                'is_public' => true,
                'status' => 'approved',
                'approved_by' => User::where('role', 'waka_kurikulum')->first()?->id ?? $guru->id,
                'approved_at' => now(),
                'created_by' => $guru->id,
                'view_count' => 120,
                'download_count' => 0,
            ]);
        }

        // Sample 4: LKPD Bahasa Indonesia (Draft)
        if ($bahasaIndonesia) {
            TeachingMaterial::create([
                'title' => 'Bank Soal UTS Bahasa Indonesia Semester 1',
                'description' => 'Kumpulan soal UTS Bahasa Indonesia untuk kelas X semester 1.',
                'category' => 'bank_soal',
                'subject_id' => $bahasaIndonesia->id,
                'academic_year_id' => $academicYear->id,
                'grade' => 'X',
                'phase' => 'E',
                'semester' => '1',
                'file_type' => 'link',
                'external_link' => 'https://forms.gle/sample-soal-bahasa-indonesia',
                'dimension_5_bernalar_kritis' => true,
                'dimension_8_literasi' => true,
                'tags' => ['soal', 'uts', 'bahasa_indonesia'],
                'is_public' => false,
                'status' => 'draft',
                'created_by' => $guru->id,
                'view_count' => 3,
            ]);
        }

        // Sample 5: Job Sheet Praktikum
        TeachingMaterial::create([
            'title' => 'Job Sheet: Praktikum Komputer Akuntansi MYOB',
            'description' => 'Panduan praktikum penggunaan software MYOB untuk akuntansi keuangan.',
            'category' => 'job_sheet',
            'subject_id' => $akuntansi?->id,
            'academic_year_id' => $academicYear->id,
            'grade' => 'XI',
            'phase' => 'F',
            'semester' => '1',
            'file_type' => 'link',
            'external_link' => 'https://docs.google.com/document/d/sample-job-sheet-myob',
            'dimension_4_mandiri' => true,
            'dimension_5_bernalar_kritis' => true,
            'dimension_6_kreatif' => true,
            'tags' => ['myob', 'praktikum', 'komputer', 'akuntansi'],
            'is_public' => true,
            'status' => 'approved',
            'approved_by' => User::where('role', 'waka_kurikulum')->first()?->id ?? $guru->id,
            'approved_at' => now(),
            'created_by' => $guru->id,
            'view_count' => 67,
            'download_count' => 23,
        ]);

        $this->command->info('✅ Seeding completed! Created 5 sample teaching materials.');
    }
}

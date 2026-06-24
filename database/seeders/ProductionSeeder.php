<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ActivityType;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds for production environment
     * This seeder contains all essential data for E-KALDIK to function
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Production Seeder...');
        
        // 1. Create Users
        $this->createUsers();
        
        // 2. Create Activity Types
        $this->createActivityTypes();
        
        // 3. Create Settings
        $this->createSettings();
        
        // 4. Create Academic Year & Semesters
        $this->createAcademicYearAndSemesters();
        
        $this->command->info('✅ Production Seeder completed successfully!');
    }
    
    /**
     * Create default users
     */
    private function createUsers(): void
    {
        $this->command->info('Creating users...');
        
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@ekaldik.local',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Tim Kurikulum',
                'username' => 'kurikulum',
                'email' => 'kurikulum@ekaldik.local',
                'password' => Hash::make('password'),
                'role' => 'waka_kurikulum',
            ],
            [
                'name' => 'Guru Contoh',
                'username' => 'guru',
                'email' => 'guru@ekaldik.local',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ],
        ];
        
        foreach ($users as $userData) {
            User::create($userData);
        }
        
        $this->command->info('✓ Users created');
    }
    
    /**
     * Create activity types
     */
    private function createActivityTypes(): void
    {
        $this->command->info('Creating activity types...');
        
        $activityTypes = [
            [
                'name' => 'Libur Awal Puasa',
                'code' => 'LAP',
                'category' => 'akademik',
                'default_color' => '#3B82F6',
                'is_holiday' => true,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Libur menjelang bulan Ramadan',
                'sort_order' => 1,
            ],
            [
                'name' => 'PKL (Praktik Kerja Lapangan)',
                'code' => 'PKL',
                'category' => 'akademik',
                'default_color' => '#3B82F6',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Kegiatan praktik kerja lapangan siswa',
                'sort_order' => 2,
            ],
            [
                'name' => 'MPLS',
                'code' => 'MPLS',
                'category' => 'non_akademik',
                'default_color' => '#10B981',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Masa Pengenalan Lingkungan Sekolah',
                'sort_order' => 3,
            ],
            [
                'name' => 'PTS',
                'code' => 'PTS',
                'category' => 'akademik',
                'default_color' => '#F59E0B',
                'is_holiday' => false,
                'is_exam' => true,
                'is_system' => true,
                'description' => 'Penilaian Tengah Semester',
                'sort_order' => 4,
            ],
            [
                'name' => 'PAS',
                'code' => 'PAS',
                'category' => 'akademik',
                'default_color' => '#EF4444',
                'is_holiday' => false,
                'is_exam' => true,
                'is_system' => true,
                'description' => 'Penilaian Akhir Semester',
                'sort_order' => 5,
            ],
            [
                'name' => 'PAT',
                'code' => 'PAT',
                'category' => 'akademik',
                'default_color' => '#DC2626',
                'is_holiday' => false,
                'is_exam' => true,
                'is_system' => true,
                'description' => 'Penilaian Akhir Tahun',
                'sort_order' => 6,
            ],
            [
                'name' => 'ANBK',
                'code' => 'ANBK',
                'category' => 'akademik',
                'default_color' => '#8B5CF6',
                'is_holiday' => false,
                'is_exam' => true,
                'is_system' => true,
                'description' => 'Asesmen Nasional Berbasis Komputer',
                'sort_order' => 7,
            ],
            [
                'name' => 'Libur Nasional',
                'code' => 'LIBNAS',
                'category' => 'non_akademik',
                'default_color' => '#6B7280',
                'is_holiday' => true,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Hari libur nasional resmi',
                'sort_order' => 8,
            ],
            [
                'name' => 'Libur Semester',
                'code' => 'LIBSEM',
                'category' => 'non_akademik',
                'default_color' => '#3B82F6',
                'is_holiday' => true,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Libur akhir semester',
                'sort_order' => 9,
            ],
            [
                'name' => 'Rapat Guru',
                'code' => 'RAPAT',
                'category' => 'non_akademik',
                'default_color' => '#14B8A6',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Kegiatan rapat guru dan tenaga pendidik',
                'sort_order' => 10,
            ],
            [
                'name' => 'Kegiatan Sekolah',
                'code' => 'KEGIATAN',
                'category' => 'non_akademik',
                'default_color' => '#EC4899',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Kegiatan umum sekolah',
                'sort_order' => 11,
            ],
            [
                'name' => 'Upacara',
                'code' => 'UPACARA',
                'category' => 'non_akademik',
                'default_color' => '#8B5CF6',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Upacara bendera dan hari besar nasional',
                'sort_order' => 12,
            ],
            [
                'name' => 'TKA',
                'code' => 'TKA',
                'category' => 'akademik',
                'default_color' => '#DC2626',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Tes Kemampuan Akademik',
                'sort_order' => 13,
            ],
            [
                'name' => 'Pembagian Rapor',
                'code' => 'RAPOR',
                'category' => 'akademik',
                'default_color' => '#059669',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Pembagian rapor hasil belajar siswa',
                'sort_order' => 14,
            ],
        ];
        
        foreach ($activityTypes as $typeData) {
            ActivityType::create($typeData);
        }
        
        $this->command->info('✓ Activity types created');
    }
    
    /**
     * Create default settings
     */
    private function createSettings(): void
    {
        $this->command->info('Creating settings...');
        
        $settings = [
            [
                'key' => 'school_name',
                'value' => 'NAMA SEKOLAH',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Nama sekolah yang akan ditampilkan di kalender',
            ],
            [
                'key' => 'school_address',
                'value' => 'Alamat Sekolah',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Alamat lengkap sekolah',
            ],
            [
                'key' => 'school_logo',
                'value' => null,
                'type' => 'string',
                'group' => 'school',
                'description' => 'Logo sekolah (upload di Settings)',
            ],
            [
                'key' => 'principal_name',
                'value' => '________________',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Nama Kepala Sekolah',
            ],
            [
                'key' => 'principal_niy',
                'value' => '______________',
                'type' => 'string',
                'group' => 'school',
                'description' => 'NIY/NIP Kepala Sekolah',
            ],
            [
                'key' => 'weekend_days',
                'value' => '["saturday","sunday"]',
                'type' => 'json',
                'group' => 'calendar',
                'description' => 'Hari libur akhir pekan (default: Sabtu & Minggu)',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Timezone aplikasi',
            ],
        ];
        
        foreach ($settings as $settingData) {
            Setting::create($settingData);
        }
        
        $this->command->info('✓ Settings created');
    }
    
    /**
     * Create academic year and semesters for 2026/2027
     */
    private function createAcademicYearAndSemesters(): void
    {
        $this->command->info('Creating academic year and semesters...');
        
        // Create Academic Year 2026/2027
        $academicYear = AcademicYear::create([
            'year' => '2026/2027',
            'start_date' => '2026-07-13',
            'end_date' => '2027-06-20',
            'is_active' => true,
        ]);
        
        // Create Semester Ganjil
        Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Semester Ganjil 2026/2027',
            'type' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-20',
        ]);
        
        // Create Semester Genap
        Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Semester Genap 2026/2027',
            'type' => 'genap',
            'start_date' => '2027-01-05',
            'end_date' => '2027-06-20',
        ]);
        
        $this->command->info('✓ Academic year and semesters created');
    }
}

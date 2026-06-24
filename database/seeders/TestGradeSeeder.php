<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year and semester
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        $semester = $activeYear->semesters()->first();
        if (!$semester) {
            $this->command->error('No semester found!');
            return;
        }

        // Create test students for each grade
        $students = [
            [
                'name' => 'Siswa Kelas X - Ahmad',
                'username' => 'siswa_x',
                'email' => 'siswa_x@test.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'X',
                'is_active' => true,
            ],
            [
                'name' => 'Siswa Kelas XI - Budi',
                'username' => 'siswa_xi',
                'email' => 'siswa_xi@test.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'XI',
                'is_active' => true,
            ],
            [
                'name' => 'Siswa Kelas XII - Citra',
                'username' => 'siswa_xii',
                'email' => 'siswa_xii@test.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'XII',
                'is_active' => true,
            ],
        ];

        foreach ($students as $studentData) {
            User::firstOrCreate(
                ['username' => $studentData['username']],
                $studentData
            );
        }

        $this->command->info('✓ Test students created');

        // Get activity types
        $activityTypes = ActivityType::all();
        if ($activityTypes->isEmpty()) {
            $this->command->error('No activity types found!');
            return;
        }

        $ujianType = $activityTypes->where('name', 'Ujian')->first() ?? $activityTypes->first();
        $liburType = $activityTypes->where('name', 'Libur')->first() ?? $activityTypes->first();

        // Create sample activities with different target grades
        $activities = [
            [
                'name' => 'MPLS (Masa Pengenalan Lingkungan Sekolah)',
                'activity_type_id' => $ujianType->id,
                'academic_year_id' => $activeYear->id,
                'semester_id' => $semester->id,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(7),
                'description' => 'Kegiatan khusus untuk siswa baru kelas X',
                'target_grades' => ['X'], // Hanya kelas X
                'created_by' => 1,
            ],
            [
                'name' => 'PKL (Praktik Kerja Lapangan)',
                'activity_type_id' => $ujianType->id,
                'academic_year_id' => $activeYear->id,
                'semester_id' => $semester->id,
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(100),
                'description' => 'Kegiatan PKL untuk siswa kelas XII',
                'target_grades' => ['XII'], // Hanya kelas XII
                'color' => '#9333EA',
                'created_by' => 1,
            ],
            [
                'name' => 'Ujian Tengah Semester',
                'activity_type_id' => $ujianType->id,
                'academic_year_id' => $activeYear->id,
                'semester_id' => $semester->id,
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(35),
                'description' => 'UTS untuk kelas XI dan XII',
                'target_grades' => ['XI', 'XII'], // Kelas XI & XII
                'created_by' => 1,
            ],
            [
                'name' => 'Libur Semester Ganjil',
                'activity_type_id' => $liburType->id,
                'academic_year_id' => $activeYear->id,
                'semester_id' => $semester->id,
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(74),
                'description' => 'Libur untuk semua kelas',
                'target_grades' => null, // Semua kelas
                'created_by' => 1,
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::firstOrCreate(
                [
                    'name' => $activityData['name'],
                    'academic_year_id' => $activityData['academic_year_id'],
                ],
                $activityData
            );
        }

        $this->command->info('✓ Sample activities created');
        $this->command->info('');
        $this->command->info('Test Users Created:');
        $this->command->info('  Username: siswa_x   | Password: password | Grade: X');
        $this->command->info('  Username: siswa_xi  | Password: password | Grade: XI');
        $this->command->info('  Username: siswa_xii | Password: password | Grade: XII');
        $this->command->info('');
        $this->command->info('Sample Activities:');
        $this->command->info('  1. MPLS → Kelas X only');
        $this->command->info('  2. PKL → Kelas XII only');
        $this->command->info('  3. Ujian Tengah Semester → Kelas XI & XII');
        $this->command->info('  4. Libur Semester → Semua Kelas');
    }
}

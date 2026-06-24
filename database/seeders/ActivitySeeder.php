<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->command->warn('No active academic year found. Creating one...');
            
            // Create academic year 2026/2027
            $academicYear = AcademicYear::create([
                'year' => '2026/2027',
                'start_date' => '2026-07-15',
                'end_date' => '2027-06-30',
                'is_active' => true,
            ]);
        }

        // Get semesters
        $semesterGanjil = $academicYear->semesters()->where('type', 'ganjil')->first();
        $semesterGenap = $academicYear->semesters()->where('type', 'genap')->first();

        // Get activity types
        $activityTypes = ActivityType::all()->keyBy('code');

        // Get admin user
        $admin = User::where('role', 'admin')->first();

        // Sample activities for Semester Ganjil
        $activitiesGanjil = [
            [
                'name' => 'Masa Pengenalan Lingkungan Sekolah (MPLS)',
                'type' => 'MPLS',
                'start_date' => '2026-07-15',
                'end_date' => '2026-07-17',
                'description' => 'Kegiatan pengenalan lingkungan sekolah untuk siswa baru',
            ],
            [
                'name' => 'Kegiatan Sekolah Reguler',
                'type' => 'KEGIATAN',
                'start_date' => '2026-07-20',
                'end_date' => '2026-10-09',
                'description' => 'Kegiatan belajar mengajar normal',
            ],
            [
                'name' => 'Penilaian Tengah Semester Ganjil',
                'type' => 'PTS',
                'start_date' => '2026-10-12',
                'end_date' => '2026-10-17',
                'description' => 'Ujian tengah semester ganjil',
            ],
            [
                'name' => 'Kegiatan Sekolah Lanjutan',
                'type' => 'KEGIATAN',
                'start_date' => '2026-10-19',
                'end_date' => '2026-12-04',
                'description' => 'Kegiatan belajar mengajar lanjutan',
            ],
            [
                'name' => 'Penilaian Akhir Semester Ganjil',
                'type' => 'PAS',
                'start_date' => '2026-12-07',
                'end_date' => '2026-12-14',
                'description' => 'Ujian akhir semester ganjil',
            ],
            [
                'name' => 'Libur Semester Ganjil',
                'type' => 'LIBNAS',
                'start_date' => '2026-12-21',
                'end_date' => '2027-01-02',
                'description' => 'Libur akhir tahun dan tahun baru',
            ],
        ];

        // Sample activities for Semester Genap
        $activitiesGenap = [
            [
                'name' => 'Kegiatan Sekolah Semester Genap',
                'type' => 'KEGIATAN',
                'start_date' => '2027-01-04',
                'end_date' => '2027-03-19',
                'description' => 'Kegiatan belajar mengajar semester genap',
            ],
            [
                'name' => 'Penilaian Tengah Semester Genap',
                'type' => 'PTS',
                'start_date' => '2027-03-22',
                'end_date' => '2027-03-27',
                'description' => 'Ujian tengah semester genap',
            ],
            [
                'name' => 'Kegiatan Sekolah Lanjutan Genap',
                'type' => 'KEGIATAN',
                'start_date' => '2027-03-29',
                'end_date' => '2027-05-28',
                'description' => 'Kegiatan belajar mengajar lanjutan semester genap',
            ],
            [
                'name' => 'Asesmen Nasional Berbasis Komputer (ANBK)',
                'type' => 'ANBK',
                'start_date' => '2027-05-10',
                'end_date' => '2027-05-14',
                'description' => 'Asesmen Nasional untuk siswa kelas tertentu',
            ],
            [
                'name' => 'Penilaian Akhir Tahun (PAT)',
                'type' => 'PAT',
                'start_date' => '2027-06-01',
                'end_date' => '2027-06-08',
                'description' => 'Ujian akhir tahun pelajaran',
            ],
            [
                'name' => 'Pembagian Rapor Semester Genap',
                'type' => 'LIBSEM',
                'start_date' => '2027-06-26',
                'end_date' => '2027-06-26',
                'description' => 'Penyerahan rapor kepada orang tua/wali',
            ],
        ];

        $this->command->info('Creating activities for Semester Ganjil...');
        foreach ($activitiesGanjil as $data) {
            if (isset($activityTypes[$data['type']])) {
                Activity::create([
                    'name' => $data['name'],
                    'activity_type_id' => $activityTypes[$data['type']]->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semesterGanjil->id,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'description' => $data['description'],
                    'created_by' => $admin->id,
                ]);
                $this->command->info("  ✓ {$data['name']}");
            }
        }

        $this->command->info('Creating activities for Semester Genap...');
        foreach ($activitiesGenap as $data) {
            if (isset($activityTypes[$data['type']])) {
                Activity::create([
                    'name' => $data['name'],
                    'activity_type_id' => $activityTypes[$data['type']]->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semesterGenap->id,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'description' => $data['description'],
                    'created_by' => $admin->id,
                ]);
                $this->command->info("  ✓ {$data['name']}");
            }
        }

        $totalActivities = Activity::count();
        $this->command->info("Activities created successfully! Total: {$totalActivities}");
    }
}

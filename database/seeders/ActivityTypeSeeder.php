<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityTypes = [
            [
                'name' => 'MPLS',
                'code' => 'MPLS',
                'category' => 'non_akademik',
                'default_color' => '#10B981',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Masa Pengenalan Lingkungan Sekolah',
                'sort_order' => 1,
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
                'sort_order' => 2,
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
                'sort_order' => 3,
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
                'sort_order' => 4,
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
                'sort_order' => 5,
            ],
            [
                'name' => 'Libur Nasional',
                'code' => 'LIBNAS',
                'category' => 'non_akademik',
                'default_color' => '#6B7280',
                'is_holiday' => true,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Hari Libur Nasional',
                'sort_order' => 6,
            ],
            [
                'name' => 'Libur Semester',
                'code' => 'LIBSEM',
                'category' => 'non_akademik',
                'default_color' => '#3B82F6',
                'is_holiday' => true,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Libur Semester',
                'sort_order' => 7,
            ],
            [
                'name' => 'Rapat Guru',
                'code' => 'RAPAT',
                'category' => 'non_akademik',
                'default_color' => '#14B8A6',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Rapat Guru dan Tenaga Pendidik',
                'sort_order' => 8,
            ],
            [
                'name' => 'Kegiatan Sekolah',
                'code' => 'KEGIATAN',
                'category' => 'non_akademik',
                'default_color' => '#EC4899',
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => true,
                'description' => 'Kegiatan Sekolah Lainnya',
                'sort_order' => 9,
            ],
        ];

        foreach ($activityTypes as $type) {
            ActivityType::create($type);
        }

        $this->command->info('Activity types created successfully!');
        $this->command->info('Total: ' . count($activityTypes) . ' activity types');
    }
}

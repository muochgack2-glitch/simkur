<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MissingActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityTypes = [
            [
                'name' => 'Upacara',
                'code' => 'UPACARA',
                'category' => 'non_akademik',
                'default_color' => '#8B5CF6', // Purple
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => false,
                'description' => 'Upacara bendera dan upacara hari besar nasional',
                'sort_order' => 60,
            ],
            [
                'name' => 'UAS',
                'code' => 'UAS',
                'category' => 'akademik',
                'default_color' => '#DC2626', // Red
                'is_holiday' => false,
                'is_exam' => true,
                'is_system' => false,
                'description' => 'Ujian Akhir Semester',
                'sort_order' => 70,
            ],
            [
                'name' => 'Pembagian Rapor',
                'code' => 'RAPOR',
                'category' => 'akademik',
                'default_color' => '#059669', // Green
                'is_holiday' => false,
                'is_exam' => false,
                'is_system' => false,
                'description' => 'Pembagian rapor hasil belajar siswa',
                'sort_order' => 80,
            ],
        ];

        foreach ($activityTypes as $type) {
            // Check if already exists
            $exists = ActivityType::where('name', $type['name'])->exists();
            
            if (!$exists) {
                ActivityType::create($type);
                $this->command->info("✓ Jenis kegiatan '{$type['name']}' berhasil ditambahkan");
            } else {
                $this->command->warn("- Jenis kegiatan '{$type['name']}' sudah ada");
            }
        }

        $this->command->info("\nSeeder selesai dijalankan!");
    }
}

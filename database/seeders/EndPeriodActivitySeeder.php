<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class EndPeriodActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing activity types untuk Ujian Sekolah
        ActivityType::where('code', 'UJIANSEKOLAH')
            ->orWhere('name', 'LIKE', '%Ujian Sekolah%')
            ->update([
                'marks_end_of_period' => true,
                'affects_grades' => ['XII'], // Hanya kelas XII
            ]);

        // Atau buat activity type baru khusus untuk penanda
        ActivityType::updateOrCreate(
            ['code' => 'AKHIR_KBM_XII'],
            [
                'name' => 'Akhir KBM Kelas XII',
                'category' => 'akademik',
                'default_color' => '#DC2626', // Merah
                'is_holiday' => false,
                'is_exam' => false,
                'marks_end_of_period' => true,
                'affects_grades' => ['XII'],
                'is_system' => false,
                'description' => 'Penanda akhir kegiatan belajar mengajar untuk kelas XII. Setelah tanggal ini, kelas XII tidak lagi melakukan KBM reguler.',
                'sort_order' => 100,
            ]
        );

        // Contoh: Bisa juga untuk kelas lain jika ada kasus khusus
        ActivityType::updateOrCreate(
            ['code' => 'AKHIR_KBM_XI'],
            [
                'name' => 'Akhir KBM Kelas XI',
                'category' => 'akademik',
                'default_color' => '#F59E0B', // Orange
                'is_holiday' => false,
                'is_exam' => false,
                'marks_end_of_period' => true,
                'affects_grades' => ['XI'],
                'is_system' => false,
                'description' => 'Penanda akhir kegiatan belajar mengajar untuk kelas XI (jika ada kasus khusus).',
                'sort_order' => 101,
            ]
        );

        $this->command->info('✅ Activity types untuk penanda akhir periode berhasil dibuat/diupdate!');
        $this->command->info('   - AKHIR_KBM_XII (affects: XII)');
        $this->command->info('   - AKHIR_KBM_XI (affects: XI)');
        $this->command->info('   - Ujian Sekolah (updated: affects XII)');
    }
}

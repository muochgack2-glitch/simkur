<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateClassDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Update data kelas dengan:
     * - Wali kelas
     * - Tahun ajaran aktif
     */
    public function run(): void
    {
        $this->command->info('📝 Updating class data (wali kelas & tahun ajaran)...');

        // Get active academic year
        $activeYear = DB::table('academic_years')
            ->where('is_active', true)
            ->first();

        if (!$activeYear) {
            $this->command->error('❌ Tidak ada tahun ajaran aktif! Silakan buat tahun ajaran terlebih dahulu.');
            return;
        }

        $this->command->info("✅ Tahun ajaran aktif: {$activeYear->year}");

        // Data wali kelas berdasarkan data real
        $homeroomTeachers = [
            'X AKL' => 'Dhani Kisworo Jati, S.Pd.',
            'X MPLB' => 'Pancawati Puji Lestari, A. Md.',
            'X Busana' => 'Marista Bela Octaviana, S.Pd',
            'XI AKL' => 'Adela Wulan Kurniasari, S.Pd',
            'XI Busana' => 'Debby Fury Wijayanti, S. Pd.',
            'XI MPLB' => 'Ade Rua Nur Lemoniar, S.Pd.',
            'XII AKL' => 'Liliyana Ayu Widiyaningrum, S.Pd.',
            'XII Busana' => 'Dewi Wartini, S.Pd.',
            'XII MPLB' => 'Ervinda Sekar Asmara, S.Pd.',
        ];

        $updatedCount = 0;

        foreach ($homeroomTeachers as $className => $teacherName) {
            // Find class
            $class = DB::table('classes')->where('name', $className)->first();
            
            if (!$class) {
                $this->command->warn("⚠️  Kelas {$className} tidak ditemukan");
                continue;
            }

            // Find teacher by name
            $teacher = DB::table('users')
                ->where('name', 'LIKE', "%{$teacherName}%")
                ->where('role', 'guru')
                ->first();

            if (!$teacher) {
                $this->command->warn("⚠️  Guru wali kelas {$teacherName} tidak ditemukan");
                // Update only academic year
                DB::table('classes')
                    ->where('id', $class->id)
                    ->update([
                        'academic_year_id' => $activeYear->id,
                        'updated_at' => now(),
                    ]);
                $updatedCount++;
                continue;
            }

            // Update class with homeroom teacher and academic year
            DB::table('classes')
                ->where('id', $class->id)
                ->update([
                    'homeroom_teacher_id' => $teacher->id,
                    'academic_year_id' => $activeYear->id,
                    'updated_at' => now(),
                ]);

            $updatedCount++;
            $this->command->info("✅ {$className}: {$teacher->name}");
        }

        $this->command->info('');
        $this->command->info("📊 Total {$updatedCount} kelas berhasil diupdate!");
        $this->command->info('');
        $this->command->info('💡 Catatan:');
        $this->command->info('   - Jika wali kelas belum ada di database, hanya tahun ajaran yang diupdate');
        $this->command->info('   - Pastikan data guru sudah di-seed terlebih dahulu (TeacherSeeder)');
    }
}

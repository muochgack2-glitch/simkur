<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingSchedule;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\AcademicYear;

class TeachingScheduleSeederV2 extends Seeder
{
    private $academicYear;
    private $teacherCache = [];
    private $classCache = [];
    private $subjectCache = [];
    private $timeSlotCache = []; // [day => [order => time_slot_id]]
    
    // Mapping Jam ke-X to Order (konsisten untuk semua hari)
    private $jamToOrderMap = [
        1 => 2,   // Jam ke-1 → order 2
        2 => 3,   // Jam ke-2 → order 3
        3 => 4,   // Jam ke-3 → order 4
        4 => 6,   // Jam ke-4 → order 6 (skip 5 = istirahat)
        5 => 7,   // Jam ke-5 → order 7
        6 => 8,   // Jam ke-6 → order 8
        7 => 9,   // Jam ke-7 → order 9
        8 => 11,  // Jam ke-8 → order 11 (skip 10 = istirahat)
        9 => 12,  // Jam ke-9 → order 12
        10 => 13, // Jam ke-10 → order 13
    ];

    public function run(): void
    {
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$this->academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        TeachingSchedule::where('academic_year_id', $this->academicYear->id)->delete();
        $this->command->info('Seeding teaching schedules (V2 - with proper mapping)...');

        // Preload all data to cache
        $this->preloadData();

        // Define all schedules - Format: [teacher, subject, class, day, jam_start, jam_end]
        $schedules = $this->getAllSchedules();

        $created = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            $result = $this->createSchedule($schedule);
            if ($result > 0) {
                $created += $result;
            } else {
                $skipped++;
            }
        }

        $this->command->info("✓ Created: $created schedule records");
        $this->command->warn("⊘ Skipped: $skipped schedule entries (missing data)");
    }

    private function preloadData()
    {
        // Cache teachers
        User::whereIn('role', ['guru', 'waka_kurikulum', 'kepala_sekolah'])
            ->get()
            ->each(function($user) {
                $this->teacherCache[strtolower($user->name)] = $user->id;
            });

        // Cache classes
        SchoolClass::where('academic_year_id', $this->academicYear->id)
            ->get()
            ->each(function($class) {
                $this->classCache[strtolower($class->name)] = $class->id;
            });

        // Cache subjects
        Subject::all()->each(function($subject) {
            $this->subjectCache[strtolower($subject->name)] = $subject->id;
        });

        // Cache time slots by day and order
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($days as $day) {
            $this->timeSlotCache[$day] = [];
            TimeSlot::where('day_of_week', $day)
                ->orderBy('order')
                ->get()
                ->each(function($slot) use ($day) {
                    $this->timeSlotCache[$day][$slot->order] = $slot->id;
                });
        }
    }

    private function getAllSchedules()
    {
        // Format: [teacher_name, subject_name, class_name, day, jam_start, jam_end]
        // jam_start dan jam_end adalah "Jam ke-X" (1-10)
        // Kita abaikan kelas XII untuk sementara
        
        return [
            // Data dari jadwal Excel/Gambar - HANYA X dan XI (skip XII)
            
            // ========================================
            // 1. Drs. Suseno (PKN) - 12 JP
            // ========================================
            ['Suseno', 'PKN', 'X MPLB', 'Monday', 5, 6],
            ['Suseno', 'PKN', 'X BUSANA', 'Monday', 8, 9],
            ['Suseno', 'PKN', 'X AKL', 'Tuesday', 4, 5],
            ['Suseno', 'PKN', 'XI AKL', 'Thursday', 4, 5],
            ['Suseno', 'PKN', 'XI MPLB', 'Thursday', 9, 10],
            ['Suseno', 'PKN', 'XI BUSANA', 'Friday', 4, 5],
            
            // ========================================
            // 2. Munisah, S.Pd (B. Jawa) - 12 JP
            // ========================================
            ['Munisah', 'B. Jawa', 'XI AKL', 'Monday', 7, 8],
            ['Munisah', 'B. Jawa', 'XI BUSANA', 'Tuesday', 1, 2],
            ['Munisah', 'B. Jawa', 'XI MPLB', 'Thursday', 1, 2],
            ['Munisah', 'B. Jawa', 'X BUSANA', 'Thursday', 8, 9],
            ['Munisah', 'B. Jawa', 'X MPLB', 'Friday', 5, 6],
            ['Munisah', 'B. Jawa', 'X AKL', 'Friday', 9, 10],
            
            // ========================================
            // 3. Ari Yunitasari, S.Pd - 18 JP (Fixed conflicts)
            // ========================================
            ['Ari Yunitasari', 'Bisnis Retail', 'XI AKL', 'Monday', 4, 5],
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Wednesday', 7, 9], // Changed from 4-9 to avoid overlap with Huda
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Thursday', 5, 6],
            ['Ari Yunitasari', 'Bisnis Retail', 'XI MPLB', 'Thursday', 8, 9], // Changed from 7-8 to avoid overlap
            ['Ari Yunitasari', 'Bisnis Retail', 'XI BUSANA', 'Thursday', 9, 10],
            ['Ari Yunitasari', 'Sejarah Indonesia', 'X MPLB', 'Friday', 7, 8], // Changed from 2-3 to avoid overlap
            ['Ari Yunitasari', 'EkoBis dan Adm Umum', 'XI AKL', 'Friday', 7, 8], // Changed from 5-6 to avoid overlap with Pancawati KIK
            
            // ========================================
            // 4. M. Huda Muttaqin (KKA) - 5 JP
            // ========================================
            ['Huda', 'KKA', 'X BUSANA', 'Wednesday', 4, 5],
            ['Huda', 'KKA', 'X AKL', 'Wednesday', 5, 6],
            ['Huda', 'KKA', 'X MPLB', 'Wednesday', 6, 8],
            
            // ========================================
            // 5. Budi Siswanto (PAIBP & Publik Speaking) - 21 JP
            // ========================================
            ['Budi Siswanto', 'PAIBP', 'X AKL', 'Tuesday', 1, 4],
            ['Budi Siswanto', 'PAIBP', 'X BUSANA', 'Tuesday', 4, 6],
            ['Budi Siswanto', 'PAIBP', 'XI BUSANA', 'Wednesday', 7, 10],
            ['Budi Siswanto', 'PAIBP', 'X MPLB', 'Thursday', 4, 7],
            ['Budi Siswanto', 'PAIBP', 'XI AKL', 'Thursday', 7, 9],
            ['Budi Siswanto', 'PAIBP', 'XI MPLB', 'Friday', 1, 3],
            
            // ========================================
            // 6. Dewi Wartini (Matematika & PIPAS) - 15 JP
            // ========================================
            ['Dewi Wartini', 'Matematika', 'X BUSANA', 'Monday', 2, 3],
            ['Dewi Wartini', 'PIPAS', 'X BUSANA', 'Monday', 3, 6],
            ['Dewi Wartini', 'Matematika', 'X MPLB', 'Tuesday', 5, 6],
            ['Dewi Wartini', 'Matematika', 'X BUSANA', 'Tuesday', 6, 7],
            ['Dewi Wartini', 'Matematika', 'X AKL', 'Wednesday', 1, 3],
            ['Dewi Wartini', 'Matematika', 'X MPLB', 'Friday', 1, 3],
            ['Dewi Wartini', 'Matematika', 'X AKL', 'Friday', 7, 9],
            
            // ========================================
            // 7. Tri Mulyaningsih (Akuntansi) - 12 JP
            // ========================================
            ['Tri Mulyaningsih', 'Sejarah Indonesia', 'X AKL', 'Monday', 1, 2],
            ['Tri Mulyaningsih', 'Akuntansi Keuangan', 'XI AKL', 'Tuesday', 7, 10],
            ['Tri Mulyaningsih', 'Perpajakan', 'XI AKL', 'Thursday', 1, 2],
            ['Tri Mulyaningsih', 'Komp. Akuntansi', 'XI AKL', 'Thursday', 4, 7],
            
            // ========================================
            // 8. Ilham Hardiyan (BK & Ke PGRI an) - 6 JP (BK only, no overlaps)
            // ========================================
            ['Ilham', 'Bimbingan Konseling', 'X BUSANA', 'Monday', 1, 1],
            ['Ilham', 'Bimbingan Konseling', 'X AKL', 'Monday', 3, 3],
            ['Ilham', 'Bimbingan Konseling', 'X MPLB', 'Monday', 6, 7],
            ['Ilham', 'Bimbingan Konseling', 'X BUSANA', 'Wednesday', 6, 6],
            ['Ilham', 'Bimbingan Konseling', 'X MPLB', 'Wednesday', 9, 10],
            
            // ========================================
            // 9. Pancawati (KIK & Seni Budaya) - 26 JP
            // ========================================
            ['Pancawati', 'KIK', 'XI MPLB', 'Monday', 1, 5],
            ['Pancawati', 'Seni Budaya', 'X MPLB', 'Monday', 7, 9],
            ['Pancawati', 'Seni Budaya', 'X BUSANA', 'Tuesday', 9, 10],
            ['Pancawati', 'KIK', 'XI BUSANA', 'Wednesday', 1, 6],
            ['Pancawati', 'Seni Budaya', 'X AKL', 'Wednesday', 8, 9],
            ['Pancawati', 'KIK', 'XI AKL', 'Friday', 1, 6],
            ['Pancawati', 'Membatik', 'XI AKL', 'Friday', 9, 10],
            ['Pancawati', 'Membatik', 'XI BUSANA', 'Friday', 9, 10],
            ['Pancawati', 'Membatik', 'XI MPLB', 'Friday', 9, 10],
            
            // ========================================
            // 10. Nia Dani (MPLB) - 13 JP
            // ========================================
            ['Nia Dani', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Monday', 1, 4],
            ['Nia Dani', 'Publik Speaking', 'XI MPLB', 'Wednesday', 1, 2],
            ['Nia Dani', 'Teknogi Perkantoran', 'XI MPLB', 'Wednesday', 3, 7],
            ['Nia Dani', 'Publik Speaking', 'XI AKL', 'Wednesday', 9, 9],
            ['Nia Dani', 'Publik Speaking', 'XI BUSANA', 'Thursday', 7, 7],
            
            // ========================================
            // 11. Ade Rua (MPLB) - 24 JP
            // ========================================
            ['Ade Rua', 'Adm Umum', 'XI MPLB', 'Monday', 5, 10],
            ['Ade Rua', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Tuesday', 1, 4],
            ['Ade Rua', 'Kearsipan', 'XI MPLB', 'Tuesday', 6, 10],
            ['Ade Rua', 'Ekonomi Bisnis', 'XI MPLB', 'Thursday', 3, 7],
            ['Ade Rua', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Thursday', 7, 10],
            
            // ========================================
            // 12. Liliyana (AKL) - 19 JP
            // ========================================
            ['Liliyana', 'Akuntansi Lembaga', 'XI AKL', 'Monday', 5, 6],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Monday', 6, 7],
            ['Liliyana', 'AKPIJM', 'XI AKL', 'Tuesday', 1, 5],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Tuesday', 5, 7],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Friday', 4, 6],
            ['Liliyana', 'PIPAS', 'X MPLB', 'Friday', 7, 10],
            
            // ========================================
            // 13. Debby Furi (Busana) - 16 JP
            // ========================================
            ['Debby Furi', 'Gambar Teknis', 'XI BUSANA', 'Monday', 1, 5],
            ['Debby Furi', 'Dasar Prog Keahlian Busana', 'X BUSANA', 'Wednesday', 7, 10],
            ['Debby Furi', 'Dasar Prog Keahlian Busana', 'X BUSANA', 'Thursday', 1, 7],
            
            // ========================================
            // 14. Dhani (Informatika & Matematika) - 27 JP
            // ========================================
            ['Dhani', 'INFORMATIKA', 'X AKL', 'Monday', 4, 7],
            ['Dhani', 'Matematika', 'XI AKL', 'Wednesday', 1, 3],
            ['Dhani', 'INFORMATIKA', 'X MPLB', 'Wednesday', 4, 7],
            ['Dhani', 'Matematika', 'XI MPLB', 'Wednesday', 8, 10],
            ['Dhani', 'PIPAS', 'X AKL', 'Thursday', 1, 4],
            ['Dhani', 'INFORMATIKA', 'X BUSANA', 'Friday', 1, 5],
            ['Dhani', 'Matematika', 'XI BUSANA', 'Friday', 6, 9],
            
            // ========================================
            // 15. Wiwit Mergi (Busana) - 6 JP
            // ========================================
            ['Wiwit Mergi', 'Persiapan Pembuatan Busana', 'XI BUSANA', 'Tuesday', 5, 10],
            
            // ========================================
            // 16. Adela (PJOK & Sejarah Indonesia) - 25 JP
            // ========================================
            ['Adela', 'PJOK', 'XI MPLB', 'Tuesday', 1, 3],
            ['Adela', 'PJOK', 'XI BUSANA', 'Tuesday', 3, 5],
            ['Adela', 'Sejarah Indonesia', 'XI AKL', 'Tuesday', 5, 5],
            ['Adela', 'PJOK', 'X BUSANA', 'Wednesday', 1, 3],
            ['Adela', 'PJOK', 'XI AKL', 'Wednesday', 4, 6],
            ['Adela', 'Sejarah Indonesia', 'XI BUSANA', 'Wednesday', 5, 7],
            ['Adela', 'PJOK', 'X MPLB', 'Thursday', 1, 3],
            ['Adela', 'PJOK', 'X AKL', 'Friday', 1, 4],
            ['Adela', 'Sejarah Indonesia', 'XI MPLB', 'Friday', 7, 9],
            
            // ========================================
            // 17. Ervinda (Bahasa Inggris) - 20 JP
            // ========================================
            ['Ervinda', 'Bahasa Inggris', 'X BUSANA', 'Tuesday', 1, 3],
            ['Ervinda', 'Bahasa Inggris', 'X AKL', 'Tuesday', 8, 10],
            ['Ervinda', 'Bahasa Inggris', 'X MPLB', 'Wednesday', 1, 4],
            ['Ervinda', 'Bahasa Inggris', 'XI AKL', 'Wednesday', 6, 8],
            ['Ervinda', 'Bahasa Inggris', 'XI BUSANA', 'Friday', 1, 4],
            ['Ervinda', 'Bahasa Inggris', 'XI MPLB', 'Friday', 4, 6],
            
            // ========================================
            // 18. Marista (B. Indonesia) - 24 JP
            // ========================================
            ['Marista', 'B. Indonesia', 'XI AKL', 'Monday', 1, 3],
            ['Marista', 'B. Indonesia', 'XI BUSANA', 'Monday', 7, 10],
            ['Marista', 'B. Indonesia', 'XI MPLB', 'Tuesday', 3, 6],
            ['Marista', 'B. Indonesia', 'X MPLB', 'Tuesday', 7, 10],
            ['Marista', 'B. Indonesia', 'X AKL', 'Thursday', 7, 10],
            ['Marista', 'B. Indonesia', 'X BUSANA', 'Friday', 5, 8],
            ['Marista', 'Sejarah Indonesia', 'X BUSANA', 'Friday', 8, 9],
        ];
    }

    private function createSchedule($data)
    {
        [$teacherName, $subjectName, $className, $day, $jamStart, $jamEnd] = $data;

        $teacherId = $this->findTeacher($teacherName);
        $subjectId = $this->findSubject($subjectName);
        $classId = $this->findClass($className);

        if (!$teacherId || !$subjectId || !$classId) {
            $this->command->warn("⊘ Skipped: $teacherName | $subjectName | $className | $day (missing data)");
            return 0;
        }

        $dayMap = [
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
        ];
        
        $mappedDay = $dayMap[$day];
        $created = 0;

        // Loop dari Jam ke-X start sampai end
        for ($jam = $jamStart; $jam <= $jamEnd; $jam++) {
            // Convert Jam ke-X to Order
            if (!isset($this->jamToOrderMap[$jam])) {
                $this->command->warn("  Warning: Jam ke-{$jam} tidak ada dalam mapping");
                continue;
            }
            
            $order = $this->jamToOrderMap[$jam];
            
            // Get time_slot_id untuk day dan order ini
            if (!isset($this->timeSlotCache[$mappedDay][$order])) {
                $this->command->warn("  Warning: Order {$order} tidak ada untuk {$mappedDay}");
                continue;
            }
            
            $timeSlotId = $this->timeSlotCache[$mappedDay][$order];

            TeachingSchedule::create([
                'teacher_id' => $teacherId,
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'academic_year_id' => $this->academicYear->id,
                'day_of_week' => $mappedDay,
                'time_slot_id' => $timeSlotId,
                'is_active' => true,
            ]);
            
            $created++;
        }

        return $created;
    }

    private function findTeacher($name)
    {
        $name = strtolower($name);
        
        if (isset($this->teacherCache[$name])) {
            return $this->teacherCache[$name];
        }

        foreach ($this->teacherCache as $fullName => $id) {
            if (str_contains($fullName, $name)) {
                return $id;
            }
        }

        return null;
    }

    private function findSubject($name)
    {
        $name = strtolower($name);
        
        if (isset($this->subjectCache[$name])) {
            return $this->subjectCache[$name];
        }

        foreach ($this->subjectCache as $fullName => $id) {
            if (str_contains($fullName, $name) || str_contains($name, $fullName)) {
                return $id;
            }
        }

        return null;
    }

    private function findClass($name)
    {
        $name = strtolower($name);
        
        if (isset($this->classCache[$name])) {
            return $this->classCache[$name];
        }

        foreach ($this->classCache as $fullName => $id) {
            if (str_contains($fullName, $name)) {
                return $id;
            }
        }

        return null;
    }
}

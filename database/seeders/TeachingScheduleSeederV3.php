<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingSchedule;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\AcademicYear;

class TeachingScheduleSeederV3 extends Seeder
{
    private $academicYear;
    private $teacherCache = [];
    private $classCache = [];
    private $subjectCache = [];
    private $timeSlotCache = [];
    
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
        10 => 13, // Jam ke-10 → order 13 (tapi diabaikan untuk Senin)
    ];

    public function run(): void
    {
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$this->academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        TeachingSchedule::where('academic_year_id', $this->academicYear->id)->delete();
        $this->command->info('Seeding teaching schedules (V3 - from actual images)...');

        $this->preloadData();
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
        User::whereIn('role', ['guru', 'waka_kurikulum', 'kepala_sekolah'])
            ->get()
            ->each(function($user) {
                $this->teacherCache[strtolower($user->name)] = $user->id;
            });

        SchoolClass::where('academic_year_id', $this->academicYear->id)
            ->get()
            ->each(function($class) {
                $this->classCache[strtolower($class->name)] = $class->id;
            });

        Subject::all()->each(function($subject) {
            $this->subjectCache[strtolower($subject->name)] = $subject->id;
        });

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
        // Format: [teacher_name, subject_name, class_name, day, jam_start, jam_end, skip_monday_10]
        // skip_monday_10 = true means don't create if Monday and jam = 10
        
        return [
            // ========================================
            // X AKL - 49 JP (berdasarkan gambar)
            // ========================================
            // Senin
            ['Tri Mulyaningsih', 'Sejarah Indonesia', 'X AKL', 'Monday', 1, 2],
            ['Ilham', 'Bimbingan Konseling', 'X AKL', 'Monday', 3, 3],
            ['Dhani', 'INFORMATIKA', 'X AKL', 'Monday', 4, 7],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Monday', 8, 8],
            // Jam 9: BTQ/GURU BTQ (user will add manually if needed, we skip)
            // Jam 10: KOSONG (skip)
            
            // Selasa
            ['Budi Siswanto', 'PAIBP', 'X AKL', 'Tuesday', 1, 4],
            ['Suseno', 'PKN', 'X AKL', 'Tuesday', 5, 5],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Tuesday', 6, 7],
            ['Ervinda', 'Bahasa Inggris', 'X AKL', 'Tuesday', 8, 10],
            
            // Rabu
            ['Dewi Wartini', 'Matematika', 'X AKL', 'Wednesday', 1, 3],
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Wednesday', 4, 5],
            ['Huda', 'KKA', 'X AKL', 'Wednesday', 6, 6],
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Wednesday', 7, 8],
            ['Ilham', 'Ke PGRI an', 'X AKL', 'Wednesday', 9, 9],
            ['Pancawati', 'Seni Budaya', 'X AKL', 'Wednesday', 9, 9],
            
            // Kamis
            ['Dhani', 'PIPAS', 'X AKL', 'Thursday', 1, 4],
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Thursday', 5, 6],
            ['Marista', 'B. Indonesia', 'X AKL', 'Thursday', 7, 10],
            
            // Jumat
            ['Adela', 'PJOK', 'X AKL', 'Friday', 1, 4],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Friday', 5, 7],
            ['Dewi Wartini', 'Matematika', 'X AKL', 'Friday', 8, 9],
            ['Munisah', 'B. Jawa', 'X AKL', 'Friday', 10, 10],
            
            // ========================================
            // X MPLB - 49 JP
            // ========================================
            // Senin
            ['Nia Dani', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Monday', 1, 4],
            ['Suseno', 'PKN', 'X MPLB', 'Monday', 5, 5],
            ['Ilham', 'Bimbingan Konseling', 'X MPLB', 'Monday', 6, 6],
            ['Pancawati', 'Seni Budaya', 'X MPLB', 'Monday', 7, 8],
            // Jam 9-10: BTQ/KOKURI (skip)
            
            // Selasa
            ['Ade Rua', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Tuesday', 1, 4],
            ['Dewi Wartini', 'Matematika', 'X MPLB', 'Tuesday', 5, 5],
            ['Marista', 'B. Indonesia', 'X MPLB', 'Tuesday', 6, 10],
            
            // Rabu
            ['Ervinda', 'Bahasa Inggris', 'X MPLB', 'Wednesday', 1, 4],
            ['Dhani', 'INFORMATIKA', 'X MPLB', 'Wednesday', 5, 8],
            ['Huda', 'KKA', 'X MPLB', 'Wednesday', 9, 9],
            ['Ilham', 'Ke PGRI an', 'X MPLB', 'Wednesday', 9, 9],
            
            // Kamis
            ['Adela', 'PJOK', 'X MPLB', 'Thursday', 1, 3],
            ['Budi Siswanto', 'PAIBP', 'X MPLB', 'Thursday', 4, 4],
            ['Rieswati', 'PAKBP', 'X MPLB', 'Thursday', 5, 5],
            ['Rieswati', 'PABBP', 'X MPLB', 'Thursday', 6, 6],
            ['Ade Rua', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Thursday', 7, 10],
            
            // Jumat
            ['Dewi Wartini', 'Matematika', 'X MPLB', 'Friday', 1, 2],
            ['Ari Yunitasari', 'Sejarah Indonesia', 'X MPLB', 'Friday', 3, 4],
            ['Munisah', 'B. Jawa', 'X MPLB', 'Friday', 5, 5],
            ['Liliyana', 'PIPAS', 'X MPLB', 'Friday', 6, 10],
            
            // ========================================
            // X BUSANA - 49 JP
            // ========================================
            // Senin
            ['Ilham', 'Bimbingan Konseling', 'X BUSANA', 'Monday', 1, 1],
            ['Dewi Wartini', 'Matematika', 'X BUSANA', 'Monday', 2, 2],
            ['Dewi Wartini', 'PIPAS', 'X BUSANA', 'Monday', 3, 6],
            ['Suseno', 'PKN', 'X BUSANA', 'Monday', 8, 8],
            // Jam 9-10: BTQ/KOKURI (skip)
            
            // Selasa
            ['Ervinda', 'Bahasa Inggris', 'X BUSANA', 'Tuesday', 1, 3],
            ['Budi Siswanto', 'PAIBP', 'X BUSANA', 'Tuesday', 4, 6],
            ['Dewi Wartini', 'Matematika', 'X BUSANA', 'Tuesday', 7, 7],
            ['Pancawati', 'Seni Budaya', 'X BUSANA', 'Tuesday', 8, 9],
            
            // Rabu
            ['Adela', 'PJOK', 'X BUSANA', 'Wednesday', 1, 3],
            ['Huda', 'KKA', 'X BUSANA', 'Wednesday', 4, 4],
            ['Ilham', 'Ke PGRI an', 'X BUSANA', 'Wednesday', 5, 5],
            ['Debby Furi', 'Dasar Prog Keahlian Busana', 'X BUSANA', 'Wednesday', 6, 10],
            
            // Kamis
            ['Debby Furi', 'Dasar Prog Keahlian Busana', 'X BUSANA', 'Thursday', 1, 7],
            ['Munisah', 'B. Jawa', 'X BUSANA', 'Thursday', 8, 8],
            
            // Jumat
            ['Dhani', 'INFORMATIKA', 'X BUSANA', 'Friday', 1, 5],
            ['Marista', 'B. Indonesia', 'X BUSANA', 'Friday', 6, 9],
            ['Marista', 'Sejarah Indonesia', 'X BUSANA', 'Friday', 10, 10],
            
            // ========================================
            // XI AKL - 49 JP
            // ========================================
            // Senin
            ['Marista', 'B. Indonesia', 'XI AKL', 'Monday', 1, 3],
            ['Ari Yunitasari', 'Bisnis Retail', 'XI AKL', 'Monday', 4, 5],
            ['Liliyana', 'Akuntansi Lembaga', 'XI AKL', 'Monday', 6, 6],
            ['Munisah', 'B. Jawa', 'XI AKL', 'Monday', 7, 8],
            // Jam 9-10: BTQ/KOKURI (skip)
            
            // Selasa
            ['Liliyana', 'AKPJDM', 'XI AKL', 'Tuesday', 1, 4],
            ['Adela', 'Sejarah Indonesia', 'XI AKL', 'Tuesday', 5, 5],
            ['Tri Mulyaningsih', 'Akuntansi Keuangan', 'XI AKL', 'Tuesday', 6, 10],
            
            // Rabu
            ['Dhani', 'Matematika', 'XI AKL', 'Wednesday', 1, 3],
            ['Adela', 'PJOK', 'XI AKL', 'Wednesday', 4, 6],
            ['Ervinda', 'Bahasa Inggris', 'XI AKL', 'Wednesday', 7, 8],
            ['Nia Dani', 'Publik Speaking', 'XI AKL', 'Wednesday', 9, 9],
            
            // Kamis
            ['Tri Mulyaningsih', 'Perpajakan', 'XI AKL', 'Thursday', 1, 1],
            ['Suseno', 'PKN', 'XI AKL', 'Thursday', 2, 2],
            ['Tri Mulyaningsih', 'Komp. Akuntansi', 'XI AKL', 'Thursday', 4, 7],
            ['Budi Siswanto', 'PAIBP', 'XI AKL', 'Thursday', 8, 9],
            
            // Jumat
            ['Pancawati', 'KIK', 'XI AKL', 'Friday', 1, 6],
            ['Ari Yunitasari', 'EkoBis dan Adm Umum', 'XI AKL', 'Friday', 7, 7],
            ['Pancawati', 'Membatik', 'XI AKL', 'Friday', 9, 10],
            
            // ========================================
            // XI MPLB - 49 JP
            // ========================================
            // Senin
            ['Pancawati', 'KIK', 'XI MPLB', 'Monday', 1, 4],
            ['Ade Rua', 'Adm Umum', 'XI MPLB', 'Monday', 5, 8],
            // Jam 9-10: BTQ/KOKURI (skip)
            
            // Selasa
            ['Adela', 'PJOK', 'XI MPLB', 'Tuesday', 1, 2],
            ['Marista', 'B. Indonesia', 'XI MPLB', 'Tuesday', 3, 6],
            ['Ade Rua', 'Kearsipan', 'XI MPLB', 'Tuesday', 7, 10],
            
            // Rabu
            ['Nia Dani', 'Publik Speaking', 'XI MPLB', 'Wednesday', 1, 2],
            ['Nia Dani', 'Teknogi Perkantoran', 'XI MPLB', 'Wednesday', 3, 7],
            ['Dhani', 'Matematika', 'XI MPLB', 'Wednesday', 8, 10],
            
            // Kamis
            ['Munisah', 'B. Jawa', 'XI MPLB', 'Thursday', 1, 1],
            ['Ade Rua', 'Ekonomi Bisnis', 'XI MPLB', 'Thursday', 2, 2],
            ['Ade Rua', 'Ekonomi Bisnis', 'XI MPLB', 'Thursday', 4, 7],
            ['Ari Yunitasari', 'Bisnis Retail', 'XI MPLB', 'Thursday', 8, 8],
            ['Suseno', 'PKN', 'XI MPLB', 'Thursday', 9, 10],
            
            // Jumat
            ['Budi Siswanto', 'PAIBP', 'XI MPLB', 'Friday', 1, 3],
            ['Ervinda', 'Bahasa Inggris', 'XI MPLB', 'Friday', 4, 6],
            ['Adela', 'Sejarah Indonesia', 'XI MPLB', 'Friday', 7, 8],
            ['Pancawati', 'Membatik', 'XI MPLB', 'Friday', 9, 10],
            
            // ========================================
            // XI BUSANA - 49 JP
            // ========================================
            // Senin
            ['Debby Furi', 'Gambar Teknis', 'XI BUSANA', 'Monday', 1, 5],
            ['Marista', 'B. Indonesia', 'XI BUSANA', 'Monday', 6, 8],
            // Jam 9-10: BTQ/KOKURI (skip)
            
            // Selasa
            ['Munisah', 'B. Jawa', 'XI BUSANA', 'Tuesday', 1, 2],
            ['Adela', 'PJOK', 'XI BUSANA', 'Tuesday', 3, 4],
            ['Wiwit Mergi', 'Persiapan Pembuatan Busana', 'XI BUSANA', 'Tuesday', 5, 10],
            
            // Rabu
            ['Pancawati', 'KIK', 'XI BUSANA', 'Wednesday', 1, 4],
            ['Adela', 'Sejarah Indonesia', 'XI BUSANA', 'Wednesday', 5, 7],
            ['Budi Siswanto', 'PAIBP', 'XI BUSANA', 'Wednesday', 8, 10],
            
            // Kamis
            ['Yully Setyo', 'Menjahit Produk Busana', 'XI BUSANA', 'Thursday', 1, 5],
            ['Nia Dani', 'Publik Speaking', 'XI BUSANA', 'Thursday', 6, 6],
            ['Ari Yunitasari', 'Bisnis Retail', 'XI BUSANA', 'Thursday', 7, 7],
            
            // Jumat
            ['Ervinda', 'Bahasa Inggris', 'XI BUSANA', 'Friday', 1, 4],
            ['Suseno', 'PKN', 'XI BUSANA', 'Friday', 5, 5],
            ['Dhani', 'Matematika', 'XI BUSANA', 'Friday', 6, 9],
            ['Pancawati', 'Membatik', 'XI BUSANA', 'Friday', 10, 10],
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

        for ($jam = $jamStart; $jam <= $jamEnd; $jam++) {
            // Skip HANYA Senin Jam 10 (BTQ/KOKURI), Jam 9 tetap jalan
            if ($mappedDay === 'Monday' && $jam == 10) {
                continue;
            }
            
            if (!isset($this->jamToOrderMap[$jam])) {
                $this->command->warn("  Warning: Jam ke-{$jam} tidak ada dalam mapping");
                continue;
            }
            
            $order = $this->jamToOrderMap[$jam];
            
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

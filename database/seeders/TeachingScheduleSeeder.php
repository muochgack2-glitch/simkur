<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingSchedule;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\AcademicYear;

class TeachingScheduleSeeder extends Seeder
{
    private $academicYear;
    private $teacherCache = [];
    private $classCache = [];
    private $subjectCache = [];
    private $timeSlotCache = [];

    public function run(): void
    {
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$this->academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        TeachingSchedule::where('academic_year_id', $this->academicYear->id)->delete();
        $this->command->info('Seeding teaching schedules...');

        // Preload all data to cache
        $this->preloadData();

        // Define all schedules in compact format
        $schedules = $this->getAllSchedules();

        $created = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            if ($this->createSchedule($schedule)) {
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->command->info("✓ Created: $created schedules");
        $this->command->warn("⊘ Skipped: $skipped schedules (missing data)");
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
        SchoolClass::all()->each(function($class) {
            $this->classCache[strtolower($class->name)] = $class->id;
        });

        // Cache subjects
        Subject::all()->each(function($subject) {
            $this->subjectCache[strtolower($subject->name)] = $subject->id;
        });

        // Cache time slots (by order)
        TimeSlot::orderBy('order')->get()->each(function($slot) {
            $this->timeSlotCache[$slot->order] = $slot->id;
        });
    }

    private function getAllSchedules()
    {
        // Format: [teacher_name, subject_name, class_name, day, slot_start, slot_end]
        return [
            // GURU BTQ
            ['GURU BTQ', 'KOKURI', 'X BTQ', 'Monday', 9, 10],
            
            // Wiwit Mergi W., A.Md
            ['Wiwit Mergi', 'Penyusunan Koleksi Busana', 'XII BUSANA', 'Monday', 5, 8],
            ['Wiwit Mergi', 'Persiapan Pembuatan Busana', 'XI BUSANA', 'Tuesday', 5, 10],
            ['Wiwit Mergi', 'Penyusunan Koleksi Busana', 'XII BUSANA', 'Wednesday', 6, 10],
            
            // Debby Furi Wijayanti, S.Pd
            ['Debby Furi', 'Gambar Teknis', 'XI BUSANA', 'Monday', 1, 4],
            ['Debby Furi', 'KIK', 'XII MPLB', 'Tuesday', 1, 4],
            ['Debby Furi', 'KIK', 'XII BUSANA', 'Wednesday', 1, 4],
            ['Debby Furi', 'Dasar Prog Keahlian Busana', 'X BUSANA', 'Wednesday', 7, 10],
            ['Debby Furi', 'Dasar Prog Keahlian Busana', 'X BUSANA', 'Thursday', 1, 6],
            
            // Drs. Suseno (PKN)
            ['Suseno', 'PKN', 'XII BUSANA', 'Monday', 1, 2],
            ['Suseno', 'PKN', 'X MPLB', 'Monday', 5, 6],
            ['Suseno', 'PKN', 'X BUSANA', 'Monday', 8, 9],
            ['Suseno', 'PKN', 'X AKL', 'Tuesday', 4, 6],
            ['Suseno', 'PKN', 'XI AKL', 'Thursday', 4, 5],
            ['Suseno', 'PKN', 'XI MPLB', 'Thursday', 9, 10],
            ['Suseno', 'PKN', 'XII MPLB', 'Friday', 1, 2],
            ['Suseno', 'PKN', 'XI BUSANA', 'Friday', 4, 6],
            ['Suseno', 'PKN', 'XII AKL', 'Friday', 9, 10],
            
            // Ervinda Sekar Asmara, S.Pd (Bahasa Inggris)
            ['Ervinda', 'Bahasa Inggris', 'X BUSANA', 'Tuesday', 1, 3],
            ['Ervinda', 'Bahasa Inggris', 'XII AKL', 'Tuesday', 6, 7],
            ['Ervinda', 'Bahasa Inggris', 'X AKL', 'Tuesday', 8, 10],
            ['Ervinda', 'Bahasa Inggris', 'X MPLB', 'Wednesday', 1, 4],
            ['Ervinda', 'Bahasa Inggris', 'XI AKL', 'Wednesday', 6, 8],
            ['Ervinda', 'Bahasa Inggris', 'XII MPLB', 'Wednesday', 8, 10],
            ['Ervinda', 'Bahasa Inggris', 'XII BUSANA', 'Thursday', 6, 8],
            ['Ervinda', 'Bahasa Inggris', 'XI BUSANA', 'Friday', 1, 4],
            ['Ervinda', 'Bahasa Inggris', 'XI MPLB', 'Friday', 4, 6],
            ['Ervinda', 'Bahasa Inggris', 'XII AKL', 'Friday', 7, 8],
            ['Ervinda', 'Bahasa Inggris', 'XII BUSANA', 'Friday', 9, 10],
            
            // Adela Wulan Kurniasari, S.Pd (PJOK & Sejarah Indonesia)
            ['Adela', 'PJOK', 'XI MPLB', 'Tuesday', 1, 3],
            ['Adela', 'PJOK', 'XI BUSANA', 'Tuesday', 3, 5],
            ['Adela', 'Sejarah Indonesia', 'XI AKL', 'Tuesday', 5, 5],
            ['Adela', 'PJOK', 'X BUSANA', 'Wednesday', 1, 3],
            ['Adela', 'PJOK', 'XI AKL', 'Wednesday', 3, 5],
            ['Adela', 'Sejarah Indonesia', 'XI BUSANA', 'Wednesday', 5, 6],
            ['Adela', 'PJOK', 'X MPLB', 'Thursday', 1, 3],
            ['Adela', 'PJOK', 'X AKL', 'Friday', 1, 4],
            ['Adela', 'Sejarah Indonesia', 'XI MPLB', 'Friday', 7, 8],
            
            // Marista Bela Octaviana, S.Pd (B. Indonesia)
            ['Marista', 'B. Indonesia', 'XI AKL', 'Monday', 1, 3],
            ['Marista', 'B. Indonesia', 'XI BUSANA', 'Monday', 7, 10],
            ['Marista', 'B. Indonesia', 'XI MPLB', 'Tuesday', 3, 6],
            ['Marista', 'B. Indonesia', 'X MPLB', 'Tuesday', 7, 10],
            ['Marista', 'B. Indonesia', 'X AKL', 'Thursday', 7, 10],
            ['Marista', 'B. Indonesia', 'X BUSANA', 'Friday', 5, 8],
            ['Marista', 'Sejarah Indonesia', 'X BUSANA', 'Friday', 8, 9],
            
            // Tri Mulyaningsih, S.E (Sejarah Indonesia, Komp. Akuntansi, Akuntansi Keuangan)
            ['Tri Mulyaningsih', 'Sejarah Indonesia', 'X AKL', 'Monday', 1, 2],
            ['Tri Mulyaningsih', 'Komp. Akuntansi', 'XII AKL', 'Monday', 7, 9],
            ['Tri Mulyaningsih', 'KIK', 'XII AKL', 'Tuesday', 1, 6],
            ['Tri Mulyaningsih', 'Akuntansi Keuangan', 'XI AKL', 'Tuesday', 7, 10],
            ['Tri Mulyaningsih', 'Akuntansi Keuangan', 'XII AKL', 'Wednesday', 3, 8],
            ['Tri Mulyaningsih', 'Komp. Akuntansi', 'XII AKL', 'Wednesday', 8, 10],
            ['Tri Mulyaningsih', 'Perpajakan', 'XI AKL', 'Thursday', 1, 2],
            ['Tri Mulyaningsih', 'Komp. Akuntansi', 'XI AKL', 'Thursday', 4, 7],
            
            // Budi Siswanto, S.Pd.I (PAIBP & Publik Speaking)
            ['Budi Siswanto', 'PAIBP', 'X AKL', 'Tuesday', 1, 4],
            ['Budi Siswanto', 'PAIBP', 'X BUSANA', 'Tuesday', 4, 6],
            ['Budi Siswanto', 'Publik Speaking', 'XII MPLB', 'Tuesday', 8, 10],
            ['Budi Siswanto', 'Publik Speaking', 'XII AKL', 'Wednesday', 1, 2],
            ['Budi Siswanto', 'PAIBP', 'XI BUSANA', 'Wednesday', 7, 10],
            ['Budi Siswanto', 'Publik Speaking', 'XII BUSANA', 'Thursday', 1, 2],
            ['Budi Siswanto', 'PAIBP', 'X MPLB', 'Thursday', 4, 7],
            ['Budi Siswanto', 'PAIBP', 'XI AKL', 'Thursday', 7, 9],
            ['Budi Siswanto', 'PAIBP', 'XI MPLB', 'Friday', 1, 3],
            
            // Dewi Wartini, S.Pd (Matematika & PIPAS)
            ['Dewi Wartini', 'Matematika', 'X BUSANA', 'Monday', 2, 3],
            ['Dewi Wartini', 'PIPAS', 'X BUSANA', 'Monday', 3, 5],
            ['Dewi Wartini', 'Matematika', 'XII BUSANA', 'Tuesday', 1, 3],
            ['Dewi Wartini', 'Matematika', 'X MPLB', 'Tuesday', 5, 6],
            ['Dewi Wartini', 'Matematika', 'X BUSANA', 'Tuesday', 6, 7],
            ['Dewi Wartini', 'Matematika', 'X AKL', 'Wednesday', 1, 2],
            ['Dewi Wartini', 'Matematika', 'XII MPLB', 'Wednesday', 6, 8],
            ['Dewi Wartini', 'Matematika', 'X MPLB', 'Friday', 1, 3],
            ['Dewi Wartini', 'Matematika', 'XII AKL', 'Friday', 4, 6],
            ['Dewi Wartini', 'Matematika', 'X AKL', 'Friday', 7, 9],
            
            // Munisah, S.Pd (B. Jawa)
            ['Munisah', 'B. Jawa', 'XII AKL', 'Monday', 1, 2],
            ['Munisah', 'B. Jawa', 'XII BUSANA', 'Monday', 2, 3],
            ['Munisah', 'B. Jawa', 'XI AKL', 'Monday', 7, 8],
            ['Munisah', 'B. Jawa', 'XI BUSANA', 'Tuesday', 1, 2],
            ['Munisah', 'B. Jawa', 'XI MPLB', 'Thursday', 1, 2],
            ['Munisah', 'B. Jawa', 'XII MPLB', 'Thursday', 7, 8],
            ['Munisah', 'B. Jawa', 'X BUSANA', 'Thursday', 8, 9],
            ['Munisah', 'B. Jawa', 'X MPLB', 'Friday', 5, 6],
            ['Munisah', 'B. Jawa', 'X AKL', 'Friday', 9, 10],
            
            // Ari Yunitasari, S.Pd (Bisnis Retail, Dasar Prog Keahlian AKL, Perpajakan, EkoBis, Adm Umum)
            ['Ari Yunitasari', 'Bisnis Retail', 'XI AKL', 'Monday', 4, 5],
            ['Ari Yunitasari', 'Bisnis Retail', 'XII AKL', 'Monday', 5, 7],
            ['Ari Yunitasari', 'Bisnis Retail', 'XII MPLB', 'Monday', 7, 9],
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Wednesday', 4, 8],
            ['Ari Yunitasari', 'Perpajakan', 'XII AKL', 'Thursday', 1, 3],
            ['Ari Yunitasari', 'Dasar Prog Keahlian AKL', 'X AKL', 'Thursday', 5, 7],
            ['Ari Yunitasari', 'Bisnis Retail', 'XI MPLB', 'Thursday', 7, 9],
            ['Ari Yunitasari', 'Bisnis Retail', 'XI BUSANA', 'Thursday', 9, 10],
            ['Ari Yunitasari', 'Bisnis Retail', 'XII BUSANA', 'Friday', 1, 2],
            ['Ari Yunitasari', 'Sejarah Indonesia', 'X MPLB', 'Friday', 2, 3],
            ['Ari Yunitasari', 'EkoBis dan Adm Umum', 'XI AKL', 'Friday', 5, 6],
            
            // Meiranti Trisnaning S., S.Pd (B. Indonesia)
            ['Meiranti', 'B. Indonesia', 'XII MPLB', 'Tuesday', 6, 8],
            ['Meiranti', 'B. Indonesia', 'XII BUSANA', 'Thursday', 8, 10],
            ['Meiranti', 'B. Indonesia', 'XII AKL', 'Friday', 1, 4],
            
            // M. Huda Muttaqin, S.Pd.I (PAIBP & KKA)
            ['Huda', 'PAIBP', 'XII MPLB', 'Monday', 4, 6],
            ['Huda', 'KKA', 'X BUSANA', 'Wednesday', 4, 5],
            ['Huda', 'KKA', 'X AKL', 'Wednesday', 5, 6],
            ['Huda', 'KKA', 'X MPLB', 'Wednesday', 6, 8],
            ['Huda', 'PAIBP', 'XII BUSANA', 'Thursday', 3, 6],
            ['Huda', 'PAIBP', 'XII AKL', 'Thursday', 7, 10],
            
            // Yully Setyo. A., S.Pd (Gaya dan Pengembangan Desain, Menjahit Produk Busana)
            ['Yully', 'Gaya dan Pengembangan Desain', 'XII BUSANA', 'Tuesday', 4, 10],
            ['Yully', 'Menjahit Produk Busana', 'XI BUSANA', 'Thursday', 1, 7],
            ['Yully', 'Gaya dan Pengembangan Desain', 'XII BUSANA', 'Friday', 3, 8],
            
            // Ilham Hardiyan P., S.Pd (Bimbingan Konseling & Ke PGRI an)
            ['Ilham', 'Bimbingan Konseling', 'X BUSANA', 'Monday', 1, 1],
            ['Ilham', 'Bimbingan Konseling', 'X AKL', 'Monday', 2, 2],
            ['Ilham', 'Bimbingan Konseling', 'X MPLB', 'Monday', 6, 7],
            ['Ilham', 'Bimbingan Konseling', 'XII BUSANA', 'Tuesday', 9, 10],
            ['Ilham', 'Bimbingan Konseling', 'XII MPLB', 'Wednesday', 1, 1],
            ['Ilham', 'Ke PGRI an', 'X BUSANA', 'Wednesday', 6, 6],
            ['Ilham', 'Bimbingan Konseling', 'XII AKL', 'Wednesday', 7, 7],
            ['Ilham', 'Ke PGRI an', 'X AKL', 'Wednesday', 8, 8],
            ['Ilham', 'Ke PGRI an', 'X MPLB', 'Wednesday', 9, 10],
            
            // Pancawati Puji L., A.Md (KIK & Seni Budaya)
            ['Pancawati', 'KIK', 'XI MPLB', 'Monday', 1, 5],
            ['Pancawati', 'Seni Budaya', 'X MPLB', 'Monday', 7, 9],
            ['Pancawati', 'Seni Budaya', 'X BUSANA', 'Tuesday', 9, 10],
            ['Pancawati', 'KIK', 'XI BUSANA', 'Wednesday', 1, 6],
            ['Pancawati', 'Seni Budaya', 'X AKL', 'Wednesday', 8, 9],
            ['Pancawati', 'KIK', 'XI AKL', 'Friday', 1, 6],
            ['Pancawati', 'Membatik', 'XI AKL', 'Friday', 9, 10],
            ['Pancawati', 'Membatik', 'XI BUSANA', 'Friday', 9, 10],
            ['Pancawati', 'Membatik', 'XI MPLB', 'Friday', 9, 10],
            
            // Nia Dani Rahayu, S.Pd (Dasar Prog Keahlian MPLB, Publik Speaking, Teknogi Perkantoran, Pengelolaan Rapat, Pengelolaan Keuangan)
            ['Nia Dani', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Monday', 1, 4],
            ['Nia Dani', 'Publik Speaking', 'XI MPLB', 'Wednesday', 1, 2],
            ['Nia Dani', 'Teknogi Perkantoran', 'XI MPLB', 'Wednesday', 3, 6],
            ['Nia Dani', 'Publik Speaking', 'XI AKL', 'Wednesday', 9, 9],
            ['Nia Dani', 'Pengelolaan Rapat', 'XII MPLB', 'Thursday', 1, 3],
            ['Nia Dani', 'Pengelolaan Keuangan', 'XII MPLB', 'Thursday', 3, 6],
            ['Nia Dani', 'Publik Speaking', 'XI BUSANA', 'Thursday', 7, 7],
            ['Nia Dani', 'Pengelolaan Rapat', 'XII MPLB', 'Friday', 3, 6],
            ['Nia Dani', 'Pengelolaan Keuangan', 'XII MPLB', 'Friday', 8, 10],
            
            // Ade Rua Nur Lemoniar, S.Pd (Pengelolaan Sarpras, Adm Umum, Kearsipan, Dasar Prog Keahlian MPLB, Ekonomi Bisnis)
            ['Ade Rua', 'Pengelolaan Sarpras', 'XII MPLB', 'Monday', 1, 4],
            ['Ade Rua', 'Adm Umum', 'XI MPLB', 'Monday', 5, 10],
            ['Ade Rua', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Tuesday', 1, 4],
            ['Ade Rua', 'Kearsipan', 'XI MPLB', 'Tuesday', 5, 10],
            ['Ade Rua', 'Pengelolaan Sarpras', 'XII MPLB', 'Wednesday', 2, 5],
            ['Ade Rua', 'Ekonomi Bisnis', 'XI MPLB', 'Thursday', 3, 6],
            ['Ade Rua', 'Dasar Prog Keahlian MPLB', 'X MPLB', 'Thursday', 7, 10],
            
            // Liliyana Ayu W., S.Pd (AKPIJM, Akuntansi Lembaga, Dasar Prog Keahlian AKL, PIPAS)
            ['Liliyana', 'AKPIJM', 'XII AKL', 'Monday', 3, 6],
            ['Liliyana', 'Akuntansi Lembaga', 'XI AKL', 'Monday', 5, 6],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Monday', 6, 7],
            ['Liliyana', 'AKPIJM', 'XI AKL', 'Tuesday', 1, 5],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Tuesday', 5, 7],
            ['Liliyana', 'AKPIJM', 'XII AKL', 'Tuesday', 8, 10],
            ['Liliyana', 'Akuntansi Lembaga', 'XII AKL', 'Thursday', 4, 8],
            ['Liliyana', 'Dasar Prog Keahlian AKL', 'X AKL', 'Friday', 4, 6],
            ['Liliyana', 'PIPAS', 'X MPLB', 'Friday', 7, 9],
            
            // Dhani Kisworo Jati, S.Pd (INFORMATIKA, Matematika, PIPAS)
            ['Dhani', 'INFORMATIKA', 'X AKL', 'Monday', 4, 7],
            ['Dhani', 'Matematika', 'XI AKL', 'Wednesday', 1, 3],
            ['Dhani', 'INFORMATIKA', 'X MPLB', 'Wednesday', 4, 7],
            ['Dhani', 'Matematika', 'XI MPLB', 'Wednesday', 8, 10],
            ['Dhani', 'PIPAS', 'X AKL', 'Thursday', 1, 4],
            ['Dhani', 'INFORMATIKA', 'X BUSANA', 'Friday', 1, 5],
            ['Dhani', 'Matematika', 'XI BUSANA', 'Friday', 6, 8],
        ];
    }

    private function createSchedule($data)
    {
        [$teacherName, $subjectName, $className, $day, $slotStart, $slotEnd] = $data;

        $teacherId = $this->findTeacher($teacherName);
        $subjectId = $this->findSubject($subjectName);
        $classId = $this->findClass($className);

        if (!$teacherId || !$subjectId || !$classId) {
            $this->command->warn("⊘ Skipped: $teacherName | $subjectName | $className | $day");
            return false;
        }

        $dayMap = [
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
        ];

        for ($slot = $slotStart; $slot <= $slotEnd; $slot++) {
            // Skip slot 0 - tidak ada di jadwal
            if ($slot < 1) continue;
            
            if (!isset($this->timeSlotCache[$slot])) {
                continue;
            }

            TeachingSchedule::create([
                'teacher_id' => $teacherId,
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'academic_year_id' => $this->academicYear->id,
                'day_of_week' => $dayMap[$day],
                'time_slot_id' => $this->timeSlotCache[$slot],
                'is_active' => true,
            ]);
        }

        return true;
    }

    private function findTeacher($name)
    {
        $name = strtolower($name);
        
        // Exact match
        if (isset($this->teacherCache[$name])) {
            return $this->teacherCache[$name];
        }

        // Partial match
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
        
        // Exact match
        if (isset($this->subjectCache[$name])) {
            return $this->subjectCache[$name];
        }

        // Partial match
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
        
        // Exact match
        if (isset($this->classCache[$name])) {
            return $this->classCache[$name];
        }

        // Partial match
        foreach ($this->classCache as $fullName => $id) {
            if (str_contains($fullName, $name)) {
                return $id;
            }
        }

        return null;
    }
}

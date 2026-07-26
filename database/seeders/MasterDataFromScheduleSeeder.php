<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;

class MasterDataFromScheduleSeeder extends Seeder
{
    /**
     * Seeder ini dibuat berdasarkan jadwal guru yang ada.
     * Akan menambahkan data yang hilang: guru, kelas, mata pelajaran
     */
    public function run(): void
    {
        $this->command->info('Creating master data from schedule...');

        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // 1. Create missing teachers
        $this->createTeachers();

        // 2. Create missing subjects
        $this->createSubjects();

        // 3. Create missing classes
        $this->createClasses($academicYear);

        $this->command->info('✓ Master data seeding completed!');
    }

    private function createTeachers()
    {
        $this->command->info('Creating teachers...');

        $teachers = [
            'GURU BTQ',
            'Wiwit Mergi W., A.Md',
            'Debby Furi Wijayanti, S.Pd',
            'Drs. Suseno',
            'Ervinda Sekar Asmara, S.Pd',
            'Adela Wulan Kurniasari, S.Pd',
            'Marista Bela Octaviana, S.Pd',
            'Tri Mulyaningsih, S.E',
            'Budi Siswanto, S.Pd.I',
            'Dewi Wartini, S.Pd',
            'Munisah, S.Pd',
            'Ari Yunitasari, S.Pd',
            'Meiranti Trisnaning S., S.Pd',
            'M. Huda Muttaqin, S.Pd.I',
            'Yully Setyo. A., S.Pd',
            'Ilham Hardiyan P., S.Pd',
            'Pancawati Puji L., A.Md',
            'Nia Dani Rahayu, S.Pd',
            'Ade Rua Nur Lemoniar, S.Pd',
            'Liliyana Ayu W., S.Pd',
            'Dhani Kisworo Jati, S.Pd',
        ];

        foreach ($teachers as $name) {
            if (!User::where('name', $name)->exists()) {
                $username = strtolower(str_replace([' ', '.', ',', '(', ')'], '', $name));
                User::create([
                    'name' => $name,
                    'username' => $username,
                    'email' => $username . '@smkpgriblora.sch.id',
                    'password' => Hash::make('password123'),
                    'role' => 'guru',
                ]);
                $this->command->info("  + Created teacher: $name");
            }
        }
    }

    private function createSubjects()
    {
        $this->command->info('Creating subjects...');

        $subjects = [
            ['code' => 'KOKURI', 'name' => 'Kokurikuler'],
            ['code' => 'PKN', 'name' => 'PKN'],
            ['code' => 'B-ING', 'name' => 'Bahasa Inggris'],
            ['code' => 'B-IND', 'name' => 'B. Indonesia'],
            ['code' => 'B-JAW', 'name' => 'B. Jawa'],
            ['code' => 'MTK', 'name' => 'Matematika'],
            ['code' => 'PJOK', 'name' => 'PJOK'],
            ['code' => 'SEJ-ID', 'name' => 'Sejarah Indonesia'],
            ['code' => 'PAIBP', 'name' => 'PAIBP'],
            ['code' => 'KIK', 'name' => 'KIK'],
            ['code' => 'PIPAS', 'name' => 'PIPAS'],
            ['code' => 'SENBUD', 'name' => 'Seni Budaya'],
            ['code' => 'KKA', 'name' => 'KKA'],
            ['code' => 'PGRI', 'name' => 'Ke PGRI an'],
            ['code' => 'BK', 'name' => 'Bimbingan Konseling'],
            ['code' => 'BATIK', 'name' => 'Membatik'],
            ['code' => 'PUBSPK', 'name' => 'Publik Speaking'],
            ['code' => 'INFORM', 'name' => 'INFORMATIKA'],
            
            // BUSANA
            ['code' => 'PKB-01', 'name' => 'Penyusunan Koleksi Busana'],
            ['code' => 'PKB-02', 'name' => 'Persiapan Pembuatan Busana'],
            ['code' => 'PKB-03', 'name' => 'Gambar Teknis'],
            ['code' => 'PKB-04', 'name' => 'Dasar Prog Keahlian Busana'],
            ['code' => 'PKB-05', 'name' => 'Gaya dan Pengembangan Desain'],
            ['code' => 'PKB-06', 'name' => 'Menjahit Produk Busana'],
            
            // AKL (Akuntansi)
            ['code' => 'AKL-01', 'name' => 'Dasar Prog Keahlian AKL'],
            ['code' => 'AKL-02', 'name' => 'Akuntansi Keuangan'],
            ['code' => 'AKL-03', 'name' => 'Komp. Akuntansi'],
            ['code' => 'AKL-04', 'name' => 'Perpajakan'],
            ['code' => 'AKL-05', 'name' => 'Akuntansi Lembaga'],
            ['code' => 'AKL-06', 'name' => 'AKPIJM'],
            
            // MPLB (Manajemen Perkantoran dan Layanan Bisnis)
            ['code' => 'MPLB-01', 'name' => 'Dasar Prog Keahlian MPLB'],
            ['code' => 'MPLB-02', 'name' => 'Adm Umum'],
            ['code' => 'MPLB-03', 'name' => 'Kearsipan'],
            ['code' => 'MPLB-04', 'name' => 'Teknogi Perkantoran'],
            ['code' => 'MPLB-05', 'name' => 'Pengelolaan Rapat'],
            ['code' => 'MPLB-06', 'name' => 'Pengelolaan Keuangan'],
            ['code' => 'MPLB-07', 'name' => 'Pengelolaan Sarpras'],
            
            // Bisnis & Retail
            ['code' => 'BIZ-01', 'name' => 'Bisnis Retail'],
            ['code' => 'BIZ-02', 'name' => 'EkoBis dan Adm Umum'],
            ['code' => 'BIZ-03', 'name' => 'Ekonomi Bisnis'],
        ];

        foreach ($subjects as $subject) {
            if (!Subject::where('name', $subject['name'])->exists()) {
                Subject::create([
                    'code' => $subject['code'],
                    'name' => $subject['name'],
                    'is_active' => true,
                ]);
                $this->command->info("  + Created subject: {$subject['name']}");
            }
        }
    }

    private function createClasses($academicYear)
    {
        $this->command->info('Creating classes...');

        $classes = [
            // Kelas X
            ['name' => 'X BTQ', 'grade' => 'X', 'major' => 'BUSANA'], // Assuming BTQ goes under BUSANA
            ['name' => 'X BUSANA', 'grade' => 'X', 'major' => 'BUSANA'],
            ['name' => 'X AKL', 'grade' => 'X', 'major' => 'AKL'],
            ['name' => 'X MPLB', 'grade' => 'X', 'major' => 'MPLB'],
            
            // Kelas XI
            ['name' => 'XI BUSANA', 'grade' => 'XI', 'major' => 'BUSANA'],
            ['name' => 'XI AKL', 'grade' => 'XI', 'major' => 'AKL'],
            ['name' => 'XI MPLB', 'grade' => 'XI', 'major' => 'MPLB'],
            
            // Kelas XII
            ['name' => 'XII BUSANA', 'grade' => 'XII', 'major' => 'BUSANA'],
            ['name' => 'XII AKL', 'grade' => 'XII', 'major' => 'AKL'],
            ['name' => 'XII MPLB', 'grade' => 'XII', 'major' => 'MPLB'],
        ];

        foreach ($classes as $classData) {
            if (!SchoolClass::where('name', $classData['name'])->where('academic_year_id', $academicYear->id)->exists()) {
                SchoolClass::create([
                    'name' => $classData['name'],
                    'grade' => $classData['grade'],
                    'major' => $classData['major'],
                    'academic_year_id' => $academicYear->id,
                    'rombel' => null,
                ]);
                $this->command->info("  + Created class: {$classData['name']}");
            }
        }
    }
}

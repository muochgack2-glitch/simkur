<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * READY TO BE FILLED WITH DATA FROM JADWAL GURU
     */
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'Drs. Suseno',
                'username' => 'suseno',
                'email' => 'suseno@smkpgriblora.sch.id',
                'subjects' => ['PKN'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Budi Siswanto, S.Pd.I',
                'username' => 'budi.siswanto',
                'email' => 'budi.siswanto@smkpgriblora.sch.id',
                'subjects' => ['PAIBP', 'Publik Speaking'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Yully Setyo A., S.Pd',
                'username' => 'yully.setyo',
                'email' => 'yully.setyo@smkpgriblora.sch.id',
                'subjects' => ['Gaya dan Pengembangan Desain', 'Menjahit Produk Busana'],
                'taught_majors' => ['BUSANA'],
            ],
            [
                'name' => 'Ilham Hardiyan P., S.Pd',
                'username' => 'ilham.hardiyan',
                'email' => 'ilham.hardiyan@smkpgriblora.sch.id',
                'subjects' => ['Bimbingan Konseling', 'Ke PGRI an'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Pancawati Puji L., A.Md',
                'username' => 'pancawati.puji',
                'email' => 'pancawati.puji@smkpgriblora.sch.id',
                'subjects' => ['KIK', 'Seni Budaya', 'Membatik'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Nia Dani Rahayu, S.Pd',
                'username' => 'nia.dani',
                'email' => 'nia.dani@smkpgriblora.sch.id',
                'subjects' => ['Pengelolaan Rapat', 'Publik Speaking', 'Teknogi Perkantoran', 'Penglolaan Keuangan', 'Dasar Prog Keahlian MPLB'],
                'taught_majors' => ['MPLB', 'BUSANA'],
            ],
            [
                'name' => 'Dewi Wartini, S.Pd',
                'username' => 'dewi.wartini',
                'email' => 'dewi.wartini@smkpgriblora.sch.id',
                'subjects' => ['PIPAS', 'Matematika'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Ade Rua Nur Lemoniar, S.Pd',
                'username' => 'ade.rua',
                'email' => 'ade.rua@smkpgriblora.sch.id',
                'subjects' => ['Dasar Prog Keahlian MPLB', 'Adm Umum', 'Pengelolaan Sarpras', 'Kearsipan', 'Ekonomi Bisnis'],
                'taught_majors' => ['MPLB'],
            ],
            [
                'name' => 'Liliyana Ayu W., S.Pd',
                'username' => 'liliyana.ayu',
                'email' => 'liliyana.ayu@smkpgriblora.sch.id',
                'subjects' => ['AKPJDM', 'Akuntansi Lembaga', 'Dasar Prog Keahlian AKL'],
                'taught_majors' => ['AKL'],
            ],
            [
                'name' => 'Dhani Kisworo Jati, S.Pd',
                'username' => 'dhani.kisworo',
                'email' => 'dhani.kisworo@smkpgriblora.sch.id',
                'subjects' => ['Matematika', 'PIPAS', 'INFORMATIKA'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Tri Mulyaningsih, S.E',
                'username' => 'tri.mulyaningsih',
                'email' => 'tri.mulyaningsih@smkpgriblora.sch.id',
                'subjects' => ['Akuntansi Keuangan', 'Komp. Akuntansi', 'Sejarah Indonesia', 'KIK', 'Perpajakan'],
                'taught_majors' => ['AKL'],
            ],
            [
                'name' => 'Munisah, S.Pd',
                'username' => 'munisah',
                'email' => 'munisah@smkpgriblora.sch.id',
                'subjects' => ['B. Jawa'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Wiwit Mergi W., A.Md',
                'username' => 'wiwit.mergi',
                'email' => 'wiwit.mergi@smkpgriblora.sch.id',
                'subjects' => ['Penyusunan Koleksi Busana', 'Persiapan Pembuatan Busana'],
                'taught_majors' => ['BUSANA'],
            ],
            [
                'name' => 'Meiranti Trisnaning S., S.Pd',
                'username' => 'meiranti.trisnaning',
                'email' => 'meiranti.trisnaning@smkpgriblora.sch.id',
                'subjects' => ['B. Indonesia'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Debby Furi Wijayanti, S.Pd',
                'username' => 'debby.furi',
                'email' => 'debby.furi@smkpgriblora.sch.id',
                'subjects' => ['Gambar Teknis (Technical Drawing)', 'KIK', 'Dasar Prog Keahlian Busana'],
                'taught_majors' => ['BUSANA', 'MPLB'],
            ],
            [
                'name' => 'Ari Yunitasari, S.Pd',
                'username' => 'ari.yunitasari',
                'email' => 'ari.yunitasari@smkpgriblora.sch.id',
                'subjects' => ['Dasar Prog Keahlian AKL', 'Perpajakan', 'Bisnis Retail', 'Sejarah Indonesia', 'EkoBis dan Adm Umum'],
                'taught_majors' => ['AKL', 'MPLB', 'BUSANA'],
            ],
            [
                'name' => 'Ervinda Sekar Asmara, S.Pd',
                'username' => 'ervinda.sekar',
                'email' => 'ervinda.sekar@smkpgriblora.sch.id',
                'subjects' => ['Bahasa Inggris'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Adela Wulan Kurniasari, S.Pd',
                'username' => 'adela.wulan',
                'email' => 'adela.wulan@smkpgriblora.sch.id',
                'subjects' => ['PJOK', 'Sejarah Indonesia'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Marista Bela Octaviana, S.Pd',
                'username' => 'marista.bela',
                'email' => 'marista.bela@smkpgriblora.sch.id',
                'subjects' => ['B. Indonesia', 'Sejarah Indonesia'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Guru BTQ',
                'username' => 'guru.btq',
                'email' => 'guru.btq@smkpgriblora.sch.id',
                'subjects' => ['KOKURIKULER BTQ'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
            [
                'name' => 'Eko Budhi Lestari, S.Pd.B',
                'username' => 'eko.budhi',
                'email' => 'eko.budhi@smkpgriblora.sch.id',
                'subjects' => ['PAIBP'],
                'taught_majors' => ['MPLB'],
            ],
            [
                'name' => 'Rinawati, S.Pd',
                'username' => 'rinawati',
                'email' => 'rinawati@smkpgriblora.sch.id',
                'subjects' => ['PAIBP'],
                'taught_majors' => ['MPLB'],
            ],
            [
                'name' => 'M. Huda Muttaqin, S.Pd.I',
                'username' => 'huda.muttaqin',
                'email' => 'huda.muttaqin@smkpgriblora.sch.id',
                'subjects' => ['PAIBP', 'KKA'],
                'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
            ],
        ];

        foreach ($teachers as $teacherData) {
            // Create user
            $user = User::create([
                'name' => $teacherData['name'],
                'username' => $teacherData['username'],
                'nip_nuptk' => $teacherData['nip_nuptk'] ?? null,
                'email' => $teacherData['email'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'beban_mengajar' => $teacherData['beban_mengajar'] ?? 24,
                'taught_majors' => $teacherData['taught_majors'],
                'is_active' => true,
            ]);

            // Attach subjects
            $subjectNames = $teacherData['subjects'];
            $subjects = Subject::whereIn('name', $subjectNames)->get();
            
            if ($subjects->count() > 0) {
                $user->subjects()->attach($subjects->pluck('id'));
                $this->command->info("✓ Guru created: {$user->name} - " . $subjects->count() . " subjects");
            } else {
                $this->command->warn("⚠️  Guru created but no subjects matched: {$user->name}");
            }
        }

        $this->command->info("\n✅ Teacher seeding completed!");
        $this->command->info("📊 Total teachers created: " . count($teachers));
        $this->command->info("🔑 Default password: password");
    }
}

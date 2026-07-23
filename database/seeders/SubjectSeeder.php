<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * READY TO BE FILLED WITH DATA FROM JADWAL GURU
     */
    public function run(): void
    {
        $subjects = [
            // Dari screenshot - urutan sesuai tampilan
            ['name' => 'PJOK', 'code' => 'PJOK', 'description' => 'Pendidikan Jasmani Olahraga dan Kesehatan'],
            ['name' => 'PKN', 'code' => 'PKN', 'description' => 'Pendidikan Pancasila dan Kewarganegaraan'],
            ['name' => 'INFORMATIKA', 'code' => 'INFORMATIKA', 'description' => 'Informatika'],
            ['name' => 'PAIBP', 'code' => 'PAIBP', 'description' => 'Pendidikan Agama Islam dan Budi Pekerti'],
            ['name' => 'Ke PGRI an', 'code' => 'Ke PGRI an', 'description' => 'Ke PGRI an'],
            ['name' => 'KIK', 'code' => 'Kreatifitas Inovasi Kewirausaan', 'description' => 'Kreatifitas Inovasi Kewirausaan'],
            ['name' => 'B. Jawa', 'code' => 'B. Jawa', 'description' => 'Bahasa Jawa'],
            ['name' => 'Komp. Akuntansi', 'code' => 'Komp. Akuntansi', 'description' => 'Komputer Akuntansi'],
            ['name' => 'Akuntansi Keuangan', 'code' => 'Akuntansi Keuangan', 'description' => 'Akuntansi Keuangan'],
            ['name' => 'Dasar Prog Keahlian AKL', 'code' => 'Dasar Prog Keahlian AKL', 'description' => 'Dasar Program Keahlian AKL'],
            ['name' => 'Dasar Prog Keahlian MPLB', 'code' => 'Dasar Prog Keahlian MPLB', 'description' => 'Dasar Program Keahlian MPLB'],
            ['name' => 'Dasar Prog Keahlian Busana', 'code' => 'Dasar Prog Keahlian Busana', 'description' => 'Dasar Program Keahlian Busana'],
            ['name' => 'EkoBis dan Adm Umum', 'code' => 'EkoBis dan Adm Umum', 'description' => 'Ekonomi Bisnis dan Administrasi Umum'],
            ['name' => 'Perpajakan', 'code' => 'Perpajakan', 'description' => 'Perpajakan'],
            ['name' => 'Bisnis Retail', 'code' => 'Bisnis Retail', 'description' => 'Bisnis Retail'],
            ['name' => 'B. Indonesia', 'code' => 'B. Indonesia', 'description' => 'Bahasa Indonesia'],
            ['name' => 'KKA', 'code' => 'Koding dan Kecerdasan Artifisial', 'description' => 'Koding dan Kecerdasan Artifisial'],
            ['name' => 'Gaya dan Pengembangan Desain', 'code' => 'Gaya dan Pengembangan Desain', 'description' => 'Gaya dan Pengembangan Desain'],
            ['name' => 'Menjahit Produk Busana', 'code' => 'Menjahit Produk Busana', 'description' => 'Menjahit Produk Busana'],
            ['name' => 'Sejarah Indonesia', 'code' => 'Sejarah Indonesia', 'description' => 'Sejarah Indonesia'],
            ['name' => 'Seni Budaya', 'code' => 'Seni Budaya', 'description' => 'Seni Budaya'],
            ['name' => 'Bahasa Inggris', 'code' => 'Bahasa Inggris', 'description' => 'Bahasa Inggris'],
            ['name' => 'Publik Speaking', 'code' => 'Publik Speaking', 'description' => 'Publik Speaking'],
            ['name' => 'Pengelolaan Rapat', 'code' => 'Pengelolaan Rapat', 'description' => 'Pengelolaan Rapat'],
            ['name' => 'Penglolaan Keuangan', 'code' => 'Penglolaan Keuangan', 'description' => 'Pengelolaan Keuangan'],
            ['name' => 'Teknogi Perkantoran', 'code' => 'Teknogi Perkantoran', 'description' => 'Teknologi Perkantoran'],
            ['name' => 'Ekonomi Bisnis', 'code' => 'Ekonomi Bisnis', 'description' => 'Ekonomi Bisnis'],
            ['name' => 'Pengelolaan Sarpras', 'code' => 'Pengelolaan Sarpras', 'description' => 'Pengelolaan Sarana Prasarana'],
            ['name' => 'Kearsipan', 'code' => 'Kearsipan', 'description' => 'Kearsipan'],
            ['name' => 'Adm Umum', 'code' => 'Adm Umum', 'description' => 'Administrasi Umum'],
            ['name' => 'AKPJDM', 'code' => 'AKPJDM', 'description' => 'Akuntansi Perusahaan Jasa, Dagang dan Manufaktur'],
            ['name' => 'Akuntansi Lembaga', 'code' => 'Akuntansi Lembaga', 'description' => 'Akuntansi Lembaga'],
            ['name' => 'PIPAS', 'code' => 'PIPAS', 'description' => 'Pendidikan Agama Islam dan Budi Pekerti Sejarah'],
            ['name' => 'Koleksi Busana', 'code' => 'Koleksi Busana', 'description' => 'Koleksi Busana'],
            ['name' => 'Persiapan Pembuatan Busana', 'code' => 'Persiapan Pembuatan Busana', 'description' => 'Persiapan Pembuatan Busana'],
            ['name' => 'Membatik', 'code' => 'Membatik', 'description' => 'Membatik'],
            ['name' => 'Eksperimen Bahan Tekstil dan Desain Hiasan', 'code' => 'Eksperimen Bahan Tekstil dan Desain Hiasan', 'description' => 'Eksperimen Bahan Tekstil dan Desain Hiasan'],
            ['name' => 'Bimbingan Konseling', 'code' => 'Bimbingan Konseling', 'description' => 'Bimbingan Konseling'],
            ['name' => 'Matematika', 'code' => 'Matematika', 'description' => 'Matematika'],
            ['name' => 'Pengelolaan SDM', 'code' => 'Pengelolaan SDM', 'description' => 'Pengelolaan SDM'],
            ['name' => 'Komunikasi di tempat kerja', 'code' => 'Komunikasi di tempat kerja', 'description' => 'Komunikasi di tempat kerja'],
            ['name' => 'Pengelolaan Humas dan Keprotokolan', 'code' => 'Pengelolaan Humas dan Keprotokolan', 'description' => 'Pengelolaan Humas dan Keprotokolan'],
            ['name' => 'Mapel PKL', 'code' => 'Mapel PKL', 'description' => 'Mapel PKL'],
            ['name' => 'Perpajakan', 'code' => 'Perpajakan', 'description' => 'Perpajakan'],
            ['name' => 'Penyusunan Koleksi Busana', 'code' => 'Penyusunan Koleksi Busana', 'description' => 'Penyusunan Koleksi Busana'],
            ['name' => 'Gambar Teknis (Technical Drawing)', 'code' => 'Gambar Teknis (Technical Drawing)', 'description' => 'Gambar Teknis (Technical Drawing)'],
            ['name' => 'KOKURIKULER BTQ', 'code' => 'KOKURIKULER BTQ', 'description' => 'Kokurikuler BTQ'],
            ['name' => 'KOSONG', 'code' => 'PULANG', 'description' => 'PULANG'],
            ['name' => 'PAKBP', 'code' => 'PAKBP', 'description' => 'Pendidikan Agama Kristen dan Budi Pekerti'],
            ['name' => 'PABBP', 'code' => 'PABBP', 'description' => 'Pendidikan Agama Budha dan Budi Pekerti'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
            $this->command->info("✓ Subject created: {$subject['name']}");
        }

        $this->command->info("\n✅ Subject seeding completed!");
        $this->command->info("📊 Total subjects created: " . count($subjects));
    }
}

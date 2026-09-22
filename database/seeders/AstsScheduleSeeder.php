<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AstsSchedule;

class AstsScheduleSeeder extends Seeder
{
    /**
     * Nama guru   = PERSIS sama dengan kolom `name` di tabel users.
     * Nama mapel  = PERSIS sama dengan kolom `name` di tabel subjects.
     * (Disesuaikan dari output tinker server 2026-09-22)
     */
    public function run(): void
    {
        AstsSchedule::truncate();

        $jadwal = [
            // ═══════════════════════════════════════════════════════
            // X AKL
            // ═══════════════════════════════════════════════════════
            ['X','AKL','Senin',  1,'Bahasa Indonesia',                          'Marista Bela Octaviana, S.Pd'],
            ['X','AKL','Senin',  2,'Dasar Program Keahlian AKL',                'Liliyana Ayu W., S.Pd'],
            ['X','AKL','Senin',  3,'Ke PGRI an',                                'Ilham Hardiyan P., S.Pd'],
            ['X','AKL','Selasa', 1,'Matematika',                                'Dewi Wartini, S.Pd'],
            ['X','AKL','Selasa', 2,'PJOK X',                                    'Adela Wulan Kurniasari, S.Pd'],
            ['X','AKL','Selasa', 3,'Bahasa Jawa',                               'Munisah, S.Pd'],
            ['X','AKL','Rabu',   1,'Koding dan Kecerdasan Artifisial',           'Muhammad Huda Muttaqin'],
            ['X','AKL','Rabu',   2,'Bahasa Inggris',                            'Ervinda Sekar Asmara, S.Pd'],
            ['X','AKL','Rabu',   3,'Sejarah Indonesia',                         'Tri Mulyaniningsih, S.E'],
            ['X','AKL','Kamis',  1,'Pendidikan Agama Islam dan Budi Pekerti',   'Budi Siswanto, S.Pd.I'],
            ['X','AKL','Kamis',  2,'INFORMATIKA',                               'Dhani Kisworo Jati, S.Pd'],
            ['X','AKL','Kamis',  3,'Pendidikan Pancasila dan Kewarganegaraan',  'Drs. Suseno'],
            ['X','AKL','Jumat',  1,'Seni Budaya',                               'Pancawati Puji L., A.Md'],
            ['X','AKL','Jumat',  2,'Dasar Program Keahlian AKL',                'Ari Yunitasari, S.Pd'],
            ['X','AKL','Jumat',  3,'Proyek Ilmu Pengetahuan Alam dan Sosial',   'Dhani Kisworo Jati, S.Pd'],

            // ═══════════════════════════════════════════════════════
            // X BUSANA
            // ═══════════════════════════════════════════════════════
            ['X','BUSANA','Senin',  1,'Ke PGRI an',                               'Ilham Hardiyan P., S.Pd'],
            ['X','BUSANA','Senin',  2,'Bahasa Indonesia',                         'Marista Bela Octaviana, S.Pd'],
            ['X','BUSANA','Selasa', 1,'PJOK X',                                   'Adela Wulan Kurniasari, S.Pd'],
            ['X','BUSANA','Selasa', 2,'Bahasa Jawa',                              'Munisah, S.Pd'],
            ['X','BUSANA','Selasa', 3,'Matematika',                               'Dewi Wartini, S.Pd'],
            ['X','BUSANA','Rabu',   1,'Bahasa Inggris',                           'Ervinda Sekar Asmara, S.Pd'],
            ['X','BUSANA','Rabu',   2,'Dasar Program Keahlian Busana',            'Debby Furi Wijayanti, S.Pd'],
            ['X','BUSANA','Rabu',   3,'Koding dan Kecerdasan Artifisial',         'Muhammad Huda Muttaqin'],
            ['X','BUSANA','Kamis',  1,'INFORMATIKA',                              'Dhani Kisworo Jati, S.Pd'],
            ['X','BUSANA','Kamis',  2,'Pendidikan Pancasila dan Kewarganegaraan', 'Drs. Suseno'],
            ['X','BUSANA','Kamis',  3,'Pendidikan Agama Islam dan Budi Pekerti',  'Budi Siswanto, S.Pd.I'],
            ['X','BUSANA','Jumat',  1,'Sejarah Indonesia',                        'Marista Bela Octaviana, S.Pd'],
            ['X','BUSANA','Jumat',  2,'Seni Budaya',                              'Pancawati Puji L., A.Md'],
            ['X','BUSANA','Jumat',  3,'Proyek Ilmu Pengetahuan Alam dan Sosial',  'Dewi Wartini, S.Pd'],

            // ═══════════════════════════════════════════════════════
            // X MPLB
            // ═══════════════════════════════════════════════════════
            ['X','MPLB','Senin',  1,'Dasar Program Keahlian MPLB',               'Ade Rua Nur Lemoniar, S.Pd'],
            ['X','MPLB','Senin',  2,'Ke PGRI an',                                'Ilham Hardiyan P., S.Pd'],
            ['X','MPLB','Senin',  3,'Bahasa Indonesia',                          'Marista Bela Octaviana, S.Pd'],
            ['X','MPLB','Selasa', 1,'Bahasa Jawa',                               'Munisah, S.Pd'],
            ['X','MPLB','Selasa', 2,'Matematika',                                'Dewi Wartini, S.Pd'],
            ['X','MPLB','Selasa', 3,'PJOK X',                                    'Adela Wulan Kurniasari, S.Pd'],
            ['X','MPLB','Rabu',   1,'Sejarah Indonesia',                         'Ari Yunitasari, S.Pd'],
            ['X','MPLB','Rabu',   2,'Koding dan Kecerdasan Artifisial',          'Muhammad Huda Muttaqin'],
            ['X','MPLB','Rabu',   3,'Bahasa Inggris',                            'Ervinda Sekar Asmara, S.Pd'],
            ['X','MPLB','Kamis',  1,'Pendidikan Pancasila dan Kewarganegaraan',  'Drs. Suseno'],
            ['X','MPLB','Kamis',  2,'Pendidikan Agama Islam dan Budi Pekerti',   'Budi Siswanto, S.Pd.I'],
            ['X','MPLB','Kamis',  3,'INFORMATIKA',                               'Dhani Kisworo Jati, S.Pd'],
            ['X','MPLB','Jumat',  1,'Proyek Ilmu Pengetahuan Alam dan Sosial',   'Liliyana Ayu W., S.Pd'],
            ['X','MPLB','Jumat',  2,'Dasar Program Keahlian MPLB',               'Nia Dani Rahayu, S.Pd'],
            ['X','MPLB','Jumat',  3,'Seni Budaya',                               'Pancawati Puji L., A.Md'],

            // ═══════════════════════════════════════════════════════
            // XI AKL
            // ═══════════════════════════════════════════════════════
            ['XI','AKL','Senin',  1,'Perpajakan',                                        'Tri Mulyaniningsih, S.E'],
            ['XI','AKL','Senin',  2,'Bahasa Jawa',                                       'Munisah, S.Pd'],
            ['XI','AKL','Senin',  3,'Bisnis Retail',                                     'Ari Yunitasari, S.Pd'],
            ['XI','AKL','Senin',  4,'Kreatifitas Inovasi Kewirausaan',                   'Pancawati Puji L., A.Md'],
            ['XI','AKL','Selasa', 1,'Pendidikan Agama Islam dan Budi Pekerti',           'Budi Siswanto, S.Pd.I'],
            ['XI','AKL','Selasa', 2,'Pendidikan Pancasila dan Kewarganegaraan',          'Drs. Suseno'],
            ['XI','AKL','Selasa', 3,'Akuntansi Lembaga',                                 'Liliyana Ayu W., S.Pd'],
            ['XI','AKL','Selasa', 4,'Komputer Akuntansi',                                'Tri Mulyaniningsih, S.E'],
            ['XI','AKL','Rabu',   1,'PJOK XI',                                           'Adela Wulan Kurniasari, S.Pd'],
            ['XI','AKL','Rabu',   2,'Membatik',                                          'Pancawati Puji L., A.Md'],
            ['XI','AKL','Rabu',   3,'Ekonomi Bisnis dan Adminitrasi Umum',               'Ari Yunitasari, S.Pd'],
            ['XI','AKL','Rabu',   4,'Matematika',                                        'Dhani Kisworo Jati, S.Pd'],
            ['XI','AKL','Kamis',  1,'Publik Speaking',                                   'Nia Dani Rahayu, S.Pd'],
            ['XI','AKL','Kamis',  2,'Akuntansi Keuangan',                                'Tri Mulyaniningsih, S.E'],
            ['XI','AKL','Kamis',  3,'Bahasa Indonesia',                                  'Marista Bela Octaviana, S.Pd'],
            ['XI','AKL','Jumat',  1,'Sejarah Indonesia',                                 'Adela Wulan Kurniasari, S.Pd'],
            ['XI','AKL','Jumat',  2,'Bahasa Inggris',                                    'Ervinda Sekar Asmara, S.Pd'],
            ['XI','AKL','Jumat',  3,'Akuntansi Perusahaan Jasa, Dagang dan Manufaktur',  'Liliyana Ayu W., S.Pd'],

            // ═══════════════════════════════════════════════════════
            // XI BUSANA
            // ═══════════════════════════════════════════════════════
            ['XI','BUSANA','Senin',  1,'Bisnis Retail',                                  'Ari Yunitasari, S.Pd'],
            ['XI','BUSANA','Senin',  2,'Kreatifitas Inovasi Kewirausaan',                'Pancawati Puji L., A.Md'],
            ['XI','BUSANA','Senin',  3,'Bahasa Jawa',                                    'Munisah, S.Pd'],
            ['XI','BUSANA','Selasa', 1,'Pendidikan Pancasila dan Kewarganegaraan',       'Drs. Suseno'],
            ['XI','BUSANA','Selasa', 2,'Persiapan Pembuatan Busana',                     'Wiwit Mergi W., A.Md'],
            ['XI','BUSANA','Selasa', 3,'Pendidikan Agama Islam dan Budi Pekerti',        'Budi Siswanto, S.Pd.I'],
            ['XI','BUSANA','Rabu',   1,'Membatik',                                       'Pancawati Puji L., A.Md'],
            ['XI','BUSANA','Rabu',   2,'Matematika',                                     'Dhani Kisworo Jati, S.Pd'],
            ['XI','BUSANA','Rabu',   3,'PJOK XI',                                        'Adela Wulan Kurniasari, S.Pd'],
            ['XI','BUSANA','Kamis',  1,'Gambar Teknis (Technical Drawing)',              'Debby Furi Wijayanti, S.Pd'],
            ['XI','BUSANA','Kamis',  2,'Bahasa Indonesia',                               'Marista Bela Octaviana, S.Pd'],
            ['XI','BUSANA','Kamis',  3,'Publik Speaking',                                'Nia Dani Rahayu, S.Pd'],
            ['XI','BUSANA','Jumat',  1,'Bahasa Inggris',                                 'Ervinda Sekar Asmara, S.Pd'],
            ['XI','BUSANA','Jumat',  2,'Menjahit Produk Busana',                         'Yully Setyo A., S.Pd'],
            ['XI','BUSANA','Jumat',  3,'Sejarah Indonesia',                              'Adela Wulan Kurniasari, S.Pd'],

            // ═══════════════════════════════════════════════════════
            // XI MPLB
            // ═══════════════════════════════════════════════════════
            ['XI','MPLB','Senin',  1,'Bahasa Jawa',                                     'Munisah, S.Pd'],
            ['XI','MPLB','Senin',  2,'Bisnis Retail',                                   'Ari Yunitasari, S.Pd'],
            ['XI','MPLB','Senin',  3,'Kreatifitas Inovasi Kewirausaan',                 'Pancawati Puji L., A.Md'],
            ['XI','MPLB','Selasa', 1,'Ekonomi Bisnis',                                  'Ade Rua Nur Lemoniar, S.Pd'],
            ['XI','MPLB','Selasa', 2,'Pendidikan Agama Islam dan Budi Pekerti',         'Budi Siswanto, S.Pd.I'],
            ['XI','MPLB','Selasa', 3,'Administrasi Umum',                               'Ade Rua Nur Lemoniar, S.Pd'],
            ['XI','MPLB','Selasa', 4,'Pendidikan Pancasila dan Kewarganegaraan',        'Drs. Suseno'],
            ['XI','MPLB','Rabu',   1,'Matematika',                                      'Dhani Kisworo Jati, S.Pd'],
            ['XI','MPLB','Rabu',   2,'PJOK XI',                                         'Adela Wulan Kurniasari, S.Pd'],
            ['XI','MPLB','Rabu',   3,'Membatik',                                        'Pancawati Puji L., A.Md'],
            ['XI','MPLB','Kamis',  1,'Bahasa Indonesia',                                'Marista Bela Octaviana, S.Pd'],
            ['XI','MPLB','Kamis',  2,'Publik Speaking',                                 'Nia Dani Rahayu, S.Pd'],
            ['XI','MPLB','Kamis',  3,'Kearsipan',                                       'Ade Rua Nur Lemoniar, S.Pd'],
            ['XI','MPLB','Jumat',  1,'Teknogi Perkantoran',                             'Nia Dani Rahayu, S.Pd'],
            ['XI','MPLB','Jumat',  2,'Sejarah Indonesia',                               'Adela Wulan Kurniasari, S.Pd'],
            ['XI','MPLB','Jumat',  3,'Bahasa Inggris',                                  'Ervinda Sekar Asmara, S.Pd'],
        ];

        foreach ($jadwal as [$kelas, $jurusan, $hari, $sesi, $mapel, $guru]) {
            AstsSchedule::create([
                'kelas'     => $kelas,
                'jurusan'   => $jurusan,
                'hari'      => $hari,
                'sesi'      => $sesi,
                'mapel'     => $mapel,
                'nama_guru' => $guru,
            ]);
        }

        $this->command->info('AstsScheduleSeeder: ' . count($jadwal) . ' baris berhasil disimpan.');
    }
}
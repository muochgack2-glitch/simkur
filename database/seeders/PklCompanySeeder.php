<?php

namespace Database\Seeders;

use App\Models\PklCompany;
use Illuminate\Database\Seeder;

class PklCompanySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // MPLB
            ['Badan Kesatuan Bangsa Dan Politik Blora','Jalan KH. Ahmad Dahlan No. 27 Blora','MPLB'],
            ['Dealer Astra Motor Blora','Jl. Mr. Iskandar No.47 Blora','MPLB'],
            ['Dinas Pemberdayaan Masyarakat Dan Desa','Jl. Gor No.8, Ketanggar, Karangjati, Kec. Blora','MPLB'],
            ['Dinas Perdagangan, Koperasi, Dan UMKM','Jl. Rembang Blora No.KM. 4, Blora','MPLB'],
            ['Dinas Perpustakaan Dan Kearsipan Blora','Jalan A. Yani Taman M. Sarbini Blora','MPLB'],
            ['Kantor BPJS Ketenagakerjaan Blora','Jl. Jendral Sudirman No.101, Jenar, Kedungjenar, Kec. Blora','MPLB'],
            ['Pengadilan Negeri Blora','Jalan Raya Cepu-Blora Km. 5 Blora','MPLB'],
            ['Radio XFM Blora','Jalan Dr. Sutomo Nomor 22 Blora','MPLB'],
            ['Sekretariat Daerah Blora','Jl. Pemuda No. 12, Blora','MPLB'],
            // BUSANA
            ['Anadom Taylor','Jl. Tuntang Barat Rt. 1 Rw. 4','BUSANA'],
            ['Sony Kebaya','Jl. Musi No.25, Jenar, Kedungjenar, Kec. Blora','BUSANA'],
            ['EMYFA','Jl. A.Yani Gang 11 B, Karangjati, Blora','BUSANA'],
            // AKL
            ['BPPKAD','Jl. Gatot Subroto No. 111','AKL'],
            ['BPR Dhana Mitratama','Jl. Nusantara No.28, Jetis, Kauman, Kec. Blora, Kabupaten Blora','AKL'],
        ];

        foreach ($data as [$name, $address, $dept]) {
            PklCompany::updateOrCreate(
                ['name' => $name],
                [
                    'address'              => $address,
                    'capacity'             => 3,
                    'suitable_departments' => [$dept],
                    'status'               => 'active',
                ]
            );
        }

        $this->command->info('DU/DI seeded: ' . count($data) . ' (MPLB:9, BUSANA:3, AKL:2)');
    }
}
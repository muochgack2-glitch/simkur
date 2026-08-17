<?php
namespace Database\Seeders;
use App\Models\AcademicYear; use App\Models\PklCompany; use App\Models\PklPlacement;
use Illuminate\Database\Seeder;
class PklPlacementMplbSeeder extends Seeder {
    public function run(): void {
        $ay = AcademicYear::active()->first();
        if (!$ay) { $this->command->error('Tidak ada tahun ajaran aktif!'); return; }
        // [student_id, company_name_keyword, teacher_id, teacher_name]
        $data = [
            // Badan Kesatuan Bangsa - Dewi Wartini (18)
            [150, 'badan kesatuan', 18],
            [163, 'badan kesatuan', 18],
            // Dealer Astra Motor - Ari Yunitasari (27)
            [172, 'astra motor', 27],
            [156, 'astra motor', 27],
            // Dinas Pemberdayaan - Ade Rua (19)
            [151, 'pemberdayaan', 19],
            [152, 'pemberdayaan', 19],
            [168, 'pemberdayaan', 19],
            // Dinas Perdagangan - Munisah (23)
            [158, 'perdagangan', 23],
            [170, 'perdagangan', 23],
            // Dinas Perpustakaan - Huda (175)
            [173, 'perpustakaan', 175],
            [153, 'perpustakaan', 175],
            [161, 'perpustakaan', 175],
            // BPJS Kesehatan - Budi Siswanto (13)
            [155, 'bpjs kesehatan', 13],
            [160, 'bpjs kesehatan', 13],
            // BPJS Ketenagakerjaan - Drs. Suseno (12)
            [159, 'ketenagakerjaan', 12],
            [166, 'ketenagakerjaan', 12],
            // Pengadilan Negeri - Ervinda (28)
            [154, 'pengadilan', 28],
            [157, 'pengadilan', 28],
            [162, 'pengadilan', 28],
            // Radio XFM - Ilham (15)
            [165, 'xfm', 15],
            [167, 'xfm', 15],
            // Sekretariat Daerah - Nia Dani (17)
            [164, 'sekretariat', 17],
            [169, 'sekretariat', 17],
            [171, 'sekretariat', 17],
        ];
        $ok = 0; $skip = 0;
        foreach ($data as [$sid, $cKey, $tid]) {
            $company = PklCompany::whereRaw('LOWER(name) LIKE ?', ['%' . $cKey . '%'])->first();
            if (!$company) { $this->command->warn('DU/DI not found: ' . $cKey); $skip++; continue; }
            PklPlacement::updateOrCreate(
                ['academic_year_id' => $ay->id, 'student_id' => $sid],
                ['pkl_company_id' => $company->id, 'status' => 'active',
                 'notes' => 'Pembimbing ID: ' . $tid]
            );
            $ok++;
        }
        $this->command->info("Placement MPLB: $ok berhasil, $skip dilewati.");
    }
}
<?php
namespace Database\Seeders;
use App\Models\AcademicYear; use App\Models\PklCompany; use App\Models\PklCompanySupervisor;
use Illuminate\Database\Seeder;
class PklSupervisorSeeder extends Seeder {
    public function run(): void {
        $ay = AcademicYear::active()->first();
        if (!$ay) { $this->command->error('Tidak ada tahun ajaran aktif!'); return; }
        // [company_keyword, teacher_id, teacher_name (komentar)]
        $data = [
            // MPLB
            ['badan kesatuan',   18,  'Dewi Wartini, S.Pd'],
            ['astra motor',       27,  'Ari Yunitasari, S.Pd'],
            ['pemberdayaan',      19,  'Ade Rua Nur Lemoniar, S.Pd'],
            ['perdagangan',       23,  'Munisah, S.Pd'],
            ['perpustakaan',      175, 'Muhammad Huda Muttaqin, S.Pd.I'],
            ['bpjs kesehatan',    13,  'Budi Siswanto, S.Pd.I'],
            ['ketenagakerjaan',   12,  'Drs. Suseno'],
            ['pengadilan',        28,  'Ervinda Sekar Asmara, S.Pd'],
            ['xfm',               15,  'Ilham Hardiyan P., S.Pd'],
            ['sekretariat',       17,  'Nia Dani Rahayu, S.Pd'],
            // BUSANA
            ['anadom',            14,  'Yully Setyo A., S.Pd'],
            ['sony',              24,  'Wiwit Mergi W., A.Md'],
            ['emyfa',             26,  'Debby Furi Wijayanti, S.Pd'],
            // AKL
            ['bppkad',            20,  'Liliyana Ayu W., S.Pd'],
            ['bpr dhana',         22,  'Tri Mulyaniningsih, S.E'],
        ];
        $ok = 0; $skip = 0;
        foreach ($data as [$cKey, $tid, $tName]) {
            $company = PklCompany::whereRaw('LOWER(name) LIKE ?', ['%' . $cKey . '%'])->first();
            if (!$company) { $this->command->warn('DU/DI not found: ' . $cKey); $skip++; continue; }
            PklCompanySupervisor::updateOrCreate(
                ['academic_year_id' => $ay->id, 'pkl_company_id' => $company->id, 'teacher_id' => $tid],
                []
            );
            $this->command->line(" - " . $company->name . " → " . $tName);
            $ok++;
        }
        $this->command->info("Supervisor assigned: $ok, skip: $skip");
    }
}
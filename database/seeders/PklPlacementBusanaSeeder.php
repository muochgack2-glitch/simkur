<?php
namespace Database\Seeders;
use App\Models\AcademicYear; use App\Models\PklCompany; use App\Models\PklPlacement;
use Illuminate\Database\Seeder;
class PklPlacementBusanaSeeder extends Seeder {
    public function run(): void {
        $ay = AcademicYear::active()->first();
        if (!$ay) { $this->command->error('Tidak ada tahun ajaran aktif!'); return; }
        // [student_id, company_keyword, teacher_id]
        $data = [
            // Anadom Taylor - Yully Setyo (14)
            [149, 'anadom', 14],
            [148, 'anadom', 14],
            // Sony Kebaya - Wiwit (24)
            [147, 'sony', 24],
            [143, 'sony', 24],
            [144, 'sony', 24],
            // EMYFA - Debby (26)
            [146, 'emyfa', 26],
            [145, 'emyfa', 26],
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
        $this->command->info("Placement BUSANA: $ok berhasil, $skip dilewati.");
    }
}
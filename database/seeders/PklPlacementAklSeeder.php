<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\PklCompany;
use App\Models\PklPlacement;
use App\Models\User;
use Illuminate\Database\Seeder;

class PklPlacementAklSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::active()->first();
        if (!$academicYear) {
            $this->command->error('Tidak ada tahun ajaran aktif!');
            return;
        }

        $placements = [
            // BPPKAD - Pembimbing: Liliyana Ayu W S.Pd
            ['student' => 'ANGGUN TRI LESTARI',  'company' => 'BPPKAD', 'supervisor' => 'Liliyana Ayu W'],
            ['student' => 'DIANA MUTIARA',        'company' => 'BPPKAD', 'supervisor' => 'Liliyana Ayu W'],
            ['student' => 'EKA ANGGRAINI',        'company' => 'BPPKAD', 'supervisor' => 'Liliyana Ayu W'],
            // BPR Dhana Mitratama - Pembimbing: Tri Mulyaniningsih, SE
            ['student' => 'MUHAMAD ADI WALUYO',  'company' => 'BPR Dhana Mitratama', 'supervisor' => 'Tri Mulyaniningsih'],
            ['student' => 'YAHYA FEBRIANI',       'company' => 'BPR Dhana Mitratama', 'supervisor' => 'Tri Mulyaniningsih'],
        ];

        $ok = 0; $skip = 0;
        foreach ($placements as $p) {
            $student = User::where('role', 'student')
                ->where('major', 'AKL')
                ->whereRaw('UPPER(name) = ?', [strtoupper($p['student'])])
                ->first();
            if (!$student) {
                $this->command->warn('Siswa tidak ditemukan: ' . $p['student']);
                $skip++; continue;
            }

            $company = PklCompany::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($p['company']) . '%'])->first();
            if (!$company) {
                $this->command->warn('DU/DI tidak ditemukan: ' . $p['company']);
                $skip++; continue;
            }

            $supervisor = User::where('role', 'teacher')
                ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($p['supervisor']) . '%'])
                ->first();

            PklPlacement::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'student_id'       => $student->id,
                ],
                [
                    'pkl_company_id' => $company->id,
                    'status'         => 'active',
                    'notes'          => $supervisor ? 'Pembimbing: ' . $supervisor->name : null,
                ]
            );
            $ok++;
        }

        $this->command->info("Placement AKL: $ok berhasil, $skip dilewati.");
    }
}
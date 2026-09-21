<?php

namespace App\Imports;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class NilaiImport implements ToCollection, WithStartRow
{
    /** Baris 1=judul, 2=info kelas, 3=header kolom, mulai data dari baris 4 */
    public function startRow(): int { return 4; }

    public array $imported = [];
    public array $skipped  = [];

    public function __construct(
        private Assessment $assessment,
        private int $classId
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            // Kolom: A=NO, B=NIS, C=NAMA, D=NILAI
            $nis   = trim((string) ($row[1] ?? ''));
            $nilai = $row[3] ?? null;

            if ($nis === '' || $nis === '-' || $nilai === '' || $nilai === null) {
                continue;
            }

            $nilai = (int) round((float) $nilai);
            if ($nilai < 0 || $nilai > 100) {
                $this->skipped[] = "NIS $nis — nilai $nilai di luar 0-100";
                continue;
            }

            $student = User::where('nis', $nis)
                ->where('class_id', $this->classId)
                ->first();

            if (!$student) {
                $this->skipped[] = "NIS $nis tidak ditemukan di kelas ini";
                continue;
            }

            $session = AssessmentStudentSession::where('assessment_id', $this->assessment->id)
                ->where('user_id', $student->id)
                ->first();

            if ($session) {
                $session->total_score        = $nilai;
                $session->max_possible_score = 100;
                $session->submitted_at       = $session->submitted_at ?? now();
                $session->started_at         = $session->started_at   ?? now();
                $session->save();
            } else {
                AssessmentStudentSession::create([
                    'assessment_id'      => $this->assessment->id,
                    'user_id'            => $student->id,
                    'attempt_number'     => 1,
                    'auto_score'         => 0,
                    'manual_score'       => 0,
                    'total_score'        => $nilai,
                    'max_possible_score' => 100,
                    'started_at'         => now(),
                    'submitted_at'       => now(),
                    'answers_data'       => ['source' => 'import_excel'],
                ]);
            }

            $this->imported[] = $student->name;
        }
    }
}
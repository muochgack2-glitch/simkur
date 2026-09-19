<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\TeachingSchedule;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class InputNilaiDirect extends Component
{
    public Assessment $assessment;
    public array $scores = [];
    public bool $saved = false;

    public function mount(int $id): void
    {
        $this->assessment = Assessment::with(['subject', 'assessmentLabel'])->findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru' && $this->assessment->teacher_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses asesmen ini.');
        }
        $this->loadExistingScores();
    }

    private function loadExistingScores(): void
    {
        AssessmentStudentSession::where('assessment_id', $this->assessment->id)
            ->whereNotNull('submitted_at')
            ->get(['user_id', 'total_score', 'max_possible_score'])
            ->each(function ($s) {
                if ($s->max_possible_score && $s->max_possible_score > 0) {
                    $pct = round($s->total_score / $s->max_possible_score * 100);
                    if ($pct <= 100) {
                        // Normal: persentase valid
                        $raw = $pct;
                    } elseif ($s->total_score <= 100) {
                        // max_possible tidak konsisten, tapi total_score sudah dalam range 0-100
                        $raw = (int) round((float) $s->total_score);
                    } else {
                        // Data tidak bisa dinormalisasi → kosongkan, biar guru isi ulang
                        $raw = null;
                    }
                } else {
                    $raw = ($s->total_score <= 100)
                        ? (int) round((float) $s->total_score)
                        : null;
                }
                if ($raw !== null) {
                    $this->scores[$s->user_id] = max(0, min(100, $raw));
                }
            });
    }

    #[Computed]
    public function students()
    {
        $user = auth()->user();
        $q = User::where('role', 'siswa')->with('schoolClass')->orderBy('name');
        if (!empty($this->assessment->target_grades)) {
            $q->whereIn('grade', $this->assessment->target_grades);
        }
        if (!empty($this->assessment->target_majors)) {
            $q->whereIn('major', $this->assessment->target_majors);
        }
        if ($user->role === 'guru' && $this->assessment->subject_id) {
            $classIds = TeachingSchedule::where('teacher_id', $user->id)
                ->where('subject_id', $this->assessment->subject_id)
                ->where('is_active', true)->distinct()->pluck('class_id');
            if ($classIds->isNotEmpty()) {
                $q->whereIn('class_id', $classIds);
            }
        }
        return $q->get();
    }

    public function save(): void
    {
        foreach ($this->scores as $studentId => $nilai) {
            if ($nilai === '' || $nilai === null) continue;
            $nilai = max(0, min(100, (int) $nilai));
            $session = AssessmentStudentSession::where('assessment_id', $this->assessment->id)
                ->where('user_id', $studentId)->first();
            if ($session) {
                $session->total_score        = $nilai;
                $session->max_possible_score = 100;
                $session->submitted_at       = $session->submitted_at ?? now();
                $session->started_at         = $session->started_at   ?? now();
                $session->save();
            } else {
                AssessmentStudentSession::create([
                    'assessment_id'      => $this->assessment->id,
                    'user_id'            => $studentId,
                    'attempt_number'     => 1,
                    'auto_score'         => 0,
                    'manual_score'       => 0,
                    'total_score'        => $nilai,
                    'max_possible_score' => 100,
                    'started_at'         => now(),
                    'submitted_at'       => now(),
                    'answers_data'       => ['source' => 'direct_input'],
                ]);
            }
        }
        $this->saved = true;
        session()->flash('success', 'Nilai berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.teacher-assessment.input-nilai-direct')
            ->layout('components.layouts.app', ['title' => 'Input Nilai ASTS']);
    }
}

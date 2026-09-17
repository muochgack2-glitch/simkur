<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\User;
use Livewire\Component;

class Results extends Component
{
    public Assessment $assessment;
    public string $filterClass = '';
    public string $filterStatus = '';

    public function mount(int $id): void
    {
        $this->assessment = Assessment::with(['questions'])
            ->where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();
    }

    public function render()
    {
        // Ambil semua session (attempt terbaru per siswa)
        $sessions = AssessmentStudentSession::with(['student'])
            ->where('assessment_id', $this->assessment->id)
            ->when($this->filterStatus === 'submitted', fn($q) => $q->whereNotNull('submitted_at'))
            ->when($this->filterStatus === 'in_progress', fn($q) => $q->whereNull('submitted_at')->whereNotNull('started_at'))
            ->orderByDesc('attempt_number')
            ->get()
            // Ambil attempt terbaru per siswa
            ->groupBy('user_id')
            ->map(fn($group) => $group->sortByDesc('attempt_number')->first());

        // Statistik
        $totalSubmitted   = $sessions->whereNotNull('submitted_at')->count();
        $needsGrading     = $sessions->filter(fn($s) => $s->isSubmitted() && $s->manual_score === null
            && $this->assessment->questions()->whereIn('question_type', ['essay','file_upload'])->exists())->count();
        $avgScore         = $sessions->whereNotNull('submitted_at')
            ->avg(fn($s) => $s->getScorePercentage());

        return view('livewire.teacher-assessment.results', [
            'sessions'       => $sessions->values(),
            'totalSubmitted' => $totalSubmitted,
            'needsGrading'   => $needsGrading,
            'avgScore'       => round($avgScore ?? 0, 1),
            'totalQuestions' => $this->assessment->questions()->count(),
        ])->layout('components.layouts.app');
    }
}

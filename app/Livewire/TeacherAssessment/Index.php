<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Index extends Component
{
    public string $tab = 'all';
    public string $search = '';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->search = '';
    }

    public function deleteAssessment(int $id): void
    {
        $assessment = Assessment::where('id', $id)
            ->when(!in_array(auth()->user()->role, ['admin', 'waka_kurikulum']), fn($q) => $q->where('teacher_id', auth()->id()))
            ->firstOrFail();

        // Hapus semua data relasi terlebih dahulu (cascade manual)
        $assessment->studentSessions()->delete();
        $assessment->responses()->delete();
        $assessment->studentProfiles()->delete();
        $assessment->questions()->each(function ($question) {
            $question->options()->delete();
            $question->responses()->delete();
            $question->delete();
        });

        $assessment->delete();
        session()->flash('success', 'Asesmen dan semua data pengerjaan siswa berhasil dihapus.');
    }

    #[Computed]
    public function assessments()
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        $query = Assessment::with(['creator', 'subject', 'assessmentLabel', 'teacher'])
            ->where('assessment_type', 'quiz')
            ->when(!$isAdmin, fn($q) => $q->where('teacher_id', $user->id))
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"));

        $now = now();

        $query = match($this->tab) {
            'upcoming' => $query->where(function($q) use ($now) {
                $q->where('start_date', '>', $now->toDateString())
                  ->orWhere(function($q2) use ($now) {
                      $q2->where('start_date', $now->toDateString())
                         ->where('start_time', '>', $now->toTimeString());
                  });
            }),
            'ongoing' => $query->where('start_date', '<=', $now->toDateString())
                               ->where('end_date', '>=', $now->toDateString()),
            'closed'  => $query->where('end_date', '<', $now->toDateString()),
            'draft'   => $query->where('is_published', false),
            default   => $query,
        };

        return $query->orderBy('start_date', 'desc')->orderBy('start_time', 'desc')->get()
            ->map(function($a) {
                $a->sessions_count   = $a->studentSessions()->count();
                $a->submitted_count  = $a->studentSessions()->whereNotNull('submitted_at')->count();
                $a->questions_count  = $a->questions()->count();
                return $a;
            });
    }

    public function render()
    {
        return view('livewire.teacher-assessment.index', [
            'assessments' => $this->assessments,
        ])->layout('components.layouts.app');
    }
}
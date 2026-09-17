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
            ->where('created_by', auth()->id())
            ->firstOrFail();

        // Hanya boleh hapus jika belum ada yang mengerjakan
        if ($assessment->studentSessions()->where('submitted_at', '!=', null)->exists()) {
            session()->flash('error', 'Tidak bisa menghapus asesmen yang sudah dikerjakan siswa.');
            return;
        }

        $assessment->delete();
        session()->flash('success', 'Asesmen berhasil dihapus.');
    }

    #[Computed]
    public function assessments()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'waka_kurikulum']);

        $query = Assessment::with(['subject', 'creator'])
            ->where('assessment_type', 'quiz')
            ->when(!$isAdmin, fn($q) => $q->where('created_by', $user->id))
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

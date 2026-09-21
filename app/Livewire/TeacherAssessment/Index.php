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

    // ── Copy modal ─────────────────────────────────────────
    public bool   $showCopyModal    = false;
    public ?int   $copyId           = null;
    public string $copyTitle        = '';
    public string $copyStartDate    = '';
    public string $copyStartTime    = '07:00';
    public string $copyEndDate      = '';
    public string $copyEndTime      = '23:59';
    public array  $copyTargetGrades = [];
    public array  $copyTargetMajors = [];

    public array $gradeOptions = ['X', 'XI', 'XII'];
    public array $majorOptions = ['MPLB', 'AKL', 'BUSANA'];
    // ───────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->tab    = $tab;
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

    // ── Copy ───────────────────────────────────────────────

    public function openCopyModal(int $id): void
    {
        $a = Assessment::findOrFail($id);

        $this->copyId           = $id;
        $this->copyTitle        = $a->title;
        $this->copyStartDate    = '';
        $this->copyStartTime    = $a->start_time ? substr($a->start_time, 0, 5) : '07:00';
        $this->copyEndDate      = '';
        $this->copyEndTime      = $a->end_time   ? substr($a->end_time,   0, 5) : '23:59';
        $this->copyTargetGrades = $a->target_grades ?? [];
        $this->copyTargetMajors = $a->target_majors ?? [];
        $this->showCopyModal    = true;
    }

    public function closeCopyModal(): void
    {
        $this->showCopyModal = false;
        $this->resetCopyFields();
    }

    public function confirmCopy(): void
    {
        $this->validate([
            'copyTitle'     => 'required|string|max:255',
            'copyStartDate' => 'required|date',
            'copyEndDate'   => 'required|date|after_or_equal:copyStartDate',
        ], [
            'copyTitle.required'         => 'Judul wajib diisi.',
            'copyStartDate.required'     => 'Tanggal mulai wajib diisi.',
            'copyEndDate.required'       => 'Tanggal selesai wajib diisi.',
            'copyEndDate.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $original = Assessment::with(['questions.options'])->findOrFail($this->copyId);

        // Duplikat assessment — ikut setting asli, hanya ganti tanggal & target
        $new = $original->replicate();
        $new->title         = $this->copyTitle;
        $new->start_date    = $this->copyStartDate;
        $new->start_time    = $this->copyStartTime;
        $new->end_date      = $this->copyEndDate;
        $new->end_time      = $this->copyEndTime;
        $new->target_grades = $this->copyTargetGrades ?: null;
        $new->target_majors = $this->copyTargetMajors ?: null;
        $new->created_by    = auth()->id();
        $new->save();

        // Duplikat soal + opsi jawaban
        foreach ($original->questions as $question) {
            $newQ = $question->replicate();
            $newQ->assessment_id = $new->id;
            $newQ->save();

            foreach ($question->options as $option) {
                $newOpt = $option->replicate();
                $newOpt->assessment_question_id = $newQ->id;
                $newOpt->save();
            }
        }

        $this->showCopyModal = false;
        $this->resetCopyFields();
        session()->flash('success', "Kuis '{$original->title}' berhasil disalin.");
    }

    private function resetCopyFields(): void
    {
        $this->copyId           = null;
        $this->copyTitle        = '';
        $this->copyStartDate    = '';
        $this->copyStartTime    = '07:00';
        $this->copyEndDate      = '';
        $this->copyEndTime      = '23:59';
        $this->copyTargetGrades = [];
        $this->copyTargetMajors = [];
    }
    // ───────────────────────────────────────────────────────

    #[Computed]
    public function assessments()
    {
        $user    = auth()->user();
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
                $a->sessions_count  = $a->studentSessions()->count();
                $a->submitted_count = $a->studentSessions()->whereNotNull('submitted_at')->count();
                $a->questions_count = $a->questions()->count();
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
<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklAssignment;
use App\Models\PklSubmission;

class AssignmentGrading extends BaseComponent
{
    public PklAssignment $assignment;
    public $submissions = [];
    public $scores      = [];
    public $feedbacks   = [];

    // Revision modal state (pure Livewire)
    public bool   $showRevisionModal    = false;
    public ?int   $revisionSubmissionId = null;
    public string $revisionNote         = '';

    // Answer modal
    public bool   $showAnswerModal = false;
    public string $answerContent   = '';
    public string $answerStudent   = '';

    public function mount(PklAssignment $assignment)
    {
        $this->assignment = $assignment->load(['course.subject', 'course.teacher']);
        if (auth()->user()->role === 'guru' && $this->assignment->course->teacher_id !== auth()->id()) {
            abort(403);
        }
        $this->loadSubmissions();
    }

    public function loadSubmissions()
    {
        $this->submissions = $this->assignment->submissions()
            ->with('student.schoolClass')
            ->orderBy('submitted_at', 'desc')
            ->get();
        foreach ($this->submissions as $sub) {
            $this->scores[$sub->id]    = $sub->score;
            $this->feedbacks[$sub->id] = $sub->feedback;
        }
    }

    public function grade($submissionId)
    {
        $this->validate([
            "scores.{$submissionId}" => 'required|numeric|min:0|max:' . $this->assignment->max_score,
        ]);
        $submission = PklSubmission::findOrFail($submissionId);
        $submission->update([
            'score'                 => $this->scores[$submissionId],
            'feedback'              => $this->feedbacks[$submissionId] ?? null,
            'graded_at'             => now(),
            'revision_requested'    => false,
            'revision_note'         => null,
            'revision_requested_at' => null,
        ]);
        session()->flash('success', 'Nilai berhasil disimpan');
        $this->loadSubmissions();
    }

    public function openRevisionModal(int $submissionId)
    {
        $this->revisionSubmissionId = $submissionId;
        $this->revisionNote         = '';
        $this->showRevisionModal    = true;
    }

    public function closeRevisionModal()
    {
        $this->showRevisionModal    = false;
        $this->revisionSubmissionId = null;
        $this->revisionNote         = '';
    }

    public function requestRevision()
    {
        if (!$this->revisionSubmissionId) return;
        $submission = PklSubmission::findOrFail($this->revisionSubmissionId);
        $submission->update([
            'revision_requested'    => true,
            'revision_note'         => $this->revisionNote ?: null,
            'revision_requested_at' => now(),
            'score'                 => null,
            'graded_at'             => null,
        ]);
        $this->closeRevisionModal();
        session()->flash('success', 'Permintaan kerjakan ulang berhasil dikirim ke siswa.');
        $this->loadSubmissions();
    }

    public function openAnswerModal(string $content, string $student)
    {
        $this->answerContent   = $content;
        $this->answerStudent   = $student;
        $this->showAnswerModal = true;
    }

    public function closeAnswerModal()
    {
        $this->showAnswerModal = false;
    }

    public function render()
    {
        return view('livewire.pkl-learning.assignment-grading');
    }
}

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

    // Revision modal state
    public $revisionSubmissionId = null;
    public $revisionNote         = '';

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
            'score'    => $this->scores[$submissionId],
            'feedback' => $this->feedbacks[$submissionId] ?? null,
            'graded_at'=> now(),
            // Bila nilai disimpan, batalkan permintaan revisi
            'revision_requested'    => false,
            'revision_note'         => null,
            'revision_requested_at' => null,
        ]);

        session()->flash('success', 'Nilai berhasil disimpan');
        $this->loadSubmissions();
    }

    /**
     * Buka modal konfirmasi kerjakan ulang.
     */
    public function openRevisionModal($submissionId)
    {
        $this->revisionSubmissionId = $submissionId;
        $this->revisionNote         = '';
        $this->dispatch('open-revision-modal');
    }

    /**
     * Simpan permintaan revisi — reset submission siswa.
     */
    public function requestRevision()
    {
        $this->validate([
            'revisionNote' => 'nullable|string|max:500',
        ]);

        $submission = PklSubmission::findOrFail($this->revisionSubmissionId);
        $submission->update([
            'revision_requested'    => true,
            'revision_note'         => $this->revisionNote ?: null,
            'revision_requested_at' => now(),
            // Reset nilai & graded agar guru tidak bingung
            'score'     => null,
            'graded_at' => null,
        ]);

        $this->revisionSubmissionId = null;
        $this->revisionNote         = '';
        $this->dispatch('close-revision-modal');
        session()->flash('success', 'Permintaan kerjakan ulang berhasil dikirim ke siswa.');
        $this->loadSubmissions();
    }

    public function render()
    {
        return view('livewire.pkl-learning.assignment-grading');
    }
}

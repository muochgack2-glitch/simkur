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
            'score'                  => $this->scores[$submissionId],
            'feedback'               => $this->feedbacks[$submissionId] ?? null,
            'graded_at'              => now(),
            'revision_requested'     => false,
            'revision_note'          => null,
            'revision_requested_at'  => null,
        ]);

        session()->flash('success', 'Nilai berhasil disimpan');
        $this->loadSubmissions();
    }

    /**
     * Simpan permintaan revisi — dipanggil langsung dari Alpine via $wire.call()
     */
    public function requestRevision($submissionId, $note = '')
    {
        $submission = PklSubmission::findOrFail($submissionId);
        $submission->update([
            'revision_requested'    => true,
            'revision_note'         => $note ?: null,
            'revision_requested_at' => now(),
            'score'                 => null,
            'graded_at'             => null,
        ]);

        session()->flash('success', 'Permintaan kerjakan ulang berhasil dikirim ke siswa.');
        $this->loadSubmissions();
    }

    public function render()
    {
        return view('livewire.pkl-learning.assignment-grading');
    }
}

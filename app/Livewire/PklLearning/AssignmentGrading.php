<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklAssignment;
use App\Models\PklSubmission;

class AssignmentGrading extends BaseComponent
{
    public PklAssignment $assignment;
    public $submissions = [];
    public $scores = [];
    public $feedbacks = [];

    public function mount(PklAssignment $assignment)
    {
        $this->assignment = $assignment->load(['course.subject', 'course.teacher']);

        // Ensure only the teacher who owns the course can grade
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
            $this->scores[$sub->id] = $sub->score;
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
            'score' => $this->scores[$submissionId],
            'feedback' => $this->feedbacks[$submissionId] ?? null,
            'graded_at' => now(),
        ]);

        session()->flash('success', 'Nilai berhasil disimpan');
        $this->loadSubmissions();
    }

    public function render()
    {
        return view('livewire.pkl-learning.assignment-grading');
    }
}

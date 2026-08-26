<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklCourse;

class StudentCourse extends BaseComponent
{
    public PklCourse $course;
    public $progress = [];
    public $assignmentStatuses = [];
    public $quizStatuses = [];

    public function mount(PklCourse $course)
    {
        $user = auth()->user();

        // Verify student has access
        if (!in_array((int) $user->class_id, $course->target_classes ?? [])) {
            abort(403, 'Anda tidak memiliki akses ke course ini');
        }

        $this->course = $course->load(['materials', 'assignments', 'quizzes.questions', 'subject', 'teacher']);
        $this->progress = $course->getProgressForStudent($user->id);

        // Assignment statuses
        foreach ($this->course->assignments as $asg) {
            $submission = $asg->getSubmissionForStudent($user->id);
            $this->assignmentStatuses[$asg->id] = [
                'submitted' => $submission?->isSubmitted() ?? false,
                'graded' => $submission?->isGraded() ?? false,
                'score' => $submission?->score,
                'is_late' => $submission?->is_late ?? false,
                'feedback' => $submission?->feedback,
            ];
        }

        // Quiz statuses
        foreach ($this->course->quizzes as $quiz) {
            $response = $quiz->getResponseForStudent($user->id);
            $this->quizStatuses[$quiz->id] = [
                'started' => $response !== null,
                'submitted' => $response?->isSubmitted() ?? false,
                'graded' => $response?->isGraded() ?? false,
                'score' => $response?->score,
            ];
        }
    }

    public function render()
    {
        return view('livewire.pkl-learning.student-course');
    }
}


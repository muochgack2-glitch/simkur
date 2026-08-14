<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklQuiz;
use App\Models\PklQuizResponse;

class StudentQuiz extends BaseComponent
{
    public PklQuiz $quiz;
    public ?PklQuizResponse $response = null;
    public $answers = [];
    public $timeRemaining = null;
    public $isFinished = false;

    public function mount(PklQuiz $quiz)
    {
        $user = auth()->user();
        $course = $quiz->course;

        if (!in_array((int) $user->class_id, $course->target_classes ?? [])) {
            abort(403);
        }

        $this->quiz = $quiz->load('questions');

        // Check existing response
        $this->response = PklQuizResponse::where('pkl_quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->first();

        if ($this->response && $this->response->isSubmitted()) {
            $this->isFinished = true;
            $this->answers = $this->response->answers ?? [];
            return;
        }

        // Start new attempt or resume
        if (!$this->response) {
            $this->response = PklQuizResponse::create([
                'pkl_quiz_id' => $quiz->id,
                'student_id' => $user->id,
                'started_at' => now(),
                'answers' => [],
            ]);
        }

        $this->answers = $this->response->answers ?? [];

        // Shuffle questions if needed
        if ($quiz->shuffle_questions) {
            $this->quiz->setRelation('questions', $this->quiz->questions->shuffle());
        }
    }

    public function saveProgress()
    {
        if ($this->isFinished) return;
        $this->response->update(['answers' => $this->answers]);
    }

    public function submitQuiz()
    {
        if ($this->isFinished) return;

        // Auto-grade multiple choice & true/false
        $autoScore = $this->quiz->autoGrade(
            tap($this->response, fn($r) => $r->answers = $this->answers)
        );

        // Check if there are essay questions (need manual grading)
        $hasEssay = $this->quiz->questions->contains('question_type', 'essay');

        $this->response->update([
            'answers' => $this->answers,
            'score' => $autoScore,
            'submitted_at' => now(),
            'is_graded' => !$hasEssay,
        ]);

        $this->isFinished = true;
        session()->flash('success', 'Kuis berhasil dikumpulkan!');
    }

    public function render()
    {
        return view('livewire.pkl-learning.student-quiz');
    }
}

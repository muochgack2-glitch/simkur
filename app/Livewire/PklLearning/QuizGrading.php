<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklQuiz;
use App\Models\PklQuizResponse;

class QuizGrading extends BaseComponent
{
    public $quiz;
    public $responses = [];
    public $essayScores = [];
    public $essayFeedbacks = [];

    public function mount(PklQuiz $quiz)
    {
        $this->quiz = $quiz->load(['course.subject', 'questions']);

        $this->loadResponses();
    }

    public function loadResponses()
    {
        $this->responses = PklQuizResponse::where('pkl_quiz_id', $this->quiz->id)
            ->whereNotNull('submitted_at')
            ->with('student.schoolClass')
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($response) {
                $answers = $response->answers ?? [];
                $autoScore = 0;
                $essayQuestions = [];

                foreach ($this->quiz->questions as $question) {
                    $studentAnswer = $answers[$question->id] ?? '';

                    if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                        if (strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer))) {
                            $autoScore += $question->score;
                        }
                    } elseif ($question->question_type === 'essay') {
                        $essayQuestions[] = [
                            'question_id' => $question->id,
                            'question' => $question->question,
                            'answer' => $studentAnswer,
                            'max_score' => $question->score,
                        ];
                    }
                }

                return [
                    'id' => $response->id,
                    'student_name' => $response->student->name ?? '-',
                    'student_class' => $response->student->schoolClass->name ?? '-',
                    'submitted_at' => $response->submitted_at->translatedFormat('d M Y H:i'),
                    'auto_score' => $autoScore,
                    'total_score' => $response->score,
                    'is_graded' => $response->score !== null,
                    'essay_questions' => $essayQuestions,
                    'answers' => $answers,
                ];
            })
            ->toArray();

        // Initialize essay scores
        foreach ($this->responses as $resp) {
            foreach ($resp['essay_questions'] as $eq) {
                $key = $resp['id'] . '_' . $eq['question_id'];
                if (!isset($this->essayScores[$key])) {
                    $this->essayScores[$key] = '';
                }
            }
        }
    }

    public function gradeResponse($responseId)
    {
        $response = PklQuizResponse::findOrFail($responseId);
        $answers = $response->answers ?? [];
        $totalScore = 0;

        foreach ($this->quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? '';

            if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                if (strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer))) {
                    $totalScore += $question->score;
                }
            } elseif ($question->question_type === 'essay') {
                $key = $responseId . '_' . $question->id;
                $essayScore = intval($this->essayScores[$key] ?? 0);
                $totalScore += min($essayScore, $question->score);
            }
        }

        $response->update([
            'score' => $totalScore,
        ]);

        session()->flash('success', "Nilai berhasil disimpan: {$totalScore}");
        $this->loadResponses();
    }

    public function render()
    {
        return view('livewire.pkl-learning.quiz-grading');
    }
}
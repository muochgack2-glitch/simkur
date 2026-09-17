<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\StudentAssessmentResponse;
use Livewire\Component;

class GradeEssay extends Component
{
    public Assessment $assessment;
    public $student;
    public AssessmentStudentSession $session;

    // Map question_id => teacher_score
    public array $scores = [];
    // Map question_id => teacher_feedback
    public array $feedbacks = [];

    public function mount(int $assessmentId, int $studentId): void
    {
        $this->assessment = Assessment::where('id', $assessmentId)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $this->student = \App\Models\User::findOrFail($studentId);

        // Ambil session terbaru yang sudah submit
        $this->session = AssessmentStudentSession::where('assessment_id', $assessmentId)
            ->where('user_id', $studentId)
            ->whereNotNull('submitted_at')
            ->orderByDesc('attempt_number')
            ->firstOrFail();

        // Pre-fill skor dan feedback yang sudah ada
        $responses = StudentAssessmentResponse::where('assessment_id', $assessmentId)
            ->where('user_id', $studentId)
            ->where('attempt_number', $this->session->attempt_number)
            ->get();

        foreach ($responses as $r) {
            $this->scores[$r->assessment_question_id]    = $r->teacher_score ?? '';
            $this->feedbacks[$r->assessment_question_id] = $r->teacher_feedback ?? '';
        }
    }

    public function saveGrades(): void
    {
        $responses = StudentAssessmentResponse::where('assessment_id', $this->assessment->id)
            ->where('user_id', $this->student->id)
            ->where('attempt_number', $this->session->attempt_number)
            ->get();

        $totalManual = 0;

        foreach ($responses as $response) {
            if ($response->question && $response->question->isManualScored()) {
                $qid   = $response->assessment_question_id;
                $score = isset($this->scores[$qid]) && $this->scores[$qid] !== ''
                    ? (float) $this->scores[$qid] : null;
                $maxScore = $response->question->getEffectiveMaxScore();

                // Pastikan skor tidak melebihi max
                if ($score !== null) {
                    $score = min($score, $maxScore);
                    $totalManual += $score;
                }

                $response->update([
                    'teacher_score'    => $score,
                    'teacher_feedback' => $this->feedbacks[$qid] ?? null,
                    'is_graded'        => $score !== null,
                ]);
            }
        }

        // Update manual_score di session dan recalculate total
        $this->session->manual_score = $totalManual;
        $this->session->recalculateTotal();

        session()->flash('success', 'Penilaian berhasil disimpan.');
        $this->redirect(route('teacher.assessment.results', $this->assessment->id), navigate: true);
    }

    public function render()
    {
        $questions = $this->assessment->questions()->with('options')->orderBy('order_number')->get();
        $responses = StudentAssessmentResponse::with(['question', 'selectedOption'])
            ->where('assessment_id', $this->assessment->id)
            ->where('user_id', $this->student->id)
            ->where('attempt_number', $this->session->attempt_number)
            ->get()
            ->keyBy('assessment_question_id');

        return view('livewire.teacher-assessment.grade-essay', [
            'questions' => $questions,
            'responses' => $responses,
        ])->layout('components.layouts.app');
    }
}

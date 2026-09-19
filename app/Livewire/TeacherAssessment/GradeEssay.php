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
            ->when(!in_array(auth()->user()->role, ['admin', 'waka_kurikulum']), fn($q) => $q->where('teacher_id', auth()->id()))
            ->firstOrFail();

        $this->student = \App\Models\User::findOrFail($studentId);

        // Ambil session terbaru yang sudah submit
        $this->session = AssessmentStudentSession::where('assessment_id', $assessmentId)
            ->where('user_id', $studentId)
            ->whereNotNull('submitted_at')
            ->orderByDesc('attempt_number')
            ->firstOrFail();

        // Pre-fill skor dan feedback yang sudah ada (dari response table)
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
        // Loop melalui SOAL (bukan response), sehingga bekerja
        // bahkan jika student_assessment_responses masih kosong
        $questions = $this->assessment->questions()
            ->where(function ($q) {
                $q->where('question_type', 'essay')
                  ->orWhere('question_type', 'file_upload');
            })
            ->get();

        $totalManual = 0;

        foreach ($questions as $question) {
            $qid      = $question->id;
            $maxScore = $question->getEffectiveMaxScore();
            $score    = isset($this->scores[$qid]) && $this->scores[$qid] !== ''
                ? min((float) $this->scores[$qid], $maxScore)
                : null;

            if ($score !== null) {
                $totalManual += $score;
            }

            // Upsert response record (buat jika belum ada, update jika sudah ada)
            StudentAssessmentResponse::updateOrCreate(
                [
                    'assessment_id'          => $this->assessment->id,
                    'user_id'                => $this->student->id,
                    'assessment_question_id' => $qid,
                    'attempt_number'         => $this->session->attempt_number ?? 1,
                ],
                [
                    'teacher_score'    => $score,
                    'teacher_feedback' => $this->feedbacks[$qid] ?? null,
                    'is_graded'        => $score !== null,
                    'answered_at'      => now(),
                ]
            );
        }

        // Simpan manual_score lalu recalculate total
        $this->session->manual_score = $totalManual;
        $this->session->save();
        $this->session->recalculateTotal(); // total = auto + manual

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
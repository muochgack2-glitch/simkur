<?php

namespace App\Livewire\StudentAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\AssessmentQuestion;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizTake extends Component
{
    public Assessment $assessment;
    public ?AssessmentStudentSession $session = null;
    public array $answers = []; // [question_id => answer_value]
    public int $currentPage = 0;
    public int $questionsPerPage = 1;
    public bool $submitted = false;

    public function mount(int $id): void
    {
        $this->assessment = Assessment::with(['questions.options'])->findOrFail($id);

        // Cek waktu
        $now = Carbon::now();
        $start = Carbon::parse($this->assessment->start_date->toDateString() . ' ' . ($this->assessment->start_time ?? '00:00:00'));
        $end   = Carbon::parse($this->assessment->end_date->toDateString() . ' ' . ($this->assessment->end_time ?? '23:59:59'));

        if ($now->lt($start)) {
            session()->flash('error', 'Asesmen belum dimulai.');
            $this->redirect(route('student.assessment.index'), navigate: true);
            return;
        }
        if ($now->gt($end)) {
            session()->flash('error', 'Asesmen sudah ditutup.');
            $this->redirect(route('student.assessment.index'), navigate: true);
            return;
        }

        // Guard: cek apakah siswa boleh mengerjakan quiz ini
        $student = auth()->user();
        if (!$this->assessment->isForStudent($student)) {
            session()->flash('error', 'Anda tidak memiliki akses ke asesmen ini.');
            $this->redirect(route('student.assessment.index'), navigate: true);
            return;
        }

        // Cek atau buat sesi
        $this->session = AssessmentStudentSession::firstOrCreate(
            ['assessment_id' => $this->assessment->id, 'user_id' => auth()->id()],
            ['started_at' => now(), 'answers_data' => [], 'score' => null]
        );

        if ($this->session->submitted_at) {
            // Sudah submit — ke result
            $this->redirect(route('student.assessment.result', $this->assessment->id), navigate: true);
            return;
        }

        // Muat jawaban yang sudah ada
        $this->answers = $this->session->answers_data ?? [];
    }

    public function getQuestionsProperty()
    {
        $questions = $this->assessment->questions;

        if ($this->assessment->shuffle_questions && !$this->session?->question_order) {
            // Simpan urutan acak
            $order = $questions->pluck('id')->shuffle()->values()->toArray();
            $this->session?->update(['question_order' => $order]);
        }

        if ($this->session?->question_order) {
            $order = $this->session->question_order;
            $questions = $questions->sortBy(fn($q) => array_search($q->id, $order))->values();
        }

        return $questions;
    }

    public function saveAnswer(int $questionId, $value): void
    {
        $this->answers[$questionId] = $value;
        // Autosave ke DB
        $this->session?->update(['answers_data' => $this->answers]);
    }

    public function saveMatchingAnswer(int $questionId, string $left, string $right): void
    {
        $current = $this->answers[$questionId] ?? [];
        if (!is_array($current)) {
            $current = json_decode($current, true) ?? [];
        }
        $current[$left] = $right;
        $this->answers[$questionId] = $current;
        $this->session?->update(['answers_data' => $this->answers]);
    }

    public function nextPage(): void
    {
        $total = $this->questions->count();
        if ($this->currentPage < $total - 1) {
            $this->currentPage++;
        }
    }

    public function prevPage(): void
    {
        if ($this->currentPage > 0) {
            $this->currentPage--;
        }
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public function submit(): void
    {
        if ($this->submitted) return;
        $this->submitted = true;

        $session = $this->session;
        $questions = $this->questions;
        $totalScore = 0;
        $maxScore = 0;
        $needsManual = false;

        foreach ($questions as $q) {
            $maxScore += $q->getEffectiveMaxScore();
            $answer = $this->answers[$q->id] ?? null;

            if ($q->isAutoScored() && $answer !== null) {
                // PG / B-S / Menjodohkan — hitung otomatis
                if ($q->isMultipleChoice() || $q->isTrueFalse()) {
                    // answer = option_id
                    $correctOption = $q->options->where('is_correct', true)->first();
                    if ($correctOption && (int)$answer === $correctOption->id) {
                        $totalScore += $q->getEffectiveMaxScore();
                    }
                } elseif ($q->isMatching()) {
                    // answer = json {left => right}
                    $pairs = is_array($answer) ? $answer : json_decode($answer, true) ?? [];
                    $correctPairs = $q->matching_pairs ?? [];
                    $correct = 0;
                    foreach ($correctPairs as $pair) {
                        if (isset($pairs[$pair['left']]) && $pairs[$pair['left']] === $pair['right']) {
                            $correct++;
                        }
                    }
                    if (count($correctPairs) > 0) {
                        $totalScore += round($q->getEffectiveMaxScore() * ($correct / count($correctPairs)));
                    }
                }
            } elseif ($q->isManualScored()) {
                $needsManual = true;
            }
        }

        $session->update([
            'answers_data'  => $this->answers,
            'submitted_at'  => now(),
            'score'         => $needsManual ? null : $totalScore,
            'max_score'     => $maxScore,
            'needs_grading' => $needsManual,
        ]);

        $this->redirect(route('student.assessment.result', $this->assessment->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.student-assessment.quiz-take', [
            'questions' => $this->questions,
            'total'     => $this->questions->count(),
            'answered'  => count(array_filter($this->answers, fn($v) => $v !== null && $v !== '')),
        ])->layout('components.layouts.app');
    }
}
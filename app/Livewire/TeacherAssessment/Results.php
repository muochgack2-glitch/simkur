<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\StudentAssessmentResponse;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Results extends Component
{
    public Assessment $assessment;
    public string $filterClass = '';
    public string $filterStatus = '';

    public function mount(int $id): void
    {
        $this->assessment = Assessment::with(['questions.options'])
            ->where('id', $id)
            ->when(!in_array(auth()->user()->role, ['admin', 'waka_kurikulum']), fn($q) => $q->where('teacher_id', auth()->id()))
            ->firstOrFail();
    }

    /**
     * Force submit satu siswa yang masih Mengerjakan.
     * Jawaban yang sudah tersimpan di answers_data dinilai otomatis (PG/B-S/matching),
     * esai/file_upload ditandai menunggu penilaian manual.
     */
    public function forceSubmit(int $sessionId): void
    {
        $session = AssessmentStudentSession::where('id', $sessionId)
            ->where('assessment_id', $this->assessment->id)
            ->whereNull('submitted_at')
            ->whereNotNull('started_at')
            ->firstOrFail();

        $this->doForceSubmit($session);

        session()->flash('success', 'Berhasil force submit jawaban siswa.');
    }

    /**
     * Force submit SEMUA siswa yang masih Mengerjakan sekaligus.
     */
    public function forceSubmitAll(): void
    {
        $sessions = AssessmentStudentSession::where('assessment_id', $this->assessment->id)
            ->whereNull('submitted_at')
            ->whereNotNull('started_at')
            ->get();

        if ($sessions->isEmpty()) {
            session()->flash('info', 'Tidak ada siswa yang sedang mengerjakan.');
            return;
        }

        $count = 0;
        foreach ($sessions as $session) {
            $this->doForceSubmit($session);
            $count++;
        }

        session()->flash('success', "Berhasil force submit {$count} siswa.");
    }

    /**
     * Proses force-submit internal: hitung skor dari answers_data, buat response records, update sesi.
     */
    private function doForceSubmit(AssessmentStudentSession $session): void
    {
        DB::transaction(function () use ($session) {
            $questions  = $this->assessment->questions;
            $answers    = $session->answers_data ?? [];
            $totalScore = 0;
            $maxScore   = 0;
            $needsManual = false;

            foreach ($questions as $q) {
                $maxScore += $q->getEffectiveMaxScore();
                $answer    = $answers[$q->id] ?? null;
                $qScore    = null;
                $optionId  = null;
                $textAns   = null;

                if ($q->isAutoScored() && $answer !== null) {
                    if ($q->isMultipleChoice() || $q->isTrueFalse()) {
                        $correctOption = $q->options->where('score_value', '>', 0)->first();
                        $qScore      = ($correctOption && (int)$answer === $correctOption->id)
                            ? $q->getEffectiveMaxScore() : 0;
                        $totalScore += $qScore;
                        $optionId    = (int)$answer;
                    } elseif ($q->isMatching()) {
                        $pairs        = is_array($answer) ? $answer : json_decode($answer, true) ?? [];
                        $correctPairs = $q->matching_pairs ?? [];
                        $correct      = 0;
                        foreach ($correctPairs as $pair) {
                            if (isset($pairs[$pair['left']]) && $pairs[$pair['left']] === $pair['right']) {
                                $correct++;
                            }
                        }
                        $qScore      = count($correctPairs) > 0
                            ? round($q->getEffectiveMaxScore() * ($correct / count($correctPairs))) : 0;
                        $totalScore += $qScore;
                        $textAns     = json_encode($pairs);
                    }
                } elseif ($q->isAutoScored() && $answer === null) {
                    // Soal auto-scored tapi tidak dijawab → skor 0
                    $qScore = 0;
                } elseif ($q->isManualScored()) {
                    $needsManual = true;
                    $textAns     = is_string($answer) ? $answer : null;
                    $qScore      = null;
                }

                // Simpan response record
                StudentAssessmentResponse::updateOrCreate(
                    [
                        'assessment_id'          => $this->assessment->id,
                        'user_id'                => $session->user_id,
                        'assessment_question_id' => $q->id,
                        'attempt_number'         => $session->attempt_number ?? 1,
                    ],
                    [
                        'selected_option_id' => $optionId,
                        'text_answer'        => $textAns,
                        'score'              => $qScore,
                        'answered_at'        => now(),
                    ]
                );
            }

            // Update sesi → tandai submitted
            $session->update([
                'submitted_at'       => now(),
                'auto_score'         => $totalScore,
                'max_possible_score' => $maxScore,
                'manual_score'       => $needsManual ? null : 0,
                'total_score'        => $needsManual ? null : $totalScore,
            ]);
        });
    }

    public function render()
    {
        // Ambil semua session (attempt terbaru per siswa)
        $sessions = AssessmentStudentSession::with(['student'])
            ->where('assessment_id', $this->assessment->id)
            ->when($this->filterStatus === 'submitted', fn($q) => $q->whereNotNull('submitted_at'))
            ->when($this->filterStatus === 'in_progress', fn($q) => $q->whereNull('submitted_at')->whereNotNull('started_at'))
            ->orderByDesc('attempt_number')
            ->get()
            // Ambil attempt terbaru per siswa
            ->groupBy('user_id')
            ->map(fn($group) => $group->sortByDesc('attempt_number')->first());

        // Statistik
        $totalSubmitted   = $sessions->whereNotNull('submitted_at')->count();
        $totalInProgress  = $sessions->filter(fn($s) => $s->isInProgress())->count();
        $needsGrading     = $sessions->filter(fn($s) => $s->isSubmitted() && $s->manual_score === null
            && $this->assessment->questions()->whereIn('question_type', ['essay','file_upload'])->exists())->count();
        $avgScore         = $sessions->whereNotNull('submitted_at')
            ->avg(fn($s) => $s->total_score ?? (($s->auto_score ?? 0) + ($s->manual_score ?? 0)));

        $isClosed = $this->assessment->status === 'closed';

        return view('livewire.teacher-assessment.results', [
            'sessions'        => $sessions->values(),
            'totalSubmitted'  => $totalSubmitted,
            'totalInProgress' => $totalInProgress,
            'needsGrading'    => $needsGrading,
            'avgScore'        => round($avgScore ?? 0, 1),
            'totalQuestions'  => $this->assessment->questions()->count(),
            'isClosed'        => $isClosed,
        ])->layout('components.layouts.app');
    }
}
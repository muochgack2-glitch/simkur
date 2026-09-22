<?php

namespace App\Console\Commands;

use App\Models\AssessmentStudentSession;
use App\Models\StudentAssessmentResponse;
use Illuminate\Console\Command;

class RecalculateQuizScores extends Command
{
    protected $signature = 'quiz:recalculate-scores';
    protected $description = 'Recalculate auto-scored responses (PG, B/S, Menjodohkan) per attempt terbaru';

    public function handle(): void
    {
        $this->info('Recalculating quiz scores...');

        // Fix 1: Recalculate score per response (PG/B-S) berdasarkan score_value opsi
        $responses = StudentAssessmentResponse::whereNotNull('selected_option_id')
            ->with(['selectedOption', 'question'])
            ->get();

        $fixed = 0;
        foreach ($responses as $r) {
            $optionScore = $r->selectedOption?->score_value ?? 0;
            $maxScore    = $r->question?->getEffectiveMaxScore() ?? 0;
            $newScore    = $optionScore > 0 ? $maxScore : 0;

            if ((float) $r->score !== (float) $newScore) {
                $r->update(['score' => $newScore]);
                $fixed++;
            }
        }
        $this->info("Fixed {$fixed} individual responses.");

        // Fix 2: Recalculate session totals — HANYA dari attempt terbaru
        $sessions = AssessmentStudentSession::whereNotNull('submitted_at')->get();
        $sessionFixed = 0;

        foreach ($sessions as $session) {
            // Hitung auto_score dari responses milik attempt INI saja
            $autoScore = StudentAssessmentResponse::where('assessment_id', $session->assessment_id)
                ->where('user_id', $session->user_id)
                ->where('attempt_number', $session->attempt_number)
                ->whereHas('question', fn($q) => $q->whereIn('question_type', ['multiple_choice', 'true_false', 'matching']))
                ->sum('score');

            $oldAuto = (float)$session->auto_score;
            $session->auto_score  = $autoScore;
            $session->total_score = $autoScore + ($session->manual_score ?? 0);
            $session->save();

            if ($oldAuto !== (float)$autoScore) {
                $sessionFixed++;
            }
        }

        $this->info("Fixed {$sessionFixed} sessions. Done!");
    }
}

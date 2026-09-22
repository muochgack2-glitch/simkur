<?php

namespace App\Console\Commands;

use App\Models\StudentAssessmentResponse;
use App\Models\AssessmentQuestion;
use Illuminate\Console\Command;

class RecalculateQuizScores extends Command
{
    protected $signature = 'quiz:recalculate-scores';
    protected $description = 'Recalculate auto-scored responses (PG, B/S, Menjodohkan) using score_value';

    public function handle(): void
    {
        $this->info('Recalculating quiz scores...');

        // Ambil semua response yang punya selected_option_id (PG/B-S)
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

        // Recalculate session totals
        $sessions = \App\Models\AssessmentStudentSession::all();
        foreach ($sessions as $session) {
            $autoScore = StudentAssessmentResponse::where('assessment_id', $session->assessment_id)
                ->where('user_id', $session->user_id)
                ->whereHas('question', fn($q) => $q->whereIn('question_type', ['multiple_choice', 'true_false', 'matching']))
                ->sum('score');

            $session->update([
                'auto_score'  => $autoScore,
                'total_score' => $autoScore + ($session->manual_score ?? 0),
            ]);
        }

        $this->info("Done! Fixed {$fixed} responses. Sessions recalculated.");
    }
}

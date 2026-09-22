<?php
// Cek skor per soal vs total session untuk assessment 59, user 70
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$assessId = 59;
$userId   = 70;

echo "=== RESPONSES (per soal) ===\n";
$responses = \App\Models\StudentAssessmentResponse::where('assessment_id', $assessId)
    ->where('user_id', $userId)
    ->with(['question', 'selectedOption'])
    ->get();

$totalFromResponses = 0;
foreach ($responses as $r) {
    $type = $r->question?->question_type ?? '?';
    $optText = $r->selectedOption?->option_text ?? $r->text_answer ?? '-';
    $optScoreVal = $r->selectedOption?->score_value ?? '-';
    echo "Q{$r->assessment_question_id} [{$type}] answer='{$optText}' | option.score_value={$optScoreVal} | response.score={$r->score} | teacher_score={$r->teacher_score}\n";
    $totalFromResponses += (float)$r->score + (float)($r->teacher_score ?? 0);
}
echo "\nTotal dari responses: {$totalFromResponses}\n";

echo "\n=== SESSION ===\n";
$session = \App\Models\AssessmentStudentSession::where('assessment_id', $assessId)
    ->where('user_id', $userId)->first();
if ($session) {
    echo "auto_score={$session->auto_score} | manual_score={$session->manual_score} | total_score={$session->total_score}\n";
} else {
    echo "Session NOT FOUND\n";
}

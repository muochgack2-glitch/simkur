<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$assessId = 59;
$userId   = 70;

echo "=== ALL SESSIONS ===\n";
$sessions = \App\Models\AssessmentStudentSession::where('assessment_id', $assessId)
    ->where('user_id', $userId)->orderBy('attempt_number')->get();
foreach ($sessions as $s) {
    echo "attempt={$s->attempt_number} | auto={$s->auto_score} | manual={$s->manual_score} | total={$s->total_score} | submitted={$s->submitted_at}\n";
}

echo "\n=== RESPONSES with attempt_number ===\n";
$responses = \App\Models\StudentAssessmentResponse::where('assessment_id', $assessId)
    ->where('user_id', $userId)
    ->with(['question', 'selectedOption'])
    ->orderBy('attempt_number')->orderBy('assessment_question_id')
    ->get();

foreach ($responses as $r) {
    $type = $r->question?->question_type ?? '?';
    $optText = $r->selectedOption?->option_text ?? substr($r->text_answer ?? '-', 0, 30);
    $optSV = $r->selectedOption?->score_value ?? '-';
    echo "attempt={$r->attempt_number} | Q{$r->assessment_question_id} [{$type}] ans='{$optText}' optSV={$optSV} score={$r->score} t_score={$r->teacher_score}\n";
}

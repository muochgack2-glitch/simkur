<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\User;
use App\Models\StudentAssessmentResponse;
use App\Models\StudentLearningProfile;
use App\Services\LearningProfileService;
use App\Services\DiagnosticProfileService;

echo "=== TESTING ASSESSMENT SUBMISSION ===\n\n";

// Get assessment
$assessment = Assessment::with(['questions.options'])->find(1);
if (!$assessment) {
    echo "❌ Assessment not found!\n";
    exit(1);
}

echo "✅ Assessment found: {$assessment->title}\n";
echo "   Type: {$assessment->assessment_type}\n";
echo "   Questions: {$assessment->questions->count()}\n\n";

// Get a student user
$student = User::where('role', 'siswa')->first();
if (!$student) {
    echo "❌ No student user found!\n";
    exit(1);
}

echo "✅ Test student: {$student->name} (ID: {$student->id})\n\n";

// Check questions and options
echo "📝 Checking questions and options...\n";
$questionsData = [];
$totalOptions = 0;

foreach ($assessment->questions as $question) {
    $optionsCount = $question->options()->count();
    $totalOptions += $optionsCount;
    $questionsData[] = [
        'question_id' => $question->id,
        'order' => $question->order_number,
        'options_count' => $optionsCount,
        'first_option_id' => $question->options()->first()->id ?? null,
    ];
}

echo "   Total questions: " . count($questionsData) . "\n";
echo "   Total options: {$totalOptions}\n\n";

echo "📊 Question details:\n";
foreach (array_slice($questionsData, 0, 5) as $data) {
    echo "   Q{$data['order']} (ID:{$data['question_id']}): {$data['options_count']} options, first option ID: {$data['first_option_id']}\n";
}
echo "   ...\n\n";

// Simulate answer submission
echo "🔄 Simulating answer submission...\n";

// Clean up previous attempts
StudentAssessmentResponse::where('assessment_id', $assessment->id)
    ->where('user_id', $student->id)
    ->delete();
StudentLearningProfile::where('assessment_id', $assessment->id)
    ->where('user_id', $student->id)
    ->delete();

echo "   ✓ Cleaned up previous data\n";

// Create responses
DB::beginTransaction();

try {
    $responseCount = 0;
    
    foreach ($assessment->questions as $question) {
        // Pick first option for each question
        $option = $question->options()->first();
        
        if (!$option) {
            throw new Exception("No options found for question {$question->id}");
        }
        
        StudentAssessmentResponse::create([
            'assessment_id' => $assessment->id,
            'user_id' => $student->id,
            'assessment_question_id' => $question->id,
            'selected_option_id' => $option->id,
            'score' => $option->score_value,
            'answered_at' => now(),
        ]);
        
        $responseCount++;
    }
    
    echo "   ✓ Created {$responseCount} responses\n";
    
    // Calculate profile
    echo "   🧮 Calculating profile...\n";
    
    if ($assessment->isVark()) {
        echo "   Type: VARK\n";
        $profileService = new LearningProfileService();
        $profile = $profileService->calculateProfile($assessment, $student);
    } else {
        echo "   Type: Diagnostic\n";
        $diagnosticService = new DiagnosticProfileService();
        $profileData = $diagnosticService->calculateProfile($student, $assessment);
        $profile = $diagnosticService->saveProfile($student, $assessment, $profileData);
    }
    
    if (!$profile || !$profile->id) {
        throw new Exception("Failed to create profile");
    }
    
    echo "   ✓ Profile created (ID: {$profile->id})\n";
    
    DB::commit();
    
    echo "\n✅ SUCCESS! Assessment submission completed!\n\n";
    
    echo "📋 Profile Summary:\n";
    if ($assessment->isVark()) {
        echo "   Dominant Style: {$profile->dominant_style}\n";
        echo "   Visual: {$profile->visual_score}\n";
        echo "   Auditory: {$profile->auditory_score}\n";
        echo "   Kinesthetic: {$profile->kinesthetic_score}\n";
        echo "   Reading/Writing: {$profile->reading_writing_score}\n";
    } else {
        $aspects = $profile->aspect_scores ?? [];
        echo "   Overall Score: {$profile->diagnostic_score}\n";
        echo "   Category: {$profile->diagnostic_category}\n";
        foreach ($aspects as $key => $value) {
            echo "   " . ucfirst($key) . ": {$value}\n";
        }
    }
    
    exit(0);
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

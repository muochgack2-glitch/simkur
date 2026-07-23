<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\StudentLearningProfile;
use App\Models\StudentAssessmentResponse;
use App\Models\User;
use Illuminate\Database\Seeder;

class VARKDummyResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get VARK Assessment (Revised)
        $assessment = Assessment::where('title', 'like', '%VARK%Revisi%')->first();
        
        if (!$assessment) {
            $this->command->error('VARK Assessment (Revisi) not found. Please run VARKAssessmentRevisedSeeder first.');
            return;
        }

        // Get all students
        $students = User::where('role', 'siswa')->get();
        
        if ($students->isEmpty()) {
            $this->command->error('No students found in database.');
            return;
        }

        $this->command->info("Found {$students->count()} students");
        $this->command->info("Generating VARK dummy responses...");

        // Get all questions with their options
        $questions = AssessmentQuestion::where('assessment_id', $assessment->id)
            ->with('options')
            ->orderBy('order_number')
            ->get();

        // Learning style distribution (realistic)
        $styleDistribution = [
            'visual' => 30,           // 30% Visual
            'auditory' => 25,          // 25% Auditory
            'kinesthetic' => 30,       // 30% Kinesthetic
            'reading_writing' => 15,   // 15% Reading/Writing
        ];

        $processedCount = 0;
        
        foreach ($students as $student) {
            // Check if student already completed this assessment
            $existingProfile = StudentLearningProfile::where('user_id', $student->id)
                ->where('assessment_id', $assessment->id)
                ->first();

            if ($existingProfile) {
                $this->command->warn("Student {$student->name} already has VARK profile. Skipping.");
                continue;
            }

            // Determine dominant style based on distribution
            $dominantStyle = $this->getRandomStyleByDistribution($styleDistribution);

            // Initialize scores
            $scores = [
                'visual' => 0,
                'auditory' => 0,
                'kinesthetic' => 0,
                'reading_writing' => 0,
            ];

            // Answer questions
            foreach ($questions as $question) {
                $indicator = $question->learning_style_indicator;
                
                // 70% chance to choose the correct answer for dominant style
                // 30% chance to choose other answers (for realism)
                if ($indicator === $dominantStyle && rand(1, 100) <= 70) {
                    // Choose the correct answer (score = 3)
                    $selectedOption = $question->options->firstWhere('score_value', 3);
                } else {
                    // Choose random answer
                    $selectedOption = $question->options->random();
                }

                // Save response
                StudentAssessmentResponse::create([
                    'user_id' => $student->id,
                    'assessment_id' => $assessment->id,
                    'assessment_question_id' => $question->id,
                    'selected_option_id' => $selectedOption->id,
                    'score' => $selectedOption->score_value,
                    'answered_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                ]);

                // Add to score
                $scores[$indicator] += $selectedOption->score_value;
            }

            // Find dominant style (highest score)
            $maxScore = max($scores);
            $dominantStyleResult = array_search($maxScore, $scores);

            // Create learning profile
            StudentLearningProfile::create([
                'user_id' => $student->id,
                'assessment_id' => $assessment->id,
                'academic_year_id' => $assessment->academic_year_id,
                'semester_id' => $assessment->semester_id,
                'visual_score' => $scores['visual'],
                'auditory_score' => $scores['auditory'],
                'kinesthetic_score' => $scores['kinesthetic'],
                'reading_writing_score' => $scores['reading_writing'],
                'dominant_style' => $dominantStyleResult,
                'completed_at' => now()->subDays(rand(0, 7)), // Random completion within last 7 days
            ]);

            $processedCount++;
            
            if ($processedCount % 10 === 0) {
                $this->command->info("Processed {$processedCount}/{$students->count()} students");
            }
        }

        $this->command->info("✅ VARK dummy responses created for {$processedCount} students!");
        
        // Show distribution
        $this->showDistribution($assessment->id);
    }

    /**
     * Get random learning style based on distribution
     */
    private function getRandomStyleByDistribution(array $distribution): string
    {
        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($distribution as $style => $percentage) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $style;
            }
        }

        return 'visual'; // fallback
    }

    /**
     * Show distribution of learning styles
     */
    private function showDistribution(int $assessmentId): void
    {
        $profiles = StudentLearningProfile::where('assessment_id', $assessmentId)->get();
        $total = $profiles->count();

        if ($total === 0) {
            return;
        }

        $distribution = [
            'visual' => $profiles->where('dominant_style', 'visual')->count(),
            'auditory' => $profiles->where('dominant_style', 'auditory')->count(),
            'kinesthetic' => $profiles->where('dominant_style', 'kinesthetic')->count(),
            'reading_writing' => $profiles->where('dominant_style', 'reading_writing')->count(),
        ];

        $this->command->info("\n📊 Learning Style Distribution:");
        $this->command->info("👁️  Visual: {$distribution['visual']} students (" . round(($distribution['visual']/$total)*100, 1) . "%)");
        $this->command->info("👂 Auditory: {$distribution['auditory']} students (" . round(($distribution['auditory']/$total)*100, 1) . "%)");
        $this->command->info("🤸 Kinesthetic: {$distribution['kinesthetic']} students (" . round(($distribution['kinesthetic']/$total)*100, 1) . "%)");
        $this->command->info("📖 Reading/Writing: {$distribution['reading_writing']} students (" . round(($distribution['reading_writing']/$total)*100, 1) . "%)");
    }
}

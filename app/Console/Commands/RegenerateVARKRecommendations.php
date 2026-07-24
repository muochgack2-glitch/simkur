<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\StudentLearningProfile;
use App\Services\LearningProfileService;
use Illuminate\Console\Command;

class RegenerateVARKRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vark:regenerate-recommendations {--assessment-id= : Specific assessment ID to regenerate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate recommendations for existing VARK learning profiles';

    /**
     * Execute the console command.
     */
    public function handle(LearningProfileService $service): int
    {
        $assessmentId = $this->option('assessment-id');

        if ($assessmentId) {
            $assessment = Assessment::find($assessmentId);
            if (!$assessment) {
                $this->error("Assessment with ID {$assessmentId} not found.");
                return Command::FAILURE;
            }
            $assessments = collect([$assessment]);
        } else {
            // Get all VARK assessments
            $assessments = Assessment::where('assessment_type', 'vark')->get();
        }

        if ($assessments->isEmpty()) {
            $this->error('No VARK assessments found.');
            return Command::FAILURE;
        }

        $this->info("Found {$assessments->count()} VARK assessment(s)");
        
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($assessments as $assessment) {
            $this->info("\nProcessing: {$assessment->title}");

            // Get all profiles for this assessment that don't have recommendations
            $profiles = StudentLearningProfile::where('assessment_id', $assessment->id)
                ->with('student')
                ->get();

            if ($profiles->isEmpty()) {
                $this->warn("No profiles found for this assessment.");
                continue;
            }

            $this->info("Found {$profiles->count()} profile(s)");
            $bar = $this->output->createProgressBar($profiles->count());
            $bar->start();

            foreach ($profiles as $profile) {
                // Generate recommendations based on existing scores
                $scores = [
                    'visual' => $profile->visual_score,
                    'auditory' => $profile->auditory_score,
                    'kinesthetic' => $profile->kinesthetic_score,
                    'reading_writing' => $profile->reading_writing_score,
                ];

                $dominantStyle = $profile->dominant_style;
                $totalScore = array_sum($scores);

                // Use reflection to access private method
                $reflectionClass = new \ReflectionClass($service);
                $method = $reflectionClass->getMethod('generateRecommendations');
                $method->setAccessible(true);
                $recommendations = $method->invoke($service, $dominantStyle, $scores, $totalScore);

                // Update profile with recommendations
                $profile->update([
                    'recommendations' => $recommendations,
                ]);

                $totalUpdated++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->newLine();
        $this->info("✅ Regeneration completed!");
        $this->info("Updated: {$totalUpdated} profile(s)");
        
        if ($totalSkipped > 0) {
            $this->info("Skipped: {$totalSkipped} profile(s) (already have recommendations)");
        }

        return Command::SUCCESS;
    }
}

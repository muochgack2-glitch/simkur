<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\StudentLearningProfile;
use App\Models\StudentAssessmentResponse;
use App\Models\AssessmentQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateAssessment extends Command
{
    protected $signature = 'assessment:fix-duplicate';
    protected $description = 'Fix duplicate diagnostic assessment issue';

    public function handle()
    {
        $this->info('=== Fixing Duplicate Diagnostic Assessment ===');
        $this->newLine();

        DB::beginTransaction();

        try {
            // Get both diagnostic assessments
            $assessment3 = Assessment::find(3);
            $assessment4 = Assessment::find(4);
            
            if (!$assessment3 || !$assessment4) {
                $this->error('Assessments not found!');
                return 1;
            }
            
            $this->info("Assessment 3: {$assessment3->title}");
            $this->line("  - Questions: " . $assessment3->questions()->count());
            $this->line("  - Profiles: " . $assessment3->studentProfiles()->count());
            $this->newLine();
            
            $this->info("Assessment 4: {$assessment4->title}");
            $this->line("  - Questions: " . $assessment4->questions()->count());
            $this->line("  - Profiles: " . $assessment4->studentProfiles()->count());
            $this->newLine();
            
            // Move all data from assessment 4 to assessment 3
            $this->info('Moving data from Assessment 4 to Assessment 3...');
            $this->newLine();
            
            // Update profiles
            $profilesUpdated = StudentLearningProfile::where('assessment_id', 4)
                ->update(['assessment_id' => 3]);
            $this->info("✓ Updated $profilesUpdated profile(s)");
            
            // Update responses
            $responsesUpdated = StudentAssessmentResponse::where('assessment_id', 4)
                ->update(['assessment_id' => 3]);
            $this->info("✓ Updated $responsesUpdated response(s)");
            
            // Move questions from assessment 4 to 3 if assessment 3 has no questions
            if ($assessment3->questions()->count() === 0 && $assessment4->questions()->count() > 0) {
                $questionsUpdated = AssessmentQuestion::where('assessment_id', 4)
                    ->update(['assessment_id' => 3]);
                $this->info("✓ Updated $questionsUpdated question(s)");
            } else {
                // Delete questions from assessment 4 (assessment 3 already has questions)
                $questionsDeleted = AssessmentQuestion::where('assessment_id', 4)->delete();
                $this->info("✓ Deleted $questionsDeleted duplicate question(s) from Assessment 4");
            }
            
            // Delete assessment 4
            $assessment4->delete();
            $this->info("✓ Deleted Assessment 4");
            $this->newLine();
            
            DB::commit();
            
            $this->info('✅ Successfully fixed duplicate assessment!');
            $this->newLine();
            $this->info('All data now points to Assessment ID 3');
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}

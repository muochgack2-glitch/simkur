<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\StudentAssessmentResponse;
use App\Models\StudentLearningProfile;
use App\Models\User;
use App\Services\DiagnosticProfileService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosticDummyResponseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('🔍 Starting Diagnostic Assessment Dummy Response Seeder...');

        // Get diagnostic assessment
        $assessment = Assessment::where('assessment_type', 'diagnostic')
            ->where('is_published', true)
            ->first();

        if (!$assessment) {
            $this->command->error('❌ Tidak ada Assessment Diagnostik yang aktif!');
            return;
        }

        $this->command->info("📝 Assessment: {$assessment->title}");

        // Get all students
        $students = User::where('role', 'siswa')
            ->whereNotNull('grade')
            ->whereNotNull('major')
            ->get();

        if ($students->isEmpty()) {
            $this->command->error('❌ Tidak ada siswa yang ditemukan!');
            return;
        }

        $this->command->info("👥 Total Siswa: {$students->count()}");

        // Clear existing responses and profiles for this assessment
        $this->command->warn('🗑️  Menghapus data lama...');
        StudentLearningProfile::where('assessment_id', $assessment->id)->delete();
        StudentAssessmentResponse::where('assessment_id', $assessment->id)->delete();

        $diagnosticService = new DiagnosticProfileService();
        $progressBar = $this->command->getOutput()->createProgressBar($students->count());
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($students as $student) {
            try {
                DB::beginTransaction();

                // Get questions for this student (filtered by major)
                $questions = $assessment->questions()
                    ->where(function($query) use ($student) {
                        $query->whereNull('major')
                              ->orWhere('major', '')
                              ->orWhere('major', $student->major);
                    })
                    ->orderBy('order_number')
                    ->get();

                if ($questions->isEmpty()) {
                    $this->command->newLine();
                    $this->command->warn("⚠️  Tidak ada soal untuk {$student->name} (Grade: {$student->grade}, Major: {$student->major})");
                    $errorCount++;
                    $progressBar->advance();
                    DB::rollBack();
                    continue;
                }

                // Generate responses based on student performance profile
                $performanceProfile = $this->determinePerformanceProfile($student);

                foreach ($questions as $question) {
                    $options = $question->options()->orderBy('order_number')->get();
                    
                    if ($options->isEmpty()) {
                        continue;
                    }

                    // Select option based on performance profile and aspect
                    $selectedOption = $this->selectOptionBasedOnProfile(
                        $options, 
                        $performanceProfile,
                        $question->aspect
                    );

                    StudentAssessmentResponse::create([
                        'assessment_id' => $assessment->id,
                        'user_id' => $student->id,
                        'assessment_question_id' => $question->id,
                        'selected_option_id' => $selectedOption->id,
                        'score' => $selectedOption->score_value,
                        'answered_at' => now()->subDays(rand(0, 7)),
                    ]);
                }

                // Calculate and save profile
                $profileData = $diagnosticService->calculateProfile($student, $assessment);
                $diagnosticService->saveProfile($student, $assessment, $profileData);

                DB::commit();
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->newLine();
                $this->command->error("❌ Error untuk {$student->name}: {$e->getMessage()}");
                $errorCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);

        // Summary
        $this->command->info("✅ Berhasil: {$successCount} siswa");
        if ($errorCount > 0) {
            $this->command->warn("⚠️  Gagal: {$errorCount} siswa");
        }

        // Show category distribution
        $this->showCategoryDistribution($assessment);

        $this->command->info('✨ Selesai!');
    }

    /**
     * Determine performance profile for student
     * Returns array with tendency for each aspect (1-5)
     */
    private function determinePerformanceProfile(User $student): array
    {
        // Create varied profiles based on student characteristics
        // We'll create 5 different profiles: Excellent, Good, Average, Below Average, Needs Support
        
        $profileTypes = [
            // 20% Excellent students
            'excellent' => [
                'kesiapan' => [4, 5],
                'motivasi' => [4, 5],
                'kemandirian' => [4, 5],
                'kolaborasi' => [4, 5],
                'preferensi' => [4, 5],
                'dunia_kerja' => [4, 5],
            ],
            // 30% Good students
            'good' => [
                'kesiapan' => [3, 4, 5],
                'motivasi' => [3, 4, 5],
                'kemandirian' => [3, 4],
                'kolaborasi' => [3, 4, 5],
                'preferensi' => [3, 4],
                'dunia_kerja' => [3, 4],
            ],
            // 30% Average students
            'average' => [
                'kesiapan' => [2, 3, 4],
                'motivasi' => [2, 3, 4],
                'kemandirian' => [2, 3],
                'kolaborasi' => [3, 4],
                'preferensi' => [2, 3, 4],
                'dunia_kerja' => [2, 3],
            ],
            // 15% Below Average students
            'below_average' => [
                'kesiapan' => [2, 3],
                'motivasi' => [2, 3],
                'kemandirian' => [1, 2, 3],
                'kolaborasi' => [2, 3],
                'preferensi' => [2, 3],
                'dunia_kerja' => [1, 2, 3],
            ],
            // 5% Needs Support students
            'needs_support' => [
                'kesiapan' => [1, 2],
                'motivasi' => [1, 2],
                'kemandirian' => [1, 2],
                'kolaborasi' => [1, 2, 3],
                'preferensi' => [1, 2],
                'dunia_kerja' => [1, 2],
            ],
        ];

        // Distribute students across profiles based on ID
        $rand = $student->id % 100;
        
        if ($rand < 20) {
            $profileType = 'excellent';
        } elseif ($rand < 50) {
            $profileType = 'good';
        } elseif ($rand < 80) {
            $profileType = 'average';
        } elseif ($rand < 95) {
            $profileType = 'below_average';
        } else {
            $profileType = 'needs_support';
        }

        return [
            'type' => $profileType,
            'aspects' => $profileTypes[$profileType],
        ];
    }

    /**
     * Select option based on performance profile
     */
    private function selectOptionBasedOnProfile($options, array $profile, ?string $aspect): object
    {
        $aspectProfile = $profile['aspects'][$aspect] ?? [2, 3, 4];
        
        // Add some randomness (80% follow profile, 20% random)
        if (rand(1, 100) <= 80) {
            // Follow profile tendency
            $targetScore = $aspectProfile[array_rand($aspectProfile)];
        } else {
            // Random selection
            $targetScore = rand(1, 5);
        }

        // Find option closest to target score
        $closestOption = null;
        $closestDiff = 999;

        foreach ($options as $option) {
            $diff = abs($option->score_value - $targetScore);
            if ($diff < $closestDiff) {
                $closestDiff = $diff;
                $closestOption = $option;
            }
        }

        return $closestOption ?? $options->first();
    }

    /**
     * Show category distribution summary
     */
    private function showCategoryDistribution(Assessment $assessment): void
    {
        $this->command->newLine();
        $this->command->info('📊 Distribusi Kategori:');

        $distribution = StudentLearningProfile::where('assessment_id', $assessment->id)
            ->select('diagnostic_category', DB::raw('count(*) as total'))
            ->groupBy('diagnostic_category')
            ->get();

        foreach ($distribution as $item) {
            $label = match($item->diagnostic_category) {
                'sangat_baik' => '🌟 Sangat Baik',
                'baik' => '👍 Baik',
                'cukup' => '😊 Cukup',
                'perlu_pendampingan' => '💪 Perlu Pendampingan',
                default => $item->diagnostic_category,
            };

            $this->command->line("   {$label}: {$item->total} siswa");
        }

        // Show average scores per aspect
        $this->command->newLine();
        $this->command->info('📈 Rata-rata Skor per Aspek:');

        $profiles = StudentLearningProfile::where('assessment_id', $assessment->id)->get();
        
        if ($profiles->isNotEmpty()) {
            $aspectKeys = array_keys($profiles->first()->aspect_scores ?? []);
            
            foreach ($aspectKeys as $aspect) {
                $scores = $profiles->pluck('aspect_scores')->pluck($aspect)->filter();
                $average = round($scores->avg(), 2);
                
                $label = match($aspect) {
                    'kesiapan' => '📚 Kesiapan Belajar',
                    'motivasi' => '🔥 Motivasi Belajar',
                    'kemandirian' => '💪 Kemandirian Belajar',
                    'kolaborasi' => '🤝 Kolaborasi & Komunikasi',
                    'preferensi' => '🎯 Preferensi Belajar',
                    'dunia_kerja' => '💼 Kesiapan Dunia Kerja',
                    default => $aspect,
                };

                $this->command->line("   {$label}: {$average}%");
            }
        }
    }
}

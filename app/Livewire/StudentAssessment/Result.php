<?php

namespace App\Livewire\StudentAssessment;

use App\Models\Assessment;
use App\Models\StudentLearningProfile;
use Livewire\Component;

class Result extends Component
{
    public Assessment $assessment;
    public ?StudentLearningProfile $profile = null;

    public function mount($id)
    {
        $this->assessment = Assessment::with(['academicYear', 'semester'])->findOrFail($id);
        
        // Get student's learning profile for this assessment
        $this->profile = StudentLearningProfile::where('user_id', auth()->id())
            ->where('assessment_id', $this->assessment->id)
            ->first();

        if (!$this->profile) {
            \Log::error('Profile not found on Result page', [
                'assessment_id' => $this->assessment->id,
                'user_id' => auth()->id()
            ]);

            session()->flash('error', 'Profil pembelajaran tidak ditemukan. Silakan hubungi admin.');
            // Don't use redirect in mount - it causes loops
            return;
        }

        \Log::info('Profile loaded successfully', ['profile_id' => $this->profile->id]);
    }

    public function getChartData()
    {
        if ($this->assessment->isVark()) {
            // VARK chart data
            return [
                'labels' => ['Visual', 'Auditory', 'Kinesthetic', 'Reading/Writing'],
                'data' => [
                    $this->profile->visual_score,
                    $this->profile->auditory_score,
                    $this->profile->kinesthetic_score,
                    $this->profile->reading_writing_score,
                ],
                'percentages' => $this->profile->getScoresPercentage(),
            ];
        } else {
            // Diagnostic chart data
            $aspectScores = $this->profile->aspect_scores ?? [];
            return [
                'labels' => array_map(fn($key) => ucfirst($key), array_keys($aspectScores)),
                'data' => array_values($aspectScores),
                'aspects' => $aspectScores,
            ];
        }
    }

    public function render()
    {
        // If profile not loaded, show error page instead of redirect loop
        if (!isset($this->profile) || !$this->profile) {
            return view('livewire.student-assessment.result-error')
                ->layout('components.layouts.app');
        }

        return view('livewire.student-assessment.result', [
            'chartData' => $this->getChartData(),
        ])->layout('components.layouts.app');
    }
}

<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use App\Models\StudentLearningProfile;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class StudentProfile extends Component
{
    public User $student;
    public Assessment $assessment;
    public StudentLearningProfile $profile;

    public function mount($userId, $assessmentId)
    {
        $this->student = User::findOrFail($userId);
        $this->assessment = Assessment::with(['academicYear', 'semester'])->findOrFail($assessmentId);
        
        // Get student's profile for this assessment
        $this->profile = StudentLearningProfile::where('user_id', $this->student->id)
            ->where('assessment_id', $this->assessment->id)
            ->firstOrFail();

        // Check permission: Only certain roles can view student profiles
        $currentUser = auth()->user();
        
        if (!$currentUser->canViewAllStudentProfiles()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat profil siswa.');
        }

        // If guru, can only view students in their grade
        if ($currentUser->isGuru() && $currentUser->grade !== $this->student->grade) {
            abort(403, 'Anda hanya dapat melihat profil siswa di kelas Anda.');
        }
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

    #[Layout('components.layouts.app')]
    #[Title('Detail Profil Siswa - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.assessment.student-profile', [
            'chartData' => $this->getChartData(),
        ]);
    }
}

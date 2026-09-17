<?php

namespace App\Livewire\StudentAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\StudentLearningProfile;
use Livewire\Component;
use App\Livewire\BaseComponent;

class Result extends BaseComponent
{
    public Assessment $assessment;
    public ?StudentLearningProfile $profile = null;
    public ?AssessmentStudentSession $session = null;

    public function mount($id)
    {
        $this->assessment = Assessment::with(['academicYear', 'semester'])->findOrFail($id);

        $isLearningProfile = $this->assessment->isVark() || $this->assessment->isDiagnostic();

        if ($isLearningProfile) {
            // VARK / Diagnostik: cari profil gaya belajar
            $this->profile = StudentLearningProfile::where('user_id', auth()->id())
                ->where('assessment_id', $this->assessment->id)
                ->first();

            if (!$this->profile) {
                \Log::error('StudentLearningProfile not found on Result page', [
                    'assessment_id' => $this->assessment->id,
                    'user_id'       => auth()->id(),
                ]);
                session()->flash('error', 'Profil pembelajaran tidak ditemukan. Silakan selesaikan asesmen terlebih dahulu.');
            }
        } else {
            // Quiz guru: cari sesi kuis yang sudah submit
            $this->session = AssessmentStudentSession::where('assessment_id', $this->assessment->id)
                ->where('user_id', auth()->id())
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->first();

            if (!$this->session) {
                \Log::warning('AssessmentStudentSession not found or not submitted on Result page', [
                    'assessment_id' => $this->assessment->id,
                    'user_id'       => auth()->id(),
                ]);
                session()->flash('error', 'Hasil kuis tidak ditemukan. Pastikan Anda sudah mengumpulkan jawaban.');
            }
        }
    }

    public function getChartData(): array
    {
        if (!$this->profile) return [];

        if ($this->assessment->isVark()) {
            return [
                'labels'      => ['Visual', 'Auditory', 'Kinesthetic', 'Reading/Writing'],
                'data'        => [
                    $this->profile->visual_score,
                    $this->profile->auditory_score,
                    $this->profile->kinesthetic_score,
                    $this->profile->reading_writing_score,
                ],
                'percentages' => $this->profile->getScoresPercentage(),
            ];
        } else {
            $aspectScores = $this->profile->aspect_scores ?? [];
            return [
                'labels'  => array_map(fn($key) => ucfirst($key), array_keys($aspectScores)),
                'data'    => array_values($aspectScores),
                'aspects' => $aspectScores,
            ];
        }
    }

    public function render()
    {
        $isLearningProfile = $this->assessment->isVark() || $this->assessment->isDiagnostic();

        if ($isLearningProfile) {
            if (!$this->profile) {
                return view('livewire.student-assessment.result-error')
                    ->layout('components.layouts.app');
            }
            return view('livewire.student-assessment.result', [
                'chartData' => $this->getChartData(),
            ])->layout('components.layouts.app');
        } else {
            // Quiz result
            return view('livewire.student-assessment.quiz-result', [
                'session' => $this->session,
            ])->layout('components.layouts.app');
        }
    }
}
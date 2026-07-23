<?php

namespace App\Livewire\StudentAssessment;

use App\Models\Assessment;
use App\Models\StudentLearningProfile;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $student = auth()->user();
        
        // Get available assessments for this student's grade
        $assessments = Assessment::with(['academicYear', 'semester'])
            ->active()
            ->ongoing()
            ->forGrade($student->grade)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($assessment) use ($student) {
                // Check if student has completed this assessment
                $profile = StudentLearningProfile::where('user_id', $student->id)
                    ->where('assessment_id', $assessment->id)
                    ->first();
                
                $assessment->is_completed = $profile !== null;
                $assessment->completion_date = $profile?->completed_at;
                $assessment->profile = $profile;
                
                return $assessment;
            });

        return view('livewire.student-assessment.index', [
            'assessments' => $assessments,
        ])->layout('components.layouts.app');
    }
}

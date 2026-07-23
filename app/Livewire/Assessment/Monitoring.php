<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use App\Models\User;
use Livewire\Component;

class Monitoring extends Component
{
    public Assessment $assessment;
    public $filterGrade = 'all';
    public $filterStatus = 'all'; // all, completed, pending

    public function mount($id)
    {
        $this->assessment = Assessment::with(['academicYear', 'semester'])->findOrFail($id);
    }

    public function render()
    {
        // Get all students for this assessment's target grades
        $studentsQuery = User::where('role', 'siswa')
            ->when($this->assessment->target_grades, function ($query) {
                $query->whereIn('grade', $this->assessment->target_grades);
            })
            ->when($this->filterGrade !== 'all', function ($query) {
                $query->where('grade', $this->filterGrade);
            })
            ->with(['learningProfiles' => function ($query) {
                $query->where('assessment_id', $this->assessment->id);
            }])
            ->orderBy('grade')
            ->orderBy('name')
            ->get();

        // Filter by completion status
        if ($this->filterStatus === 'completed') {
            $studentsQuery = $studentsQuery->filter(function ($student) {
                return $student->learningProfiles->isNotEmpty();
            });
        } elseif ($this->filterStatus === 'pending') {
            $studentsQuery = $studentsQuery->filter(function ($student) {
                return $student->learningProfiles->isEmpty();
            });
        }

        // Calculate statistics
        $totalStudents = User::where('role', 'siswa')
            ->when($this->assessment->target_grades, function ($query) {
                $query->whereIn('grade', $this->assessment->target_grades);
            })
            ->count();

        $completedCount = $this->assessment->studentProfiles()->count();
        $completionPercentage = $totalStudents > 0 ? round(($completedCount / $totalStudents) * 100, 2) : 0;

        return view('livewire.assessment.monitoring', [
            'students' => $studentsQuery,
            'totalStudents' => $totalStudents,
            'completedCount' => $completedCount,
            'pendingCount' => $totalStudents - $completedCount,
            'completionPercentage' => $completionPercentage,
            'availableGrades' => $this->assessment->target_grades ?? ['X', 'XI', 'XII'],
        ])->layout('components.layouts.app');
    }
}

<?php

namespace App\Livewire\Assessment;

use App\Models\StudentLearningProfile;
use App\Models\User;
use App\Models\Semester;
use App\Services\LearningProfileService;
use App\Services\DiagnosticProfileService;
use Livewire\Component;

class ClassReport extends Component
{
    public $selectedGrade = 'X';
    public $selectedMajor = 'all'; // all, MPLB, AKL, BUSANA
    public $selectedSemester;
    public $availableGrades = ['X', 'XI', 'XII'];
    public $availableMajors = ['MPLB', 'AKL', 'BUSANA'];
    public $activeTab = 'vark'; // vark or diagnostic

    public function mount()
    {
        // Get active semester
        $activeSemester = Semester::whereHas('academicYear', function ($query) {
            $query->where('is_active', true);
        })->first();

        $this->selectedSemester = $activeSemester?->id;
    }

    public function updatedSelectedGrade()
    {
        $this->dispatch('gradeChanged');
    }

    public function updatedSelectedMajor()
    {
        $this->dispatch('majorChanged');
    }

    public function updatedSelectedSemester()
    {
        $this->dispatch('semesterChanged');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        if ($this->activeTab === 'vark') {
            return $this->renderVarkReport();
        } else {
            return $this->renderDiagnosticReport();
        }
    }

    private function renderVarkReport()
    {
        $profileService = new LearningProfileService();
        
        // Get class statistics
        $statistics = $profileService->getClassStatistics($this->selectedGrade, $this->selectedSemester);

        // Get all students in this grade with their VARK profiles
        $studentsQuery = User::where('role', 'siswa')
            ->where('grade', $this->selectedGrade);

        // Filter by major if not 'all'
        if ($this->selectedMajor !== 'all') {
            $studentsQuery->where('major', $this->selectedMajor);
        }

        $students = $studentsQuery->with(['learningProfiles' => function ($query) {
                $query->where('semester_id', $this->selectedSemester)
                      ->whereHas('assessment', function($q) {
                          $q->where('assessment_type', 'vark');
                      })
                      ->with('assessment');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $profile = $student->learningProfiles->first();
                $student->profile = $profile;
                $student->has_profile = $profile !== null;
                return $student;
            });

        // Get available semesters
        $semesters = Semester::whereHas('academicYear', function ($query) {
            $query->where('is_archived', false);
        })
        ->with('academicYear')
        ->orderBy('start_date', 'desc')
        ->get();

        return view('livewire.assessment.class-report', [
            'statistics' => $statistics,
            'students' => $students,
            'semesters' => $semesters,
            'reportType' => 'vark',
        ])->layout('components.layouts.app');
    }

    private function renderDiagnosticReport()
    {
        $diagnosticService = new DiagnosticProfileService();
        
        // Get the first diagnostic assessment for this semester
        $diagnosticAssessment = \App\Models\Assessment::where('assessment_type', 'diagnostic')
            ->where('semester_id', $this->selectedSemester)
            ->first();

        if (!$diagnosticAssessment) {
            $statistics = [
                'total_students' => 0,
                'completed_students' => 0,
                'completion_percentage' => 0,
                'average_aspects' => [],
                'category_distribution' => [],
                'students_need_support' => [],
            ];
        } else {
            $statistics = $diagnosticService->getClassReport($diagnosticAssessment, $this->selectedGrade, $this->selectedMajor);
        }

        // Get all students in this grade with their Diagnostic profiles
        $studentsQuery = User::where('role', 'siswa')
            ->where('grade', $this->selectedGrade);

        // Filter by major if not 'all'
        if ($this->selectedMajor !== 'all') {
            $studentsQuery->where('major', $this->selectedMajor);
        }

        $students = $studentsQuery->with(['learningProfiles' => function ($query) {
                $query->where('semester_id', $this->selectedSemester)
                      ->whereHas('assessment', function($q) {
                          $q->where('assessment_type', 'diagnostic');
                      })
                      ->with('assessment');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $profile = $student->learningProfiles->first();
                $student->profile = $profile;
                $student->has_profile = $profile !== null;
                return $student;
            });

        // Get available semesters
        $semesters = Semester::whereHas('academicYear', function ($query) {
            $query->where('is_archived', false);
        })
        ->with('academicYear')
        ->orderBy('start_date', 'desc')
        ->get();

        return view('livewire.assessment.class-report', [
            'statistics' => $statistics,
            'students' => $students,
            'semesters' => $semesters,
            'reportType' => 'diagnostic',
            'diagnosticAssessment' => $diagnosticAssessment,
        ])->layout('components.layouts.app');
    }
}

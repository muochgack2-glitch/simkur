<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\AcademicYear;
use App\Models\TeacherSubjectRequirement;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Monitoring extends Component
{
    use WithPagination;

    public $academicYearId;
    public $teacherFilter = '';
    public $subjectFilter = '';
    public $statusFilter = 'all'; // all, complete, incomplete
    public $search = '';

    public function mount()
    {
        $this->academicYearId = AcademicYear::where('is_active', true)->value('id');
    }

    public function syncRequirements()
    {
        TeacherSubjectRequirement::syncFromSchedules($this->academicYearId);
        
        session()->flash('success', 'Data requirements berhasil disinkronkan dari jadwal mengajar.');
    }

    public function refreshAll()
    {
        $requirements = TeacherSubjectRequirement::forAcademicYear($this->academicYearId)->get();
        
        foreach ($requirements as $requirement) {
            TeacherSubjectRequirement::updateFromMaterials(
                $requirement->teacher_id,
                $requirement->subject_id,
                $requirement->academic_year_id
            );
        }

        session()->flash('success', 'Semua data berhasil direfresh dari perangkat ajar yang diupload.');
    }

    public function render()
    {
        $query = TeacherSubjectRequirement::query()
            ->with(['teacher', 'subject', 'academicYear'])
            ->forAcademicYear($this->academicYearId);

        // Filter by teacher
        if ($this->teacherFilter) {
            $query->where('teacher_id', $this->teacherFilter);
        }

        // Filter by subject
        if ($this->subjectFilter) {
            $query->where('subject_id', $this->subjectFilter);
        }

        // Filter by status
        if ($this->statusFilter === 'complete') {
            $query->complete();
        } elseif ($this->statusFilter === 'incomplete') {
            $query->incomplete();
        }

        // Search
        if ($this->search) {
            $query->whereHas('teacher', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhereHas('subject', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $requirements = $query->orderBy('completion_percentage', 'asc')
            ->orderBy('teacher_id')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => TeacherSubjectRequirement::forAcademicYear($this->academicYearId)->count(),
            'complete' => TeacherSubjectRequirement::forAcademicYear($this->academicYearId)->complete()->count(),
            'incomplete' => TeacherSubjectRequirement::forAcademicYear($this->academicYearId)->incomplete()->count(),
            'avg_completion' => round(TeacherSubjectRequirement::forAcademicYear($this->academicYearId)->avg('completion_percentage'), 1),
        ];

        return view('livewire.teaching-material.monitoring', [
            'requirements' => $requirements,
            'stats' => $stats,
            'teachers' => User::where('role', 'guru')->orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }
}

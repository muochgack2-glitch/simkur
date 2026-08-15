<?php

namespace App\Livewire\PklField;

use App\Models\AcademicYear;
use App\Models\PklCompany;
use App\Models\PklPlacement;
use App\Models\PklPlacementMove;
use App\Models\User;
use Livewire\Component;

class PlacementManager extends Component
{
    public $academicYearId = '';
    public $filterCompany = '';
    public $filterClass = '';
    public $search = '';

    // Assign form
    public $showAssign = false;
    public $assignStudentId = '';
    public $assignCompanyId = '';
    public $assignNotes = '';

    // Move form
    public $showMove = false;
    public $movePlacementId = null;
    public $moveToCompanyId = '';
    public $moveReason = '';

    public $confirmDelete = null;

    public function mount()
    {
        $activity = AcademicYear::latest()->first();
        $this->academicYearId = $activity?->id ?? '';
    }

    public function openAssign()
    {
        $this->reset(['assignStudentId', 'assignCompanyId', 'assignNotes']);
        $this->showAssign = true;
    }

    public function assignStudent()
    {
        $this->validate([
            'assignStudentId' => 'required|exists:users,id',
            'assignCompanyId' => 'required|exists:pkl_companies,id',
        ]);

        $company = PklCompany::findOrFail($this->assignCompanyId);
        if ($company->isFull($this->academicYearId)) {
            session()->flash('error', "Kapasitas {$company->name} sudah penuh!");
            return;
        }

        $exists = PklPlacement::where('academic_year_id', $this->academicYearId)
            ->where('student_id', $this->assignStudentId)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            session()->flash('error', 'Siswa sudah ditempatkan di DU/DI lain!');
            return;
        }

        $activity = AcademicYear::find($this->academicYearId);
        PklPlacement::create([
            'academic_year_id' => $this->academicYearId,
            'student_id' => $this->assignStudentId,
            'pkl_company_id' => $this->assignCompanyId,
            'start_date' => $activity?->start_date,
            'end_date' => $activity?->end_date,
            'status' => 'active',
            'notes' => $this->assignNotes,
        ]);

        session()->flash('success', 'Siswa berhasil ditempatkan');
        $this->showAssign = false;
    }

    public function openMove($placementId)
    {
        $this->movePlacementId = $placementId;
        $this->reset(['moveToCompanyId', 'moveReason']);
        $this->showMove = true;
    }

    public function moveStudent()
    {
        $this->validate([
            'moveToCompanyId' => 'required|exists:pkl_companies,id',
            'moveReason' => 'required|string|min:5',
        ]);

        $placement = PklPlacement::findOrFail($this->movePlacementId);
        $newCompany = PklCompany::findOrFail($this->moveToCompanyId);

        if ($newCompany->isFull($this->academicYearId)) {
            session()->flash('error', "Kapasitas {$newCompany->name} sudah penuh!");
            return;
        }

        PklPlacementMove::create([
            'pkl_placement_id' => $placement->id,
            'from_company_id' => $placement->pkl_company_id,
            'to_company_id' => $this->moveToCompanyId,
            'reason' => $this->moveReason,
            'moved_by' => auth()->id(),
        ]);

        $placement->update([
            'pkl_company_id' => $this->moveToCompanyId,
        ]);

        session()->flash('success', 'Siswa berhasil dipindahkan');
        $this->showMove = false;
    }

    public function removePlacement($id)
    {
        PklPlacement::findOrFail($id)->update(['status' => 'cancelled']);
        session()->flash('success', 'Penempatan dibatalkan');
        $this->confirmDelete = null;
    }

    public function render()
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $companies = PklCompany::active()->orderBy('name')->get();

        $query = PklPlacement::with(['student', 'company', 'moves'])
            ->where('academic_year_id', $this->academicYearId)
            ->where('status', '!=', 'cancelled');

        if ($this->filterCompany) $query->where('pkl_company_id', $this->filterCompany);
        if ($this->search) {
            $search = $this->search;
            $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $placements = $query->get()->sortBy(fn($p) => $p->student->name ?? '');

        // Unplaced students (kelas XII PKL)
        $placedStudentIds = PklPlacement::where('academic_year_id', $this->academicYearId)
            ->whereIn('status', ['active'])
            ->pluck('student_id');

        $unplacedStudents = collect();
        // Get all XII students from active classes
        $classes = \App\Models\SchoolClass::where(function($q) {
                $q->where('grade', '12')
                  ->orWhere('grade', 'XII')
                  ->orWhere('name', 'like', '%XII%');
            })
            ->when($this->academicYearId, fn($q) => $q->where('academic_year_id', $this->academicYearId))
            ->with('students')
            ->get();
        foreach ($classes as $class) {
            foreach ($class->students as $student) {
                if (!$placedStudentIds->contains($student->id)) {
                    $unplacedStudents->push($student);
                }
            }
        }

        // Stats
        $stats = [
            'placed' => $placements->count(),
            'unplaced' => $unplacedStudents->count(),
            'companies' => $companies->count(),
        ];

        return view('livewire.pkl-field.placement-manager', [
            'academicYears' => $academicYears,
            'companies' => $companies,
            'placements' => $placements,
            'unplacedStudents' => $unplacedStudents->sortBy('name'),
            'stats' => $stats,
        ]);
    }
}
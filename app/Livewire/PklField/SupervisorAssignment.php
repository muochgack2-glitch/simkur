<?php

namespace App\Livewire\PklField;

use App\Models\AcademicYear;
use App\Models\PklCompany;
use App\Models\PklCompanySupervisor;
use App\Models\User;
use Livewire\Component;

class SupervisorAssignment extends Component
{
    public $academicYearId = '';
    public $showAssign = false;
    public $assignTeacherId = '';
    public $assignCompanyIds = [];
    public $confirmDelete = null;

    public function mount()
    {
        $activity = AcademicYear::latest()->first();
        $this->academicYearId = $activity?->id ?? '';
    }

    public function openAssign()
    {
        $this->reset(['assignTeacherId', 'assignCompanyIds']);
        $this->showAssign = true;
    }

    public function assignSupervisor()
    {
        $this->validate([
            'assignTeacherId' => 'required|exists:users,id',
            'assignCompanyIds' => 'required|array|min:1',
        ]);

        foreach ($this->assignCompanyIds as $companyId) {
            PklCompanySupervisor::firstOrCreate([
                'academic_year_id' => $this->academicYearId,
                'teacher_id' => $this->assignTeacherId,
                'pkl_company_id' => $companyId,
            ]);
        }

        session()->flash('success', 'Guru pembimbing berhasil di-assign');
        $this->showAssign = false;
    }

    public function removeAssignment($id)
    {
        PklCompanySupervisor::findOrFail($id)->delete();
        session()->flash('success', 'Assignment dihapus');
        $this->confirmDelete = null;
    }

    public function render()
    {
        $activities = AcademicYear::orderByDesc('start_date')->get();
        $companies = PklCompany::active()->orderBy('name')->get();

        // Teachers who teach kelas XII (from teaching schedule)
        $teachers = User::where('role', 'guru')
            ->whereHas('teachingSchedules', function ($q) {
                $q->whereHas('kelas', fn($k) => $k->where('tingkat', '12'));
            })
            ->orderBy('name')
            ->get();

        // Current assignments grouped by teacher
        $assignments = PklCompanySupervisor::with(['teacher', 'company'])
            ->where('academic_year_id', $this->academicYearId)
            ->get()
            ->groupBy('teacher_id');

        return view('livewire.pkl-field.supervisor-assignment', [
            'activities' => $activities,
            'companies' => $companies,
            'teachers' => $teachers,
            'assignments' => $assignments,
        ]);
    }
}
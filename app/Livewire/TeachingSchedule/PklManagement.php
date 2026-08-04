<?php

namespace App\Livewire\TeachingSchedule;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\TeachingSchedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PklManagement extends Component
{
    public $classes = [];
    public $academicYear;
    public $selectedClasses = [];
    public $showConfirmation = false;
    public $action = ''; // 'activate' or 'deactivate'
    public $affectedTeachers = [];
    public $affectedSchedulesCount = 0;

    public function mount()
    {
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        $this->loadClasses();
    }

    public function loadClasses()
    {
        if (!$this->academicYear) {
            $this->classes = [];
            return;
        }

        // Get all classes with schedule count
        $classes = SchoolClass::orderBy('name')->get();
        
        $this->classes = $classes->map(function ($class) {
            $activeCount = TeachingSchedule::where('class_id', $class->id)
                ->where('academic_year_id', $this->academicYear->id)
                ->where('is_active', true)
                ->count();
            
            $inactiveCount = TeachingSchedule::where('class_id', $class->id)
                ->where('academic_year_id', $this->academicYear->id)
                ->where('is_active', false)
                ->count();
            
            $totalCount = $activeCount + $inactiveCount;
            
            return [
                'id' => $class->id,
                'name' => $class->name,
                'active_schedules' => $activeCount,
                'inactive_schedules' => $inactiveCount,
                'total_schedules' => $totalCount,
                'is_active' => $activeCount > 0,
                'is_xii' => str_starts_with($class->name, 'XII'),
            ];
        })->toArray();
    }

    public function toggleClass($classId)
    {
        if (in_array($classId, $this->selectedClasses)) {
            $this->selectedClasses = array_diff($this->selectedClasses, [$classId]);
        } else {
            $this->selectedClasses[] = $classId;
        }
    }

    public function selectAllXII()
    {
        $xiiClasses = collect($this->classes)
            ->filter(fn($c) => $c['is_xii'])
            ->pluck('id')
            ->toArray();
        
        $this->selectedClasses = array_unique(array_merge($this->selectedClasses, $xiiClasses));
    }

    public function deselectAll()
    {
        $this->selectedClasses = [];
    }

    public function prepareDeactivate()
    {
        if (empty($this->selectedClasses)) {
            session()->flash('error', 'Pilih minimal 1 kelas untuk dinonaktifkan.');
            return;
        }

        $this->action = 'deactivate';
        $this->loadAffectedData();
        $this->showConfirmation = true;
    }

    public function prepareActivate()
    {
        if (empty($this->selectedClasses)) {
            session()->flash('error', 'Pilih minimal 1 kelas untuk diaktifkan.');
            return;
        }

        $this->action = 'activate';
        $this->loadAffectedData();
        $this->showConfirmation = true;
    }

    protected function loadAffectedData()
    {
        // Get affected schedules count
        $this->affectedSchedulesCount = TeachingSchedule::whereIn('class_id', $this->selectedClasses)
            ->where('academic_year_id', $this->academicYear->id)
            ->where('is_active', $this->action === 'deactivate')
            ->count();

        // Get affected teachers
        $teacherIds = TeachingSchedule::whereIn('class_id', $this->selectedClasses)
            ->where('academic_year_id', $this->academicYear->id)
            ->where('is_active', $this->action === 'deactivate')
            ->distinct()
            ->pluck('teacher_id');

        $this->affectedTeachers = \App\Models\User::whereIn('id', $teacherIds)
            ->orderBy('name')
            ->get()
            ->map(fn($t) => $t->name)
            ->toArray();
    }

    public function confirmAction()
    {
        if (empty($this->selectedClasses)) {
            return;
        }

        try {
            DB::beginTransaction();

            $newStatus = $this->action === 'activate';

            TeachingSchedule::whereIn('class_id', $this->selectedClasses)
                ->where('academic_year_id', $this->academicYear->id)
                ->update(['is_active' => $newStatus]);

            DB::commit();

            $classNames = SchoolClass::whereIn('id', $this->selectedClasses)
                ->pluck('name')
                ->implode(', ');

            $actionText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            
            session()->flash('success', "Jadwal kelas {$classNames} berhasil {$actionText}!");

            $this->reset(['selectedClasses', 'showConfirmation', 'action', 'affectedTeachers', 'affectedSchedulesCount']);
            $this->loadClasses();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal mengupdate jadwal: ' . $e->getMessage());
        }
    }

    public function cancelAction()
    {
        $this->reset(['showConfirmation', 'action', 'affectedTeachers', 'affectedSchedulesCount']);
    }

    public function render()
    {
        return view('livewire.teaching-schedule.pkl-management')
            ->layout('components.layouts.app', ['title' => 'Manajemen PKL']);
    }
}

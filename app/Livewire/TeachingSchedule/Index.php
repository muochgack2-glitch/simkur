<?php

namespace App\Livewire\TeachingSchedule;

use App\Models\TeachingSchedule;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\BaseComponent;

class Index extends BaseComponent
{
    use WithPagination;

    public $search = '';
    public $filterTeacher = '';
    public $filterClass = '';
    public $filterDay = '';
    
    // Form fields
    public $showModal = false;
    public $editMode = false;
    public $scheduleId = null;
    public $teacher_id = '';
    public $class_id = '';
    public $subject_id = '';
    public $day_of_week = '';
    public $start_time_slot_id = '';
    public $end_time_slot_id = '';
    public $is_active = true;
    
    // Computed
    public $endTimeSlots = [];
    public $totalJP = 0;
    
    // Dropdown data
    public $teachers = [];
    public $classes = [];
    public $subjects = [];
    public $timeSlots = [];
    public $academicYear = null;

    protected function rules()
    {
        return [
            'teacher_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time_slot_id' => 'required|exists:time_slots,id',
            'end_time_slot_id' => 'required|exists:time_slots,id',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->loadDropdownData();
    }

    public function loadDropdownData()
    {
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        
        $this->teachers = User::whereIn('role', ['guru', 'waka_kurikulum', 'kepala_sekolah'])
            ->orderBy('name')
            ->get();
        
        $this->classes = SchoolClass::where('academic_year_id', $this->academicYear->id ?? null)
            ->orderBy('name')
            ->get();
        
        $this->subjects = Subject::orderBy('name')->get();
        
        // Load time slots based on selected day
        $this->loadTimeSlots();
    }
    
    public function loadTimeSlots()
    {
        if ($this->day_of_week) {
            // Filter by selected day
            $this->timeSlots = TimeSlot::active()
                ->where('day_of_week', $this->day_of_week)
                ->ordered()
                ->get();
        } else {
            // No day selected, show empty or all
            $this->timeSlots = collect();
        }
    }
    
    public function updatedDayOfWeek()
    {
        // Reset time slot selection when day changes
        $this->start_time_slot_id = '';
        $this->end_time_slot_id = '';
        $this->endTimeSlots = [];
        $this->totalJP = 0;
        // Reload time slots for the new day
        $this->loadTimeSlots();
    }
    
    public function updatedStartTimeSlotId()
    {
        // Reset end time slot when start changes
        $this->end_time_slot_id = '';
        $this->loadEndTimeSlots();
        $this->calculateTotalJP();
    }
    
    public function updatedEndTimeSlotId()
    {
        $this->calculateTotalJP();
    }
    
    public function loadEndTimeSlots()
    {
        if ($this->start_time_slot_id && $this->day_of_week) {
            $startSlot = TimeSlot::find($this->start_time_slot_id);
            
            if ($startSlot) {
                // Get time slots with order >= start slot order
                $this->endTimeSlots = TimeSlot::active()
                    ->where('day_of_week', $this->day_of_week)
                    ->where('order', '>=', $startSlot->order)
                    ->ordered()
                    ->get();
            }
        } else {
            $this->endTimeSlots = collect();
        }
    }
    
    public function calculateTotalJP()
    {
        if ($this->start_time_slot_id && $this->end_time_slot_id && $this->day_of_week) {
            $startSlot = TimeSlot::find($this->start_time_slot_id);
            $endSlot = TimeSlot::find($this->end_time_slot_id);
            
            if ($startSlot && $endSlot) {
                // Get all slots between start and end (inclusive)
                $slots = TimeSlot::active()
                    ->where('day_of_week', $this->day_of_week)
                    ->where('order', '>=', $startSlot->order)
                    ->where('order', '<=', $endSlot->order)
                    ->get();
                
                // Count only teaching slots (skip order 1, 5, 10)
                $this->totalJP = $slots->filter(function($slot) {
                    return $slot->order > 1 && $slot->order != 5 && $slot->order != 10;
                })->count();
            } else {
                $this->totalJP = 0;
            }
        } else {
            $this->totalJP = 0;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterTeacher()
    {
        $this->resetPage();
    }

    public function updatedFilterClass()
    {
        $this->resetPage();
    }

    public function updatedFilterDay()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['scheduleId', 'teacher_id', 'class_id', 'subject_id', 'day_of_week', 'start_time_slot_id', 'end_time_slot_id', 'is_active']);
        $this->is_active = true;
        $this->endTimeSlots = [];
        $this->totalJP = 0;
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $schedule = TeachingSchedule::findOrFail($id);
        
        $this->scheduleId = $schedule->id;
        $this->teacher_id = $schedule->teacher_id;
        $this->class_id = $schedule->class_id;
        $this->subject_id = $schedule->subject_id;
        $this->day_of_week = $schedule->day_of_week;
        $this->start_time_slot_id = $schedule->time_slot_id;
        $this->end_time_slot_id = $schedule->time_slot_id; // Same for single slot edit
        $this->is_active = $schedule->is_active;
        
        // Load time slots for the selected day
        $this->loadTimeSlots();
        $this->loadEndTimeSlots();
        $this->calculateTotalJP();
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();
        
        if (!$this->academicYear) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif!');
            return;
        }
        
        // Validate start <= end
        $startSlot = TimeSlot::find($this->start_time_slot_id);
        $endSlot = TimeSlot::find($this->end_time_slot_id);
        
        if ($startSlot->order > $endSlot->order) {
            session()->flash('error', 'Jam selesai harus >= jam mulai!');
            return;
        }
        
        try {
            if ($this->editMode) {
                // For edit mode, just update the single record
                $schedule = TeachingSchedule::findOrFail($this->scheduleId);
                $schedule->update([
                    'teacher_id' => $this->teacher_id,
                    'class_id' => $this->class_id,
                    'subject_id' => $this->subject_id,
                    'day_of_week' => $this->day_of_week,
                    'time_slot_id' => $this->start_time_slot_id,
                    'is_active' => $this->is_active,
                ]);
                
                session()->flash('success', 'Jadwal berhasil diupdate!');
            } else {
                // For create mode, create multiple records
                $slots = TimeSlot::active()
                    ->where('day_of_week', $this->day_of_week)
                    ->where('order', '>=', $startSlot->order)
                    ->where('order', '<=', $endSlot->order)
                    ->ordered()
                    ->get();
                
                $created = 0;
                foreach ($slots as $slot) {
                    // Skip pre-class activities (order <= 1) and break times (order 5, 10)
                    if ($slot->order <= 1 || $slot->order == 5 || $slot->order == 10) {
                        continue;
                    }
                    
                    TeachingSchedule::create([
                        'teacher_id' => $this->teacher_id,
                        'class_id' => $this->class_id,
                        'subject_id' => $this->subject_id,
                        'academic_year_id' => $this->academicYear->id,
                        'day_of_week' => $this->day_of_week,
                        'time_slot_id' => $slot->id,
                        'is_active' => $this->is_active,
                    ]);
                    
                    $created++;
                }
                
                session()->flash('success', "Jadwal berhasil ditambahkan! ({$created} JP)");
            }
            
            $this->showModal = false;
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            TeachingSchedule::findOrFail($id)->delete();
            session()->flash('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        $schedule = TeachingSchedule::findOrFail($id);
        $schedule->update(['is_active' => !$schedule->is_active]);
        
        session()->flash('success', 'Status jadwal berhasil diubah!');
    }

    #[Layout('components.layouts.app')]
    #[Title('Jadwal Mengajar - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = TeachingSchedule::with(['teacher', 'schoolClass', 'subject', 'timeSlot'])
            ->forAcademicYear($this->academicYear->id ?? null);
        
        if ($this->filterTeacher) {
            $query->where('teacher_id', $this->filterTeacher);
        }
        
        if ($this->filterClass) {
            $query->where('class_id', $this->filterClass);
        }
        
        if ($this->filterDay) {
            $query->where('day_of_week', $this->filterDay);
        }
        
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('teacher', function($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('schoolClass', function($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('subject', function($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }
        
        $schedules = $query->orderBy('day_of_week')
            ->paginate(20);
        
        return view('livewire.teaching-schedule.index', [
            'schedules' => $schedules,
        ]);
    }
}

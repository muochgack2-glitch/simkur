<?php

namespace App\Livewire\Activity;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Semester;
use App\Models\Setting;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $activity_type_id = '';
    public $start_date = '';
    public $end_date = '';
    public $semester_id = '';
    public $description = '';
    public $color = '';
    
    // Target Grades
    public $targetAllGrades = false;
    public $targetGradeX = false;
    public $targetGradeXI = false;
    public $targetGradeXII = false;
    
    public $conflicts = [];
    public $weekendWarning = false;
    public $hasWeekendDays = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'activity_type_id' => 'required|exists:activity_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'semester_id' => 'required|exists:semesters,id',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama kegiatan wajib diisi.',
        'activity_type_id.required' => 'Jenis kegiatan wajib dipilih.',
        'activity_type_id.exists' => 'Jenis kegiatan tidak valid.',
        'start_date.required' => 'Tanggal mulai wajib diisi.',
        'start_date.date' => 'Format tanggal mulai tidak valid.',
        'end_date.required' => 'Tanggal selesai wajib diisi.',
        'end_date.date' => 'Format tanggal selesai tidak valid.',
        'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        'semester_id.required' => 'Semester wajib dipilih.',
        'semester_id.exists' => 'Semester tidak valid.',
        'description.max' => 'Deskripsi maksimal 500 karakter.',
        'color.regex' => 'Format warna tidak valid (harus HEX: #RRGGBB).',
    ];

    public function mount()
    {
        // Set today as default start date
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        
        // Default: all grades
        $this->targetAllGrades = true;
        $this->targetGradeX = true;
        $this->targetGradeXI = true;
        $this->targetGradeXII = true;
    }

    public function updatedTargetAllGrades($value)
    {
        if ($value) {
            $this->targetGradeX = true;
            $this->targetGradeXI = true;
            $this->targetGradeXII = true;
        }
    }

    public function updatedTargetGradeX()
    {
        $this->checkAllGrades();
    }

    public function updatedTargetGradeXI()
    {
        $this->checkAllGrades();
    }

    public function updatedTargetGradeXII()
    {
        $this->checkAllGrades();
    }

    protected function checkAllGrades()
    {
        $this->targetAllGrades = $this->targetGradeX && 
                                  $this->targetGradeXI && 
                                  $this->targetGradeXII;
    }

    public function updatedActivityTypeId($value)
    {
        // Auto-fill color from activity type
        if ($value) {
            $activityType = ActivityType::find($value);
            if ($activityType && !$this->color) {
                $this->color = $activityType->color;
            }
        }
    }

    public function updatedStartDate($value)
    {
        // Auto-detect semester
        $this->detectSemester();
        
        // Check conflicts
        $this->checkConflicts();
        
        // Check weekend
        $this->checkWeekend();
    }

    public function updatedEndDate($value)
    {
        // Check conflicts
        $this->checkConflicts();
        
        // Check weekend
        $this->checkWeekend();
    }
    
    public function checkWeekend()
    {
        if (!$this->start_date || !$this->end_date) {
            $this->weekendWarning = false;
            $this->hasWeekendDays = false;
            return;
        }
        
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        $this->hasWeekendDays = false;
        
        // Check if any day in the range is weekend
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                $this->hasWeekendDays = true;
                $this->weekendWarning = true;
                break;
            }
        }
        
        if (!$this->hasWeekendDays) {
            $this->weekendWarning = false;
        }
    }

    public function detectSemester()
    {
        if (!$this->start_date) {
            return;
        }

        $date = Carbon::parse($this->start_date);
        
        $semester = Semester::whereHas('academicYear', function ($query) {
            $query->where('is_active', true);
        })
        ->where('start_date', '<=', $date)
        ->where('end_date', '>=', $date)
        ->first();

        if ($semester) {
            $this->semester_id = $semester->id;
        }
    }

    public function checkConflicts()
    {
        if (!$this->start_date || !$this->end_date) {
            $this->conflicts = [];
            return;
        }

        // Only check if conflict warning is enabled
        $enableWarning = Setting::getValue('enable_activity_conflict_warning', true);
        
        if (!$enableWarning) {
            $this->conflicts = [];
            return;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        // Find overlapping activities
        $this->conflicts = Activity::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
        })
        ->with('activityType')
        ->get()
        ->toArray();
    }

    public function save()
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk menambah kegiatan.');
            return;
        }

        $this->validate();
        
        // Validate target grades
        if (!$this->targetGradeX && !$this->targetGradeXI && !$this->targetGradeXII) {
            $this->addError('targetGrades', 'Pilih minimal 1 tingkat kelas.');
            return;
        }
        
        // Weekend warning - just warning, not blocking
        // (removed the blocking logic)

        // Get academic year from semester
        $semester = Semester::findOrFail($this->semester_id);

        // Build target_grades array
        $targetGrades = [];
        if ($this->targetGradeX) $targetGrades[] = 'X';
        if ($this->targetGradeXI) $targetGrades[] = 'XI';
        if ($this->targetGradeXII) $targetGrades[] = 'XII';
        
        // If all grades selected, save as NULL (more efficient)
        if (count($targetGrades) === 3) {
            $targetGrades = null;
        }

        $activity = Activity::create([
            'name' => $this->name,
            'activity_type_id' => $this->activity_type_id,
            'academic_year_id' => $semester->academic_year_id,
            'semester_id' => $this->semester_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'color' => $this->color ?: null,
            'target_grades' => $targetGrades,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::createLog(
            action: 'create',
            modelType: 'Activity',
            modelId: $activity->id,
            description: "Membuat kegiatan: {$activity->name}"
        );

        session()->flash('success', 'Kegiatan berhasil ditambahkan!');

        return redirect()->route('activities.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Kegiatan - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        // Get active academic year
        $activeYear = AcademicYear::active()->first();
        
        // Get semesters
        $semesters = $activeYear ? $activeYear->semesters : collect();
        
        // Get activity types
        $activityTypes = ActivityType::orderBy('name')->get();

        return view('livewire.activity.create', [
            'semesters' => $semesters,
            'activityTypes' => $activityTypes,
            'activeYear' => $activeYear,
        ]);
    }
}

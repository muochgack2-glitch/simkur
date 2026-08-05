<?php

namespace App\Livewire\JournalMonitoring;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public $today;
    public $dayName;
    public $formattedDate;
    public $teachersNotStarted = [];
    public $teachersCompleted = [];
    public $classSchedules = [];
    public $lastRefresh;
    
    // Stats
    public $totalTeachers = 0;
    public $notStartedCount = 0;
    public $completedCount = 0;

    public function mount()
    {
        $this->today = Carbon::today();
        $this->dayName = $this->today->locale('id')->dayName; // Senin, Selasa, etc.
        $this->formattedDate = $this->today->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $this->lastRefresh = now();
        
        $this->loadData();
    }

    public function refresh()
    {
        $this->lastRefresh = now();
        
        // Log untuk debugging
        \Log::info('[MONITORING] Auto-refresh triggered at ' . now()->format('Y-m-d H:i:s'));
        
        $this->loadData();
        
        // Emit event untuk JS
        $this->dispatch('refreshed');
        
        // Flash message (akan hilang otomatis)
        session()->flash('auto_refresh', 'Data diperbarui: ' . now()->format('H:i:s'));
    }

    protected function loadData()
    {
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            return;
        }

        // Get all schedules for today
        $schedules = TeachingSchedule::with(['teacher', 'schoolClass', 'subject'])
            ->where('day_of_week', $this->dayName)
            ->where('is_active', true)
            ->where('academic_year_id', $academicYear->id)
            ->whereHas('teacher', function ($query) {
                $query->where('role', 'guru')->where('is_active', true);
            })
            ->get();

        // Get all journals for today
        $journals = TeachingJournal::where('date', $this->today)
            ->where('academic_year_id', $academicYear->id)
            ->get();
        
        // Build journal lookup by teacher + class + subject for quick access
        $journalLookup = [];
        foreach ($journals as $journal) {
            $key = "{$journal->teacher_id}_{$journal->class_id}_{$journal->subject_id}";
            $journalLookup[$key] = $journal;
        }

        // Group schedules by teacher
        $teacherSchedules = $schedules->groupBy('teacher_id');
        
        // Group schedules by class for class cards
        $this->processClassSchedules($schedules, $journalLookup, $academicYear);

        // Process each teacher
        $notStarted = [];
        $completed = [];

        foreach ($teacherSchedules as $teacherId => $teacherScheduleList) {
            $teacher = $teacherScheduleList->first()->teacher;
            
            // Calculate total JP from schedules
            $totalJP = 0;
            $scheduleDetails = [];
            
            foreach ($teacherScheduleList as $schedule) {
                $jpCount = is_array($schedule->time_slot_id) ? count($schedule->time_slot_id) : 1;
                $totalJP += $jpCount;
                
                // Check if this schedule has journal
                $isFilled = false;
                $key = "{$teacherId}_{$schedule->class_id}_{$schedule->subject_id}";
                if (isset($journalLookup[$key])) {
                    $journal = $journalLookup[$key];
                    // Check if journal covers this schedule's time slots
                    $isFilled = $this->isScheduleFilled($schedule, $journal);
                }
                
                $scheduleDetails[] = [
                    'class' => $schedule->schoolClass->name ?? '-',
                    'subject' => $schedule->subject->name ?? '-',
                    'time_slots' => $schedule->compact_time_slots,
                    'is_filled' => $isFilled,
                    'jp_count' => $jpCount,
                ];
            }

            // Count filled JP
            $filledJP = 0;
            foreach ($scheduleDetails as $detail) {
                if ($detail['is_filled']) {
                    $filledJP += $detail['jp_count'];
                }
            }

            $percentage = $totalJP > 0 ? ($filledJP / $totalJP) * 100 : 0;

            $teacherData = [
                'id' => $teacherId,
                'name' => $teacher->name,
                'total_jp' => $totalJP,
                'filled_jp' => $filledJP,
                'remaining_jp' => $totalJP - $filledJP,
                'percentage' => round($percentage, 1),
                'schedules' => $scheduleDetails,
            ];

            // Categorize: <100% = Belum Lengkap, 100% = Sudah Lengkap
            if ($percentage < 100) {
                $notStarted[] = $teacherData;
            } else {
                $completed[] = $teacherData;
            }
        }

        // Sort by name (A-Z)
        usort($notStarted, fn($a, $b) => strcmp($a['name'], $b['name']));
        usort($completed, fn($a, $b) => strcmp($a['name'], $b['name']));

        $this->teachersNotStarted = $notStarted;
        $this->teachersCompleted = $completed;

        // Update stats
        $this->totalTeachers = count($teacherSchedules);
        $this->notStartedCount = count($notStarted);
        $this->completedCount = count($completed);
    }

    protected function processClassSchedules($schedules, $journalLookup, $academicYear)
    {
        $classGroups = $schedules->groupBy('class_id');
        $classSchedulesData = [];

        $classColors = [
            'X AKL' => 'blue',
            'X BUSANA' => 'purple',
            'X MPLB' => 'green',
            'XI AKL' => 'indigo',
            'XI BUSANA' => 'pink',
            'XI MPLB' => 'teal',
            'XII AKL' => 'cyan',
            'XII BUSANA' => 'rose',
            'XII MPLB' => 'emerald',
        ];

        foreach ($classGroups as $classId => $classScheduleList) {
            $className = $classScheduleList->first()->schoolClass->name ?? '-';
            
            $subjects = [];
            $filledCount = 0;
            $notFilledCount = 0;

            foreach ($classScheduleList as $schedule) {
                $teacherId = $schedule->teacher_id;
                $isFilled = false;
                
                $key = "{$teacherId}_{$schedule->class_id}_{$schedule->subject_id}";
                if (isset($journalLookup[$key])) {
                    $journal = $journalLookup[$key];
                    $isFilled = $this->isScheduleFilled($schedule, $journal);
                }

                if ($isFilled) {
                    $filledCount++;
                } else {
                    $notFilledCount++;
                }

                // Get first time slot ID for sorting
                $firstTimeSlotId = is_array($schedule->time_slot_id) 
                    ? $schedule->time_slot_id[0] 
                    : $schedule->time_slot_id;

                $subjects[] = [
                    'name' => $schedule->subject->name ?? '-',
                    'jp_range' => $schedule->compact_time_slots,
                    'is_filled' => $isFilled,
                    'teacher' => $schedule->teacher->name ?? '-',
                    'sort_order' => $firstTimeSlotId, // For sorting by time slot
                ];
            }

            // Sort subjects by time slot (jam 1, 2, 3, dst)
            usort($subjects, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

            $classSchedulesData[] = [
                'class_name' => $className,
                'color' => $classColors[$className] ?? 'gray',
                'subjects' => $subjects,
                'filled_count' => $filledCount,
                'not_filled_count' => $notFilledCount,
                'total_count' => count($subjects),
            ];
        }

        // Sort by class name
        usort($classSchedulesData, fn($a, $b) => strcmp($a['class_name'], $b['class_name']));

        $this->classSchedules = $classSchedulesData;
    }

    protected function isScheduleFilled($schedule, $journal)
    {
        // Check if journal exists for same teacher, class, subject
        if ($journal->class_id != $schedule->class_id || $journal->subject_id != $schedule->subject_id) {
            return false;
        }
        
        // SIMPLIFIED: Just check if journal exists for this teacher+class+subject combo
        // The fact that journal exists means this schedule is filled
        // Since we're already grouping by teacher_id + class_id + subject_id in lookup
        return true;
    }

    public function render()
    {
        return view('livewire.journal-monitoring.index')
            ->layout('components.layouts.public', ['title' => 'Monitoring Jurnal Hari Ini']);
    }
}

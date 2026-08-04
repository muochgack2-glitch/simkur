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
        $this->loadData();
        
        $this->dispatch('refreshed');
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
            ->get()
            ->keyBy('teacher_id');

        // Group schedules by teacher
        $teacherSchedules = $schedules->groupBy('teacher_id');
        
        // Group schedules by class for class cards
        $this->processClassSchedules($schedules, $journals, $academicYear);

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
                if (isset($journals[$teacherId])) {
                    $journal = $journals[$teacherId];
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
            if (isset($journals[$teacherId])) {
                $journal = $journals[$teacherId];
                if (is_array($journal->time_slot)) {
                    $filledJP = count($journal->time_slot);
                } else {
                    $filledJP = 1;
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

            // Categorize: 0% = Belum, 1-100% = Sudah
            if ($percentage == 0) {
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

    protected function processClassSchedules($schedules, $journals, $academicYear)
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
                
                if (isset($journals[$teacherId])) {
                    $journal = $journals[$teacherId];
                    $isFilled = $this->isScheduleFilled($schedule, $journal);
                }

                if ($isFilled) {
                    $filledCount++;
                } else {
                    $notFilledCount++;
                }

                $subjects[] = [
                    'name' => $schedule->subject->name ?? '-',
                    'jp_range' => $schedule->compact_time_slots,
                    'is_filled' => $isFilled,
                    'teacher' => $schedule->teacher->name ?? '-',
                ];
            }

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
        // Simple check: if journal exists for same teacher, class, subject, and date
        return $journal->class_id == $schedule->class_id 
            && $journal->subject_id == $schedule->subject_id;
    }

    public function render()
    {
        return view('livewire.journal-monitoring.index')
            ->layout('components.layouts.public', ['title' => 'Monitoring Jurnal Hari Ini']);
    }
}

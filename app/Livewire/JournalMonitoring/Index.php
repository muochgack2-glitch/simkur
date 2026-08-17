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

    // Holiday detection
    public $isHoliday = false;
    public $holidayName = null;
    public $holidayIcon = '🇮🇩';
    public $isWeekend = false;

    public function mount()
    {
        $this->today = Carbon::today();
        $this->dayName = $this->today->locale('id')->dayName;
        $this->formattedDate = $this->today->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $this->lastRefresh = now();

        // Deteksi akhir pekan berdasarkan pengaturan sistem
        $weekendSetting = \App\Models\Setting::where('key', 'weekend_days')->value('value');
        $weekendDays = $weekendSetting ? json_decode($weekendSetting, true) : ['saturday', 'sunday'];
        $todayDayLower = strtolower($this->today->format('l')); // e.g. sunday, monday
        $this->isWeekend = in_array($todayDayLower, $weekendDays);
        // Deteksi hari libur nasional (cache 24 jam)
        $this->isHoliday = false;
        $this->holidayName = null;
        if (!$this->isWeekend) {
            $todayStr = $this->today->format('Y-m-d');
            $cacheKey = 'holidays_' . $this->today->format('Y_m');
            $cacheKey = 'holidays_' . $this->today->format('Y');
            $year = $this->today->year;
            $holidays = cache()->remember($cacheKey, now()->addHours(24), function () use ($year) {
                try {
                    $resp = \Illuminate\Support\Facades\Http::timeout(5)
                        ->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/ID");
                    return $resp->successful() ? $resp->json() : [];
                } catch (\Exception $e) {
                    return [];
                }
            });
            foreach ($holidays as $h) {
                if (isset($h['date']) && $h['date'] === $todayStr) {
                    $this->isHoliday = true;
                    $this->holidayName = $h['localName'] ?? 'Hari Libur Nasional';
                    // Icon berdasarkan nama hari libur
                    $name = strtolower($this->holidayName);
                    $this->holidayIcon = match(true) {
                        str_contains($name, 'idul fitri') || str_contains($name, 'lebaran') => '🌙',
                        str_contains($name, 'idul adha') => '🐑',
                        str_contains($name, 'natal') || str_contains($name, 'christmas') => '🎄',
                        str_contains($name, 'tahun baru masehi') || str_contains($name, 'new year') => '🎆',
                        str_contains($name, 'tahun baru islam') || str_contains($name, 'hijriah') => '☪️',
                        str_contains($name, 'nyepi') => '🕯️',
                        str_contains($name, 'waisak') || str_contains($name, 'vesak') => '☸️',
                        str_contains($name, 'wafat') || str_contains($name, 'jumat agung') || str_contains($name, 'good friday') => '✝️',
                        str_contains($name, 'paskah') || str_contains($name, 'easter') => '🐣',
                        str_contains($name, 'kenaikan') => '✝️',
                        str_contains($name, 'kemerdekaan') || str_contains($name, 'independence') => '🇮🇩',
                        str_contains($name, 'buruh') || str_contains($name, 'labour') => '✊',
                        str_contains($name, 'pancasila') => '🦅',
                        str_contains($name, 'maulid') => '🌟',
                        str_contains($name, 'isra') || str_contains($name, 'miraj') => '🌙',
                        default => '🗓️',
                    };
                    break;
                }
            }
        }

        // === PREVIEW MODE (untuk testing tampilan) ===
        // Akses: /monitoring/jurnal-hari-ini?preview=weekend atau ?preview=holiday
        // Hapus blok ini setelah testing selesai
        $preview = request()->query('preview');
        if ($preview === 'weekend') {
            $this->isWeekend = true;
            $this->isHoliday = false;
            $this->dayName = 'Sabtu';
            $this->formattedDate = 'Sabtu, ' . $this->today->format('j F Y') . ' (Preview Mode)';
        } elseif ($preview === 'holiday') {
            $this->isWeekend = false;
            $this->isHoliday = true;
            $this->holidayName = 'Idul Fitri 1447 H (Preview)';
            $this->holidayIcon = '🌙';
        }
        // === END PREVIEW MODE ===

        $this->loadData();
    }
    public function getTimeSchedule()
    {
        // Get time slots for today to display current JP
        $timeSlots = \App\Models\TimeSlot::active()
            ->where('day_of_week', $this->dayName)
            ->ordered()
            ->get(['id', 'name', 'start_time', 'end_time', 'order'])
            ->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'name' => $slot->name,
                    'start' => $slot->start_time,
                    'end' => $slot->end_time,
                    'order' => $slot->order,
                ];
            })
            ->toArray();
        
        return $timeSlots;
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
                // Include guru, waka_kurikulum, and kepala_sekolah who teach
                $query->whereIn('role', ['guru', 'waka_kurikulum', 'kepala_sekolah'])
                      ->where('is_active', true);
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
                
                // Get first time slot ID for sorting
                $firstTimeSlotId = is_array($schedule->time_slot_id) 
                    ? $schedule->time_slot_id[0] 
                    : $schedule->time_slot_id;
                
                $scheduleDetails[] = [
                    'class' => $schedule->schoolClass->name ?? '-',
                    'subject' => $schedule->subject->name ?? '-',
                    'time_slots' => $schedule->compact_time_slots,
                    'is_filled' => $isFilled,
                    'jp_count' => $jpCount,
                    'sort_order' => $firstTimeSlotId, // For sorting by time slot

                ];
            }
            
            // Sort schedules by time slot (JP 1 first, then JP 2, etc.)
            usort($scheduleDetails, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

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
        return view('livewire.journal-monitoring.index', [
            'timeSchedule' => $this->getTimeSchedule(),
        ])
            ->layout('components.layouts.public', ['title' => 'Monitoring Jurnal Hari Ini']);
    }
}
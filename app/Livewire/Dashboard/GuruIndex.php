<?php

namespace App\Livewire\Dashboard;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\SchoolClass;
use App\Models\TeachingMaterial;
use App\Models\TeachingSchedule;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Livewire\BaseComponent;

class GuruIndex extends BaseComponent
{
    public $myJournalsThisMonth = 0;
    public $myTotalJournals = 0;
    public $myClassesCount = 0;
    public $mySubjectsCount = 0;
    public $averageAttendanceMyClasses = 0;
    public $journalChartData = [];
    public $attendanceBreakdown = [];
    public $needJournalToday = false;
    public $todayJournalCount = 0;
    public $todayScheduleCount = 0; // Jumlah jam mengajar hari ini
    public $todayScheduleDetails = []; // Detail jam mengajar hari ini
    public $missingJournalDaysWeek = 0; // Hari belum isi jurnal dalam 1 minggu
    public $missingJournalDaysMonth = 0; // Hari belum isi jurnal dalam 1 bulan
    public $weekScheduleDays = []; // Hari-hari ada jadwal minggu ini
    public $monthScheduleDays = []; // Hari-hari ada jadwal bulan ini
    
    // Perangkat Ajar Stats (My Materials)
    public $myMaterialsTotal = 0;
    public $myMaterialsDraft = 0;
    public $myMaterialsPending = 0;
    public $myMaterialsApproved = 0;
    public $myMaterialsRejected = 0;
    public $myTotalDownloads = 0;
    public $myTotalViews = 0;
    public $myMostDownloaded = null;
    public $myCategoryCoverage = [];
    public $myMaterialChartData = [];

    public function mount()
    {
        $teacherId = auth()->id();

        // My journals this month
        $this->myJournalsThisMonth = TeachingJournal::where('teacher_id', $teacherId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // My total journals
        $this->myTotalJournals = TeachingJournal::where('teacher_id', $teacherId)->count();

        // Classes I teach
        $this->myClassesCount = TeachingJournal::where('teacher_id', $teacherId)
            ->distinct('class_id')
            ->count('class_id');

        // Subjects I teach
        $this->mySubjectsCount = auth()->user()->subjects()->count();

        // Average attendance in my classes (this month)
        $myJournals = TeachingJournal::where('teacher_id', $teacherId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('total_students', '>', 0)
            ->get();

        if ($myJournals->count() > 0) {
            $totalPercentage = $myJournals->sum(function($journal) {
                return ($journal->present_count / $journal->total_students) * 100;
            });
            $this->averageAttendanceMyClasses = round($totalPercentage / $myJournals->count(), 1);
        }

        // Attendance breakdown (this month)
        $attendances = StudentAttendance::whereHas('teachingJournal', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)
              ->whereYear('date', now()->year)
              ->whereMonth('date', now()->month);
        })->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get();

        $this->attendanceBreakdown = [
            'hadir' => $attendances->where('status', 'hadir')->first()->total ?? 0,
            'sakit' => $attendances->where('status', 'sakit')->first()->total ?? 0,
            'izin' => $attendances->where('status', 'izin')->first()->total ?? 0,
            'alpha' => $attendances->where('status', 'alpha')->first()->total ?? 0,
        ];

        // Check if need to fill journal today
        $this->todayJournalCount = TeachingJournal::where('teacher_id', $teacherId)
            ->whereDate('date', today())
            ->count();
        
        // Calculate today's teaching schedule
        $this->calculateTodaySchedule($teacherId);
        
        // Calculate missing journal days in week and month
        $this->calculateMissingJournalDays($teacherId);
        
        $this->needJournalToday = $this->todayScheduleCount > 0 && $this->todayJournalCount == 0;

        // Prepare chart data (last 6 months)
        $this->prepareJournalChartData($teacherId);
        
        // Load my material stats
        $this->loadMyMaterialStats($teacherId);
        $this->prepareMyMaterialChartData($teacherId);
    }

    private function calculateTodaySchedule($teacherId)
    {
        // Get current day name in English
        $todayName = now()->locale('en')->format('l'); // Monday, Tuesday, etc.
        
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->todayScheduleCount = 0;
            $this->todayScheduleDetails = [];
            return;
        }
        
        // Get today's teaching schedules for this teacher
        $schedules = TeachingSchedule::with('timeSlot')
            ->forTeacher($teacherId)
            ->forDay($todayName)
            ->forAcademicYear($academicYear->id)
            ->active()
            ->get();
        
        $this->todayScheduleCount = $schedules->count();
        
        // Get details of schedules for display
        $this->todayScheduleDetails = $schedules->map(function($schedule) {
            return [
                'name' => $schedule->timeSlot->name,
                'time_range' => $schedule->timeSlot->time_range,
            ];
        })->toArray();
    }
    
    private function calculateMissingJournalDays($teacherId)
    {
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->missingJournalDaysWeek = 0;
            $this->missingJournalDaysMonth = 0;
            return;
        }
        
        // Calculate for 1 week (last 7 days, excluding today)
        $weekStart = now()->subDays(7)->startOfDay();
        $weekEnd = now()->subDay()->endOfDay();
        
        $this->weekScheduleDays = $this->getScheduleDaysInRange($teacherId, $academicYear->id, $weekStart, $weekEnd);
        $weekJournalDays = TeachingJournal::where('teacher_id', $teacherId)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->toArray();
        
        // Count days with schedule but no journal
        $this->missingJournalDaysWeek = collect($this->weekScheduleDays)
            ->reject(fn($day) => in_array($day, $weekJournalDays))
            ->count();
        
        // Calculate for 1 month (last 30 days, excluding today)
        $monthStart = now()->subDays(30)->startOfDay();
        $monthEnd = now()->subDay()->endOfDay();
        
        $this->monthScheduleDays = $this->getScheduleDaysInRange($teacherId, $academicYear->id, $monthStart, $monthEnd);
        $monthJournalDays = TeachingJournal::where('teacher_id', $teacherId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->toArray();
        
        // Count days with schedule but no journal
        $this->missingJournalDaysMonth = collect($this->monthScheduleDays)
            ->reject(fn($day) => in_array($day, $monthJournalDays))
            ->count();
    }
    
    private function getScheduleDaysInRange($teacherId, $academicYearId, $startDate, $endDate)
    {
        $scheduleDays = [];
        $current = $startDate->copy();
        
        // Get all teacher's schedule days for this academic year
        $teacherScheduleDays = TeachingSchedule::forTeacher($teacherId)
            ->forAcademicYear($academicYearId)
            ->active()
            ->pluck('day_of_week')
            ->unique()
            ->toArray();
        
        while ($current <= $endDate) {
            // Get day name
            $dayName = $current->locale('en')->format('l');
            
            // Check if teacher has schedule on this day
            if (in_array($dayName, $teacherScheduleDays)) {
                $scheduleDays[] = $current->format('Y-m-d');
            }
            
            $current->addDay();
        }
        
        return $scheduleDays;
    }
    
    private function prepareJournalChartData($teacherId)
    {
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->locale('id')->isoFormat('MMM');
            
            $count = TeachingJournal::where('teacher_id', $teacherId)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->count();
            
            $counts[] = $count;
        }

        $this->journalChartData = [
            'labels' => $months,
            'data' => $counts,
        ];
    }
    
    private function loadMyMaterialStats($teacherId)
    {
        // My materials by status
        $this->myMaterialsTotal = TeachingMaterial::where('created_by', $teacherId)->count();
        $this->myMaterialsDraft = TeachingMaterial::where('created_by', $teacherId)->where('status', 'draft')->count();
        $this->myMaterialsPending = TeachingMaterial::where('created_by', $teacherId)->where('status', 'pending_approval')->count();
        $this->myMaterialsApproved = TeachingMaterial::where('created_by', $teacherId)->where('status', 'approved')->count();
        $this->myMaterialsRejected = TeachingMaterial::where('created_by', $teacherId)->where('status', 'rejected')->count();
        
        // My total downloads and views
        $stats = TeachingMaterial::selectRaw('SUM(download_count) as total_downloads, SUM(view_count) as total_views')
            ->where('created_by', $teacherId)
            ->first();
        
        $this->myTotalDownloads = $stats->total_downloads ?? 0;
        $this->myTotalViews = $stats->total_views ?? 0;
        
        // My most downloaded material
        $this->myMostDownloaded = TeachingMaterial::where('created_by', $teacherId)
            ->where('download_count', '>', 0)
            ->orderBy('download_count', 'desc')
            ->first();
        
        // My category coverage (top 5)
        $this->myCategoryCoverage = TeachingMaterial::select('category', DB::raw('count(*) as total'))
            ->where('created_by', $teacherId)
            ->where('status', 'approved')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->category,
                    'label' => TeachingMaterial::CATEGORIES[$item->category] ?? $item->category,
                    'count' => $item->total,
                ];
            })
            ->toArray();
    }
    
    private function prepareMyMaterialChartData($teacherId)
    {
        // Last 6 months my material upload data
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->locale('id')->isoFormat('MMM');
            
            $count = TeachingMaterial::where('created_by', $teacherId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $counts[] = $count;
        }

        $this->myMaterialChartData = [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    #[Layout('components.layouts.app')]
    #[Title('Dashboard Guru - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $teacherId = auth()->id();

        // Recent journals (last 5)
        $recentJournals = TeachingJournal::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacherId)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Classes I teach (distinct)
        $myClasses = SchoolClass::whereHas('teachingJournals', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->distinct()->get();

        return view('livewire.dashboard.guru-index', [
            'recentJournals' => $recentJournals,
            'myClasses' => $myClasses,
        ]);
    }
}


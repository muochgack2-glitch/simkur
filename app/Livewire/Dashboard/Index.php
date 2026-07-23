<?php

namespace App\Livewire\Dashboard;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\EffectiveDay;
use App\Models\Semester;
use App\Models\User;
use App\Models\TeachingJournal;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    public $totalActivities = 0;
    public $totalActivityTypes = 0;
    public $totalUsers = 0;
    public $effectiveDays = null;
    public $chartData = [];
    
    // Jurnal Mengajar Stats
    public $totalJournals = 0;
    public $journalsThisMonth = 0;
    public $teachersNotFillingJournal = 0;
    public $averageAttendance = 0;
    public $topTeachers = [];
    public $totalSubjectsTaught = 0;
    public $journalChartData = [];

    public function mount()
    {
        // Get active academic year
        $activeYear = AcademicYear::active()->first();

        // KALENDER AKADEMIK STATS
        if ($activeYear) {
            $this->totalActivities = Activity::where('academic_year_id', $activeYear->id)->count();
        } else {
            $this->totalActivities = 0;
        }

        $this->totalActivityTypes = ActivityType::count();
        $this->totalUsers = User::active()->count();

        // Get effective days
        if ($activeYear) {
            $effectiveDaysData = EffectiveDay::whereHas('semester', function($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })->get();
            
            if ($effectiveDaysData->count() > 0) {
                $this->effectiveDays = [
                    'study_days' => $effectiveDaysData->sum('study_days'),
                    'effective_weeks' => $effectiveDaysData->sum('effective_weeks'),
                ];
            }
        }

        // JURNAL MENGAJAR STATS
        $this->loadJournalStats($activeYear);

        // Prepare chart data
        $this->prepareChartData($activeYear);
        $this->prepareJournalChartData();
    }

    private function loadJournalStats($activeYear)
    {
        // Total journals (all time or this year)
        $journalQuery = TeachingJournal::query();
        if ($activeYear) {
            $journalQuery->where('academic_year_id', $activeYear->id);
        }
        $this->totalJournals = $journalQuery->count();

        // Journals this month
        $this->journalsThisMonth = TeachingJournal::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // Teachers not filling journal this month
        $totalTeachers = User::where('role', 'guru')->where('is_active', true)->count();
        $teachersWithJournal = TeachingJournal::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->distinct('teacher_id')
            ->count('teacher_id');
        $this->teachersNotFillingJournal = $totalTeachers - $teachersWithJournal;

        // Average attendance percentage
        $journals = TeachingJournal::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('total_students', '>', 0)
            ->get();
        
        if ($journals->count() > 0) {
            $totalAttendancePercentage = $journals->sum(function($journal) {
                return ($journal->present_count / $journal->total_students) * 100;
            });
            $this->averageAttendance = round($totalAttendancePercentage / $journals->count(), 1);
        }

        // Top 3 teachers (most journals this month)
        $this->topTeachers = TeachingJournal::select('teacher_id', DB::raw('count(*) as journal_count'))
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->groupBy('teacher_id')
            ->orderBy('journal_count', 'desc')
            ->limit(3)
            ->with('teacher')
            ->get();

        // Total unique subjects taught this month
        $this->totalSubjectsTaught = TeachingJournal::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->distinct('subject_id')
            ->count('subject_id');
    }

    private function prepareChartData($activeYear)
    {
        if (!$activeYear) {
            $this->chartData = [];
            return;
        }

        $startDate = Carbon::parse($activeYear->start_date);
        $endDate = Carbon::parse($activeYear->end_date);

        $months = [];
        $counts = [];

        $current = $startDate->copy()->startOfMonth();
        while ($current->lte($endDate)) {
            $monthName = $current->locale('id')->isoFormat('MMM YY');

            $count = Activity::where('academic_year_id', $activeYear->id)
                ->where(function ($query) use ($current) {
                    $monthStart = $current->copy()->startOfMonth();
                    $monthEnd = $current->copy()->endOfMonth();

                    $query->whereBetween('start_date', [$monthStart, $monthEnd])
                        ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                        ->orWhere(function ($q) use ($monthStart, $monthEnd) {
                            $q->where('start_date', '<=', $monthStart)
                              ->where('end_date', '>=', $monthEnd);
                        });
                })
                ->count();

            $months[] = $monthName;
            $counts[] = $count;

            $current->addMonth();
        }

        $this->chartData = [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    private function prepareJournalChartData()
    {
        // Last 6 months journal data
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->locale('id')->isoFormat('MMM');
            
            $count = TeachingJournal::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->count();
            
            $counts[] = $count;
        }

        $this->journalChartData = [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    #[Layout('components.layouts.app')]
    #[Title('Dashboard - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $activeYear = AcademicYear::active()->first();
        
        $upcomingActivities = collect();
        if ($activeYear) {
            $upcomingActivities = Activity::with(['activityType', 'semester'])
                ->where('academic_year_id', $activeYear->id)
                ->whereDate('start_date', '>=', now())
                ->whereDate('start_date', '<=', now()->addDays(7))
                ->orderBy('start_date')
                ->limit(5)
                ->get();
        }
        
        // Recent journals (last 5)
        $recentJournals = TeachingJournal::with(['teacher', 'schoolClass', 'subject'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        return view('livewire.dashboard.index', [
            'activeYear' => $activeYear,
            'upcomingActivities' => $upcomingActivities,
            'recentJournals' => $recentJournals,
        ]);
    }
}

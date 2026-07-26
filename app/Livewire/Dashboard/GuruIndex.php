<?php

namespace App\Livewire\Dashboard;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\SchoolClass;
use App\Models\TeachingMaterial;
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
        
        $this->needJournalToday = $this->todayJournalCount == 0;

        // Prepare chart data (last 6 months)
        $this->prepareJournalChartData($teacherId);
        
        // Load my material stats
        $this->loadMyMaterialStats($teacherId);
        $this->prepareMyMaterialChartData($teacherId);
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


<?php

namespace App\Livewire\Dashboard;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\EffectiveDay;
use App\Models\User;
use App\Models\TeachingMaterial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class KepsekIndex extends Component
{
    #[Layout('components.layouts.app')]
    #[Title('Dashboard Kepala Sekolah - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Statistics Cards
        $totalActivitiesThisYear = $activeYear 
            ? Activity::whereHas('semester', function($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })->count()
            : 0;

        $activitiesThisMonth = Activity::whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->count();

        $examActivities = Activity::whereHas('activityType', function($q) {
            $q->where('is_exam', true);
        });
        if ($activeYear) {
            $examActivities->whereHas('semester', function($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            });
        }
        $totalExams = $examActivities->count();

        $holidayActivities = Activity::whereHas('activityType', function($q) {
            $q->where('is_holiday', true);
        });
        if ($activeYear) {
            $holidayActivities->whereHas('semester', function($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            });
        }
        $totalHolidays = $holidayActivities->count();

        $effectiveDays = null;
        if ($activeYear) {
            // Get effective days from active year's semesters
            $effectiveDays = EffectiveDay::whereHas('semester', function($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })->first();
        }

        $totalUsers = User::where('is_active', true)->count();
        $totalActivityTypes = ActivityType::count();

        // Chart Data: Activities per Month (last 12 months)
        $monthlyData = [];
        $monthLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->format('M Y');
            
            $count = Activity::whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month)
                ->count();
            
            $monthlyData[] = $count;
        }

        // Pie Chart: Activities by Category
        $categoryData = [
            'ujian' => Activity::whereHas('activityType', function($q) {
                $q->where('is_exam', true);
            })->count(),
            'libur' => Activity::whereHas('activityType', function($q) {
                $q->where('is_holiday', true);
            })->count(),
            'reguler' => Activity::whereHas('activityType', function($q) {
                $q->where('is_exam', false)->where('is_holiday', false);
            })->count(),
        ];

        // Activity Timeline (last 10 activities)
        $activityLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // User Activity Stats
        $mostActiveUser = ActivityLog::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->with('user')
            ->first();

        // Quick Insights
        $insights = [
            'current_month_activities' => $activitiesThisMonth,
            'effective_days_progress' => $effectiveDays ? round(($effectiveDays->study_days / ($effectiveDays->study_days + $effectiveDays->holiday_days)) * 100, 1) : 0,
            'most_active_user' => $mostActiveUser,
        ];
        
        // PERANGKAT AJAR STATS
        $materialStats = $this->getMaterialStats($activeYear);

        return view('livewire.dashboard.kepsek-index', [
            'activeYear' => $activeYear,
            'totalActivitiesThisYear' => $totalActivitiesThisYear,
            'activitiesThisMonth' => $activitiesThisMonth,
            'totalExams' => $totalExams,
            'totalHolidays' => $totalHolidays,
            'effectiveDays' => $effectiveDays,
            'totalUsers' => $totalUsers,
            'totalActivityTypes' => $totalActivityTypes,
            'monthlyData' => $monthlyData,
            'monthLabels' => $monthLabels,
            'categoryData' => $categoryData,
            'activityLogs' => $activityLogs,
            'insights' => $insights,
            'materialStats' => $materialStats,
        ]);
    }
    
    private function getMaterialStats($activeYear)
    {
        $query = TeachingMaterial::query();
        if ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        }
        
        $stats = [
            'total' => $query->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'pending' => (clone $query)->where('status', 'pending_approval')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];
        
        // Category coverage percentage (approved / 20 categories * 100)
        $categoriesWithMaterial = TeachingMaterial::select('category')
            ->where('status', 'approved')
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->distinct()
            ->count();
        
        $stats['category_coverage_percent'] = round(($categoriesWithMaterial / 20) * 100, 1);
        
        // Dimension coverage (8 dimensi)
        $stats['dimensions'] = [
            'beriman' => TeachingMaterial::where('dimension_1_beriman', true)->where('status', 'approved')->count(),
            'kebinekaan' => TeachingMaterial::where('dimension_2_kebinekaan', true)->where('status', 'approved')->count(),
            'gotong_royong' => TeachingMaterial::where('dimension_3_gotong_royong', true)->where('status', 'approved')->count(),
            'mandiri' => TeachingMaterial::where('dimension_4_mandiri', true)->where('status', 'approved')->count(),
            'bernalar_kritis' => TeachingMaterial::where('dimension_5_bernalar_kritis', true)->where('status', 'approved')->count(),
            'kreatif' => TeachingMaterial::where('dimension_6_kreatif', true)->where('status', 'approved')->count(),
            'numerasi' => TeachingMaterial::where('dimension_7_numerasi', true)->where('status', 'approved')->count(),
            'literasi' => TeachingMaterial::where('dimension_8_literasi', true)->where('status', 'approved')->count(),
        ];
        
        // Monthly submission trend (last 6 months)
        $monthlyMaterials = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->locale('id')->isoFormat('MMM');
            
            $count = TeachingMaterial::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'approved')
                ->count();
            
            $monthlyMaterials[] = $count;
        }
        
        $stats['monthly_chart'] = [
            'labels' => $monthlyLabels,
            'data' => $monthlyMaterials,
        ];
        
        return $stats;
    }
}

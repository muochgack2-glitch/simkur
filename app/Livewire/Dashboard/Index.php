<?php

namespace App\Livewire\Dashboard;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\EffectiveDay;
use App\Models\Semester;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    public $activeYear;
    public $totalActivities = 0;
    public $totalActivityTypes = 0;
    public $totalUsers = 0;
    public $upcomingActivities = [];
    public $effectiveDays = null;
    public $chartData = [];

    public function mount()
    {
        // Get active academic year
        $this->activeYear = AcademicYear::active()->first();

        // Get statistics for the whole academic year
        if ($this->activeYear) {
            $this->totalActivities = Activity::where('academic_year_id', $this->activeYear->id)->count();
        } else {
            $this->totalActivities = 0;
        }

        $this->totalActivityTypes = ActivityType::count();
        $this->totalUsers = User::active()->count();

        // Get upcoming activities (next 7 days) from the whole academic year
        if ($this->activeYear) {
            $this->upcomingActivities = Activity::with(['activityType', 'semester'])
                ->where('academic_year_id', $this->activeYear->id)
                ->whereDate('start_date', '>=', now())
                ->whereDate('start_date', '<=', now()->addDays(7))
                ->orderBy('start_date')
                ->limit(5)
                ->get();
        } else {
            $this->upcomingActivities = collect();
        }

        // Get effective days - TOTAL for the whole academic year (both semesters)
        if ($this->activeYear) {
            $effectiveDaysData = EffectiveDay::whereHas('semester', function($q) {
                $q->where('academic_year_id', $this->activeYear->id);
            })->get();
            
            if ($effectiveDaysData->count() > 0) {
                $this->effectiveDays = [
                    'study_days' => $effectiveDaysData->sum('study_days'),
                    'effective_weeks' => $effectiveDaysData->sum('effective_weeks'),
                ];
            }
        }

        // Prepare chart data (activities per month for the whole year)
        $this->prepareChartData();
    }

    private function prepareChartData()
    {
        if (!$this->activeYear) {
            $this->chartData = [];
            return;
        }

        // Get all months in the academic year
        $startDate = Carbon::parse($this->activeYear->start_date);
        $endDate = Carbon::parse($this->activeYear->end_date);

        $months = [];
        $counts = [];

        $current = $startDate->copy()->startOfMonth();
        while ($current->lte($endDate)) {
            $monthKey = $current->format('Y-m');
            $monthName = $current->locale('id')->isoFormat('MMM YY');

            // Count activities in this month
            $count = Activity::where('academic_year_id', $this->activeYear->id)
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

    #[Layout('components.layouts.app')]
    #[Title('Dashboard - e-KALDIK')]
    public function render()
    {
        return view('livewire.dashboard.index');
    }
}

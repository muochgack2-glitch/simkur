<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Setting;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportPdfService
{
    /**
     * Export yearly calendar (12 months overview)
     */
    public function exportYearly(?int $academicYearId = null): string
    {
        $academicYear = $academicYearId 
            ? AcademicYear::findOrFail($academicYearId)
            : AcademicYear::active()->firstOrFail();
        
        $activities = Activity::where('academic_year_id', $academicYear->id)
            ->with(['activityType', 'semester'])
            ->orderBy('start_date')
            ->get();
        
        // Group activities by month
        $months = [];
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        
        $current = $startDate->copy()->startOfMonth();
        while ($current->lte($endDate)) {
            $monthActivities = $activities->filter(function ($activity) use ($current) {
                $actStart = Carbon::parse($activity->start_date);
                $actEnd = Carbon::parse($activity->end_date);
                return $actStart->month === $current->month || $actEnd->month === $current->month
                    || ($actStart->lte($current->startOfMonth()) && $actEnd->gte($current->endOfMonth()));
            });
            
            $months[] = [
                'name' => $current->locale('id')->isoFormat('MMMM YYYY'),
                'year' => $current->year,
                'month' => $current->month,
                'activities' => $monthActivities,
                'calendar' => $this->generateMonthCalendar($current, $monthActivities),
            ];
            
            $current->addMonth();
        }
        
        $data = [
            'academicYear' => $academicYear,
            'months' => collect($months), // Convert to Collection
            'schoolName' => Setting::getValue('school_name', 'SMK Negeri 1'),
            'generatedAt' => now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'),
        ];
        
        $pdf = Pdf::loadView('pdf.calendar-yearly', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->output();
    }
    
    /**
     * Export monthly calendar
     */
    public function exportMonthly(int $year, int $month): string
    {
        $date = Carbon::create($year, $month, 1);
        
        $activities = Activity::whereYear('start_date', '<=', $year)
            ->whereMonth('start_date', '<=', $month)
            ->whereYear('end_date', '>=', $year)
            ->whereMonth('end_date', '>=', $month)
            ->with(['activityType', 'semester'])
            ->orderBy('start_date')
            ->get();
        
        $calendar = $this->generateMonthCalendar($date, $activities);
        
        $data = [
            'monthName' => $date->locale('id')->isoFormat('MMMM YYYY'),
            'year' => $year,
            'month' => $month,
            'activities' => $activities,
            'calendar' => $calendar,
            'schoolName' => Setting::getValue('school_name', 'SMK Negeri 1'),
            'generatedAt' => now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'),
        ];
        
        $pdf = Pdf::loadView('pdf.calendar-monthly', $data);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->output();
    }
    
    /**
     * Export activity list (table format)
     */
    public function exportActivityList(array $filters = []): string
    {
        $query = Activity::with(['activityType', 'semester', 'academicYear']);
        
        // Apply filters
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        } else {
            $academicYear = AcademicYear::active()->first();
            if ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            }
        }
        
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        
        if (!empty($filters['activity_type_id'])) {
            $query->where('activity_type_id', $filters['activity_type_id']);
        }
        
        $activities = $query->orderBy('start_date')->get();
        
        $data = [
            'activities' => $activities,
            'schoolName' => Setting::getValue('school_name', 'SMK Negeri 1'),
            'generatedAt' => now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'),
            'filters' => $filters,
        ];
        
        $pdf = Pdf::loadView('pdf.activity-list', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->output();
    }
    
    /**
     * Generate calendar grid for a month
     */
    private function generateMonthCalendar(Carbon $date, $activities): array
    {
        $calendar = [];
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Get weekend days setting
        $weekendDays = Setting::getValue('weekend_days', ['saturday', 'sunday']);
        
        // Start from Monday before or on the 1st
        $current = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        
        // Generate 6 weeks (max weeks in a month)
        for ($week = 0; $week < 6; $week++) {
            $weekDays = [];
            
            for ($day = 0; $day < 7; $day++) {
                // Check if current day is weekend based on settings
                $dayName = strtolower($current->format('l')); // monday, tuesday, etc.
                $isWeekend = in_array($dayName, $weekendDays);
                
                // Filter activities: only show if NOT weekend
                $dayActivities = $activities->filter(function ($activity) use ($current, $isWeekend) {
                    if ($isWeekend) {
                        return false; // Don't show activities on weekend
                    }
                    
                    $actStart = Carbon::parse($activity->start_date);
                    $actEnd = Carbon::parse($activity->end_date);
                    return $current->between($actStart, $actEnd);
                });
                
                $weekDays[] = [
                    'date' => $current->copy(),
                    'day' => $current->day,
                    'isCurrentMonth' => $current->month === $date->month,
                    'isWeekend' => $isWeekend,
                    'activities' => $dayActivities,
                ];
                
                $current->addDay();
            }
            
            $calendar[] = $weekDays;
            
            // Break if we've passed the end of month
            if ($current->gt($endOfMonth->addWeek())) {
                break;
            }
        }
        
        return $calendar;
    }
}

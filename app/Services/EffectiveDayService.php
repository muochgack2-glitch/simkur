<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\EffectiveDay;
use App\Models\Semester;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EffectiveDayService
{
    /**
     * Calculate effective days for a semester
     */
    public function calculate(Semester $semester): array
    {
        $startDate = Carbon::parse($semester->start_date);
        $endDate = Carbon::parse($semester->end_date);
        
        // Get total days in semester
        $totalDays = $startDate->diffInDays($endDate) + 1;
        
        // Get weekend days setting
        $weekendDays = Setting::getValue('weekend_days', ['saturday', 'sunday']);
        
        // Count weekends
        $weekendCount = $this->countWeekends($startDate, $endDate, $weekendDays);
        
        // Get holiday days from activities (excluding weekends)
        $holidayDays = $this->countActivityDays($semester, 'is_holiday');
        
        // Get exam days from activities (also subtracted from study days)
        $examDays = $this->countActivityDays($semester, 'is_exam');
        
        // Calculate study days (exclude weekends, holidays, AND exams)
        // Both holidays and exams are subtracted from effective days
        $studyDays = $totalDays - $weekendCount - $holidayDays - $examDays;
        
        // Calculate effective weeks (study days / 5 working days per week)
        $effectiveWeeks = round($studyDays / 5, 2);
        
        // Calculate percentage
        $percentage = $totalDays > 0 ? round(($studyDays / $totalDays) * 100, 2) : 0;
        
        return [
            'total_days' => $totalDays,
            'weekend_days' => $weekendCount,
            'holiday_days' => $holidayDays,
            'exam_days' => $examDays, // tracked but not subtracted
            'study_days' => $studyDays,
            'effective_weeks' => $effectiveWeeks,
            'percentage' => $percentage,
        ];
    }
    
    /**
     * Count weekend days in a date range
     */
    private function countWeekends(Carbon $startDate, Carbon $endDate, array $weekendDays): int
    {
        $count = 0;
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $dayName = strtolower($date->format('l')); // monday, tuesday, etc.
            
            if (in_array($dayName, $weekendDays)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Count activity days by type (holiday or exam) - excluding weekends
     */
    private function countActivityDays(Semester $semester, string $type): int
    {
        $activities = Activity::where('semester_id', $semester->id)
            ->whereHas('activityType', function ($query) use ($type) {
                $query->where($type, true);
            })
            ->get();
        
        $count = 0;
        
        foreach ($activities as $activity) {
            $start = Carbon::parse($activity->start_date);
            $end = Carbon::parse($activity->end_date);
            
            // Count only weekdays (exclude Saturday and Sunday)
            $period = CarbonPeriod::create($start, $end);
            
            foreach ($period as $date) {
                // Skip Saturday (6) and Sunday (0)
                if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Save calculated effective days to database
     */
    public function saveEffectiveDay(Semester $semester, array $calculation): EffectiveDay
    {
        return EffectiveDay::updateOrCreate(
            ['semester_id' => $semester->id],
            [
                'total_days' => $calculation['total_days'],
                'weekend_days' => $calculation['weekend_days'],
                'holiday_days' => $calculation['holiday_days'],
                'exam_days' => $calculation['exam_days'],
                'study_days' => $calculation['study_days'],
                'effective_weeks' => $calculation['effective_weeks'],
                'percentage' => $calculation['percentage'],
                'calculated_at' => now(),
            ]
        );
    }
    
    /**
     * Recalculate all effective days for active academic year
     */
    public function recalculateAll(): int
    {
        $semesters = Semester::whereHas('academicYear', function ($query) {
            $query->where('is_active', true);
        })->get();
        
        $count = 0;
        
        foreach ($semesters as $semester) {
            $calculation = $this->calculate($semester);
            $this->saveEffectiveDay($semester, $calculation);
            $count++;
        }
        
        return $count;
    }
}

<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\EffectiveDay;
use App\Models\EffectiveDayByGrade;
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
        
        // Get holiday days from activities (weekdays only, excluding weekends)
        $holidayDays = $this->countActivityDays($semester, 'is_holiday');
        
        // Get exam days from activities (weekdays only, excluding weekends)
        $examDays = $this->countActivityDays($semester, 'is_exam');
        
        // Calculate study days
        // Formula: Total - Weekends - Holidays - Exams
        // Note: holidayDays and examDays already exclude weekends (counted in countActivityDays)
        $studyDays = $totalDays - $weekendCount - $holidayDays - $examDays;
        
        // Ensure study days is not negative
        $studyDays = max(0, $studyDays);
        
        // Calculate effective weeks (study days / 5 working days per week)
        $effectiveWeeks = round($studyDays / 5, 2);
        
        // Calculate percentage (study days / total weekdays, not total days)
        $totalWeekdays = $totalDays - $weekendCount;
        $percentage = $totalWeekdays > 0 ? round(($studyDays / $totalWeekdays) * 100, 2) : 0;
        
        return [
            'total_days' => $totalDays,
            'weekend_days' => $weekendCount,
            'holiday_days' => $holidayDays,
            'exam_days' => $examDays,
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
            $effectiveDay = $this->saveEffectiveDay($semester, $calculation);
            
            // Calculate per grade
            $this->calculateByGrades($effectiveDay, $semester);
            
            $count++;
        }
        
        return $count;
    }

    /**
     * Calculate effective days per grade (X, XI, XII)
     */
    public function calculateByGrades(EffectiveDay $effectiveDay, Semester $semester): void
    {
        $semesterStart = Carbon::parse($semester->start_date);
        $semesterEnd = Carbon::parse($semester->end_date);
        
        foreach (EffectiveDayByGrade::GRADES as $grade) {
            // Determine end date for each grade
            $gradeEndDate = $this->getGradeEndDate($semester, $grade);
            
            // Calculate for this grade
            $calculation = $this->calculateForGrade($semester, $semesterStart, $gradeEndDate, $grade);
            
            // Save to database
            EffectiveDayByGrade::updateOrCreate(
                [
                    'effective_day_id' => $effectiveDay->id,
                    'grade' => $grade,
                ],
                [
                    'start_date' => $semesterStart,
                    'end_date' => $gradeEndDate,
                    'total_days' => $calculation['total_days'],
                    'weekend_days' => $calculation['weekend_days'],
                    'holiday_days' => $calculation['holiday_days'],
                    'exam_days' => $calculation['exam_days'],
                    'study_days' => $calculation['study_days'],
                    'effective_weeks' => $calculation['effective_weeks'],
                    'percentage' => $calculation['percentage'],
                    'exam_notes' => $calculation['exam_notes'],
                    'calculated_at' => now(),
                ]
            );
        }
    }

    /**
     * Get end date for specific grade
     * Kelas XII biasanya selesai lebih cepat
     * 
     * UPDATED: Sekarang bisa ditentukan dari Activity yang marks_end_of_period = true
     */
    private function getGradeEndDate(Semester $semester, string $grade): Carbon
    {
        $semesterEnd = Carbon::parse($semester->end_date);
        
        // PRIORITAS 1: Cek apakah ada Activity yang menandai "akhir periode" untuk grade ini
        $endPeriodActivity = Activity::where('semester_id', $semester->id)
            ->whereHas('activityType', function ($query) use ($grade) {
                $query->where('marks_end_of_period', true)
                      ->whereJsonContains('affects_grades', $grade);
            })
            ->orderBy('end_date', 'desc') // Ambil yang paling akhir
            ->first();
        
        if ($endPeriodActivity) {
            // Gunakan end_date dari activity tersebut
            return Carbon::parse($endPeriodActivity->end_date);
        }
        
        // PRIORITAS 2: Fallback ke logic lama untuk backward compatibility
        // Kelas XII logic
        if ($grade === 'XII') {
            // Check if this is semester genap (semester 2)
            if ($semester->type === 'genap') {
                // Kelas XII selesai lebih cepat di semester genap
                // Default: 31 Maret atau 3 bulan sebelum semester end
                $earlyEnd = $semesterEnd->copy()->subMonths(3);
                
                // Atau bisa set tanggal fix: 31 Maret
                $marchEnd = Carbon::create($semesterEnd->year, 3, 31);
                
                // Pilih yang lebih awal
                return $earlyEnd < $marchEnd ? $earlyEnd : $marchEnd;
            }
        }
        
        // PRIORITAS 3: Default - Kelas X dan XI atau semester ganjil: sampai akhir semester
        return $semesterEnd;
    }

    /**
     * Calculate effective days for specific grade
     */
    private function calculateForGrade(Semester $semester, Carbon $startDate, Carbon $endDate, string $grade): array
    {
        // Get total days
        $totalDays = $startDate->diffInDays($endDate) + 1;
        
        // Get weekend days setting
        $weekendDays = Setting::getValue('weekend_days', ['saturday', 'sunday']);
        
        // Count weekends
        $weekendCount = $this->countWeekends($startDate, $endDate, $weekendDays);
        
        // Get holiday days (same for all grades)
        $holidayDays = $this->countActivityDaysInRange($semester, 'is_holiday', $startDate, $endDate);
        
        // Get exam days (different per grade)
        $examDays = $this->countExamDaysForGrade($semester, $grade, $startDate, $endDate);
        
        // Generate exam notes
        $examNotes = $this->generateExamNotes($grade);
        
        // Calculate study days
        $studyDays = $totalDays - $weekendCount - $holidayDays - $examDays;
        $studyDays = max(0, $studyDays);
        
        // Calculate effective weeks
        $effectiveWeeks = round($studyDays / 5, 2);
        
        // Calculate percentage
        $totalWeekdays = $totalDays - $weekendCount;
        $percentage = $totalWeekdays > 0 ? round(($studyDays / $totalWeekdays) * 100, 2) : 0;
        
        return [
            'total_days' => $totalDays,
            'weekend_days' => $weekendCount,
            'holiday_days' => $holidayDays,
            'exam_days' => $examDays,
            'study_days' => $studyDays,
            'effective_weeks' => $effectiveWeeks,
            'percentage' => $percentage,
            'exam_notes' => $examNotes,
        ];
    }

    /**
     * Count activity days in specific date range
     */
    private function countActivityDaysInRange(Semester $semester, string $type, Carbon $startDate, Carbon $endDate): int
    {
        $activities = Activity::where('semester_id', $semester->id)
            ->whereHas('activityType', function ($query) use ($type) {
                $query->where($type, true);
            })
            ->get();
        
        $count = 0;
        
        foreach ($activities as $activity) {
            $actStart = Carbon::parse($activity->start_date);
            $actEnd = Carbon::parse($activity->end_date);
            
            // Only count if activity is within range
            if ($actStart > $endDate || $actEnd < $startDate) {
                continue;
            }
            
            // Adjust to range
            $actStart = $actStart < $startDate ? $startDate : $actStart;
            $actEnd = $actEnd > $endDate ? $endDate : $actEnd;
            
            $period = CarbonPeriod::create($actStart, $actEnd);
            
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
     * Count exam days for specific grade
     */
    private function countExamDaysForGrade(Semester $semester, string $grade, Carbon $startDate, Carbon $endDate): int
    {
        $examDays = $this->countActivityDaysInRange($semester, 'is_exam', $startDate, $endDate);
        
        // Kelas XII memiliki ujian tambahan di semester genap
        if ($grade === 'XII' && $semester->type === 'genap') {
            // Tambahan untuk Ujian Sekolah, UTBK, dll
            // Asumsi: +10 hari (bisa di-adjust sesuai kebutuhan)
            // Ini bisa di-set via Activity juga, tapi untuk default kita tambah manual
            $examDays += 10; // Ujian Sekolah + UTBK
        }
        
        return $examDays;
    }

    /**
     * Generate exam notes for grade
     */
    private function generateExamNotes(string $grade): string
    {
        if ($grade === 'XII') {
            return 'UTS, UAS, Ujian Sekolah, UTBK/Ujian Kelulusan';
        }
        
        return 'UTS, UAS';
    }
}

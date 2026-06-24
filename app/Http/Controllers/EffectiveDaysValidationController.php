<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\Semester;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class EffectiveDaysValidationController extends Controller
{
    /**
     * Display validation page for effective days calculation
     */
    public function index()
    {
        // Get active academic year
        $academicYear = AcademicYear::with(['semesters.effectiveDay'])->active()->first();
        
        if (!$academicYear) {
            return redirect()->route('dashboard')->with('error', 'Belum ada tahun pelajaran aktif.');
        }
        
        $validationData = [];
        
        foreach ($academicYear->semesters as $semester) {
            $validationData[] = $this->calculateSemesterBreakdown($semester);
        }
        
        // Calculate yearly totals
        $yearlyTotal = $this->calculateYearlyTotal($validationData);
        
        // Expected values from Excel reference
        $expectedValues = [
            'ganjil' => [
                'hari_belajar_efektif' => 102,
                'total_days' => 161,
                'weekend_days' => 46,
                'holiday_days' => 12,
            ],
            'genap' => [
                'hari_belajar_efektif' => 105,
                'total_days' => 167,
                'weekend_days' => 48,
                'holiday_days' => 14,
            ],
            'yearly' => [
                'hari_belajar_efektif' => 207,
                'total_days' => 328,
                'weekend_days' => 94,
                'holiday_days' => 26,
            ]
        ];
        
        // Get actual values from EffectiveDay model
        $actualValues = [];
        foreach ($academicYear->semesters as $semester) {
            if ($semester->effectiveDay) {
                $actualValues[$semester->type] = [
                    'hari_belajar_efektif' => $semester->effectiveDay->study_days,
                    'total_days' => $semester->effectiveDay->total_days,
                    'weekend_days' => $semester->effectiveDay->weekend_days,
                    'holiday_days' => $semester->effectiveDay->holiday_days,
                    'exam_days' => $semester->effectiveDay->exam_days,
                    'calculated_at' => $semester->effectiveDay->calculated_at,
                ];
            }
        }
        
        // Calculate comparison
        $comparison = [];
        foreach (['ganjil', 'genap'] as $type) {
            if (isset($actualValues[$type])) {
                $comparison[$type] = [
                    'expected' => $expectedValues[$type]['hari_belajar_efektif'],
                    'actual' => $actualValues[$type]['hari_belajar_efektif'],
                    'difference' => $actualValues[$type]['hari_belajar_efektif'] - $expectedValues[$type]['hari_belajar_efektif'],
                    'match' => $actualValues[$type]['hari_belajar_efektif'] === $expectedValues[$type]['hari_belajar_efektif'],
                ];
            }
        }
        
        // Yearly comparison
        $yearlyActual = array_sum(array_column($actualValues, 'hari_belajar_efektif'));
        $comparison['yearly'] = [
            'expected' => $expectedValues['yearly']['hari_belajar_efektif'],
            'actual' => $yearlyActual,
            'difference' => $yearlyActual - $expectedValues['yearly']['hari_belajar_efektif'],
            'match' => $yearlyActual === $expectedValues['yearly']['hari_belajar_efektif'],
        ];
        
        return view('effective-days.validation', [
            'academicYear' => $academicYear,
            'semesters' => $validationData,
            'yearlyTotal' => $yearlyTotal,
            'expectedValues' => $expectedValues,
            'actualValues' => $actualValues,
            'comparison' => $comparison,
        ]);
    }
    
    /**
     * Calculate detailed breakdown per month for a semester
     */
    private function calculateSemesterBreakdown(Semester $semester)
    {
        $startDate = Carbon::parse($semester->start_date);
        $endDate = Carbon::parse($semester->end_date);
        
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current->lte($endDate)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Adjust to semester boundaries
            if ($monthStart->lt($startDate)) {
                $monthStart = $startDate->copy();
            }
            if ($monthEnd->gt($endDate)) {
                $monthEnd = $endDate->copy();
            }
            
            // Skip if month is outside semester
            if ($monthStart->gt($endDate) || $monthEnd->lt($startDate)) {
                $current->addMonth();
                continue;
            }
            
            $months[] = $this->calculateMonthData($monthStart, $monthEnd, $semester);
            
            $current->addMonth();
        }
        
        // Calculate semester totals
        $semesterTotal = [
            'hari_belajar_efektif' => array_sum(array_column($months, 'hari_belajar_efektif')),
            'hari_pertama_masuk' => array_sum(array_column($months, 'hari_pertama_masuk')),
            'tka_kemampuan' => array_sum(array_column($months, 'tka_kemampuan')),
            'mengikuti_upacara' => array_sum(array_column($months, 'mengikuti_upacara')),
            'penyerahan_rapor' => array_sum(array_column($months, 'penyerahan_rapor')),
            'jumla_hari_efektif' => array_sum(array_column($months, 'jumla_hari_efektif')),
            'libur_akhir_semester' => array_sum(array_column($months, 'libur_akhir_semester')),
            'hari_minggu' => array_sum(array_column($months, 'hari_minggu')),
            'hari_sabtu' => array_sum(array_column($months, 'hari_sabtu')),
            'libur_umum' => array_sum(array_column($months, 'libur_umum')),
            'libur_ramadhan' => array_sum(array_column($months, 'libur_ramadhan')),
            'jumlah_hari_libur' => array_sum(array_column($months, 'jumlah_hari_libur')),
            'jumlah_hari' => array_sum(array_column($months, 'jumlah_hari')),
        ];
        
        return [
            'semester' => $semester,
            'months' => $months,
            'total' => $semesterTotal,
        ];
    }
    
    /**
     * Calculate data for a single month
     */
    private function calculateMonthData(Carbon $start, Carbon $end, Semester $semester)
    {
        $period = CarbonPeriod::create($start, $end);
        
        $data = [
            'bulan' => $start->locale('id')->isoFormat('MMMM'),
            'tahun' => $start->year,
            'hari_belajar_efektif' => 0,
            'hari_pertama_masuk' => 0,
            'tka_kemampuan' => 0,
            'mengikuti_upacara' => 0,
            'penyerahan_rapor' => 0,
            'jumla_hari_efektif' => 0,
            'libur_akhir_semester' => 0,
            'hari_minggu' => 0,
            'hari_sabtu' => 0,
            'libur_umum' => 0,
            'libur_ramadhan' => 0,
            'jumlah_hari_libur' => 0,
            'jumlah_hari' => 0,
        ];
        
        // Count days
        foreach ($period as $date) {
            $data['jumlah_hari']++;
            
            if ($date->isSunday()) {
                $data['hari_minggu']++;
            } elseif ($date->isSaturday()) {
                $data['hari_sabtu']++;
            } else {
                // Weekday - count as study day unless it's a holiday
                $isHoliday = $this->isHoliday($date, $semester);
                
                if (!$isHoliday) {
                    $data['hari_belajar_efektif']++;
                }
            }
        }
        
        // Get activities for this month
        $activities = Activity::where('semester_id', $semester->id)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)
                         ->where('end_date', '>=', $end);
                  });
            })
            ->with('activityType')
            ->get();
        
        foreach ($activities as $activity) {
            $actStart = Carbon::parse($activity->start_date)->max($start);
            $actEnd = Carbon::parse($activity->end_date)->min($end);
            $actPeriod = CarbonPeriod::create($actStart, $actEnd);
            
            foreach ($actPeriod as $date) {
                if ($date->isWeekday()) {
                    // Count specific activity types
                    if ($activity->activityType->is_holiday) {
                        $data['libur_umum']++;
                    }
                    // You can add more specific counting here based on activity names
                }
            }
        }
        
        $data['jumla_hari_efektif'] = $data['hari_belajar_efektif'];
        $data['jumlah_hari_libur'] = $data['libur_akhir_semester'] + $data['libur_umum'] + $data['libur_ramadhan'];
        
        return $data;
    }
    
    /**
     * Check if a date is a holiday
     */
    private function isHoliday(Carbon $date, Semester $semester): bool
    {
        $activities = Activity::where('semester_id', $semester->id)
            ->whereHas('activityType', function ($q) {
                $q->where('is_holiday', true);
            })
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
            
        return $activities;
    }
    
    /**
     * Calculate yearly totals
     */
    private function calculateYearlyTotal(array $semesters)
    {
        $total = [
            'hari_belajar_efektif' => 0,
            'hari_pertama_masuk' => 0,
            'tka_kemampuan' => 0,
            'mengikuti_upacara' => 0,
            'penyerahan_rapor' => 0,
            'jumla_hari_efektif' => 0,
            'libur_akhir_semester' => 0,
            'hari_minggu' => 0,
            'hari_sabtu' => 0,
            'libur_umum' => 0,
            'libur_ramadhan' => 0,
            'jumlah_hari_libur' => 0,
            'jumlah_hari' => 0,
        ];
        
        foreach ($semesters as $semesterData) {
            foreach ($total as $key => $value) {
                $total[$key] += $semesterData['total'][$key];
            }
        }
        
        return $total;
    }
}

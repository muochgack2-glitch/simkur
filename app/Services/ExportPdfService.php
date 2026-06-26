<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\EffectiveDay;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportPdfService
{
    /**
     * Export yearly calendar (12 months overview)
     * Menggunakan logic dan template yang sama dengan PublicCalendarController
     */
    public function exportYearly(?int $academicYearId = null): string
    {
        $academicYear = $academicYearId 
            ? AcademicYear::with(['semesters.effectiveDay', 'activities.activityType'])->findOrFail($academicYearId)
            : AcademicYear::with(['semesters.effectiveDay', 'activities.activityType'])->active()->firstOrFail();
        
        // Get all activities for this academic year (no grade filter for admin export)
        $activities = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('start_date')
            ->get();

        // Generate 12 months calendar data (SAME LOGIC as PublicCalendarController)
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current->format('Y-m') <= $endDate->format('Y-m')) {
            $monthActivities = $activities->filter(function ($activity) use ($current) {
                $activityStart = Carbon::parse($activity->start_date);
                $activityEnd = Carbon::parse($activity->end_date);
                
                $monthStart = $current->copy()->startOfMonth();
                $monthEnd = $current->copy()->endOfMonth();
                
                return $activityStart->format('Y-m') === $current->format('Y-m') ||
                       $activityEnd->format('Y-m') === $current->format('Y-m') ||
                       ($activityStart->lte($monthEnd) && $activityEnd->gte($monthStart));
            });

            $months[] = [
                'name' => $current->locale('id')->isoFormat('MMMM'),
                'year' => $current->year,
                'days' => $this->generateMonthGrid($current->copy(), $monthActivities),
                'activities' => $monthActivities,
            ];

            $current->addMonth();
        }

        // Get effective days summary
        $effectiveDays = EffectiveDay::whereHas('semester', function ($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })
        ->with('semester')
        ->get();

        // Calculate totals
        $totalDays = $effectiveDays->sum('total_days');
        $totalWeekends = $effectiveDays->sum('weekend_days');
        $totalHolidays = $effectiveDays->sum('holiday_days');
        $totalExams = $effectiveDays->sum('exam_days');
        $totalStudyDays = $effectiveDays->sum('study_days');
        $totalEffectiveWeeks = $effectiveDays->sum('effective_weeks');

        // Get school settings
        $schoolName = Setting::getValue('school_name', 'NAMA SEKOLAH');
        $schoolAddress = Setting::getValue('school_address', 'Alamat Sekolah');
        $schoolLogo = Setting::getValue('school_logo', null);
        
        // Get signature settings
        $signatureCity = Setting::getValue('signature_city', 'Kota');
        $signatureDate = Setting::getValue('signature_date', now()->locale('id')->isoFormat('MMMM YYYY'));
        $signaturePosition = Setting::getValue('signature_position', 'Kepala Sekolah');
        $signatureName = Setting::getValue('signature_name', '________________');
        $signatureNiy = Setting::getValue('signature_niy', '______________');
        $signatureDegree = Setting::getValue('signature_degree', '');
        
        // Fallback to old settings if signature settings are empty
        $principalName = !empty($signatureName) && $signatureName !== '________________' 
            ? $signatureName 
            : Setting::getValue('principal_name', '________________');
        $principalNiy = !empty($signatureNiy) && $signatureNiy !== '______________' 
            ? $signatureNiy 
            : Setting::getValue('principal_niy', '______________');

        $data = [
            'academicYear' => $academicYear,
            'months' => $months,
            'effectiveDays' => $effectiveDays,
            'totalDays' => $totalDays,
            'totalWeekends' => $totalWeekends,
            'totalHolidays' => $totalHolidays,
            'totalExams' => $totalExams,
            'totalStudyDays' => $totalStudyDays,
            'totalEffectiveWeeks' => round($totalEffectiveWeeks, 1),
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'schoolLogo' => $schoolLogo,
            'signatureCity' => $signatureCity,
            'signatureDate' => $signatureDate,
            'signaturePosition' => $signaturePosition,
            'signatureName' => $signatureName,
            'signatureNiy' => $signatureNiy,
            'signatureDegree' => $signatureDegree,
            'principalName' => $principalName,
            'principalNiy' => $principalNiy,
            'selectedGrade' => null, // No grade filter for admin export
        ];
        
        // USE SAME TEMPLATE AND PAPER SIZE as PublicCalendarController
        $pdf = Pdf::loadView('kaldik.pdf', $data)
            ->setPaper([0, 0, 609.45, 935.43], 'portrait') // F4 portrait: 215mm x 330mm
            ->setOption('dpi', 150)
            ->setOption('enable-local-file-access', true);
        
        return $pdf->output();
    }
    
    /**
     * Export monthly calendar
     * Menggunakan logic yang sama dengan PublicCalendarController
     */
    public function exportMonthly(int $year, int $month): string
    {
        $date = Carbon::create($year, $month, 1);
        
        // Get academic year that contains this month
        $academicYear = AcademicYear::where(function($q) use ($date) {
            $q->whereRaw('? BETWEEN start_date AND end_date', [$date->format('Y-m-d')]);
        })
        ->with(['semesters.effectiveDay', 'activities.activityType'])
        ->first();
        
        if (!$academicYear) {
            $academicYear = AcademicYear::active()->with(['semesters.effectiveDay', 'activities.activityType'])->firstOrFail();
        }
        
        $activities = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id)
            ->whereRaw('? BETWEEN start_date AND end_date', [$date->format('Y-m-d')])
            ->orWhere(function($q) use ($year, $month) {
                $q->whereYear('start_date', $year)->whereMonth('start_date', $month);
            })
            ->orWhere(function($q) use ($year, $month) {
                $q->whereYear('end_date', $year)->whereMonth('end_date', $month);
            })
            ->orderBy('start_date')
            ->get();
        
        $monthData = [
            'name' => $date->locale('id')->isoFormat('MMMM'),
            'year' => $date->year,
            'days' => $this->generateMonthGrid($date->copy(), $activities),
            'activities' => $activities,
        ];

        // Get school settings
        $schoolName = Setting::getValue('school_name', 'NAMA SEKOLAH');
        $schoolAddress = Setting::getValue('school_address', 'Alamat Sekolah');
        $schoolLogo = Setting::getValue('school_logo', null);

        $data = [
            'academicYear' => $academicYear,
            'month' => $monthData,
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'schoolLogo' => $schoolLogo,
        ];
        
        // Use A4 landscape for single month view
        $pdf = Pdf::loadView('pdf.calendar-monthly', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('dpi', 150)
            ->setOption('enable-local-file-access', true);
        
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
     * SAME LOGIC as PublicCalendarController->generateMonthGrid()
     */
    private function generateMonthGrid($month, $activities)
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        
        $days = [];
        $current = $start->copy();
        
        while ($current->lte($end)) {
            $isWeekend = in_array($current->dayOfWeek, [0, 6]); // Sunday = 0, Saturday = 6
            
            $dayActivities = $activities->filter(function ($activity) use ($current, $isWeekend) {
                $activityStart = Carbon::parse($activity->start_date);
                $activityEnd = Carbon::parse($activity->end_date);
                
                // Check if activity falls on this date
                $isOnThisDate = $current->between($activityStart, $activityEnd);
                
                if (!$isOnThisDate) {
                    return false;
                }
                
                // If weekend, only show LIBNAS (Libur Nasional)
                if ($isWeekend) {
                    $activityCode = strtoupper($activity->activityType->code ?? '');
                    // Only show LIBNAS activities on weekends
                    return $activityCode === 'LIBNAS';
                }
                
                return true;
            });

            $days[] = [
                'date' => $current->day,
                'dayOfWeek' => $current->dayOfWeek,
                'isWeekend' => $isWeekend,
                'hasActivity' => $dayActivities->count() > 0,
                'activities' => $dayActivities,
            ];

            $current->addDay();
        }

        return $days;
    }
}

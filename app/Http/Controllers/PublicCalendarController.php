<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\EffectiveDay;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicCalendarController extends Controller
{
    /**
     * Display the official calendar page (public, no login required)
     */
    public function index(Request $request)
    {
        $data = $this->getCalendarData($request->get('grade'));
        
        return view('kaldik.index', $data);
    }

    /**
     * Download PDF of official calendar
     */
    public function downloadPdf(Request $request)
    {
        $data = $this->getCalendarData(null); // Always show all grades in PDF
        
        // Replace slash in year to avoid filename error
        $yearSafe = str_replace('/', '-', $data['academicYear']->year);
        
        $pdf = Pdf::loadView('kaldik.pdf', $data)
            ->setPaper([0, 0, 609.45, 935.43], 'portrait') // F4 portrait: 215mm x 330mm
            ->setOption('dpi', 150)
            ->setOption('enable-local-file-access', true);
        
        $fileName = 'Kalender-Pendidikan-' . $yearSafe . '.pdf';
        
        // If preview mode, stream (open in browser)
        if ($request->get('preview')) {
            return $pdf->stream($fileName);
        }
        
        // Default: download
        return $pdf->download($fileName);
    }

    /**
     * Get calendar data for view
     */
    private function getCalendarData($selectedGrade = null)
    {
        // Get active academic year
        $academicYear = AcademicYear::with(['semesters.effectiveDay', 'activities.activityType'])
            ->active()
            ->firstOrFail();

        // Get all activities grouped by month with grade filtering
        $activitiesQuery = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id);
        
        // Apply grade filter if provided
        if ($selectedGrade && in_array($selectedGrade, ['X', 'XI', 'XII'])) {
            $activitiesQuery->forGrade($selectedGrade);
        }
        
        $activities = $activitiesQuery->orderBy('start_date')->get();

        // Generate 12 months calendar data
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        
        // Ensure we process all months including the end month
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
        
        // Get signature settings (new)
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

        return [
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
            'selectedGrade' => $selectedGrade,
        ];
    }

    /**
     * Generate calendar grid for a month
     */
    private function generateMonthGrid($month, $activities)
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        
        $days = [];
        $current = $start->copy();
        
        while ($current->lte($end)) {
            $isWeekend = in_array($current->dayOfWeek, [0, 6]);
            
            $dayActivities = $activities->filter(function ($activity) use ($current, $isWeekend) {
                $activityStart = Carbon::parse($activity->start_date);
                $activityEnd = Carbon::parse($activity->end_date);
                
                // Check if activity falls on this date
                $isOnThisDate = $current->between($activityStart, $activityEnd);
                
                if (!$isOnThisDate) {
                    return false;
                }
                
                // If weekend, only show holiday/libur activities
                if ($isWeekend) {
                    $activityCode = strtoupper($activity->activityType->code ?? '');
                    // Only show activities with "LIB" in code (LIBNAS, LIBSEM, etc)
                    return str_contains($activityCode, 'LIB');
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

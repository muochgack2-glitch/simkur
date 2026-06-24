<?php

namespace App\Livewire\Activity;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\Setting;
use App\Services\ExportPdfService;
use Carbon\Carbon;
use Livewire\Component;

class PreviewExport extends Component
{
    public $showModal = false;
    public $exportType = 'yearly'; // yearly, monthly, list
    public $academicYearId;
    public $year;
    public $month;
    public $paperSize = 'a4'; // a4, letter
    public $orientation = 'portrait'; // portrait, landscape
    
    public $previewData = [];
    
    protected $listeners = ['openPreview'];
    
    public function openPreview($type, $params = [])
    {
        \Log::info('openPreview called', ['type' => $type, 'params' => $params]);
        
        $this->exportType = $type;
        $this->academicYearId = $params['academicYearId'] ?? null;
        $this->year = $params['year'] ?? now()->year;
        $this->month = $params['month'] ?? now()->month;
        $this->paperSize = 'a4';
        
        // Set orientation based on type
        $this->orientation = $type === 'monthly' ? 'landscape' : 'portrait';
        
        try {
            $this->loadPreviewData($params);
            $this->showModal = true;
            \Log::info('Preview loaded successfully', ['type' => $type]);
        } catch (\Exception $e) {
            \Log::error('Failed to load preview', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Gagal memuat preview: ' . $e->getMessage());
        }
    }
    
    public function loadPreviewData($params = [])
    {
        $service = new ExportPdfService();
        
        switch ($this->exportType) {
            case 'yearly':
                $this->loadYearlyData($params);
                break;
            case 'monthly':
                $this->loadMonthlyData($params);
                break;
            case 'list':
                $this->loadListData($params);
                break;
        }
    }
    
    private function loadYearlyData($params = [])
    {
        // Use academicYearId from params if provided
        if (isset($params['academicYearId'])) {
            $academicYear = AcademicYear::findOrFail($params['academicYearId']);
        } else {
            $academicYear = AcademicYear::active()->firstOrFail();
        }
        
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
        
        $this->previewData = [
            'academicYear' => $academicYear,
            'months' => collect($months),
            'schoolName' => Setting::getValue('school_name', 'SMK Negeri 1'),
            'generatedAt' => now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'),
        ];
    }
    
    private function loadMonthlyData($params = [])
    {
        $date = Carbon::create($this->year, $this->month, 1);
        
        $activities = Activity::whereYear('start_date', '<=', $this->year)
            ->whereMonth('start_date', '<=', $this->month)
            ->whereYear('end_date', '>=', $this->year)
            ->whereMonth('end_date', '>=', $this->month)
            ->with(['activityType', 'semester'])
            ->orderBy('start_date')
            ->get();
        
        $calendar = $this->generateMonthCalendar($date, $activities);
        
        $this->previewData = [
            'monthName' => $date->locale('id')->isoFormat('MMMM YYYY'),
            'year' => $this->year,
            'month' => $this->month,
            'activities' => $activities,
            'calendar' => $calendar,
            'schoolName' => Setting::getValue('school_name', 'SMK Negeri 1'),
            'generatedAt' => now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'),
        ];
    }
    
    private function loadListData($params = [])
    {
        $query = Activity::query();
        
        // Apply filters from params
        if (isset($params['academic_year_id'])) {
            $query->where('academic_year_id', $params['academic_year_id']);
        }
        
        if (isset($params['semester_id'])) {
            $query->where('semester_id', $params['semester_id']);
        }
        
        if (isset($params['activity_type_id'])) {
            $query->where('activity_type_id', $params['activity_type_id']);
        }
        
        $activities = $query->with(['activityType', 'semester', 'academicYear'])
            ->orderBy('start_date')
            ->get();
        
        $this->previewData = [
            'activities' => $activities,
            'schoolName' => Setting::getValue('school_name', 'SMK Negeri 1'),
            'generatedAt' => now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'),
            'filters' => $params,
        ];
    }
    
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
                $dayName = strtolower($current->format('l'));
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
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function downloadPdf()
    {
        return redirect()->route('activities.export', [
            'type' => $this->exportType,
            'format' => $this->paperSize,
            'year' => $this->year,
            'month' => $this->month,
        ]);
    }
    
    public function render()
    {
        return view('livewire.activity.preview-export');
    }
}

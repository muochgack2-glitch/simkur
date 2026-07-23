<?php

namespace App\Livewire\Activity;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Semester;
use App\Services\ExportPdfService;
use App\Services\ExportExcelService;
use Livewire\Component;

class Export extends Component
{
    public $exportType = 'yearly'; // yearly, monthly, list, excel
    public $selectedYear;
    public $selectedMonth;
    public $filterAcademicYear;
    public $filterSemester;
    public $filterActivityType;
    
    public $academicYears;
    public $semesters = [];
    public $activityTypes;
    
    public function mount()
    {
        // Check authorization
        if (!auth()->user()->canManageActivities() && !auth()->user()->isGuru()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        
        // Load data
        $this->academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $this->activityTypes = ActivityType::orderBy('sort_order')->get();
        
        // Set defaults
        $activeYear = AcademicYear::active()->first();
        if ($activeYear) {
            $this->filterAcademicYear = $activeYear->id;
            $this->semesters = $activeYear->semesters;
        }
        
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }
    
    public function updatedFilterAcademicYear($value)
    {
        if ($value) {
            $year = AcademicYear::find($value);
            $this->semesters = $year ? $year->semesters : [];
        } else {
            $this->semesters = [];
        }
        $this->filterSemester = null;
    }
    
    public function exportYearly()
    {
        try {
            // Validate
            if (!$this->filterAcademicYear) {
                session()->flash('error', 'Silakan pilih tahun pelajaran terlebih dahulu');
                return;
            }
            
            // Instead of redirect, use Livewire's redirect with full URL
            return $this->redirect(route('activities.export.yearly', ['year' => $this->filterAcademicYear]), navigate: false);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    public function previewYearly()
    {
        if (!$this->filterAcademicYear) {
            session()->flash('error', 'Silakan pilih tahun pelajaran terlebih dahulu');
            return;
        }
        
        $this->dispatch('openPreview', type: 'yearly', params: ['academicYearId' => $this->filterAcademicYear]);
    }
    
    public function exportMonthly()
    {
        $this->validate([
            'selectedYear' => 'required|integer|min:2020|max:2100',
            'selectedMonth' => 'required|integer|min:1|max:12',
        ]);
        
        try {
            // Use Livewire redirect without navigate
            return $this->redirect(route('activities.export.monthly', [
                'year' => $this->selectedYear,
                'month' => $this->selectedMonth
            ]), navigate: false);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    public function previewMonthly()
    {
        $this->validate([
            'selectedYear' => 'required|integer|min:2020|max:2100',
            'selectedMonth' => 'required|integer|min:1|max:12',
        ]);
        
        $this->dispatch('openPreview', type: 'monthly', params: ['year' => $this->selectedYear, 'month' => $this->selectedMonth]);
    }
    
    public function exportActivityList()
    {
        try {
            $filters = [];
            
            if ($this->filterAcademicYear) {
                $filters['academic_year_id'] = $this->filterAcademicYear;
            }
            
            if ($this->filterSemester) {
                $filters['semester_id'] = $this->filterSemester;
            }
            
            if ($this->filterActivityType) {
                $filters['activity_type_id'] = $this->filterActivityType;
            }
            
            // Use Livewire redirect without navigate
            return $this->redirect(route('activities.export.list', $filters), navigate: false);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    public function previewActivityList()
    {
        $filters = [];
        
        if ($this->filterAcademicYear) {
            $filters['academic_year_id'] = $this->filterAcademicYear;
        }
        
        if ($this->filterSemester) {
            $filters['semester_id'] = $this->filterSemester;
        }
        
        if ($this->filterActivityType) {
            $filters['activity_type_id'] = $this->filterActivityType;
        }
        
        $this->dispatch('openPreview', type: 'list', params: $filters);
    }
    
    public function exportExcel()
    {
        try {
            $filters = [];
            
            if ($this->filterAcademicYear) {
                $filters['academic_year_id'] = $this->filterAcademicYear;
            }
            
            if ($this->filterSemester) {
                $filters['semester_id'] = $this->filterSemester;
            }
            
            if ($this->filterActivityType) {
                $filters['activity_type_id'] = $this->filterActivityType;
            }
            
            // Use Livewire redirect without navigate
            return $this->redirect(route('activities.export.excel', $filters), navigate: false);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.activity.export')
            ->layout('components.layouts.app', ['title' => 'Export Kalender - SIM Kurikulum SMK PGRI Blora']);
    }
}

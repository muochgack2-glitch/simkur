<?php

namespace App\Livewire\EffectiveDay;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Services\EffectiveDayService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    public $selectedSemesterId = null;
    
    protected $effectiveDayService;
    
    public function boot(EffectiveDayService $effectiveDayService)
    {
        $this->effectiveDayService = $effectiveDayService;
    }

    public function mount()
    {
        // Auto-select first semester if exists
        $activeYear = AcademicYear::active()->first();
        if ($activeYear && $activeYear->semesters->count() > 0) {
            $this->selectedSemesterId = $activeYear->semesters->first()->id;
        }
    }

    public function recalculate($semesterId = null)
    {
        if (!auth()->user()->canManageActivities()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghitung ulang hari efektif.');
            return;
        }

        if ($semesterId) {
            // Recalculate specific semester
            $semester = \App\Models\Semester::findOrFail($semesterId);
            $calculation = $this->effectiveDayService->calculate($semester);
            $this->effectiveDayService->saveEffectiveDay($semester, $calculation);
            
            ActivityLog::createLog(
                action: 'recalculate',
                modelType: 'EffectiveDay',
                modelId: $semester->effectiveDay?->id,
                description: "Menghitung ulang hari efektif: {$semester->name}"
            );
            
            session()->flash('success', "Hari efektif {$semester->name} berhasil dihitung ulang!");
        } else {
            // Recalculate all semesters
            $count = $this->effectiveDayService->recalculateAll();
            
            ActivityLog::createLog(
                action: 'recalculate',
                modelType: 'EffectiveDay',
                modelId: null,
                description: "Menghitung ulang semua hari efektif"
            );
            
            session()->flash('success', "Berhasil menghitung ulang {$count} semester!");
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Hari Efektif - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        // Get active academic year
        $activeYear = AcademicYear::with(['semesters.effectiveDay'])->active()->first();
        
        // Get semesters with effective days
        $semesters = $activeYear ? $activeYear->semesters : collect();
        
        // Calculate if not exists
        foreach ($semesters as $semester) {
            if (!$semester->effectiveDay) {
                $calculation = $this->effectiveDayService->calculate($semester);
                $this->effectiveDayService->saveEffectiveDay($semester, $calculation);
            }
        }
        
        // Reload to get fresh data
        if ($activeYear) {
            $activeYear->load(['semesters.effectiveDay']);
            $semesters = $activeYear->semesters;
        }

        return view('livewire.effective-day.index', [
            'activeYear' => $activeYear,
            'semesters' => $semesters,
        ]);
    }
}

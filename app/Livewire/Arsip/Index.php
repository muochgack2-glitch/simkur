<?php

namespace App\Livewire\Arsip;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\PklCompanySupervisor;
use App\Models\PklJournal;
use App\Models\PklPlacement;
use App\Models\TeachingJournal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Arsip Tahun Ajaran - SIM Kurikulum')]
class Index extends Component
{
    public $selectedYearId = null;
    public $activeTab = 'pkl_journal';

    public function mount()
    {
        $archived = AcademicYear::where('is_active', false)
            ->orderByDesc('start_date')->first();
        $this->selectedYearId = $archived?->id;
    }

    public function render()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $selectedYear  = $this->selectedYearId ? AcademicYear::find($this->selectedYearId) : null;

        $pklData          = collect();
        $teachingJournals = collect();
        $assessments      = collect();
        $placements       = collect();

        if ($selectedYear) {
            if ($this->activeTab === 'pkl_journal') {
                $pklData = PklCompanySupervisor::where('academic_year_id', $selectedYear->id)
                    ->with(['company.placements' => fn($q) => $q
                        ->where('academic_year_id', $selectedYear->id)
                        ->with(['student', 'journals' => fn($q2) => $q2
                            ->whereNotNull('attendance_status')
                            ->orderBy('journal_date')])
                    ])->get();
            } elseif ($this->activeTab === 'teaching_journal') {
                $teachingJournals = TeachingJournal::where('academic_year_id', $selectedYear->id)
                    ->with(['teacher', 'subject'])
                    ->orderByDesc('date')->get()
                    ->groupBy('teacher_id');
            } elseif ($this->activeTab === 'assessment') {
                $assessments = Assessment::where('academic_year_id', $selectedYear->id)
                    ->with(['creator', 'semester'])
                    ->withCount('questions')
                    ->orderByDesc('created_at')->get();
            } elseif ($this->activeTab === 'pkl_placement') {
                $placements = PklPlacement::where('academic_year_id', $selectedYear->id)
                    ->with(['student', 'company'])
                    ->orderBy('created_at')->get();
            }
        }

        return view('livewire.arsip.index', compact(
            'academicYears','selectedYear','pklData','teachingJournals','assessments','placements'
        ));
    }
}
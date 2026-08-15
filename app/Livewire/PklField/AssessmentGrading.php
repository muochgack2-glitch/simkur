<?php

namespace App\Livewire\PklField;

use App\Models\AcademicYear;
use App\Models\PklAssessmentComponent;
use App\Models\PklCompanySupervisor;
use App\Models\PklFieldAssessment;
use App\Models\PklPlacement;
use Livewire\Component;

class AssessmentGrading extends Component
{
    public $academicYearId = '';
    public $filterCompany = '';
    public $scores = [];
    public $notes = [];

    public function mount()
    {
        $ay = AcademicYear::where('is_active', true)->first();
        $this->academicYearId = $ay?->id ?? '';
    }

    public function saveScores($placementId)
    {
        $placement = PklPlacement::findOrFail($placementId);
        $components = PklAssessmentComponent::where('academic_year_id', $this->academicYearId)->get();

        foreach ($components as $comp) {
            $key = "{$placementId}_{$comp->id}";
            $score = $this->scores[$key] ?? null;
            $note = $this->notes[$key] ?? null;

            if ($score !== null && $score !== '') {
                PklFieldAssessment::updateOrCreate(
                    ['pkl_placement_id' => $placementId, 'component_id' => $comp->id],
                    [
                        'student_id' => $placement->student_id,
                        'score' => $score,
                        'assessor_id' => auth()->id(),
                        'notes' => $note,
                    ]
                );
            }
        }

        session()->flash('success', 'Nilai berhasil disimpan');
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'waka_kurikulum', 'kepala_sekolah']);

        $components = PklAssessmentComponent::where('academic_year_id', $this->academicYearId)->ordered()->get();

        // Get placements
        $query = PklPlacement::with(['student', 'company'])
            ->where('academic_year_id', $this->academicYearId);

        if (!$isAdmin) {
            $companyIds = PklCompanySupervisor::where('teacher_id', $user->id)
                ->where('academic_year_id', $this->academicYearId)
                ->pluck('pkl_company_id');
            $query->whereIn('pkl_company_id', $companyIds);
        }

        if ($this->filterCompany) $query->where('pkl_company_id', $this->filterCompany);

        $placements = $query->whereIn('status', ['active', 'completed'])->get()->sortBy(fn($p) => $p->student->name ?? '');

        // Load existing scores
        $existingScores = PklFieldAssessment::whereIn('pkl_placement_id', $placements->pluck('id'))->get();
        foreach ($existingScores as $es) {
            $key = "{$es->pkl_placement_id}_{$es->component_id}";
            if (!isset($this->scores[$key])) $this->scores[$key] = $es->score;
            if (!isset($this->notes[$key])) $this->notes[$key] = $es->notes;
        }

        // Calculate final scores
        $finalScores = [];
        foreach ($placements as $p) {
            $total = 0;
            $filled = 0;
            foreach ($components as $c) {
                $assessment = $existingScores->where('pkl_placement_id', $p->id)->where('component_id', $c->id)->first();
                if ($assessment && $assessment->score !== null) {
                    $total += ($assessment->score / $c->max_score) * $c->weight;
                    $filled++;
                }
            }
            $finalScores[$p->id] = [
                'total' => round($total, 2),
                'filled' => $filled,
                'complete' => $filled === $components->count(),
            ];
        }

        $companies = \App\Models\PklCompany::active()->orderBy('name')->get();

        return view('livewire.pkl-field.assessment-grading', [
            'components' => $components,
            'placements' => $placements,
            'finalScores' => $finalScores,
            'companies' => $companies,
            'isAdmin' => $isAdmin,
        ]);
    }
}
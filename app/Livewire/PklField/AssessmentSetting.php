<?php

namespace App\Livewire\PklField;

use App\Models\AcademicYear;
use App\Models\PklAssessmentComponent;
use Livewire\Component;

class AssessmentSetting extends Component
{
    public $academicYearId = '';
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $category = 'company';
    public $weight = 0;
    public $max_score = 100;
    public $sort_order = 0;
    public $confirmDelete = null;

    public function mount()
    {
        $ay = AcademicYear::where('is_active', true)->first();
        $this->academicYearId = $ay?->id ?? '';
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $c = PklAssessmentComponent::findOrFail($id);
            $this->editingId = $c->id;
            $this->name = $c->name;
            $this->category = $c->category;
            $this->weight = $c->weight;
            $this->max_score = $c->max_score;
            $this->sort_order = $c->sort_order;
        } else {
            $this->editingId = null;
            $this->reset(['name', 'weight', 'sort_order']);
            $this->category = 'company';
            $this->max_score = 100;
        }
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:school,company',
            'weight' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|integer|min:1',
        ]);

        $data = [
            'academic_year_id' => $this->academicYearId,
            'name' => $this->name,
            'category' => $this->category,
            'weight' => $this->weight,
            'max_score' => $this->max_score,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingId) {
            PklAssessmentComponent::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Komponen diperbarui');
        } else {
            PklAssessmentComponent::create($data);
            session()->flash('success', 'Komponen ditambahkan');
        }

        $this->showForm = false;
    }

    public function delete($id)
    {
        PklAssessmentComponent::findOrFail($id)->delete();
        session()->flash('success', 'Komponen dihapus');
        $this->confirmDelete = null;
    }

    public function seedDefaults()
    {
        $defaults = [
            ['name' => 'Disiplin & Kehadiran', 'category' => 'company', 'weight' => 20, 'sort_order' => 1],
            ['name' => 'Kompetensi Teknis', 'category' => 'company', 'weight' => 30, 'sort_order' => 2],
            ['name' => 'Kerjasama & Komunikasi', 'category' => 'company', 'weight' => 15, 'sort_order' => 3],
            ['name' => 'Inisiatif & Kreativitas', 'category' => 'company', 'weight' => 10, 'sort_order' => 4],
            ['name' => 'Kelengkapan Jurnal', 'category' => 'school', 'weight' => 10, 'sort_order' => 5],
            ['name' => 'Laporan PKL', 'category' => 'school', 'weight' => 15, 'sort_order' => 6],
        ];

        foreach ($defaults as $d) {
            PklAssessmentComponent::firstOrCreate(
                ['academic_year_id' => $this->academicYearId, 'name' => $d['name']],
                array_merge($d, ['academic_year_id' => $this->academicYearId, 'max_score' => 100])
            );
        }

        session()->flash('success', '6 komponen default berhasil dibuat');
    }

    public function render()
    {
        $components = PklAssessmentComponent::where('academic_year_id', $this->academicYearId)
            ->ordered()->get();

        $totalWeight = $components->sum('weight');

        return view('livewire.pkl-field.assessment-setting', [
            'components' => $components,
            'totalWeight' => $totalWeight,
        ]);
    }
}
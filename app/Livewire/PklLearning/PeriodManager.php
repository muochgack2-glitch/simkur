<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklPeriod;

class PeriodManager extends BaseComponent
{
    public $periods = [];
    public $showForm = false;
    public $editingId = null;

    // Form
    public $period_number = '';
    public $title = '';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $is_active = true;

    public function mount()
    {
        $this->loadPeriods();
    }

    public function loadPeriods()
    {
        $ay = AcademicYear::where('is_active', true)->first();
        if (!$ay) return;

        $this->periods = PklPeriod::where('academic_year_id', $ay->id)
            ->orderBy('period_number')
            ->withCount('courses')
            ->get();
    }

    public function create()
    {
        $this->resetForm();
        $ay = AcademicYear::where('is_active', true)->first();
        $lastNum = PklPeriod::where('academic_year_id', $ay->id)->max('period_number') ?? 0;
        $this->period_number = $lastNum + 1;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $period = PklPeriod::findOrFail($id);
        $this->editingId = $id;
        $this->period_number = $period->period_number;
        $this->title = $period->title;
        $this->description = $period->description ?? '';
        $this->start_date = $period->start_date->format('Y-m-d');
        $this->end_date = $period->end_date->format('Y-m-d');
        $this->is_active = $period->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'period_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $ay = AcademicYear::where('is_active', true)->first();

        $data = [
            'academic_year_id' => $ay->id,
            'period_number' => $this->period_number,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            PklPeriod::where('id', $this->editingId)->update($data);
            session()->flash('success', 'Periode berhasil diupdate!');
        } else {
            PklPeriod::create($data);
            session()->flash('success', 'Periode berhasil ditambahkan!');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->loadPeriods();
    }

    public function delete($id)
    {
        $period = PklPeriod::findOrFail($id);
        if ($period->courses()->count() > 0) {
            session()->flash('error', 'Tidak bisa hapus periode yang sudah memiliki course!');
            return;
        }
        $period->delete();
        session()->flash('success', 'Periode berhasil dihapus!');
        $this->loadPeriods();
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->period_number = '';
        $this->title = '';
        $this->description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.pkl-learning.period-manager');
    }
}
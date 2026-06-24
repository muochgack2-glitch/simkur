<?php

namespace App\Livewire\AcademicYear;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public AcademicYear $academicYear;
    
    public $year = '';
    public $start_date = '';
    public $end_date = '';
    public $is_active = false;

    protected function rules()
    {
        return [
            'year' => 'required|string|regex:/^\d{4}\/\d{4}$/|unique:academic_years,year,' . $this->academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    protected $messages = [
        'year.required' => 'Tahun pelajaran wajib diisi.',
        'year.unique' => 'Tahun pelajaran sudah ada.',
        'year.regex' => 'Format tahun harus YYYY/YYYY (contoh: 2024/2025).',
        'start_date.required' => 'Tanggal mulai wajib diisi.',
        'start_date.date' => 'Format tanggal mulai tidak valid.',
        'end_date.required' => 'Tanggal selesai wajib diisi.',
        'end_date.date' => 'Format tanggal selesai tidak valid.',
        'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
    ];

    public function mount($id)
    {
        $this->academicYear = AcademicYear::findOrFail($id);
        
        $this->year = $this->academicYear->year;
        $this->start_date = $this->academicYear->start_date->format('Y-m-d');
        $this->end_date = $this->academicYear->end_date->format('Y-m-d');
        $this->is_active = $this->academicYear->is_active;
    }

    public function update()
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengubah tahun pelajaran.');
            return;
        }

        $this->validate();

        $this->academicYear->update([
            'year' => $this->year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        // Update semester dates if academic year dates changed
        if ($this->academicYear->wasChanged(['start_date', 'end_date'])) {
            $this->updateSemesterDates();
        }

        ActivityLog::createLog(
            action: 'update',
            modelType: 'AcademicYear',
            modelId: $this->academicYear->id,
            description: "Mengubah tahun pelajaran: {$this->academicYear->year}"
        );

        session()->flash('success', 'Tahun pelajaran berhasil diperbarui!');

        return redirect()->route('academic-years.index');
    }

    private function updateSemesterDates()
    {
        $semesters = $this->academicYear->semesters;
        
        if ($semesters->count() === 2) {
            // Update Semester Ganjil (Juli - Desember)
            $semesterGanjil = $semesters->where('type', 'ganjil')->first();
            if ($semesterGanjil) {
                $semesterGanjil->update([
                    'start_date' => $this->start_date,
                    'end_date' => Carbon::parse($this->start_date)->addMonths(5)->endOfMonth(),
                ]);
            }

            // Update Semester Genap (Januari - Juni)
            $semesterGenap = $semesters->where('type', 'genap')->first();
            if ($semesterGenap) {
                $semesterGenap->update([
                    'start_date' => Carbon::parse($this->start_date)->addMonths(6),
                    'end_date' => $this->end_date,
                ]);
            }
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Tahun Pelajaran - e-KALDIK')]
    public function render()
    {
        return view('livewire.academic-year.edit');
    }
}

<?php

namespace App\Livewire\AcademicYear;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $year = '';
    public $start_date = '';
    public $end_date = '';
    public $is_active = false;

    protected $rules = [
        'year' => 'required|string|unique:academic_years,year|regex:/^\d{4}\/\d{4}$/',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ];

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

    public function mount()
    {
        // Set default dates (Juli - Juni next year)
        $currentYear = now()->year;
        $this->start_date = Carbon::create($currentYear, 7, 1)->format('Y-m-d');
        $this->end_date = Carbon::create($currentYear + 1, 6, 30)->format('Y-m-d');
        $this->year = "{$currentYear}/" . ($currentYear + 1);
    }

    public function save()
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk menambah tahun pelajaran.');
            return;
        }

        $this->validate();

        // Create academic year (semesters will be auto-created via model observer)
        $academicYear = AcademicYear::create([
            'year' => $this->year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        ActivityLog::createLog(
            action: 'create',
            modelType: 'AcademicYear',
            modelId: $academicYear->id,
            description: "Membuat tahun pelajaran: {$academicYear->year}"
        );

        session()->flash('success', 'Tahun pelajaran berhasil ditambahkan! 2 semester telah dibuat otomatis.');

        return redirect()->route('academic-years.index');
    }

    public function generateYear()
    {
        // Auto-generate year format from start_date
        if ($this->start_date) {
            $startYear = Carbon::parse($this->start_date)->year;
            $this->year = "{$startYear}/" . ($startYear + 1);
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Tahun Pelajaran - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.academic-year.create');
    }
}

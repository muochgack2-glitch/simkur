<?php

namespace App\Livewire\Assessment;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\Semester;
use Livewire\Component;

class Edit extends Component
{
    public Assessment $assessment;
    
    public $title = '';
    public $description = '';
    public $academic_year_id = '';
    public $semester_id = '';
    public $target_grades = [];
    public $target_majors = [];
    public $start_date = '';
    public $end_date = '';
    public $is_active = true;

    public $availableGrades = ['X', 'XI', 'XII'];
    public $availableMajors = ['MPLB', 'AKL', 'BUSANA'];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'target_grades' => 'nullable|array',
            'target_majors' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'title.required' => 'Judul asesmen harus diisi',
        'academic_year_id.required' => 'Tahun ajaran harus dipilih',
        'semester_id.required' => 'Semester harus dipilih',
        'start_date.required' => 'Tanggal mulai harus diisi',
        'end_date.required' => 'Tanggal selesai harus diisi',
        'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
    ];

    public function mount($id)
    {
        $this->assessment = Assessment::findOrFail($id);
        
        $this->title = $this->assessment->title;
        $this->description = $this->assessment->description;
        $this->academic_year_id = $this->assessment->academic_year_id;
        $this->semester_id = $this->assessment->semester_id;
        $this->target_grades = $this->assessment->target_grades ?? [];
        $this->target_majors = $this->assessment->target_majors ?? [];
        $this->start_date = $this->assessment->start_date->format('Y-m-d');
        $this->end_date = $this->assessment->end_date->format('Y-m-d');
        $this->is_active = $this->assessment->is_active;
    }

    public function updatedAcademicYearId($value)
    {
        $this->semester_id = '';
    }

    public function update()
    {
        $this->validate();

        // If no target grades selected, set to null (means all grades)
        $targetGrades = empty($this->target_grades) ? null : $this->target_grades;
        $targetMajors = empty($this->target_majors) ? null : $this->target_majors;

        $this->assessment->update([
            'title' => $this->title,
            'description' => $this->description,
            'academic_year_id' => $this->academic_year_id,
            'semester_id' => $this->semester_id,
            'target_grades' => $targetGrades,
            'target_majors' => $targetMajors,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Asesmen berhasil diperbarui!');
        return redirect()->route('assessment.index');
    }

    public function render()
    {
        $academicYears = AcademicYear::where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $semesters = [];
        if ($this->academic_year_id) {
            $semesters = Semester::where('academic_year_id', $this->academic_year_id)->get();
        }

        return view('livewire.assessment.edit', [
            'academicYears' => $academicYears,
            'semesters' => $semesters,
        ])->layout('components.layouts.app');
    }
}

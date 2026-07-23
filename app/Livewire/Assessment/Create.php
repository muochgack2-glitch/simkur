<?php

namespace App\Livewire\Assessment;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\Semester;
use Livewire\Component;

class Create extends Component
{
    public $title = '';
    public $description = '';
    public $assessment_type = 'vark';
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
            'assessment_type' => 'required|in:vark,diagnostic',
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
        'assessment_type.required' => 'Tipe asesmen harus dipilih',
        'academic_year_id.required' => 'Tahun ajaran harus dipilih',
        'semester_id.required' => 'Semester harus dipilih',
        'start_date.required' => 'Tanggal mulai harus diisi',
        'end_date.required' => 'Tanggal selesai harus diisi',
        'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
    ];

    public function mount()
    {
        // Set default academic year & semester
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $this->academic_year_id = $activeYear->id;
            $activeSemester = $activeYear->semesters()->first();
            if ($activeSemester) {
                $this->semester_id = $activeSemester->id;
            }
        }

        // Set default dates
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addMonth()->format('Y-m-d');
    }

    public function updatedAcademicYearId($value)
    {
        $this->semester_id = '';
    }

    public function save()
    {
        $this->validate();

        // If no target grades selected, set to null (means all grades)
        $targetGrades = empty($this->target_grades) ? null : $this->target_grades;
        $targetMajors = empty($this->target_majors) ? null : $this->target_majors;

        Assessment::create([
            'title' => $this->title,
            'description' => $this->description,
            'assessment_type' => $this->assessment_type,
            'academic_year_id' => $this->academic_year_id,
            'semester_id' => $this->semester_id,
            'target_grades' => $targetGrades,
            'target_majors' => $targetMajors,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Asesmen berhasil dibuat!');
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

        return view('livewire.assessment.create', [
            'academicYears' => $academicYears,
            'semesters' => $semesters,
        ])->layout('components.layouts.app');
    }
}

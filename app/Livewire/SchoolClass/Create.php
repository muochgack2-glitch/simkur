<?php

namespace App\Livewire\SchoolClass;

use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $grade = 'X';
    public $major = 'MPLB';
    public $academic_year_id = '';
    public $homeroom_teacher_id = '';
    public $capacity = 36;
    public $room = '';
    public $is_active = true;

    public function mount()
    {
        // Set default academic year to active one
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $this->academic_year_id = $activeYear->id;
        }

        // Auto-generate class name
        $this->updateClassName();
    }

    public function updatedGrade()
    {
        $this->updateClassName();
    }

    public function updatedMajor()
    {
        $this->updateClassName();
    }

    private function updateClassName()
    {
        $this->name = SchoolClass::generateClassName($this->grade, $this->major);
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'grade' => 'required|in:X,XI,XII',
        'major' => 'required|in:MPLB,AKL,BUSANA',
        'academic_year_id' => 'required|exists:academic_years,id',
        'homeroom_teacher_id' => 'nullable|exists:users,id',
        'capacity' => 'required|integer|min:1|max:50',
        'room' => 'nullable|string|max:50',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Nama kelas wajib diisi.',
        'grade.required' => 'Tingkat kelas wajib dipilih.',
        'major.required' => 'Jurusan wajib dipilih.',
        'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
        'capacity.required' => 'Kapasitas wajib diisi.',
        'capacity.min' => 'Kapasitas minimal 1 siswa.',
        'capacity.max' => 'Kapasitas maksimal 50 siswa.',
    ];

    public function save()
    {
        $this->validate();

        // Check if class already exists for this academic year
        $exists = SchoolClass::where('academic_year_id', $this->academic_year_id)
            ->where('grade', $this->grade)
            ->where('major', $this->major)
            ->exists();

        if ($exists) {
            $this->addError('name', 'Kelas ini sudah ada untuk tahun ajaran yang dipilih.');
            return;
        }

        SchoolClass::create([
            'name' => $this->name,
            'grade' => $this->grade,
            'major' => $this->major,
            'academic_year_id' => $this->academic_year_id,
            'homeroom_teacher_id' => $this->homeroom_teacher_id ?: null,
            'capacity' => $this->capacity,
            'room' => $this->room,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Kelas berhasil ditambahkan.');
        return $this->redirect(route('classes.index'), navigate: true);
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Kelas - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();

        return view('livewire.school-class.create', [
            'academicYears' => $academicYears,
            'teachers' => $teachers,
        ]);
    }
}

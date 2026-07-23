<?php

namespace App\Livewire\SchoolClass;

use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filterGrade = 'all';
    public $filterMajor = 'all';
    public $filterAcademicYear = 'current';
    public $search = '';

    protected $queryString = ['filterGrade', 'filterMajor', 'filterAcademicYear', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterGrade()
    {
        $this->resetPage();
    }

    public function updatingFilterMajor()
    {
        $this->resetPage();
    }

    public function updatingFilterAcademicYear()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $class = SchoolClass::findOrFail($id);
        
        // Check if class has students
        if ($class->students()->count() > 0) {
            session()->flash('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa.');
            return;
        }

        $class->delete();
        session()->flash('success', 'Kelas berhasil dihapus.');
    }

    public function autoGenerate()
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeAcademicYear) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
            return;
        }

        SchoolClass::autoGenerateClasses($activeAcademicYear->id);
        
        session()->flash('success', '9 kelas standar berhasil dibuat untuk tahun ajaran ' . $activeAcademicYear->name);
    }

    #[Layout('components.layouts.app')]
    #[Title('Master Data Kelas - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = SchoolClass::with(['academicYear', 'homeroomTeacher']);

        // Search
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Filter by grade
        if ($this->filterGrade !== 'all') {
            $query->where('grade', $this->filterGrade);
        }

        // Filter by major
        if ($this->filterMajor !== 'all') {
            $query->where('major', $this->filterMajor);
        }

        // Filter by academic year
        if ($this->filterAcademicYear === 'current') {
            $query->whereHas('academicYear', function($q) {
                $q->where('is_active', true);
            });
        } elseif ($this->filterAcademicYear !== 'all') {
            $query->where('academic_year_id', $this->filterAcademicYear);
        }

        $classes = $query->orderBy('grade')
                        ->orderBy('major')
                        ->paginate(15);

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        return view('livewire.school-class.index', [
            'classes' => $classes,
            'academicYears' => $academicYears,
            'activeAcademicYear' => $activeAcademicYear,
        ]);
    }
}

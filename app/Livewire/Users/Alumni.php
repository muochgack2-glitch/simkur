<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Alumni extends Component
{
    use WithPagination;

    public $search = '';
    public $graduationYear = '';
    public $major = '';
    public $selectedAlumni;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGraduationYear()
    {
        $this->resetPage();
    }

    public function updatingMajor()
    {
        $this->resetPage();
    }

    public function viewProfile($alumniId)
    {
        $this->selectedAlumni = User::with('schoolClass')->find($alumniId);
    }

    public function closeProfile()
    {
        $this->selectedAlumni = null;
    }

    #[Layout('components.layouts.app')]
    #[Title('Daftar Alumni - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = User::alumni()
            ->where('role', 'siswa')
            ->orderBy('graduation_year', 'desc')
            ->orderBy('name');

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        // Graduation year filter
        if ($this->graduationYear) {
            $query->where('graduation_year', $this->graduationYear);
        }

        // Major filter
        if ($this->major) {
            $query->where('major', $this->major);
        }

        $alumni = $query->paginate(20);

        // Get available graduation years
        $graduationYears = User::alumni()
            ->distinct()
            ->orderBy('graduation_year', 'desc')
            ->pluck('graduation_year')
            ->filter();

        // Statistics
        $stats = [
            'total' => User::alumni()->count(),
            'mplb' => User::alumni()->where('major', 'MPLB')->count(),
            'akl' => User::alumni()->where('major', 'AKL')->count(),
            'busana' => User::alumni()->where('major', 'BUSANA')->count(),
        ];

        return view('livewire.users.alumni', [
            'alumni' => $alumni,
            'graduationYears' => $graduationYears,
            'stats' => $stats,
        ]);
    }
}

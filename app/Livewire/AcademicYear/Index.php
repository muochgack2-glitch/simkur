<?php

namespace App\Livewire\AcademicYear;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showArchived = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'showArchived' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function activate($id)
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengaktifkan tahun pelajaran.');
            return;
        }

        $academicYear = AcademicYear::findOrFail($id);
        
        // Deactivate all others (handled in model)
        $academicYear->update(['is_active' => true]);

        ActivityLog::createLog(
            action: 'activate',
            modelType: 'AcademicYear',
            modelId: $id,
            description: "Mengaktifkan tahun pelajaran: {$academicYear->year}"
        );

        session()->flash('success', "Tahun pelajaran {$academicYear->year} berhasil diaktifkan!");
    }

    public function archive($id)
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengarsipkan tahun pelajaran.');
            return;
        }

        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update([
            'is_archived' => true,
            'is_active' => false,
        ]);

        ActivityLog::createLog(
            action: 'archive',
            modelType: 'AcademicYear',
            modelId: $id,
            description: "Mengarsipkan tahun pelajaran: {$academicYear->year}"
        );

        session()->flash('success', "Tahun pelajaran {$academicYear->year} berhasil diarsipkan!");
    }

    public function unarchive($id)
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengembalikan tahun pelajaran.');
            return;
        }

        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update(['is_archived' => false]);

        ActivityLog::createLog(
            action: 'unarchive',
            modelType: 'AcademicYear',
            modelId: $id,
            description: "Mengembalikan dari arsip: {$academicYear->year}"
        );

        session()->flash('success', "Tahun pelajaran {$academicYear->year} berhasil dikembalikan dari arsip!");
    }

    public function delete($id)
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk menghapus tahun pelajaran.');
            return;
        }

        $academicYear = AcademicYear::findOrFail($id);
        
        // Check if has activities
        if ($academicYear->activities()->count() > 0) {
            $this->addError('error', 'Tahun pelajaran tidak dapat dihapus karena masih memiliki kegiatan.');
            return;
        }

        $year = $academicYear->year;
        $academicYear->delete();

        ActivityLog::createLog(
            action: 'delete',
            modelType: 'AcademicYear',
            modelId: $id,
            description: "Menghapus tahun pelajaran: {$year}"
        );

        session()->flash('success', "Tahun pelajaran {$year} berhasil dihapus!");
    }

    #[Layout('components.layouts.app')]
    #[Title('Tahun Pelajaran - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = AcademicYear::query();

        if ($this->search) {
            $query->where('year', 'like', "%{$this->search}%");
        }

        if ($this->showArchived) {
            $query->archived();
        } else {
            $query->notArchived();
        }

        $academicYears = $query->latest()->paginate(10);

        return view('livewire.academic-year.index', [
            'academicYears' => $academicYears,
        ]);
    }
}

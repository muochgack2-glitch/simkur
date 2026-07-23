<?php

namespace App\Livewire\Subject;

use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, active, inactive

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $subject = Subject::findOrFail($id);
        
        // Check if subject is being used by teachers
        if ($subject->teachers()->count() > 0) {
            session()->flash('error', 'Mata pelajaran tidak dapat dihapus karena sedang diampu oleh guru.');
            return;
        }

        $subject->delete();
        session()->flash('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->is_active = !$subject->is_active;
        $subject->save();

        $status = $subject->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Mata pelajaran berhasil {$status}.");
    }

    #[Layout('components.layouts.app')]
    #[Title('Master Data Mata Pelajaran - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = Subject::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        $subjects = $query->orderBy('name')->paginate(15);

        return view('livewire.subject.index', [
            'subjects' => $subjects,
        ]);
    }
}

<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, active, inactive
    public $filterType = 'all'; // all, vark, diagnostic

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (!auth()->user()->canManageAssessments()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus asesmen.');
            return;
        }

        $assessment = Assessment::findOrFail($id);
        
        // Check if has responses
        if ($assessment->responses()->exists()) {
            session()->flash('error', 'Tidak dapat menghapus asesmen yang sudah memiliki jawaban siswa.');
            return;
        }

        $assessment->delete();
        session()->flash('success', 'Asesmen berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        if (!auth()->user()->canManageAssessments()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah status asesmen.');
            return;
        }

        $assessment = Assessment::findOrFail($id);
        $assessment->is_active = !$assessment->is_active;
        $assessment->save();

        session()->flash('success', 'Status asesmen berhasil diubah.');
    }

    public function render()
    {
        $assessments = Assessment::with(['academicYear', 'semester', 'creator'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterType !== 'all', function ($query) {
                $query->where('assessment_type', $this->filterType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.assessment.index', [
            'assessments' => $assessments,
        ])->layout('components.layouts.app');
    }
}

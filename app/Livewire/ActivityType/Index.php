<?php

namespace App\Livewire\ActivityType;

use App\Models\ActivityLog;
use App\Models\ActivityType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = 'all'; // all, exam, holiday, regular

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (!auth()->user()->canManageActivities()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus jenis kegiatan.');
            return;
        }

        $activityType = ActivityType::findOrFail($id);

        // Check if can be deleted
        if (!$activityType->canBeDeleted()) {
            session()->flash('error', "Jenis kegiatan '{$activityType->name}' tidak dapat dihapus karena sudah digunakan dalam {$activityType->activities_count} kegiatan.");
            return;
        }

        $name = $activityType->name;
        $activityType->delete();

        ActivityLog::createLog(
            action: 'delete',
            modelType: 'ActivityType',
            modelId: $id,
            description: "Menghapus jenis kegiatan: {$name}"
        );

        session()->flash('success', 'Jenis kegiatan berhasil dihapus!');
    }

    #[Layout('components.layouts.app')]
    #[Title('Jenis Kegiatan - e-KALDIK')]
    public function render()
    {
        $query = ActivityType::withCount('activities');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by type
        if ($this->filterType === 'exam') {
            $query->where('is_exam', true);
        } elseif ($this->filterType === 'holiday') {
            $query->where('is_holiday', true);
        } elseif ($this->filterType === 'regular') {
            $query->where('is_exam', false)->where('is_holiday', false);
        }

        $activityTypes = $query->orderBy('name')->paginate(15);

        return view('livewire.activity-type.index', [
            'activityTypes' => $activityTypes,
        ]);
    }
}

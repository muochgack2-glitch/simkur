<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\TeachingMaterial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Approval extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = 'all';
    public $filterSubject = 'all';
    
    // For single approval modal
    public $showApprovalModal = false;
    public $selectedMaterialId = null;
    public $approvalAction = ''; // 'approve' or 'reject'
    public $approvalNotes = '';

    // For bulk operations
    public $selectedMaterials = [];
    public $selectAll = false;
    public $showBulkModal = false;
    public $bulkAction = ''; // 'approve' or 'reject'
    public $bulkNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => 'all'],
        'filterSubject' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedMaterials = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all materials on current page
            $this->selectedMaterials = $this->getMaterialsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedMaterials = [];
        }
    }

    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;
        $this->updatedSelectAll($this->selectAll);
    }

    public function openApprovalModal($id, $action)
    {
        $this->selectedMaterialId = $id;
        $this->approvalAction = $action;
        $this->approvalNotes = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedMaterialId = null;
        $this->approvalAction = '';
        $this->approvalNotes = '';
    }

    public function submitApproval()
    {
        $material = TeachingMaterial::findOrFail($this->selectedMaterialId);

        // Validate
        if ($this->approvalAction === 'reject' && empty($this->approvalNotes)) {
            $this->addError('approvalNotes', 'Catatan penolakan wajib diisi.');
            return;
        }

        // Update status
        if ($this->approvalAction === 'approve') {
            $material->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => null,
            ]);

            session()->flash('success', "Perangkat ajar \"{$material->title}\" berhasil disetujui!");
        } else {
            $material->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $this->approvalNotes,
            ]);

            session()->flash('success', "Perangkat ajar \"{$material->title}\" ditolak. Guru akan menerima catatan revisi.");
        }

        $this->closeApprovalModal();
    }

    // Bulk operations
    public function openBulkModal($action)
    {
        if (empty($this->selectedMaterials)) {
            session()->flash('error', 'Pilih minimal 1 perangkat ajar terlebih dahulu.');
            return;
        }

        $this->bulkAction = $action;
        $this->bulkNotes = '';
        $this->showBulkModal = true;
    }

    public function closeBulkModal()
    {
        $this->showBulkModal = false;
        $this->bulkAction = '';
        $this->bulkNotes = '';
    }

    public function submitBulkOperation()
    {
        // Validate
        if ($this->bulkAction === 'reject' && empty($this->bulkNotes)) {
            $this->addError('bulkNotes', 'Catatan penolakan wajib diisi.');
            return;
        }

        if (empty($this->selectedMaterials)) {
            session()->flash('error', 'Tidak ada perangkat ajar yang dipilih.');
            $this->closeBulkModal();
            return;
        }

        // Get materials
        $materials = TeachingMaterial::whereIn('id', $this->selectedMaterials)
            ->where('status', 'pending_approval')
            ->get();

        if ($materials->isEmpty()) {
            session()->flash('error', 'Tidak ada perangkat ajar yang valid untuk diproses.');
            $this->closeBulkModal();
            return;
        }

        $count = $materials->count();

        // Perform bulk operation
        if ($this->bulkAction === 'approve') {
            foreach ($materials as $material) {
                $material->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_notes' => null,
                ]);
            }

            session()->flash('success', "✅ Berhasil menyetujui {$count} perangkat ajar!");
        } else {
            foreach ($materials as $material) {
                $material->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_notes' => $this->bulkNotes,
                ]);
            }

            session()->flash('success', "❌ Berhasil menolak {$count} perangkat ajar. Guru akan menerima catatan revisi.");
        }

        // Reset
        $this->selectedMaterials = [];
        $this->selectAll = false;
        $this->closeBulkModal();
    }

    private function getMaterialsQuery()
    {
        $query = TeachingMaterial::with(['subject', 'academicYear', 'creator'])
            ->where('status', 'pending_approval')
            ->orderBy('created_at', 'asc');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filters
        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterSubject !== 'all') {
            $query->where('subject_id', $this->filterSubject);
        }

        return $query;
    }

    #[Layout('components.layouts.app')]
    #[Title('Approval Perangkat Ajar - SIMKUR SMK PGRI Blora')]
    public function render()
    {
        $materials = $this->getMaterialsQuery()->paginate(15);

        return view('livewire.teaching-material.approval', [
            'materials' => $materials,
            'subjects' => \App\Models\Subject::orderBy('name')->get(),
        ]);
    }
}

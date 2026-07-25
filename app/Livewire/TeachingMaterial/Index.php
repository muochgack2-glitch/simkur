<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\TeachingMaterial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = 'all';
    public $filterSubject = 'all';
    public $filterGrade = 'all';
    public $filterStatus = 'all';
    public $filterAcademicYear = 'all';
    
    // 8 Dimensi filters
    public $filterDimension1 = false;
    public $filterDimension2 = false;
    public $filterDimension3 = false;
    public $filterDimension4 = false;
    public $filterDimension5 = false;
    public $filterDimension6 = false;
    public $filterDimension7 = false;
    public $filterDimension8 = false;

    // For bulk delete
    public $selectedMaterials = [];
    public $selectAll = false;
    public $showBulkDeleteModal = false;

    // For revision notes
    public $showRevisionModal = false;
    public $revisionMaterialId = null;
    public $revisionNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => 'all'],
        'filterSubject' => ['except' => 'all'],
        'filterGrade' => ['except' => 'all'],
        'filterStatus' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedMaterials = [];
        $this->selectAll = false;
    }

    // New versioning methods
    public function openRevisionModal($id)
    {
        $material = TeachingMaterial::findOrFail($id);

        // Check permission
        if ($material->created_by !== auth()->id() && !auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat revisi.');
            return;
        }

        // Check if can create revision
        if (!$material->canCreateRevision()) {
            session()->flash('error', 'Hanya material yang sudah disetujui yang bisa direvisi.');
            return;
        }

        $this->revisionMaterialId = $id;
        $this->revisionNotes = '';
        $this->showRevisionModal = true;
    }

    public function closeRevisionModal()
    {
        $this->showRevisionModal = false;
        $this->revisionMaterialId = null;
        $this->revisionNotes = '';
    }

    public function createRevision()
    {
        $material = TeachingMaterial::findOrFail($this->revisionMaterialId);

        // Check permission again
        if ($material->created_by !== auth()->id() && !auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat revisi.');
            $this->closeRevisionModal();
            return;
        }

        // Get parent ID
        $parentId = $material->parent_material_id ?? $material->id;
        
        // Calculate next version number
        $latestVersion = TeachingMaterial::where('parent_material_id', $parentId)
            ->orWhere('id', $parentId)
            ->max('version_number');
        $nextVersion = $latestVersion + 1;

        // Clone material to new draft
        $revision = $material->replicate();
        $revision->parent_material_id = $parentId;
        $revision->version_number = $nextVersion;
        $revision->status = 'draft';
        $revision->approval_notes = null;
        $revision->approved_by = null;
        $revision->approved_at = null;
        $revision->download_count = 0;
        $revision->view_count = 0;
        $revision->revision_notes = $this->revisionNotes ?: null;
        $revision->created_by = auth()->id();
        $revision->updated_by = auth()->id();
        $revision->save();

        // Clone attachments if any
        foreach ($material->attachments as $attachment) {
            $newAttachment = $attachment->replicate();
            $newAttachment->teaching_material_id = $revision->id;
            $newAttachment->download_count = 0;
            $newAttachment->uploaded_by = auth()->id();
            $newAttachment->save();
        }

        session()->flash('success', "Revisi berhasil dibuat! Sekarang Anda bisa edit sebagai Draft (v{$nextVersion}).");
        
        $this->closeRevisionModal();
        
        return redirect()->route('teaching-materials.edit', $revision->id);
    }

    public function withdrawMaterial($id)
    {
        $material = TeachingMaterial::findOrFail($id);

        // Check permission
        if ($material->created_by !== auth()->id() && !auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menarik material ini.');
            return;
        }

        // Check if can withdraw
        if (!$material->canBeWithdrawn()) {
            session()->flash('error', 'Hanya material Pending Approval yang bisa ditarik.');
            return;
        }

        // Change status back to draft
        $material->update([
            'status' => 'draft',
            'updated_by' => auth()->id(),
        ]);

        session()->flash('success', "Material berhasil ditarik. Status kembali ke Draft untuk diedit.");
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all materials on current page that are deletable
            $this->selectedMaterials = $this->getMaterialsQuery()
                ->where('status', 'draft')
                ->where(function($q) {
                    $q->where('created_by', auth()->id())
                      ->orWhereRaw('? = 1', [auth()->user()->canManageUsers() ? 1 : 0]);
                })
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedMaterials = [];
        }
    }

    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;
        $this->updatedSelectAll($this->selectAll);
    }

    public function delete($id)
    {
        $material = TeachingMaterial::findOrFail($id);

        // Check permission
        if ($material->created_by !== auth()->id() && !auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus perangkat ajar ini.');
            return;
        }

        // Only allow delete if draft
        if ($material->status !== 'draft') {
            session()->flash('error', 'Hanya perangkat ajar berstatus Draft yang dapat dihapus.');
            return;
        }

        // Delete file if exists
        if ($material->file_path && \Storage::exists($material->file_path)) {
            \Storage::delete($material->file_path);
        }

        // Delete attachments
        foreach ($material->attachments as $attachment) {
            if ($attachment->file_path && \Storage::exists($attachment->file_path)) {
                \Storage::delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $title = $material->title;
        $material->delete();

        session()->flash('success', "Perangkat ajar \"{$title}\" berhasil dihapus!");
    }

    // Bulk delete operations
    public function openBulkDeleteModal()
    {
        if (empty($this->selectedMaterials)) {
            session()->flash('error', 'Pilih minimal 1 perangkat ajar terlebih dahulu.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function closeBulkDeleteModal()
    {
        $this->showBulkDeleteModal = false;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedMaterials)) {
            session()->flash('error', 'Tidak ada perangkat ajar yang dipilih.');
            $this->closeBulkDeleteModal();
            return;
        }

        // Get materials
        $materials = TeachingMaterial::whereIn('id', $this->selectedMaterials)
            ->where('status', 'draft')
            ->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereRaw('? = 1', [auth()->user()->canManageUsers() ? 1 : 0]);
            })
            ->get();

        if ($materials->isEmpty()) {
            session()->flash('error', 'Tidak ada perangkat ajar yang valid untuk dihapus. Hanya Draft yang bisa dihapus.');
            $this->closeBulkDeleteModal();
            return;
        }

        $count = $materials->count();

        // Delete materials
        foreach ($materials as $material) {
            // Delete file if exists
            if ($material->file_path && \Storage::exists($material->file_path)) {
                \Storage::delete($material->file_path);
            }

            // Delete attachments
            foreach ($material->attachments as $attachment) {
                if ($attachment->file_path && \Storage::exists($attachment->file_path)) {
                    \Storage::delete($attachment->file_path);
                }
                $attachment->delete();
            }

            $material->delete();
        }

        session()->flash('success', "🗑️ Berhasil menghapus {$count} perangkat ajar!");

        // Reset
        $this->selectedMaterials = [];
        $this->selectAll = false;
        $this->closeBulkDeleteModal();
    }

    private function getMaterialsQuery()
    {
        $query = TeachingMaterial::with(['subject', 'academicYear', 'creator'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereJsonContains('tags', $this->search);
            });
        }

        // Filters
        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterSubject !== 'all') {
            $query->where('subject_id', $this->filterSubject);
        }

        if ($this->filterGrade !== 'all') {
            $query->where('grade', $this->filterGrade);
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        } else {
            // Default: show approved or own materials
            if (!auth()->user()->canManageUsers()) {
                $query->where(function ($q) {
                    $q->where('status', 'approved')
                      ->orWhere('created_by', auth()->id());
                });
            }
        }

        if ($this->filterAcademicYear !== 'all') {
            $query->where('academic_year_id', $this->filterAcademicYear);
        }

        // Dimension filters
        if ($this->filterDimension1) $query->where('dimension_1_beriman', true);
        if ($this->filterDimension2) $query->where('dimension_2_kebinekaan', true);
        if ($this->filterDimension3) $query->where('dimension_3_gotong_royong', true);
        if ($this->filterDimension4) $query->where('dimension_4_mandiri', true);
        if ($this->filterDimension5) $query->where('dimension_5_bernalar_kritis', true);
        if ($this->filterDimension6) $query->where('dimension_6_kreatif', true);
        if ($this->filterDimension7) $query->where('dimension_7_numerasi', true);
        if ($this->filterDimension8) $query->where('dimension_8_literasi', true);

        return $query;
    }

    #[Layout('components.layouts.app')]
    #[Title('Perangkat Ajar - SIMKUR SMK PGRI Blora')]
    public function render()
    {
        $materials = $this->getMaterialsQuery()->paginate(15);

        // Group by category
        $groupedMaterials = $materials->groupBy('category');

        return view('livewire.teaching-material.index', [
            'materials' => $materials,
            'groupedMaterials' => $groupedMaterials,
            'subjects' => Subject::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }
}

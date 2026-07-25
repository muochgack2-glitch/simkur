<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\TeachingMaterial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class VersionHistory extends Component
{
    public $materialId;
    public $material;
    public $versions;
    public $currentVersion;
    
    // For comparison
    public $showCompareModal = false;
    public $compareVersion1 = null;
    public $compareVersion2 = null;
    public $comparisonData = [];

    public function mount($id)
    {
        $this->materialId = $id;
        $this->material = TeachingMaterial::with(['creator', 'subject', 'academicYear'])
            ->findOrFail($id);
        
        $this->currentVersion = $this->material;
        $this->loadVersions();
    }

    public function loadVersions()
    {
        $this->versions = $this->material->allVersions();
    }

    public function viewVersion($versionId)
    {
        return redirect()->route('teaching-materials.show', $versionId);
    }

    public function openCompareModal($version1Id, $version2Id)
    {
        $this->compareVersion1 = TeachingMaterial::with('attachments')->findOrFail($version1Id);
        $this->compareVersion2 = TeachingMaterial::with('attachments')->findOrFail($version2Id);
        
        $this->prepareComparison();
        
        $this->showCompareModal = true;
    }

    public function closeCompareModal()
    {
        $this->showCompareModal = false;
        $this->compareVersion1 = null;
        $this->compareVersion2 = null;
        $this->comparisonData = [];
    }

    private function prepareComparison()
    {
        $v1 = $this->compareVersion1;
        $v2 = $this->compareVersion2;

        $this->comparisonData = [
            'title' => [
                'v1' => $v1->title,
                'v2' => $v2->title,
                'changed' => $v1->title !== $v2->title,
            ],
            'description' => [
                'v1' => $v1->description,
                'v2' => $v2->description,
                'changed' => $v1->description !== $v2->description,
            ],
            'category' => [
                'v1' => $v1->category_label,
                'v2' => $v2->category_label,
                'changed' => $v1->category !== $v2->category,
            ],
            'subject' => [
                'v1' => $v1->subject ? $v1->subject->name : '-',
                'v2' => $v2->subject ? $v2->subject->name : '-',
                'changed' => $v1->subject_id !== $v2->subject_id,
            ],
            'grade' => [
                'v1' => $v1->grade ?? '-',
                'v2' => $v2->grade ?? '-',
                'changed' => $v1->grade !== $v2->grade,
            ],
            'file_type' => [
                'v1' => strtoupper($v1->file_type),
                'v2' => strtoupper($v2->file_type),
                'changed' => $v1->file_type !== $v2->file_type,
            ],
            'attachments_count' => [
                'v1' => $v1->attachments->count(),
                'v2' => $v2->attachments->count(),
                'changed' => $v1->attachments->count() !== $v2->attachments->count(),
            ],
            'tags' => [
                'v1' => $v1->tags ? implode(', ', $v1->tags) : '-',
                'v2' => $v2->tags ? implode(', ', $v2->tags) : '-',
                'changed' => json_encode($v1->tags) !== json_encode($v2->tags),
            ],
            'dimensions' => [
                'v1' => count($v1->selected_dimensions),
                'v2' => count($v2->selected_dimensions),
                'changed' => json_encode($v1->selected_dimensions) !== json_encode($v2->selected_dimensions),
            ],
            'status' => [
                'v1' => $v1->status_label,
                'v2' => $v2->status_label,
                'changed' => $v1->status !== $v2->status,
            ],
        ];
    }

    public function restoreVersion($versionId)
    {
        $versionToRestore = TeachingMaterial::findOrFail($versionId);
        
        // Check permission
        if ($versionToRestore->created_by !== auth()->id() && !auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk restore versi ini.');
            return;
        }

        // Get parent ID
        $parentId = $versionToRestore->parent_material_id ?? $versionToRestore->id;
        
        // Calculate next version number
        $latestVersion = TeachingMaterial::where('parent_material_id', $parentId)
            ->orWhere('id', $parentId)
            ->max('version_number');
        $nextVersion = $latestVersion + 1;

        // Clone the version to restore
        $restored = $versionToRestore->replicate();
        $restored->parent_material_id = $parentId;
        $restored->version_number = $nextVersion;
        $restored->status = 'draft';
        $restored->approval_notes = null;
        $restored->approved_by = null;
        $restored->approved_at = null;
        $restored->download_count = 0;
        $restored->view_count = 0;
        $restored->revision_notes = "Restored from v{$versionToRestore->version_number}";
        $restored->created_by = auth()->id();
        $restored->updated_by = auth()->id();
        $restored->save();

        // Clone attachments
        foreach ($versionToRestore->attachments as $attachment) {
            $newAttachment = $attachment->replicate();
            $newAttachment->teaching_material_id = $restored->id;
            $newAttachment->download_count = 0;
            $newAttachment->uploaded_by = auth()->id();
            $newAttachment->save();
        }

        session()->flash('success', "Version v{$versionToRestore->version_number} berhasil di-restore sebagai Draft v{$nextVersion}!");
        
        return redirect()->route('teaching-materials.edit', $restored->id);
    }

    #[Layout('components.layouts.app')]
    #[Title('Version History - SIMKUR SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.teaching-material.version-history');
    }
}

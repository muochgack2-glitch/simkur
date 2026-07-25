<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\TeachingMaterial;
use App\Models\TeachingMaterialAttachment;
use App\Models\TeachingMaterialComment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    use WithFileUploads;

    public $materialId;
    public $material;
    public $newComment = '';

    // Attachment Management
    public $showAttachmentModal = false;
    public $editingAttachmentId = null;
    public $attachmentType = 'other';
    public $uploadType = 'file'; // 'file' or 'link'
    public $attachmentFile;
    public $attachmentLink;
    public $attachmentDescription;
    public $isPrimary = false;
    
    // File Preview
    public $showPreviewModal = false;
    public $previewType = ''; // pdf, image, video, office, link
    public $previewUrl = '';
    public $previewTitle = '';
    public $previewFileType = '';

    public function mount($id)
    {
        $this->materialId = $id;
        $this->loadMaterial();

        // Increment view count
        $this->material->incrementViewCount();
    }

    public function loadMaterial()
    {
        $this->material = TeachingMaterial::with([
            'subject',
            'academicYear',
            'creator',
            'approver',
            'comments.user',
            'attachments.uploader'
        ])->findOrFail($this->materialId);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|min:3',
        ]);

        TeachingMaterialComment::create([
            'teaching_material_id' => $this->materialId,
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->loadMaterial();

        session()->flash('success', 'Komentar berhasil ditambahkan!');
    }

    // Attachment Management Methods
    public function openAttachmentModal()
    {
        $this->resetAttachmentForm();
        $this->showAttachmentModal = true;
    }

    public function closeAttachmentModal()
    {
        $this->showAttachmentModal = false;
        $this->resetAttachmentForm();
    }

    public function resetAttachmentForm()
    {
        $this->editingAttachmentId = null;
        $this->attachmentType = 'other';
        $this->uploadType = 'file';
        $this->attachmentFile = null;
        $this->attachmentLink = null;
        $this->attachmentDescription = null;
        $this->isPrimary = false;
        $this->resetErrorBag();
    }

    public function saveAttachment()
    {
        // Validation
        $rules = [
            'attachmentType' => 'required|in:main,lkpd,presentation,video,assessment,rubric,answer_key,reading_material,other',
            'attachmentDescription' => 'nullable|string|max:500',
            'uploadType' => 'required|in:file,link',
        ];

        if ($this->uploadType === 'file') {
            // Validate by extension instead of MIME type to avoid Livewire temp file issues
            if (!$this->attachmentFile) {
                session()->flash('error', 'File wajib diupload.');
                $this->closeAttachmentModal();
                return;
            }
            
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'mp4', 'zip'];
            $extension = strtolower($this->attachmentFile->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                session()->flash('error', 'Format file tidak didukung. Gunakan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, MP4, ZIP');
                $this->closeAttachmentModal();
                return;
            }
            
            // Only validate that it's a file
            $rules['attachmentFile'] = 'file';
        } elseif ($this->uploadType === 'link') {
            $rules['attachmentLink'] = 'required|url|max:500';
        }

        $messages = [
            'attachmentLink.required' => 'Link eksternal wajib diisi.',
            'attachmentLink.url' => 'Format link tidak valid.',
        ];

        $this->validate($rules, $messages);
        
        // Additional check for file size after validation (avoid Flysystem issue)
        if ($this->uploadType === 'file' && $this->attachmentFile) {
            try {
                $fileSize = $this->attachmentFile->getSize();
                if ($fileSize > 102400 * 1024) { // 100MB in bytes
                    session()->flash('error', 'Ukuran file maksimal 100MB.');
                    $this->closeAttachmentModal();
                    return;
                }
            } catch (\Exception $e) {
                // If can't get size, proceed anyway (will be validated during storage)
            }
        }

        $data = [
            'teaching_material_id' => $this->materialId,
            'attachment_type' => $this->attachmentType,
            'is_primary' => $this->isPrimary,
            'description' => $this->attachmentDescription,
            'uploaded_by' => auth()->id(),
        ];

        if ($this->uploadType === 'file') {
            try {
                // Handle file upload
                $extension = $this->attachmentFile->getClientOriginalExtension();
                $fileName = time() . '_' . $this->attachmentFile->getClientOriginalName();
                $path = $this->attachmentFile->storeAs(
                    'teaching-materials/' . $this->materialId . '/attachments',
                    $fileName,
                    'public'
                );

                $data['file_name'] = $this->attachmentFile->getClientOriginalName();
                $data['file_path'] = $path;
                $data['file_type'] = $extension;
                
                // Try to get file size, fallback to 0 if fails
                try {
                    $data['file_size'] = $this->attachmentFile->getSize();
                } catch (\Exception $sizeException) {
                    \Log::warning('Could not get attachment file size, using 0: ' . $sizeException->getMessage());
                    $data['file_size'] = 0;
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal mengupload file. Silakan coba lagi.');
                return;
            }
        } else {
            // Handle link
            $data['file_name'] = 'External Link';
            $data['file_type'] = 'link';
            $data['external_link'] = $this->attachmentLink;
        }

        TeachingMaterialAttachment::create($data);

        $this->loadMaterial();
        $this->closeAttachmentModal();

        session()->flash('success', 'Lampiran berhasil ditambahkan!');
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = TeachingMaterialAttachment::where('teaching_material_id', $this->materialId)
            ->where('id', $attachmentId)
            ->firstOrFail();

        // Delete file from storage
        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        $this->loadMaterial();

        session()->flash('success', 'Lampiran berhasil dihapus!');
    }

    public function canManageAttachments()
    {
        $user = auth()->user();
        
        // Admin & Waka can manage all
        if ($user->isAdmin() || $user->isWakaKurikulum()) {
            return true;
        }
        
        // Owner can manage own materials (only draft)
        if ($this->material->created_by === $user->id && $this->material->status === 'draft') {
            return true;
        }
        
        return false;
    }
    
    // File Preview Methods
    public function previewMainFile()
    {
        if ($this->material->file_type === 'link') {
            $this->previewType = 'link';
            $this->previewUrl = $this->material->external_link;
            $this->previewTitle = $this->material->title;
        } else {
            $this->previewFile($this->material->file_path, $this->material->file_type, $this->material->title);
        }
        
        $this->showPreviewModal = true;
    }
    
    public function previewAttachment($attachmentId)
    {
        $attachment = $this->material->attachments()->findOrFail($attachmentId);
        
        if ($attachment->file_type === 'link') {
            $this->previewType = 'link';
            $this->previewUrl = $attachment->external_link;
            $this->previewTitle = $attachment->file_name;
        } else {
            $this->previewFile($attachment->file_path, $attachment->file_type, $attachment->file_name);
        }
        
        $this->showPreviewModal = true;
    }
    
    private function previewFile($filePath, $fileType, $title)
    {
        $this->previewTitle = $title;
        $this->previewFileType = strtolower($fileType);
        
        // Generate preview URL using route for proper authentication and HTTPS
        $previewRoute = route('teaching-materials.preview', ['path' => base64_encode($filePath)]);
        
        // Determine preview type
        if (in_array($this->previewFileType, ['pdf'])) {
            $this->previewType = 'pdf';
            $this->previewUrl = $previewRoute;
        } elseif (in_array($this->previewFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $this->previewType = 'image';
            $this->previewUrl = $previewRoute;
        } elseif (in_array($this->previewFileType, ['mp4', 'webm', 'ogg'])) {
            $this->previewType = 'video';
            $this->previewUrl = $previewRoute;
        } elseif (in_array($this->previewFileType, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
            // Office files langsung download saja karena tidak bisa di-preview dengan aman
            $this->previewType = 'unsupported';
            $this->previewUrl = '';
        } else {
            $this->previewType = 'unsupported';
            $this->previewUrl = '';
        }
    }
    
    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->previewType = '';
        $this->previewUrl = '';
        $this->previewTitle = '';
        $this->previewFileType = '';
    }

    #[Layout('components.layouts.app')]
    #[Title('Detail Perangkat Ajar - SIMKUR SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.teaching-material.show');
    }
}

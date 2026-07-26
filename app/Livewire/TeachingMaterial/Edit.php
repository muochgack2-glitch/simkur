<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\TeachingMaterial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Livewire\BaseComponent;
use Livewire\WithFileUploads;

class Edit extends BaseComponent
{
    use WithFileUploads;

    public $materialId;
    public $material;

    // Form fields
    public $title;
    public $description;
    public $category;
    public $subject_id;
    public $academic_year_id;
    public $grade;
    public $phase;
    public $semester;
    public $uploadType = 'file'; // 'file' or 'link'
    public $file;
    public $external_link;
    public $is_public = false;
    public $tags = '';
    
    // 8 Dimensi
    public $dimension_1_beriman = false;
    public $dimension_2_kebinekaan = false;
    public $dimension_3_gotong_royong = false;
    public $dimension_4_mandiri = false;
    public $dimension_5_bernalar_kritis = false;
    public $dimension_6_kreatif = false;
    public $dimension_7_numerasi = false;
    public $dimension_8_literasi = false;

    public function mount($id)
    {
        $this->materialId = $id;
        $this->material = TeachingMaterial::findOrFail($id);

        // Check permission
        if ($this->material->created_by !== auth()->id() && !auth()->user()->canManageUsers()) {
            abort(403, 'Unauthorized');
        }

        // Only allow edit if draft
        if ($this->material->status !== 'draft') {
            session()->flash('error', 'Hanya perangkat ajar berstatus Draft yang dapat diedit.');
            return redirect()->route('teaching-materials.index');
        }

        // Populate form fields
        $this->title = $this->material->title;
        $this->description = $this->material->description;
        $this->category = $this->material->category;
        $this->subject_id = $this->material->subject_id;
        $this->academic_year_id = $this->material->academic_year_id;
        $this->grade = $this->material->grade;
        $this->phase = $this->material->phase;
        $this->semester = $this->material->semester;
        $this->is_public = $this->material->is_public;
        $this->tags = $this->material->tags ? implode(', ', $this->material->tags) : '';
        
        $this->dimension_1_beriman = $this->material->dimension_1_beriman;
        $this->dimension_2_kebinekaan = $this->material->dimension_2_kebinekaan;
        $this->dimension_3_gotong_royong = $this->material->dimension_3_gotong_royong;
        $this->dimension_4_mandiri = $this->material->dimension_4_mandiri;
        $this->dimension_5_bernalar_kritis = $this->material->dimension_5_bernalar_kritis;
        $this->dimension_6_kreatif = $this->material->dimension_6_kreatif;
        $this->dimension_7_numerasi = $this->material->dimension_7_numerasi;
        $this->dimension_8_literasi = $this->material->dimension_8_literasi;

        $this->uploadType = $this->material->file_type === 'link' ? 'link' : 'file';
        $this->external_link = $this->material->external_link;
    }

    public function update()
    {
        // Simple inline validation
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:cp,atp,kktp,prota,prosem,modul_ajar,modul_projek,buku_teks,video_pembelajaran,presentasi_infografis,bahan_bacaan,bank_soal,rubrik_penilaian_umum,asesmen_diagnostik,instrumen_uji_kompetensi,program_remedial,program_pengayaan,job_sheet,teaching_factory,pkl',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade' => 'nullable|in:X,XI,XII',
            'phase' => 'nullable|in:E,F',
            'semester' => 'nullable|in:1,2',
            'is_public' => 'boolean',
        ];
        
        // Check file or link if uploading new
        if ($this->uploadType === 'file' && $this->file) {
            // Validate by extension instead of MIME type to avoid Livewire temp file issues
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'mp4', 'zip'];
            $extension = strtolower($this->file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                session()->flash('error', 'Format file tidak didukung. Gunakan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, MP4, ZIP');
                return;
            }
            
            // Only validate that it's a file
            $rules['file'] = 'file';
        }
        
        if ($this->uploadType === 'link' && $this->external_link) {
            $rules['external_link'] = 'url|max:500';
        }
        
        // Validate
        $this->validate($rules);
        
        // Additional check for file size after validation (avoid Flysystem issue)
        if ($this->uploadType === 'file' && $this->file) {
            try {
                $fileSize = $this->file->getSize();
                if ($fileSize > 102400 * 1024) { // 100MB in bytes
                    session()->flash('error', 'Ukuran file maksimal 100MB.');
                    return;
                }
            } catch (\Exception $e) {
                // If can't get size, proceed anyway (will be validated during storage)
            }
        }
        
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'subject_id' => $this->subject_id,
            'academic_year_id' => $this->academic_year_id,
            'grade' => $this->grade,
            'phase' => $this->phase,
            'semester' => $this->semester,
            'is_public' => $this->is_public,
            'dimension_1_beriman' => $this->dimension_1_beriman,
            'dimension_2_kebinekaan' => $this->dimension_2_kebinekaan,
            'dimension_3_gotong_royong' => $this->dimension_3_gotong_royong,
            'dimension_4_mandiri' => $this->dimension_4_mandiri,
            'dimension_5_bernalar_kritis' => $this->dimension_5_bernalar_kritis,
            'dimension_6_kreatif' => $this->dimension_6_kreatif,
            'dimension_7_numerasi' => $this->dimension_7_numerasi,
            'dimension_8_literasi' => $this->dimension_8_literasi,
            'updated_by' => auth()->id(),
        ];

        // Handle tags
        if ($this->tags) {
            $data['tags'] = array_map('trim', explode(',', $this->tags));
        } else {
            $data['tags'] = null;
        }

        // Handle file upload or link
        if ($this->uploadType === 'file' && $this->file) {
            try {
                // Delete old file if exists
                if ($this->material->file_path && \Storage::disk('public')->exists($this->material->file_path)) {
                    \Storage::disk('public')->delete($this->material->file_path);
                }

                $extension = $this->file->getClientOriginalExtension();
                $fileName = time() . '_' . \Str::slug($this->title) . '.' . $extension;
                $path = $this->file->storeAs('teaching-materials/' . $this->category, $fileName, 'public');
                
                $data['file_type'] = $extension;
                $data['file_path'] = $path;
                
                // Try to get file size, fallback to 0 if fails
                try {
                    $data['file_size'] = $this->file->getSize();
                } catch (\Exception $sizeException) {
                    \Log::warning('Could not get file size, using 0: ' . $sizeException->getMessage());
                    $data['file_size'] = 0;
                }
                
                $data['external_link'] = null;
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal mengupload file. Silakan coba lagi.');
                return;
            }
        } elseif ($this->uploadType === 'link') {
            $data['file_type'] = 'link';
            $data['external_link'] = $this->external_link;
        }

        $this->material->update($data);

        session()->flash('success', 'Perangkat ajar berhasil diupdate!');
        
        return redirect()->route('teaching-materials.show', $this->materialId);
    }

    public function submitForApproval()
    {
        $this->material->update([
            'status' => 'pending_approval',
            'updated_by' => auth()->id(),
        ]);

        session()->flash('success', 'Perangkat ajar berhasil disubmit untuk approval!');
        
        return redirect()->route('teaching-materials.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Perangkat Ajar - SIMKUR SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.teaching-material.edit', [
            'subjects' => Subject::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }
}


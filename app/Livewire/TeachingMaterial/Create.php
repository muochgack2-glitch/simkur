<?php

namespace App\Livewire\TeachingMaterial;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\TeachingMaterial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

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

    public function saveDraft()
    {
        try {
            $this->save('draft');
        } catch (\Exception $e) {
            \Log::error('Save draft error: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function submitForApproval()
    {
        \Log::info('submitForApproval called');
        
        try {
            $this->save('pending_approval');
            \Log::info('save completed');
        } catch (\Exception $e) {
            \Log::error('Submit approval error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Error submit: ' . $e->getMessage());
            $this->dispatch('show-error', message: $e->getMessage());
        }
    }

    private function save($status)
    {
        // Build rules step by step untuk avoid issues
        $rules = [];
        $rules['title'] = 'required|string|max:255';
        $rules['description'] = 'nullable|string';
        $rules['category'] = 'required|in:cp,atp,kktp,prota,prosem,modul_ajar,modul_projek,buku_teks,video_pembelajaran,presentasi_infografis,bahan_bacaan,bank_soal,rubrik_penilaian_umum,asesmen_diagnostik,instrumen_uji_kompetensi,program_remedial,program_pengayaan,job_sheet,teaching_factory,pkl';
        $rules['subject_id'] = 'nullable|exists:subjects,id';
        $rules['academic_year_id'] = 'required|exists:academic_years,id';
        $rules['grade'] = 'nullable|in:X,XI,XII';
        $rules['phase'] = 'nullable|in:E,F';
        $rules['semester'] = 'nullable|in:1,2';
        $rules['is_public'] = 'boolean';
        
        // Check file or link
        if ($this->uploadType === 'file') {
            if (!$this->file) {
                session()->flash('error', 'File wajib diupload.');
                return;
            }
            
            // Validate by extension instead of MIME type to avoid Livewire temp file issues
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'mp4', 'zip'];
            $extension = strtolower($this->file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                session()->flash('error', 'Format file tidak didukung. Gunakan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, MP4, ZIP');
                return;
            }
            
            // Only validate that it's a file
            $rules['file'] = 'file';
        } elseif ($this->uploadType === 'link') {
            if (!$this->external_link) {
                session()->flash('error', 'Link eksternal wajib diisi.');
                return;
            }
            $rules['external_link'] = 'url|max:500';
        }
        
        // Validate with try-catch
        try {
            $validated = $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }
        
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
            'status' => $status,
            'created_by' => auth()->id(),
        ];

        // Handle tags
        if ($this->tags) {
            $data['tags'] = array_map('trim', explode(',', $this->tags));
        }

        // Handle file upload or link
        if ($this->uploadType === 'file' && $this->file) {
            try {
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
            } catch (\Exception $e) {
                \Log::error('File upload error: ' . $e->getMessage());
                session()->flash('error', 'Gagal mengupload file: ' . $e->getMessage());
                return;
            }
        } else {
            $data['file_type'] = 'link';
            $data['external_link'] = $this->external_link;
        }

        try {
            $material = TeachingMaterial::create($data);
            
            // Set helpful flash message
            $baseMessage = $status === 'draft' 
                ? '✅ Perangkat ajar berhasil disimpan sebagai draft!' 
                : '✅ Perangkat ajar berhasil disubmit untuk approval!';
            
            $attachmentHint = ' 💡 Tip: Tambahkan lampiran pendukung (LKPD, PPT, Video, Rubrik, dll) untuk melengkapi perangkat ajar Anda.';
            
            session()->flash('success', $baseMessage . $attachmentHint);
            session()->flash('material_id', $material->id);
            session()->flash('show_attachment_hint', true);
            
            return $this->redirect(route('teaching-materials.show', $material->id), navigate: true);
        } catch (\Exception $e) {
            \Log::error('Create material error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
            return;
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Upload Perangkat Ajar - SIMKUR SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.teaching-material.create', [
            'subjects' => Subject::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }
}

<?php

namespace App\Livewire\Activity;

use App\Models\ActivityLog;
use App\Services\ImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Import extends Component
{
    use WithFileUploads;
    
    public $file;
    public $step = 1; // 1: Upload, 2: Preview, 3: Result
    public $previewData = [];
    public $summary = [];
    public $importResult = [];
    
    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls|max:2048', // Max 2MB
    ];
    
    protected $messages = [
        'file.required' => 'File wajib dipilih',
        'file.file' => 'File tidak valid',
        'file.mimes' => 'File harus berformat .xlsx atau .xls',
        'file.max' => 'Ukuran file maksimal 2MB',
    ];
    
    public function mount()
    {
        // Check authorization
        if (!auth()->user()->canManageActivities()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
    }
    
    public function downloadTemplate()
    {
        try {
            $importService = new ImportService();
            $filepath = $importService->generateTemplate();
            
            // Log activity
            ActivityLog::createLog(
                'download',
                'import_template',
                null,
                'Download template import kegiatan'
            );
            
            return response()->download($filepath, basename($filepath))->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat template: ' . $e->getMessage());
        }
    }
    
    public function uploadFile()
    {
        try {
            // Manual validation
            if (!$this->file) {
                session()->flash('error', 'File wajib dipilih');
                return;
            }
            
            // Check file extension
            $extension = $this->file->getClientOriginalExtension();
            if (!in_array($extension, ['xlsx', 'xls'])) {
                session()->flash('error', 'File harus berformat .xlsx atau .xls');
                return;
            }
            
            // Check file size (max 2MB)
            if ($this->file->getSize() > 2048 * 1024) {
                session()->flash('error', 'Ukuran file maksimal 2MB');
                return;
            }
            
            // Ensure temp directory exists
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            // Save file with getRealPath() instead of storeAs()
            $filename = 'import_' . uniqid() . '_' . time() . '.' . $extension;
            $fullPath = $tempPath . '/' . $filename;
            
            // Move uploaded file
            $uploadedPath = $this->file->getRealPath();
            if (!copy($uploadedPath, $fullPath)) {
                throw new \Exception('Gagal menyimpan file upload');
            }
            
            // Verify file exists and readable
            if (!file_exists($fullPath) || !is_readable($fullPath)) {
                throw new \Exception('File tidak dapat dibaca setelah upload');
            }
            
            // Parse and validate
            $importService = new ImportService();
            $result = $importService->parseAndValidate($fullPath);
            
            if (!$result['success']) {
                // Clean up temp file
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                session()->flash('error', $result['message']);
                return;
            }
            
            $this->previewData = $result['data'];
            $this->summary = $result['summary'];
            $this->step = 2;
            
            session()->flash('success', 'File berhasil divalidasi. Silakan review data sebelum import.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }
    
    public function processImport()
    {
        try {
            $importService = new ImportService();
            $result = $importService->processImport($this->previewData, auth()->id());
            
            $this->importResult = $result;
            $this->step = 3;
            
            // Log activity
            ActivityLog::createLog(
                'import',
                'activities',
                null,
                'Import ' . $result['imported'] . ' kegiatan dari Excel'
            );
            
            if ($result['success']) {
                if ($result['failed'] === 0) {
                    session()->flash('success', 'Import berhasil! ' . $result['imported'] . ' kegiatan telah ditambahkan.');
                } else {
                    session()->flash('warning', 'Import selesai dengan ' . $result['failed'] . ' data gagal. ' . $result['imported'] . ' kegiatan berhasil ditambahkan.');
                }
            } else {
                session()->flash('error', $result['message'] ?? 'Import gagal');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal melakukan import: ' . $e->getMessage());
        }
    }
    
    public function downloadErrorLog()
    {
        if (empty($this->importResult['errors'])) {
            session()->flash('error', 'Tidak ada error log untuk diunduh');
            return;
        }
        
        try {
            $importService = new ImportService();
            $filepath = $importService->generateErrorLog($this->importResult['errors']);
            
            return response()->download($filepath, basename($filepath))->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat error log: ' . $e->getMessage());
        }
    }
    
    public function resetForm()
    {
        $this->reset(['file', 'step', 'previewData', 'summary', 'importResult']);
        session()->flash('info', 'Form telah direset');
    }
    
    public function backToActivities()
    {
        return redirect()->route('activities.index');
    }
    
    public function render()
    {
        return view('livewire.activity.import')
            ->layout('components.layouts.app', ['title' => 'Import Kegiatan - e-KALDIK']);
    }
}

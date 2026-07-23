<?php

namespace App\Livewire\Settings;

use App\Models\ActivityLog;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;
    
    public $activeTab = 'school';
    
    // School settings
    public $school_name = '';
    public $school_address = '';
    public $school_phone = '';
    public $school_email = '';
    public $school_logo = '';
    public $logo_file = null; // New: for file upload
    public $principal_name = '';
    public $principal_niy = '';
    
    // Signature settings
    public $signature_city = '';
    public $signature_date = '';
    public $signature_position = 'Kepala Sekolah';
    public $signature_name = '';
    public $signature_niy = '';
    public $signature_degree = '';
    
    // Calendar settings
    public $weekend_days = [];
    public $default_calendar_view = 'month';
    
    // System settings
    public $session_timeout = 120;
    public $items_per_page = 15;
    public $date_format = 'd/m/Y';
    public $enable_activity_conflict_warning = true;
    
    // Import settings
    public $max_import_rows = 1000;
    public $allowed_import_extensions = [];
    public $max_import_file_size = 2048;
    
    // Export settings
    public $pdf_orientation = 'landscape';
    public $pdf_paper_size = 'a4';
    public $include_logo_in_export = true;

    protected function rules()
    {
        return [
            // School
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_phone' => 'required|string|max:50',
            'school_email' => 'required|email|max:255',
            'school_logo' => 'nullable|string|max:255',
            'logo_file' => 'nullable|image|max:2048', // max 2MB
            'principal_name' => 'required|string|max:255',
            'principal_niy' => 'required|string|max:50',
            
            // Signature
            'signature_city' => 'required|string|max:100',
            'signature_date' => 'required|string|max:100',
            'signature_position' => 'required|string|max:100',
            'signature_name' => 'required|string|max:255',
            'signature_niy' => 'required|string|max:50',
            'signature_degree' => 'nullable|string|max:100',
            
            // Calendar
            'weekend_days' => 'array',
            'default_calendar_view' => 'required|in:month,year,list',
            
            // System
            'session_timeout' => 'required|integer|min:5|max:480',
            'items_per_page' => 'required|integer|min:5|max:100',
            'date_format' => 'required|string',
            'enable_activity_conflict_warning' => 'boolean',
            
            // Import
            'max_import_rows' => 'required|integer|min:100|max:10000',
            'allowed_import_extensions' => 'array',
            'max_import_file_size' => 'required|integer|min:512|max:10240',
            
            // Export
            'pdf_orientation' => 'required|in:landscape,portrait',
            'pdf_paper_size' => 'required|in:a4,a3,letter,legal',
            'include_logo_in_export' => 'boolean',
        ];
    }

    protected $messages = [
        'school_name.required' => 'Nama sekolah wajib diisi.',
        'school_address.required' => 'Alamat sekolah wajib diisi.',
        'school_phone.required' => 'Telepon sekolah wajib diisi.',
        'school_email.required' => 'Email sekolah wajib diisi.',
        'school_email.email' => 'Format email tidak valid.',
        'session_timeout.min' => 'Session timeout minimal 5 menit.',
        'session_timeout.max' => 'Session timeout maksimal 480 menit (8 jam).',
        'items_per_page.min' => 'Item per halaman minimal 5.',
        'items_per_page.max' => 'Item per halaman maksimal 100.',
        'max_import_rows.min' => 'Maksimal baris import minimal 100.',
        'max_import_rows.max' => 'Maksimal baris import maksimal 10.000.',
        'max_import_file_size.min' => 'Ukuran file minimal 512 KB.',
        'max_import_file_size.max' => 'Ukuran file maksimal 10 MB (10240 KB).',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // School
        $this->school_name = Setting::getValue('school_name', '');
        $this->school_address = Setting::getValue('school_address', '');
        $this->school_phone = Setting::getValue('school_phone', '');
        $this->school_email = Setting::getValue('school_email', '');
        $this->school_logo = Setting::getValue('school_logo', '');
        $this->principal_name = Setting::getValue('principal_name', '');
        $this->principal_niy = Setting::getValue('principal_niy', '');
        
        // Signature
        $this->signature_city = Setting::getValue('signature_city', '');
        $this->signature_date = Setting::getValue('signature_date', '');
        $this->signature_position = Setting::getValue('signature_position', 'Kepala Sekolah');
        $this->signature_name = Setting::getValue('signature_name', '');
        $this->signature_niy = Setting::getValue('signature_niy', '');
        $this->signature_degree = Setting::getValue('signature_degree', '');
        
        // Calendar
        $this->weekend_days = Setting::getValue('weekend_days', ['saturday', 'sunday']);
        $this->default_calendar_view = Setting::getValue('default_calendar_view', 'month');
        
        // System
        $this->session_timeout = Setting::getValue('session_timeout', 120);
        $this->items_per_page = Setting::getValue('items_per_page', 15);
        $this->date_format = Setting::getValue('date_format', 'd/m/Y');
        $this->enable_activity_conflict_warning = Setting::getValue('enable_activity_conflict_warning', true);
        
        // Import
        $this->max_import_rows = Setting::getValue('max_import_rows', 1000);
        $this->allowed_import_extensions = Setting::getValue('allowed_import_extensions', ['xlsx', 'xls']);
        $this->max_import_file_size = Setting::getValue('max_import_file_size', 2048);
        
        // Export
        $this->pdf_orientation = Setting::getValue('pdf_orientation', 'landscape');
        $this->pdf_paper_size = Setting::getValue('pdf_paper_size', 'a4');
        $this->include_logo_in_export = Setting::getValue('include_logo_in_export', true);
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) {
            $this->addError('error', 'Hanya Admin yang dapat mengubah pengaturan sistem.');
            return;
        }

        $this->validate();

        // Handle logo upload
        if ($this->logo_file) {
            // Delete old logo if exists
            if ($this->school_logo && file_exists(public_path($this->school_logo))) {
                unlink(public_path($this->school_logo));
            }
            
            // Store new logo
            $filename = 'logo-' . time() . '.' . $this->logo_file->getClientOriginalExtension();
            $this->logo_file->storeAs('images', $filename, 'public_root');
            $this->school_logo = 'images/' . $filename;
        }

        // Save School settings
        Setting::setValue('school_name', $this->school_name, 'string', 'school');
        Setting::setValue('school_address', $this->school_address, 'string', 'school');
        Setting::setValue('school_phone', $this->school_phone, 'string', 'school');
        Setting::setValue('school_email', $this->school_email, 'string', 'school');
        Setting::setValue('school_logo', $this->school_logo, 'string', 'school');
        Setting::setValue('principal_name', $this->principal_name, 'string', 'school');
        Setting::setValue('principal_niy', $this->principal_niy, 'string', 'school');
        
        // Save Signature settings
        Setting::setValue('signature_city', $this->signature_city, 'string', 'signature');
        Setting::setValue('signature_date', $this->signature_date, 'string', 'signature');
        Setting::setValue('signature_position', $this->signature_position, 'string', 'signature');
        Setting::setValue('signature_name', $this->signature_name, 'string', 'signature');
        Setting::setValue('signature_niy', $this->signature_niy, 'string', 'signature');
        Setting::setValue('signature_degree', $this->signature_degree, 'string', 'signature');
        
        // Save Calendar settings
        Setting::setValue('weekend_days', $this->weekend_days, 'json', 'calendar');
        Setting::setValue('default_calendar_view', $this->default_calendar_view, 'string', 'calendar');
        
        // Save System settings
        Setting::setValue('session_timeout', $this->session_timeout, 'number', 'system');
        Setting::setValue('items_per_page', $this->items_per_page, 'number', 'system');
        Setting::setValue('date_format', $this->date_format, 'string', 'system');
        Setting::setValue('enable_activity_conflict_warning', $this->enable_activity_conflict_warning, 'boolean', 'system');
        
        // Save Import settings
        Setting::setValue('max_import_rows', $this->max_import_rows, 'number', 'import');
        Setting::setValue('allowed_import_extensions', $this->allowed_import_extensions, 'json', 'import');
        Setting::setValue('max_import_file_size', $this->max_import_file_size, 'number', 'import');
        
        // Save Export settings
        Setting::setValue('pdf_orientation', $this->pdf_orientation, 'string', 'export');
        Setting::setValue('pdf_paper_size', $this->pdf_paper_size, 'string', 'export');
        Setting::setValue('include_logo_in_export', $this->include_logo_in_export, 'boolean', 'export');

        ActivityLog::createLog(
            action: 'update',
            modelType: 'Setting',
            modelId: null,
            description: "Memperbarui pengaturan sistem (group: {$this->activeTab})"
        );

        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    #[Layout('components.layouts.app')]
    #[Title('Pengaturan Sistem - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.settings.index');
    }
}

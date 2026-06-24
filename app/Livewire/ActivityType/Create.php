<?php

namespace App\Livewire\ActivityType;

use App\Models\ActivityLog;
use App\Models\ActivityType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $code = '';
    public $description = '';
    public $color = '#3B82F6'; // blue-500
    public $is_exam = false;
    public $is_holiday = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:activity_types,name',
            'code' => 'required|string|max:20|unique:activity_types,code',
            'description' => 'nullable|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_exam' => 'boolean',
            'is_holiday' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama jenis kegiatan wajib diisi.',
        'name.unique' => 'Nama jenis kegiatan sudah digunakan.',
        'name.max' => 'Nama maksimal 100 karakter.',
        'code.required' => 'Kode jenis kegiatan wajib diisi.',
        'code.unique' => 'Kode jenis kegiatan sudah digunakan.',
        'code.max' => 'Kode maksimal 20 karakter.',
        'description.max' => 'Deskripsi maksimal 255 karakter.',
        'color.required' => 'Warna wajib dipilih.',
        'color.regex' => 'Format warna tidak valid (harus HEX: #RRGGBB).',
    ];

    public function generateCode()
    {
        if (!empty($this->name)) {
            // Generate code from name (uppercase, remove spaces, take first letters)
            $words = explode(' ', trim($this->name));
            $code = '';
            
            foreach ($words as $word) {
                if (!empty($word)) {
                    $code .= strtoupper(substr($word, 0, 1));
                }
            }
            
            // If too short, use first 3 chars of first word
            if (strlen($code) < 2 && isset($words[0])) {
                $code = strtoupper(substr($words[0], 0, 3));
            }
            
            $this->code = $code;
        }
    }

    public function save()
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk menambah jenis kegiatan.');
            return;
        }

        $this->validate();

        $activityType = ActivityType::create([
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'color' => $this->color,
            'is_exam' => $this->is_exam,
            'is_holiday' => $this->is_holiday,
        ]);

        ActivityLog::createLog(
            action: 'create',
            modelType: 'ActivityType',
            modelId: $activityType->id,
            description: "Membuat jenis kegiatan: {$activityType->name}"
        );

        session()->flash('success', 'Jenis kegiatan berhasil ditambahkan!');

        return redirect()->route('activity-types.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Jenis Kegiatan - e-KALDIK')]
    public function render()
    {
        return view('livewire.activity-type.create');
    }
}

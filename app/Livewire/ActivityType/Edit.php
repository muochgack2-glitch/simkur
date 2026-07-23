<?php

namespace App\Livewire\ActivityType;

use App\Models\ActivityLog;
use App\Models\ActivityType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public ActivityType $activityType;
    
    public $name = '';
    public $code = '';
    public $description = '';
    public $color = '';
    public $is_exam = false;
    public $is_holiday = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:activity_types,name,' . $this->activityType->id,
            'code' => 'required|string|max:20|unique:activity_types,code,' . $this->activityType->id,
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

    public function mount($id)
    {
        $this->activityType = ActivityType::withCount('activities')->findOrFail($id);
        
        $this->name = $this->activityType->name;
        $this->code = $this->activityType->code;
        $this->description = $this->activityType->description ?? '';
        $this->color = $this->activityType->color;
        $this->is_exam = $this->activityType->is_exam;
        $this->is_holiday = $this->activityType->is_holiday;
    }

    public function update()
    {
        if (!auth()->user()->canManageActivities()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengubah jenis kegiatan.');
            return;
        }

        $this->validate();

        $this->activityType->update([
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'color' => $this->color,
            'is_exam' => $this->is_exam,
            'is_holiday' => $this->is_holiday,
        ]);

        ActivityLog::createLog(
            action: 'update',
            modelType: 'ActivityType',
            modelId: $this->activityType->id,
            description: "Mengubah jenis kegiatan: {$this->activityType->name}"
        );

        session()->flash('success', 'Jenis kegiatan berhasil diperbarui!');

        return redirect()->route('activity-types.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Jenis Kegiatan - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.activity-type.edit');
    }
}

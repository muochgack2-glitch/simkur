<?php

namespace App\Livewire\Subject;

use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public Subject $subject;
    public $name = '';
    public $code = '';
    public $description = '';
    public $is_active = true;

    public function mount($id)
    {
        $this->subject = Subject::findOrFail($id);
        $this->name = $this->subject->name;
        $this->code = $this->subject->code;
        $this->description = $this->subject->description;
        $this->is_active = $this->subject->is_active;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:subjects,name,' . $this->subject->id,
            'code' => 'nullable|string|max:10|unique:subjects,code,' . $this->subject->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama mata pelajaran wajib diisi.',
        'name.unique' => 'Mata pelajaran sudah ada.',
        'code.unique' => 'Kode sudah digunakan.',
    ];

    public function save()
    {
        $this->validate();

        $this->subject->update([
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Mata pelajaran berhasil diperbarui.');
        return $this->redirect(route('subjects.index'), navigate: true);
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Mata Pelajaran - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.subject.edit');
    }
}

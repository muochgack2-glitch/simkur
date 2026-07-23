<?php

namespace App\Livewire\Subject;

use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $code = '';
    public $description = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255|unique:subjects,name',
        'code' => 'nullable|string|max:10|unique:subjects,code',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Nama mata pelajaran wajib diisi.',
        'name.unique' => 'Mata pelajaran sudah ada.',
        'code.unique' => 'Kode sudah digunakan.',
    ];

    public function save()
    {
        $this->validate();

        Subject::create([
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Mata pelajaran berhasil ditambahkan.');
        return $this->redirect(route('subjects.index'), navigate: true);
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Mata Pelajaran - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.subject.create');
    }
}

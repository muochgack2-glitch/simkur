<?php

namespace App\Livewire\AssessmentLabel;

use App\Models\AssessmentLabel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'all';

    // Form modal
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public bool $isActive = true;

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'name', 'isActive');
        $this->isActive = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $label = AssessmentLabel::findOrFail($id);
        $this->editingId = $id;
        $this->name     = $label->name;
        $this->isActive = $label->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset('editingId', 'name', 'isActive');
    }

    public function save(): void
    {
        $this->validate([
            'name'     => 'required|string|max:100',
            'isActive' => 'boolean',
        ], [
            'name.required' => 'Nama jenis asesmen wajib diisi.',
            'name.max'      => 'Nama tidak boleh lebih dari 100 karakter.',
        ]);

        if ($this->editingId) {
            AssessmentLabel::findOrFail($this->editingId)->update([
                'name'      => $this->name,
                'is_active' => $this->isActive,
            ]);
            session()->flash('success', 'Jenis asesmen berhasil diperbarui.');
        } else {
            AssessmentLabel::create([
                'name'       => $this->name,
                'is_active'  => $this->isActive,
                'created_by' => auth()->id(),
            ]);
            session()->flash('success', 'Jenis asesmen berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        $label = AssessmentLabel::findOrFail($id);
        $label->is_active = !$label->is_active;
        $label->save();

        $status = $label->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Jenis asesmen berhasil {$status}.");
    }

    public function delete(int $id): void
    {
        $label = AssessmentLabel::findOrFail($id);

        if ($label->assessments()->count() > 0) {
            session()->flash('error', 'Jenis asesmen tidak dapat dihapus karena sudah digunakan pada asesmen.');
            return;
        }

        $label->delete();
        session()->flash('success', 'Jenis asesmen berhasil dihapus.');
    }

    #[Layout('components.layouts.app')]
    #[Title('Jenis Asesmen - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = AssessmentLabel::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        $labels = $query->orderBy('name')->paginate(15);

        return view('livewire.assessment-label.index', compact('labels'));
    }
}
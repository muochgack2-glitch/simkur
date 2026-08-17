<?php

namespace App\Livewire\PklField;

use App\Models\PklCompany;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterDept = '';

    // Form
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $address = '';
    public $phone = '';
    public $contact_person = '';
    public $contact_phone = '';
    public $capacity = 5;
    public $business_field = '';
    public $suitable_departments = [];
    public $status = 'active';
    public $notes = '';

    public $confirmDelete = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'phone' => 'nullable|string|max:20',
        'contact_person' => 'nullable|string|max:255',
        'contact_phone' => 'nullable|string|max:20',
        'capacity' => 'required|integer|min:1',
        'business_field' => 'nullable|string|max:255',
        'status' => 'required|in:active,inactive,blacklisted',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function openForm($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $c = PklCompany::findOrFail($id);
            $this->editingId = $c->id;
            $this->name = $c->name;
            $this->address = $c->address;
            $this->phone = $c->phone;
            $this->contact_person = $c->contact_person;
            $this->contact_phone = $c->contact_phone;
            $this->capacity = $c->capacity;
            $this->business_field = $c->business_field;
            $this->suitable_departments = $c->suitable_departments ?? [];
            $this->status = $c->status;
            $this->notes = $c->notes;
        } else {
            $this->editingId = null;
            $this->reset(['name','address','phone','contact_person','contact_phone','capacity','business_field','suitable_departments','status','notes']);
            $this->capacity = 5;
            $this->status = 'active';
        }
        $this->showForm = true;
    }

    public function closeForm() { $this->showForm = false; }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'capacity' => $this->capacity,
            'business_field' => $this->business_field,
            'suitable_departments' => array_filter($this->suitable_departments),
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->editingId) {
            PklCompany::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'DU/DI berhasil diperbarui');
        } else {
            PklCompany::create($data);
            session()->flash('success', 'DU/DI berhasil ditambahkan');
        }

        $this->showForm = false;
    }

    public function delete($id)
    {
        $company = PklCompany::findOrFail($id);
        if ($company->activePlacements()->exists()) {
            session()->flash('error', 'Tidak bisa hapus, masih ada siswa aktif di DU/DI ini');
            return;
        }
        $company->delete();
        session()->flash('success', 'DU/DI berhasil dihapus');
        $this->confirmDelete = null;
    }

    public function getDepartmentsProperty()
    {
        return \App\Models\SchoolClass::select('major')->distinct()->whereNotNull('major')->orderBy('major')->pluck('major');
    }

    public function render()
    {
        $query = PklCompany::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('address', 'like', "%{$this->search}%")
                  ->orWhere('contact_person', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) $query->where('status', $this->filterStatus);
        if ($this->filterDept) $query->whereJsonContains('suitable_departments', $this->filterDept);

        $companies = $query->orderBy('name')->paginate(15);

        // Get current PKL activity for capacity count
        $currentActivity = \App\Models\AcademicYear::where('is_active', true)->first();

        return view('livewire.pkl-field.company-manager', [
            'companies' => $companies,
            'currentActivity' => $currentActivity,
        ]);
    }
}
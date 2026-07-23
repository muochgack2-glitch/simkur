<?php

namespace App\Livewire\Settings;

use App\Models\TimeSlot;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class TimeSlots extends Component
{
    public $timeSlots = [];
    public $showModal = false;
    public $editingId = null;
    
    // Form fields
    public $name = '';
    public $start_time = '';
    public $end_time = '';
    public $order = 0;
    public $day_of_week = 'all';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'order' => 'required|integer|min:0',
        'day_of_week' => 'required|string',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Nama jam mengajar wajib diisi',
        'start_time.required' => 'Jam mulai wajib diisi',
        'end_time.required' => 'Jam selesai wajib diisi',
        'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai',
        'order.required' => 'Urutan wajib diisi',
        'order.integer' => 'Urutan harus berupa angka',
        'day_of_week.required' => 'Hari wajib dipilih',
    ];

    public function mount()
    {
        $this->loadTimeSlots();
    }

    public function loadTimeSlots()
    {
        $this->timeSlots = TimeSlot::orderBy('order')->get();
    }

    public function openCreate()
    {
        $this->reset(['name', 'start_time', 'end_time', 'order', 'day_of_week', 'is_active', 'editingId']);
        $this->order = $this->timeSlots->max('order') + 1;
        $this->day_of_week = 'all';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $timeSlot = TimeSlot::findOrFail($id);
        
        $this->editingId = $id;
        $this->name = $timeSlot->name;
        $this->start_time = date('H:i', strtotime($timeSlot->start_time));
        $this->end_time = date('H:i', strtotime($timeSlot->end_time));
        $this->order = $timeSlot->order;
        $this->day_of_week = $timeSlot->day_of_week ?? 'all';
        $this->is_active = $timeSlot->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'order' => $this->order,
            'day_of_week' => $this->day_of_week === 'all' ? null : $this->day_of_week,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            // Update
            $timeSlot = TimeSlot::findOrFail($this->editingId);
            $timeSlot->update($data);

            session()->flash('success', 'Jam mengajar berhasil diperbarui!');
        } else {
            // Create
            TimeSlot::create($data);

            session()->flash('success', 'Jam mengajar berhasil ditambahkan!');
        }

        $this->closeModal();
        $this->loadTimeSlots();
    }

    public function delete($id)
    {
        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->delete();

        session()->flash('success', 'Jam mengajar berhasil dihapus!');
        $this->loadTimeSlots();
    }

    public function toggleActive($id)
    {
        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->is_active = !$timeSlot->is_active;
        $timeSlot->save();

        $this->loadTimeSlots();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'start_time', 'end_time', 'order', 'day_of_week', 'is_active', 'editingId']);
        $this->resetErrorBag();
    }

    #[Layout('components.layouts.app')]
    #[Title('Jam Mengajar - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        return view('livewire.settings.time-slots');
    }
}

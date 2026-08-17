<?php

namespace App\Livewire\PklField;

use App\Models\AcademicYear;
use App\Models\PklCompany;
use App\Models\PklCompanySupervisor;
use App\Models\PklVisit;
use Livewire\Component;
use Livewire\WithFileUploads;

class VisitMonitoring extends Component
{
    use WithFileUploads;

    public $academicYearId = '';
    public $filterStatus = '';

    // Form
    public $showForm = false;
    public $editingId = null;
    public $form_company_id = '';
    public $scheduled_date = '';
    public $actual_date = '';
    public $status = 'scheduled';
    public $notes = '';
    public $findings = '';
    public $recommendations = '';

    // Generate
    public $showGenerate = false;
    public $showComplete = false;
    public $completeVisitId = null;
    public $completeNotes = '';
    public $completeFindings = '';
    public $completeRecommendations = '';
    public $completeCompanyName = '';
    public $completePhoto = null;
    public $editPhoto = null;
    public $existingPhoto = null;
    public $generateDate = '';

    public function mount()
    {
        $ay = AcademicYear::where('is_active', true)->first();
        $this->academicYearId = $ay?->id ?? '';
        $this->generateDate = now()->format('Y-m-d');
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $v = PklVisit::findOrFail($id);
            $this->editingId = $v->id;
            $this->form_company_id = $v->pkl_company_id;
            $this->scheduled_date = $v->scheduled_date->format('Y-m-d');
            $this->actual_date = $v->actual_date?->format('Y-m-d') ?? '';
            $this->status = $v->status;
            $this->notes = $v->notes ?? '';
            $this->findings = $v->findings ?? '';
            $this->recommendations = $v->recommendations ?? '';
            $this->existingPhoto = $v->photo;
            $this->editPhoto = null;
        } else {
            $this->editingId = null;
            $this->reset(['form_company_id', 'scheduled_date', 'actual_date', 'notes', 'findings', 'recommendations']);
            $this->existingPhoto = null;
            $this->editPhoto = null;
            $this->status = 'scheduled';
        }
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'form_company_id' => 'required|exists:pkl_companies,id',
            'scheduled_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,missed',
        ]);

        $data = [
            'academic_year_id' => $this->academicYearId,
            'teacher_id' => auth()->id(),
            'pkl_company_id' => $this->form_company_id,
            'scheduled_date' => $this->scheduled_date,
            'actual_date' => $this->actual_date ?: null,
            'status' => $this->status,
            'notes' => $this->notes,
            'findings' => $this->findings,
            'recommendations' => $this->recommendations,
        ];

        if ($this->editPhoto) {
            $data['photo'] = $this->editPhoto->store('pkl-visits', 'public');
        }
        
        if ($this->editingId) {
            PklVisit::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Kunjungan diperbarui');
        } else {
            PklVisit::create($data);
            session()->flash('success', 'Kunjungan ditambahkan');
        }

        $this->showForm = false;
    }

    public function markCompleted($id)
    {
        $visit = PklVisit::with('company')->findOrFail($id);
        $this->completeVisitId = $id;
        $this->completeCompanyName = $visit->company->name ?? '-';
        $this->completeNotes = $visit->notes ?? '';
        $this->completeFindings = '';
        $this->completeRecommendations = '';
        $this->showComplete = true;
    }

    public function submitComplete()
    {
        $visit = PklVisit::findOrFail($this->completeVisitId);
        
        $photoPath = null;
        if ($this->completePhoto) {
            $photoPath = $this->completePhoto->store('pkl-visits', 'public');
        }
        
        $visit->update([
            'status' => 'completed',
            'actual_date' => now()->format('Y-m-d'),
            'notes' => $this->completeNotes,
            'findings' => $this->completeFindings,
            'recommendations' => $this->completeRecommendations,
            'photo' => $photoPath ?? $visit->photo,
        ]);
        session()->flash('success', 'Kunjungan berhasil diselesaikan ✅');
        $this->showComplete = false;
    }

    public function generateSchedule()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'waka_kurikulum', 'kepala_sekolah']);

        $supervisors = PklCompanySupervisor::where('academic_year_id', $this->academicYearId);
        if (!$isAdmin) $supervisors->where('teacher_id', $user->id);
        $supervisors = $supervisors->get();

        $date = $this->generateDate;
        $count = 0;

        foreach ($supervisors as $sup) {
            $exists = PklVisit::where('teacher_id', $sup->teacher_id)
                ->where('pkl_company_id', $sup->pkl_company_id)
                ->whereDate('scheduled_date', $date)
                
                ->exists();

            if (!$exists) {
                PklVisit::create([
                    'academic_year_id' => $this->academicYearId,
                    'teacher_id' => $sup->teacher_id,
                    'pkl_company_id' => $sup->pkl_company_id,
                    'scheduled_date' => ,
                    'status' => 'scheduled',
                ]);
                $count++;
            }
        }

        session()->flash('success', "$count jadwal kunjungan dibuat untuk tanggal $date");
        $this->showGenerate = false;
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'waka_kurikulum', 'kepala_sekolah']);

        $query = PklVisit::with(['teacher', 'company'])
            ->where('academic_year_id', $this->academicYearId);

        if (!$isAdmin) $query->where('teacher_id', $user->id);
        if ($this->filterStatus) $query->where('status', $this->filterStatus);

        $visits = $query->orderBy('scheduled_date')->get();

        $companies = PklCompany::active()->orderBy('name')->get();

        $stats = [
            'scheduled' => $visits->where('status', 'scheduled')->count(),
            'completed' => $visits->where('status', 'completed')->count(),
            'missed' => $visits->where('status', 'missed')->count(),
        ];

        // Info DU/DI & siswa untuk guru (non-admin)
        $myCompanies = collect();
        if (!$isAdmin) {
            $myCompanies = \App\Models\PklCompanySupervisor::with([
                'company.placements' => fn($q) => $q->where('academic_year_id', $this->academicYearId)->where('status', 'active')->with('student')
            ])->where('academic_year_id', $this->academicYearId)
              ->where('teacher_id', $user->id)
              ->get();
        }

        return view('livewire.pkl-field.visit-monitoring', [
            'visits' => $visits,
            'companies' => $companies,
            'stats' => $stats,
            'isAdmin' => $isAdmin,
            'myCompanies' => $myCompanies,
        ]);
    }
}
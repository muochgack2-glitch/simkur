<?php

namespace App\Livewire\PklField;

use App\Models\PklJournal;
use App\Models\PklPlacement;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentJournal extends Component
{
    use WithFileUploads;

    public $placementId = null;
    public $filterStatus = '';
    public $filterWeek = '';

    // Form
    public $showForm = false;
    public $editingId = null;
    public $journal_date = '';
    public $activities = '';
    public $learnings = '';
    public $challenges = '';
    public $attendanceStatus = 'hadir';
    public $photo = null;
    public $existingPhoto = null;

    // Review
    public $showConfirmSend = false;
    public $showReview = false;
    public $reviewJournalId = null;
    public $reviewAction = '';
    public $reviewNotes = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user->role === 'siswa') {
            $placement = PklPlacement::where('student_id', $user->id)->where('status', 'active')->first();
            $this->placementId = $placement?->id;
        }
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $j = PklJournal::findOrFail($id);
            $this->editingId = $j->id;
            $this->journal_date = $j->journal_date->format('Y-m-d');
            $this->activities = $j->activities;
            $this->learnings = $j->learnings;
            $this->challenges = $j->challenges;
            $this->attendanceStatus = $j->attendance_status ?? 'hadir';
            $this->existingPhoto = $j->photo;
            $this->photo = null;
        } else {
            $this->editingId = null;
            $this->journal_date = now()->format('Y-m-d');
            $this->reset(['activities', 'learnings', 'challenges']);
        }
        $this->showForm = true;
    }


    public function save($asDraft = false)
    {
        $this->validate([
            'journal_date'     => 'required|date',
            'activities'       => 'required|string|min:10',
            'attendanceStatus' => 'required|in:hadir,sakit,izin,alpha',
            'photo'            => 'nullable|image|max:2048',
        ]);

        // Cek duplikat tanggal (saat create baru)
        if (!$this->editingId) {
            $exists = PklJournal::where('pkl_placement_id', $this->placementId)
                ->where('journal_date', $this->journal_date)
                ->exists();
            if ($exists) {
                $this->addError('journal_date', 'Jurnal untuk tanggal ini sudah ada. Silakan edit jurnal yang sudah ada.');
                return;
            }
        }

        $data = [
            'pkl_placement_id'  => $this->placementId,
            'student_id'        => auth()->id(),
            'journal_date'      => $this->journal_date,
            'activities'        => $this->activities,
            'learnings'         => $this->learnings,
            'challenges'        => $this->challenges,
            'attendance_status' => $this->attendanceStatus,
            'status'            => $asDraft ? 'draft' : 'submitted',
        ];

        // Upload foto jika ada
        if ($this->photo) {
            $path = $this->photo->store('pkl/journals', 'public');
            $data['photo'] = $path;
        } elseif ($this->existingPhoto) {
            $data['photo'] = $this->existingPhoto;
        }

        if ($this->editingId) {
            PklJournal::findOrFail($this->editingId)->update($data);
        } else {
            PklJournal::create($data);
        }

        session()->flash('success', $asDraft ? 'Jurnal disimpan sebagai draft' : 'Jurnal berhasil dikirim');
        $this->showForm = false;
        $this->showConfirmSend = false;
    }

    public function openReview($id)
    {
        $this->reviewJournalId = $id;
        $this->reviewNotes = '';
        $this->showReview = true;
    }

    public function submitReview($action)
    {
        $journal = PklJournal::findOrFail($this->reviewJournalId);
        $journal->update([
            'status' => $action === 'approve' ? 'approved' : 'revision',
            'supervisor_notes' => $this->reviewNotes,
            'approved_by' => auth()->id(),
            'approved_at' => $action === 'approve' ? now() : null,
        ]);
        session()->flash('success', $action === 'approve' ? 'Jurnal disetujui' : 'Jurnal diminta revisi');
        $this->showReview = false;
    }

    public function unlockJournal($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'waka_kurikulum', 'kepsek'])) {
            return;
        }

        $journal = PklJournal::findOrFail($id);
        $journal->update([
            'status' => 'revision',
            'supervisor_notes' => ($journal->supervisor_notes ? $journal->supervisor_notes . ' | ' : '') . '[Dibuka kunci oleh ' . $user->name . ' pada ' . now()->format('d/m/Y H:i') . ']',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        session()->flash('success', 'Jurnal dibuka kunci - siswa bisa mengedit kembali');
    }

    public $confirmDeleteId = null;

    public function deleteJournal($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'waka_kurikulum', 'kepsek'])) return;
        $journal = PklJournal::findOrFail($id);
        if ($journal->photo) \Storage::disk('public')->delete($journal->photo);
        $journal->delete();
        $this->confirmDeleteId = null;
        session()->flash('success', 'Jurnal berhasil dihapus');
    }


    public function render()
    {
        $user = auth()->user();
        $isStudent = $user->role === 'siswa';

        $query = PklJournal::with(['student', 'placement.company']);

        if ($isStudent) {
            $query->where('student_id', $user->id);
        } elseif (in_array($user->role, ['admin', 'waka_kurikulum', 'kepsek'])) {
            // Admin/Waka/Kepsek: lihat SEMUA jurnal
            $ay = \App\Models\AcademicYear::where('is_active', true)->first();
            $placementIds = PklPlacement::where('academic_year_id', $ay?->id)->where('status', 'active')->pluck('id');
            $query->whereIn('pkl_placement_id', $placementIds);
        } else {
            // Guru: hanya jurnal siswa yang dibimbing
            $companyIds = \App\Models\PklCompanySupervisor::where('teacher_id', $user->id)->pluck('pkl_company_id');
            $placementIds = PklPlacement::whereIn('pkl_company_id', $companyIds)->where('status', 'active')->pluck('id');
            $query->whereIn('pkl_placement_id', $placementIds);
        }

        if ($this->filterStatus) $query->where('status', $this->filterStatus);

        $journals = $query->orderByDesc('journal_date')->get();

        // Group by week
        $weeklyGroups = $journals->groupBy(fn($j) => $j->journal_date->startOfWeek()->format('Y-m-d'));

        $placement = $this->placementId ? PklPlacement::with('company')->find($this->placementId) : null;

        // Get supervisor for this student's company
        $supervisor = null;
        if ($placement) {
            $ay = \App\Models\AcademicYear::where('is_active', true)->first();
            $supervisorRecord = \App\Models\PklCompanySupervisor::with('teacher')
                ->where('pkl_company_id', $placement->pkl_company_id)
                ->where('academic_year_id', $ay?->id)
                ->first();
            $supervisor = $supervisorRecord?->teacher;
        }

        // DU/DI & siswa untuk guru
        $myCompanies = collect();
        $isGuru = !$isStudent && !in_array($user->role, ['admin', 'waka_kurikulum', 'kepsek']);
        if ($isGuru) {
            $ay2 = \App\Models\AcademicYear::where('is_active', true)->first();
            $myCompanies = \App\Models\PklCompanySupervisor::with([
                'company.placements' => fn($q) => $q->where('academic_year_id', $ay2?->id)->where('status', 'active')->with('student')
            ])->where('academic_year_id', $ay2?->id)
              ->where('teacher_id', $user->id)
              ->get();
        }

        return view('livewire.pkl-field.student-journal', [
            'journals' => $journals,
            'weeklyGroups' => $weeklyGroups,
            'placement' => $placement,
            'supervisor' => $supervisor ?? null,
            'isStudent' => $isStudent,
            'isGuru' => $isGuru,
            'myCompanies' => $myCompanies,
            'reviewJournal' => $this->reviewJournalId ? PklJournal::with('student')->find($this->reviewJournalId) : null,
        ]);
    }
}
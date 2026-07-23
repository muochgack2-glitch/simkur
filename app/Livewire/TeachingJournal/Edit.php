<?php

namespace App\Livewire\TeachingJournal;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimeSlot;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public $journalId;
    public $journal;
    
    // Journal fields
    public $date;
    public $class_id;
    public $subject_id;
    public $time_slot;
    public $learning_objective;
    public $topic;
    public $teaching_method;
    public $notes;

    // Attendance data
    public $students = [];
    public $attendances = [];
    
    // Time slots for selected date
    public $timeSlots = [];

    public function mount($id)
    {
        $this->journalId = $id;
        $this->journal = TeachingJournal::with(['attendances.student', 'schoolClass.students'])->findOrFail($id);
        
        // Check authorization
        if (!auth()->user()->isAdmin() && $this->journal->teacher_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit jurnal ini.');
        }

        // Load journal data
        $this->date = $this->journal->date->format('Y-m-d');
        $this->class_id = $this->journal->class_id;
        $this->subject_id = $this->journal->subject_id;
        $this->time_slot = $this->journal->time_slot;
        $this->learning_objective = $this->journal->learning_objective;
        $this->topic = $this->journal->topic;
        $this->teaching_method = $this->journal->teaching_method;
        $this->notes = $this->journal->notes;

        // Load students and attendances
        $this->loadStudents();
        
        // Load time slots for this date
        $this->loadTimeSlotsForDate();
    }

    public function updatedDate($value)
    {
        $this->loadTimeSlotsForDate();
    }

    private function loadTimeSlotsForDate()
    {
        if ($this->date) {
            // Get day of week from date (e.g., 'monday', 'friday')
            $dayOfWeek = strtolower(date('l', strtotime($this->date)));
            
            // Load time slots for this specific day
            $this->timeSlots = TimeSlot::active()
                ->forDay($dayOfWeek)
                ->ordered()
                ->get();
        }
    }

    private function loadStudents()
    {
        $this->students = $this->journal->schoolClass->students()
            ->where('role', 'siswa')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load existing attendances
        foreach ($this->journal->attendances as $attendance) {
            $this->attendances[$attendance->student_id] = $attendance->status;
        }

        // Add new students if any (in case students were added to class after journal was created)
        foreach ($this->students as $student) {
            if (!isset($this->attendances[$student->id])) {
                $this->attendances[$student->id] = 'hadir';
            }
        }
    }

    public function update()
    {
        $this->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'time_slot' => 'required|string',
            'topic' => 'required|string|min:10',
        ], [
            'date.required' => 'Tanggal harus diisi',
            'class_id.required' => 'Kelas harus dipilih',
            'subject_id.required' => 'Mata pelajaran harus dipilih',
            'time_slot.required' => 'Jam mengajar harus dipilih',
            'topic.required' => 'Materi pokok harus diisi',
            'topic.min' => 'Materi pokok minimal 10 karakter',
        ]);

        // Update journal
        $this->journal->update([
            'date' => $this->date,
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'time_slot' => $this->time_slot,
            'learning_objective' => $this->learning_objective,
            'topic' => $this->topic,
            'teaching_method' => $this->teaching_method,
            'notes' => $this->notes,
        ]);

        // Update attendances
        foreach ($this->attendances as $student_id => $status) {
            StudentAttendance::updateOrCreate(
                [
                    'teaching_journal_id' => $this->journal->id,
                    'student_id' => $student_id,
                ],
                [
                    'status' => $status,
                ]
            );
        }

        // Update stats
        $this->journal->updateAttendanceStats();

        session()->flash('success', 'Jurnal mengajar berhasil diupdate!');
        return redirect()->route('teaching-journal.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Jurnal Mengajar - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $classes = SchoolClass::with('academicYear')->orderBy('name')->get();
        $subjects = auth()->user()->subjects()->orderBy('name')->get();

        return view('livewire.teaching-journal.edit', [
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}

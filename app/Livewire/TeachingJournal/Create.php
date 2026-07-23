<?php

namespace App\Livewire\TeachingJournal;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\TimeSlot;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    // Journal fields
    public $date;
    public $class_id;
    public $subject_id;
    public $selectedTimeSlots = []; // Array of selected time slot display names
    public $learning_objective;
    public $topic;
    public $teaching_method;
    public $notes;

    // Attendance data
    public $students = [];
    public $attendances = []; // Format: [student_id => status]
    
    // Time slots for selected date
    public $timeSlots = [];

    public function mount()
    {
        $this->date = date('Y-m-d');
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
            
            // Clear selected time slots when date changes
            $this->selectedTimeSlots = [];
        }
    }

    public function updatedClassId($value)
    {
        if ($value) {
            $this->loadStudents();
        }
    }

    private function loadStudents()
    {
        $class = SchoolClass::with(['students' => function($q) {
            $q->where('role', 'siswa')
              ->where('is_active', true)
              ->orderBy('name');
        }])->find($this->class_id);

        if ($class) {
            $this->students = $class->students;
            
            // Initialize all as 'hadir'
            foreach ($this->students as $student) {
                if (!isset($this->attendances[$student->id])) {
                    $this->attendances[$student->id] = 'hadir';
                }
            }
        }
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'selectedTimeSlots' => 'required|array|min:1',
            'topic' => 'required|string|min:10',
        ], [
            'date.required' => 'Tanggal harus diisi',
            'class_id.required' => 'Kelas harus dipilih',
            'subject_id.required' => 'Mata pelajaran harus dipilih',
            'selectedTimeSlots.required' => 'Jam mengajar harus dipilih minimal 1',
            'selectedTimeSlots.min' => 'Jam mengajar harus dipilih minimal 1',
            'topic.required' => 'Materi pokok harus diisi',
            'topic.min' => 'Materi pokok minimal 10 karakter',
        ]);

        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif. Hubungi admin.');
            return;
        }

        $journalsCreated = 0;

        // Create journal for each selected time slot
        foreach ($this->selectedTimeSlots as $timeSlot) {
            $journal = TeachingJournal::create([
                'teacher_id' => auth()->id(),
                'class_id' => $this->class_id,
                'subject_id' => $this->subject_id,
                'academic_year_id' => $academicYear->id,
                'date' => $this->date,
                'time_slot' => $timeSlot,
                'learning_objective' => $this->learning_objective,
                'topic' => $this->topic,
                'teaching_method' => $this->teaching_method,
                'notes' => $this->notes,
            ]);

            // Create attendances (same for all journals)
            foreach ($this->attendances as $student_id => $status) {
                StudentAttendance::create([
                    'teaching_journal_id' => $journal->id,
                    'student_id' => $student_id,
                    'status' => $status,
                ]);
            }

            // Update stats
            $journal->updateAttendanceStats();
            
            $journalsCreated++;
        }

        $message = $journalsCreated === 1 
            ? 'Jurnal mengajar berhasil disimpan!' 
            : $journalsCreated . ' jurnal mengajar berhasil disimpan!';
        
        session()->flash('success', $message);
        return redirect()->route('teaching-journal.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Buat Jurnal Mengajar - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        // Get classes and subjects for current teacher
        $classes = SchoolClass::with('academicYear')->orderBy('name')->get();
        $subjects = auth()->user()->subjects()->orderBy('name')->get();

        return view('livewire.teaching-journal.create', [
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}

<?php

namespace App\Livewire\TeachingJournal;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\TimeSlot;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Livewire\BaseComponent;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Edit extends BaseComponent
{
    use WithFileUploads;
    
    public $journalId;
    public $journal;
    
    // Journal fields
    public $date;
    public $class_id;
    public $subject_id;
    public $time_slot; // Original time slot (for the journal being edited)
    public $selectedTimeSlots = []; // Array of selected time slots (for multi-select)
    public $learning_objective;
    public $topic;
    public $teaching_method;
    public $notes;
    public $activity_photo; // New photo upload
    public $existing_photo; // Current photo path

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
        $this->time_slot = is_array($this->journal->time_slot) ? implode(', ', $this->journal->time_slot) : $this->journal->time_slot;
        $this->selectedTimeSlots = is_array($this->journal->time_slot) ? $this->journal->time_slot : [$this->journal->time_slot];
        $this->learning_objective = $this->journal->learning_objective;
        $this->topic = $this->journal->topic;
        $this->teaching_method = $this->journal->teaching_method;
        $this->notes = $this->journal->notes;
        $this->existing_photo = $this->journal->activity_photo;

        // Load students and attendances
        $this->loadStudents();
        
        // Load time slots for this date
        $this->loadTimeSlotsForDate();
    }

    public function updatedDate($value)
    {
        $this->loadTimeSlotsForDate();
        
        // If date changed to a different date, clear selected time slots
        // If date is same as original journal date, keep the original time slots
        if ($this->date === $this->journal->date->format('Y-m-d')) {
            // Same date as original, restore original time slots
            $this->selectedTimeSlots = is_array($this->journal->time_slot) ? $this->journal->time_slot : [$this->journal->time_slot];
        } else {
            // Different date, clear selections
            $this->selectedTimeSlots = [];
        }
    }

    public function updatedClassId($value)
    {
        if ($this->class_id) {
            $this->loadStudentsForClass($this->class_id);
        }
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
            
            // If no time slots found for specific day, try to get all active time slots
            if ($this->timeSlots->isEmpty()) {
                $this->timeSlots = TimeSlot::active()->ordered()->get();
            }
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

    private function loadStudentsForClass($classId)
    {
        $class = SchoolClass::with(['students' => function($q) {
            $q->where('role', 'siswa')
              ->where('is_active', true)
              ->orderBy('name');
        }])->find($classId);

        if ($class) {
            $this->students = $class->students;
            
            // Reset attendances for new class
            $this->attendances = [];
            
            // Initialize all as 'hadir'
            foreach ($this->students as $student) {
                $this->attendances[$student->id] = 'hadir';
            }
        } else {
            $this->students = [];
            $this->attendances = [];
        }
    }

    public function deletePhoto()
    {
        if ($this->existing_photo) {
            // Delete file from storage
            Storage::disk('public')->delete($this->existing_photo);
            
            // Update journal in database
            $this->journal->update(['activity_photo' => null]);
            
            // Clear from component state
            $this->existing_photo = null;
            
            $this->dispatch('photo-deleted');
            session()->flash('success', 'Foto berhasil dihapus!');
        }
    }

    public function update()
    {
        $this->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'selectedTimeSlots' => 'required|array|min:1',
            'topic' => 'required|string|min:10',
            'activity_photo' => 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp',
        ], [
            'date.required' => 'Tanggal harus diisi',
            'class_id.required' => 'Kelas harus dipilih',
            'subject_id.required' => 'Mata pelajaran harus dipilih',
            'selectedTimeSlots.required' => 'Jam mengajar harus dipilih minimal 1',
            'selectedTimeSlots.min' => 'Jam mengajar harus dipilih minimal 1',
            'topic.required' => 'Materi pokok harus diisi',
            'topic.min' => 'Materi pokok minimal 10 karakter',
            'activity_photo.image' => 'File harus berupa gambar',
            'activity_photo.max' => 'Ukuran foto maksimal 10MB',
            'activity_photo.mimes' => 'Format foto harus jpg, jpeg, png, atau webp',
        ]);

        // Handle photo upload
        $photoPath = $this->existing_photo; // Keep existing if no new upload
        if ($this->activity_photo) {
            // Delete old photo if exists
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
            // Upload new photo
            $photoPath = $this->processPhotoUpload($this->activity_photo);
        }

        // Update the journal with array of time slots
        $this->journal->update([
            'date' => $this->date,
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'time_slot' => $this->selectedTimeSlots, // Save as JSON array
            'learning_objective' => $this->learning_objective,
            'topic' => $this->topic,
            'teaching_method' => $this->teaching_method,
            'notes' => $this->notes,
            'activity_photo' => $photoPath,
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

    /**
     * Process photo upload with compression
     */
    private function processPhotoUpload($photo): string
    {
        // Create directory structure: journal-photos/YYYY/MM/
        $directory = 'journal-photos/' . date('Y') . '/' . date('m');
        
        // Generate unique filename
        $filename = 'user_' . auth()->id() . '_' . time() . '_' . uniqid() . '.jpg';
        $path = $directory . '/' . $filename;
        
        // Get uploaded file path
        $sourcePath = $photo->getRealPath();
        
        // Get image info
        $imageInfo = getimagesize($sourcePath);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];
        
        // Create image resource from uploaded file
        $sourceImage = match($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => imagecreatefromjpeg($sourcePath),
        };
        
        // Calculate new dimensions (max 1024x1024, maintain aspect ratio)
        $maxDimension = 1024;
        if ($width > $height) {
            $newWidth = min($width, $maxDimension);
            $newHeight = (int) ($height * ($newWidth / $width));
        } else {
            $newHeight = min($height, $maxDimension);
            $newWidth = (int) ($width * ($newHeight / $height));
        }
        
        // Create new image with resized dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save to temporary file
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        imagejpeg($resizedImage, $tempPath, 75); // 75% quality
        
        // Save to storage
        Storage::disk('public')->put($path, file_get_contents($tempPath));
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        unlink($tempPath);
        
        return $path;
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Jurnal Mengajar - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        // Get active academic year
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        
        // Get classes for active academic year only, with student count
        $classes = SchoolClass::with(['academicYear', 'students' => function($q) {
                $q->where('role', 'siswa')
                  ->where('is_active', true);
            }])
            ->when($activeAcademicYear, function($q) use ($activeAcademicYear) {
                $q->where('academic_year_id', $activeAcademicYear->id);
            })
            ->orderBy('name')
            ->get();
        
        // Get subjects - if admin, show all subjects. If teacher, show their subjects
        if (auth()->user()->isAdmin()) {
            $subjects = Subject::orderBy('name')->get();
        } else {
            $subjects = auth()->user()->subjects()->orderBy('name')->get();
            
            // Make sure the current journal's subject is included even if not in teacher's subjects
            $currentSubject = $this->journal->subject;
            if ($currentSubject && !$subjects->contains('id', $currentSubject->id)) {
                $subjects->push($currentSubject);
                $subjects = $subjects->sortBy('name')->values();
            }
        }

        return view('livewire.teaching-journal.edit', [
            'classes' => $classes,
            'subjects' => $subjects,
            'timeSlots' => $this->timeSlots, // Pass timeSlots to view
        ]);
    }
}


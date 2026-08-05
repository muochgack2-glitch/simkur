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
use Livewire\Attributes\On;
use App\Livewire\BaseComponent;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Create extends BaseComponent
{
    use WithFileUploads;
    
    // Journal fields
    public $date;
    public $class_id;
    public $subject_id;
    public $selectedTimeSlots = []; // Array of selected time slot display names
    public $learning_objective;
    public $topic;
    public $teaching_method;
    public $notes;
    public $activity_photo; // Photo upload

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

    public function updatedDate()
    {
        $this->loadTimeSlotsForDate();
    }

    private function loadTimeSlotsForDate()
    {
        if ($this->date) {
            // Get day of week from date in English first
            $dayOfWeekEnglish = date('l', strtotime($this->date)); // Monday, Tuesday, etc.
            
            // Convert to Indonesian
            $dayMapping = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu',
            ];
            
            $dayOfWeek = $dayMapping[$dayOfWeekEnglish] ?? $dayOfWeekEnglish;
            
            // Load time slots for this specific day
            $this->timeSlots = TimeSlot::active()
                ->forDay($dayOfWeek)
                ->ordered()
                ->get();
            
            // Clear selected time slots when date changes
            $this->selectedTimeSlots = [];
        }
    }

    public function updatedClassId()
    {
        if ($this->class_id) {
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

    public function deletePhoto()
    {
        $this->activity_photo = null;
        $this->dispatch('photo-deleted');
    }

    public function save()
    {
        // Validate main fields
        $rules = [
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'selectedTimeSlots' => 'required|array|min:1',
            'topic' => 'required|string|min:10',
        ];
        
        // Only validate photo if it's actually uploaded (not just temporary file issue)
        if ($this->activity_photo && is_object($this->activity_photo)) {
            $rules['activity_photo'] = 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp';
        }
        
        $this->validate($rules, [
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

        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif. Hubungi admin.');
            return;
        }

        // Handle photo upload
        $photoPath = null;
        if ($this->activity_photo) {
            $photoPath = $this->processPhotoUpload($this->activity_photo);
        }

        // Create single journal with multiple time slots
        $journal = TeachingJournal::create([
            'teacher_id' => auth()->id(),
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'academic_year_id' => $academicYear->id,
            'date' => $this->date,
            'time_slot' => $this->selectedTimeSlots, // Save as JSON array
            'learning_objective' => $this->learning_objective,
            'topic' => $this->topic,
            'teaching_method' => $this->teaching_method,
            'notes' => $this->notes,
            'activity_photo' => $photoPath,
        ]);

        // Create attendances (one set for all time slots)
        foreach ($this->attendances as $student_id => $status) {
            StudentAttendance::create([
                'teaching_journal_id' => $journal->id,
                'student_id' => $student_id,
                'status' => $status,
            ]);
        }

        // Update stats
        $journal->updateAttendanceStats();

        $timeSlotCount = count($this->selectedTimeSlots);
        $message = 'Jurnal mengajar berhasil disimpan untuk ' . $timeSlotCount . ' jam mengajar!';
        
        session()->flash('success', $message);
        return redirect()->route('teaching-journal.index');
    }

    /**
     * Process photo upload with compression
     */
    private function processPhotoUpload($photo): string
    {
        try {
            // Create directory structure: journal-photos/YYYY/MM/
            $directory = 'journal-photos/' . date('Y') . '/' . date('m');
            
            // Generate unique filename
            $filename = 'user_' . auth()->id() . '_' . time() . '_' . uniqid() . '.jpg';
            $path = $directory . '/' . $filename;
            
            // Get uploaded file path
            $sourcePath = $photo->getRealPath();
            
            // Check if source file exists
            if (!$sourcePath || !file_exists($sourcePath)) {
                \Log::warning('Photo source file not found, using alternative upload method');
                // Fallback: save directly using file contents
                $content = file_get_contents($photo->path());
                Storage::disk('public')->put($path, $content);
                return $path;
            }
            
            // Get image info
            $imageInfo = @getimagesize($sourcePath);
            if (!$imageInfo) {
                \Log::warning('Cannot get image info, using alternative upload method');
                // Fallback: save directly using file contents
                $content = file_get_contents($sourcePath);
                Storage::disk('public')->put($path, $content);
                return $path;
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mime = $imageInfo['mime'];
            
            // Create image resource from uploaded file
            $sourceImage = match($mime) {
                'image/jpeg' => @imagecreatefromjpeg($sourcePath),
                'image/png' => @imagecreatefrompng($sourcePath),
                'image/webp' => @imagecreatefromwebp($sourcePath),
                default => @imagecreatefromjpeg($sourcePath),
            };
            
            if (!$sourceImage) {
                \Log::warning('Cannot create image resource, using alternative upload method');
                // Fallback: save directly using file contents
                $content = file_get_contents($sourcePath);
                Storage::disk('public')->put($path, $content);
                return $path;
            }
            
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
            @unlink($tempPath);
            
            return $path;
        } catch (\Exception $e) {
            \Log::error('Photo processing error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Final fallback: save directly without any processing
            try {
                $directory = 'journal-photos/' . date('Y') . '/' . date('m');
                $filename = 'user_' . auth()->id() . '_' . time() . '_' . uniqid() . '.jpg';
                $path = $directory . '/' . $filename;
                
                // Try multiple methods to get file content
                $content = null;
                if (method_exists($photo, 'get')) {
                    $content = $photo->get();
                } elseif (method_exists($photo, 'path')) {
                    $content = file_get_contents($photo->path());
                } else {
                    $content = file_get_contents($photo->getRealPath());
                }
                
                Storage::disk('public')->put($path, $content);
                return $path;
            } catch (\Exception $fallbackError) {
                \Log::error('Photo fallback upload also failed: ' . $fallbackError->getMessage());
                \Log::error('Fallback stack trace: ' . $fallbackError->getTraceAsString());
                throw new \Exception('Gagal mengupload foto. Silakan coba lagi.');
            }
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Buat Jurnal Mengajar - SIM Kurikulum SMK PGRI Blora')]
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
        
        // Get subjects for current teacher
        $subjects = auth()->user()->subjects()->orderBy('name')->get();

        return view('livewire.teaching-journal.create', [
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}

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
    public $activity_photo; // Photo upload (Livewire - for fallback)
    public $photo_base64; // Base64 photo data from JavaScript

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
        // Handle photo upload - prioritize Base64, fallback to Livewire upload
        $photoPath = null;
        if ($this->photo_base64) {
            $photoPath = $this->processPhotoBase64($this->photo_base64);
        } elseif ($this->activity_photo) {
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
     * Process photo upload from Base64 data
     */
    private function processPhotoBase64(string $base64Data): string
    {
        try {
            // Extract base64 content (remove data:image/...;base64, prefix)
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $imageType = strtolower($type[1]);
            } else {
                throw new \Exception('Invalid base64 image format');
            }
            
            // Decode base64
            $imageData = base64_decode($base64Data);
            if ($imageData === false) {
                throw new \Exception('Base64 decode failed');
            }
            
            // Create directory structure
            $directory = 'journal-photos/' . date('Y') . '/' . date('m');
            $filename = 'user_' . auth()->id() . '_' . time() . '_' . uniqid() . '.jpg';
            $path = $directory . '/' . $filename;
            
            // Create image resource from decoded data
            $sourceImage = imagecreatefromstring($imageData);
            if (!$sourceImage) {
                throw new \Exception('Failed to create image from base64');
            }
            
            // Get image dimensions
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            
            // Calculate new dimensions (max 1024x1024, maintain aspect ratio)
            $maxDimension = 1024;
            if ($width > $height) {
                $newWidth = min($width, $maxDimension);
                $newHeight = (int) ($height * ($newWidth / $width));
            } else {
                $newHeight = min($height, $maxDimension);
                $newWidth = (int) ($width * ($newHeight / $height));
            }
            
            // Create resized image
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
            
            \Log::info('Photo uploaded successfully from Base64: ' . $path);
            return $path;
        } catch (\Exception $e) {
            \Log::error('Base64 photo processing failed: ' . $e->getMessage());
            throw new \Exception('Tidak dapat mengupload foto: ' . $e->getMessage());
        }
    }

    /**
     * Process photo upload with compression
     */
    private function processPhotoUpload($photo): string
    {
        $directory = 'journal-photos/' . date('Y') . '/' . date('m');
        
        // Generate unique filename
        $filename = 'user_' . auth()->id() . '_' . time() . '_' . uniqid() . '.jpg';
        $path = $directory . '/' . $filename;
        
        try {
            // Get uploaded file path
            $sourcePath = $photo->getRealPath();
            
            // Check if source file exists and is readable
            if (!$sourcePath || !file_exists($sourcePath) || !is_readable($sourcePath)) {
                \Log::warning('Photo source file not accessible');
                throw new \Exception('Source file not accessible');
            }
            
            // Get image info
            $imageInfo = @getimagesize($sourcePath);
            if (!$imageInfo) {
                \Log::warning('Cannot get image info');
                throw new \Exception('Cannot get image info');
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
                \Log::warning('Cannot create image resource');
                throw new \Exception('Cannot create image resource');
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
            \Log::error('Photo processing failed: ' . $e->getMessage());
            
            // Try one last time with Livewire store if photo object is still valid
            try {
                if (method_exists($photo, 'store') && method_exists($photo, 'getRealPath')) {
                    $testPath = $photo->getRealPath();
                    if ($testPath && file_exists($testPath) && is_readable($testPath)) {
                        \Log::info('Attempting Livewire store as fallback');
                        $storedPath = $photo->store($directory, 'public');
                        
                        if ($storedPath) {
                            \Log::info('Fallback upload successful: ' . $storedPath);
                            return $storedPath;
                        }
                    }
                }
            } catch (\Exception $storeError) {
                \Log::error('Fallback store also failed: ' . $storeError->getMessage());
            }
            
            // If everything fails, throw exception to be caught by save method
            throw new \Exception('Tidak dapat mengupload foto karena masalah temporary file');
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

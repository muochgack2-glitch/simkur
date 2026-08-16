<?php

namespace App\Livewire\TeachingJournal;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\TeachingSchedule;
use App\Models\TimeSlot;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Livewire\BaseComponent;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\AbsensiApiService;

class Create extends BaseComponent
{
    use WithFileUploads;
    
    // Journal fields
    public $date;
    public $class_id;
    public $subject_id;
    public $start_time_slot_id = ''; // Changed from selectedTimeSlots
    public $end_time_slot_id = '';   // New field for range selection
    public $learning_objective;
    public $topic;
    public $teaching_method;
    public $notes;
    public $activity_photo; // Photo upload (Livewire - for fallback)
    public $photo_base64; // Base64 photo data from JavaScript

    // Attendance data
    public $students = [];
    public $attendances = []; // Format: [student_id => status]
    public $scanStatuses = []; // Format: [student_id => ['status' => ..., 'check_in_time' => ...]]
    
    // Time slots for selected date
    public $timeSlots = [];
    
    // Computed
    public $totalJP = 0;

    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->loadTimeSlotsForDate();
    }

    public function updatedDate()
    {
        $this->loadTimeSlotsForDate();
        // Reset time slot selection when date changes
        $this->start_time_slot_id = '';
        $this->end_time_slot_id = '';
        $this->totalJP = 0;
    }
    
    public function updatedStartTimeSlotId()
    {
        // Reset end time slot when start changes
        $this->end_time_slot_id = '';
        $this->calculateTotalJP();
    }
    
    public function updatedEndTimeSlotId()
    {
        $this->calculateTotalJP();
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
        }
    }
    
    public function getEndTimeSlots()
    {
        if (!$this->start_time_slot_id || !$this->date) {
            return collect();
        }
        
        $startSlot = TimeSlot::find($this->start_time_slot_id);
        
        if (!$startSlot) {
            return collect();
        }
        
        // Get day of week from date
        $dayOfWeekEnglish = date('l', strtotime($this->date));
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
        
        return TimeSlot::active()
            ->where('day_of_week', $dayOfWeek)
            ->where('order', '>=', $startSlot->order)
            ->ordered()
            ->get();
    }
    
    public function calculateTotalJP()
    {
        if ($this->start_time_slot_id && $this->end_time_slot_id && $this->date) {
            $startSlot = TimeSlot::find($this->start_time_slot_id);
            $endSlot = TimeSlot::find($this->end_time_slot_id);
            
            if ($startSlot && $endSlot) {
                // Get day of week from date
                $dayOfWeekEnglish = date('l', strtotime($this->date));
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
                
                // Get all slots between start and end (inclusive)
                $slots = TimeSlot::active()
                    ->where('day_of_week', $dayOfWeek)
                    ->where('order', '>=', $startSlot->order)
                    ->where('order', '<=', $endSlot->order)
                    ->get();
                
                // Count only teaching slots (skip order 1, 5, 10)
                $this->totalJP = $slots->filter(function($slot) {
                    return $slot->order > 1 && $slot->order != 5 && $slot->order != 10;
                })->count();
            } else {
                $this->totalJP = 0;
            }
        } else {
            $this->totalJP = 0;
        }
    }

    public function updatedClassId()
    {
        if ($this->class_id) {
            $this->loadStudents();
            $this->autoDetectFromSchedule();
        }
    }

    public function updatedSubjectId()
    {
        $this->autoDetectFromSchedule();
    }



    /**
     * Auto-detect mapel + jam mengajar dari jadwal berdasarkan tanggal + kelas
     */
    private function autoDetectFromSchedule()
    {
        \Log::info('autoDetectFromSchedule START', ['date' => $this->date, 'class_id' => $this->class_id]);
        if (!$this->date || !$this->class_id) {
            return;
        }

        $dayOfWeekEnglish = date('l', strtotime($this->date));
        $dayMapping = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $dayOfWeek = $dayMapping[$dayOfWeekEnglish] ?? $dayOfWeekEnglish;

        // Find matching schedule for this teacher + class + day (check both ID/EN day names)
        $schedules = TeachingSchedule::where('teacher_id', auth()->id())
            ->where('class_id', $this->class_id)
            ->whereIn('day_of_week', [$dayOfWeek, $dayOfWeekEnglish])
            ->where('is_active', true)
            ->get();

        \Log::info('Auto-detect schedule', ['teacher' => auth()->id(), 'class' => $this->class_id, 'day_id' => $dayOfWeek, 'day_en' => $dayOfWeekEnglish, 'found' => $schedules->count()]);
        
        if ($schedules->count() === 1) {
            // Exact match - auto-fill subject + time slots
            $schedule = $schedules->first();
            $this->subject_id = (string) $schedule->subject_id;
            
            if (is_array($schedule->time_slot_id) && count($schedule->time_slot_id) > 0) {
                $slotIds = $schedule->time_slot_id;
                sort($slotIds);
                $this->start_time_slot_id = (string) $slotIds[0];
                $this->end_time_slot_id = (string) end($slotIds);
                $this->calculateTotalJP();
            \Log::info("Auto-detect slots", ["slotIds" => $slotIds, "start" => $this->start_time_slot_id, "end" => $this->end_time_slot_id, "endSlots" => $this->getEndTimeSlots()->pluck("id")->toArray()]);
            }
            
            $subjectName = $schedule->subject->name ?? '';
            $this->dispatch('notify', type: 'info', message: "⏰ Jadwal terdeteksi: {$subjectName} ({$dayOfWeek})");
        } elseif ($schedules->count() > 1 && $this->subject_id) {
            // Multiple schedules, but subject already selected - fill time only
            $schedule = $schedules->where('subject_id', $this->subject_id)->first();
            if ($schedule && is_array($schedule->time_slot_id) && count($schedule->time_slot_id) > 0) {
                $slotIds = $schedule->time_slot_id;
                sort($slotIds);
                $this->start_time_slot_id = (string) $slotIds[0];
                $this->end_time_slot_id = (string) end($slotIds);
                $this->calculateTotalJP();
                $this->dispatch('notify', type: 'info', message: '⏰ Jam mengajar terdeteksi dari jadwal');
            }
        } elseif ($schedules->count() > 1) {
            // Multiple schedules - notify user to pick subject
            $this->dispatch('notify', type: 'info', message: "📋 Ada {$schedules->count()} jadwal hari {$dayOfWeek} - pilih mata pelajaran");
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
            
            // Ambil data scan QR dari sistem Absensi via API
            $nisArray = $this->students->pluck('nis')->filter()->toArray();
            $absensiData = collect();
            $this->scanStatuses = [];

            if (!empty($nisArray) && $this->date) {
                try {
                    $absensiService = app(AbsensiApiService::class);
                    $absensiData = $absensiService->getAttendanceByNis($nisArray, $this->date);
                } catch (\Exception $e) {
                    \Log::warning('Gagal ambil data absensi: ' . $e->getMessage());
                }
            }

            // Auto-fill attendance berdasarkan data scan QR
            foreach ($this->students as $student) {
                if (!isset($this->attendances[$student->id])) {
                    $scanResult = $absensiData->get($student->nis);
                    
                    if ($scanResult) {
                        // Siswa sudah scan QR - map status
                        $this->attendances[$student->id] = AbsensiApiService::mapStatus($scanResult['status'] ?? null);
                        $this->scanStatuses[$student->id] = [
                            'status' => $scanResult['status'] ?? 'unknown',
                            'check_in_time' => $scanResult['check_in_time'] ?? null,
                            'source' => 'scan',
                        ];
                    } else {
                        // Belum scan = alpha (guru bisa ubah)
                        $this->attendances[$student->id] = 'alpha';
                        $this->scanStatuses[$student->id] = [
                            'status' => null,
                            'check_in_time' => null,
                            'source' => 'no_data',
                        ];
                    }
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
        // Validate main fields (NO photo validation - will handle separately)
        $this->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'start_time_slot_id' => 'required|exists:time_slots,id',
            'end_time_slot_id' => 'required|exists:time_slots,id',
            'topic' => 'required|string|min:10',
        ], [
            'date.required' => 'Tanggal harus diisi',
            'class_id.required' => 'Kelas harus dipilih',
            'subject_id.required' => 'Mata pelajaran harus dipilih',
            'start_time_slot_id.required' => 'Jam mulai harus dipilih',
            'end_time_slot_id.required' => 'Jam selesai harus dipilih',
            'topic.required' => 'Materi pokok harus diisi',
            'topic.min' => 'Materi pokok minimal 10 karakter',
        ]);

        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif. Hubungi admin.');
            return;
        }

        // Validate start <= end
        $startSlot = TimeSlot::find($this->start_time_slot_id);
        $endSlot = TimeSlot::find($this->end_time_slot_id);
        
        if ($startSlot->order > $endSlot->order) {
            session()->flash('error', 'Jam selesai harus >= jam mulai!');
            return;
        }
        
        // Get day of week from date
        $dayOfWeekEnglish = date('l', strtotime($this->date));
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
        
        // Get all slot display names between start and end (excluding breaks)
        $slots = TimeSlot::active()
            ->where('day_of_week', $dayOfWeek)
            ->where('order', '>=', $startSlot->order)
            ->where('order', '<=', $endSlot->order)
            ->ordered()
            ->get();
        
        $selectedTimeSlots = $slots->filter(function($slot) {
            // Exclude pre-class (order <= 1) and break times (order 5, 10)
            return $slot->order > 1 && $slot->order != 5 && $slot->order != 10;
        })->pluck('display_name')->toArray();
        
        if (empty($selectedTimeSlots)) {
            session()->flash('error', 'Tidak ada jam mengajar valid di rentang waktu yang dipilih!');
            return;
        }

        // Handle photo upload - prioritize Base64, fallback to Livewire upload
        $photoPath = null;
        
        // Try Base64 first (client-side compressed)
        if ($this->photo_base64) {
            try {
                $photoPath = $this->processPhotoBase64($this->photo_base64);
                \Log::info('Photo uploaded via Base64 successfully');
            } catch (\Exception $e) {
                \Log::error('Base64 photo upload failed: ' . $e->getMessage());
                session()->flash('error', 'Gagal mengupload foto: ' . $e->getMessage());
                return;
            }
        }
        // Fallback to Livewire upload (only if Base64 not provided)
        elseif ($this->activity_photo && is_object($this->activity_photo)) {
            try {
                // Check if file is actually accessible
                $realPath = $this->activity_photo->getRealPath();
                if ($realPath && file_exists($realPath) && is_readable($realPath)) {
                    $photoPath = $this->processPhotoUpload($this->activity_photo);
                    \Log::info('Photo uploaded via Livewire successfully');
                } else {
                    \Log::warning('Photo file not accessible, skipping upload');
                    // Continue without photo - it's optional
                }
            } catch (\Exception $e) {
                \Log::error('Livewire photo upload failed: ' . $e->getMessage());
                // Continue without photo - it's optional, don't block journal creation
                \Log::info('Continuing journal creation without photo');
            }
        }

        // Create single journal with multiple time slots
        $journal = TeachingJournal::create([
            'teacher_id' => auth()->id(),
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'academic_year_id' => $academicYear->id,
            'date' => $this->date,
            'time_slot' => $selectedTimeSlots, // Save as JSON array of display names
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

        $timeSlotCount = count($selectedTimeSlots);
        $message = 'Jurnal mengajar berhasil disimpan untuk ' . $timeSlotCount . ' JP!';
        
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
        
        // Get subjects from teacher's active schedules (not pivot table)
        // This ensures subject_id consistency between journal and schedule,
        // preventing monitoring mismatch bug.
        $scheduleSubjectIds = TeachingSchedule::where('teacher_id', auth()->id())
            ->where('is_active', true)
            ->when($activeAcademicYear, function($q) use ($activeAcademicYear) {
                $q->where('academic_year_id', $activeAcademicYear->id);
            })
            ->pluck('subject_id')
            ->unique();
        
        $subjects = Subject::whereIn('id', $scheduleSubjectIds)
            ->orderBy('name')
            ->get();
        
        // Fallback: if no schedules found, use pivot table
        if ($subjects->isEmpty()) {
            $subjects = auth()->user()->subjects()->orderBy('name')->get();
        }
        
        // Get end time slots for range selection
        $endTimeSlots = $this->getEndTimeSlots();

        return view('livewire.teaching-journal.create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'endTimeSlots' => $endTimeSlots,
        ]);
    }
}
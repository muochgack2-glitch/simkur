<?php

namespace App\Livewire\TeachingJournal;

use App\Models\TeachingJournal;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Livewire\BaseComponent;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class Index extends BaseComponent
{
    use WithPagination;

    public $search = '';
    public $filterClass = 'all';
    public $filterSubject = 'all';
    public $filterDate = '';
    
    // For report modal
    public $showReportModal = false;
    public $reportType = '';
    public $reportStartDate = '';
    public $reportEndDate = '';
    public $reportTeacher = 'all';
    public $reportClass = 'all';

    // For photo modal
    public $showPhotoModal = false;
    public $currentPhotoUrl = '';
    public $currentPhotoJournal = null;

    // For copy modal
    public $showCopyModal = false;
    public $copySourceJournal = null;
    public $copyTargetClasses = [];
    public $copyDate = '';
    public $copyTimeSlot = '';
    public $availableTimeSlots = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openReportModal($type)
    {
        $this->reportType = $type;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->endOfMonth()->format('Y-m-d');
        $this->showReportModal = true;
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->reset(['reportType', 'reportStartDate', 'reportEndDate', 'reportTeacher', 'reportClass']);
    }

    public function viewPhoto($journalId)
    {
        $journal = TeachingJournal::findOrFail($journalId);
        
        // Check access: Waka Kurikulum, Admin, Kepala Sekolah, or owner
        $canView = auth()->user()->isWakaKurikulum() 
                    || auth()->user()->isAdmin() 
                    || auth()->user()->isKepalaSekolah()
                    || $journal->teacher_id === auth()->id();
        
        if (!$canView) {
            session()->flash('error', 'Anda tidak memiliki akses untuk melihat foto ini.');
            return;
        }
        
        if ($journal->hasPhoto()) {
            $this->currentPhotoUrl = $journal->activity_photo_url;
            $this->currentPhotoJournal = $journal;
            $this->showPhotoModal = true;
        }
    }

    public function closePhotoModal()
    {
        $this->showPhotoModal = false;
        $this->currentPhotoUrl = '';
        $this->currentPhotoJournal = null;
    }

    public function deletePhotoFromModal()
    {
        if ($this->currentPhotoJournal) {
            // Check authorization
            $canDelete = auth()->user()->isWakaKurikulum() 
                        || auth()->user()->isAdmin() 
                        || auth()->user()->isKepalaSekolah()
                        || $this->currentPhotoJournal->teacher_id === auth()->id();
            
            if (!$canDelete) {
                session()->flash('error', 'Anda tidak memiliki akses untuk menghapus foto ini.');
                return;
            }
            
            // Delete file from storage
            if ($this->currentPhotoJournal->activity_photo) {
                \Storage::disk('public')->delete($this->currentPhotoJournal->activity_photo);
            }
            
            // Update journal
            $this->currentPhotoJournal->update(['activity_photo' => null]);
            
            session()->flash('success', 'Foto berhasil dihapus!');
            $this->closePhotoModal();
        }
    }

    public function generateReport()
    {
        $this->validate([
            'reportStartDate' => 'required|date',
            'reportEndDate' => 'required|date|after_or_equal:reportStartDate',
        ]);

        return match($this->reportType) {
            'teacher-summary' => $this->generateTeacherSummaryReport(),
            'attendance-recap' => $this->generateAttendanceRecapReport(),
            'material-recap' => $this->generateMaterialRecapReport(),
            'missing-journals' => $this->generateMissingJournalsReport(),
            'my-journals' => $this->generateMyJournalsReport(),
            default => redirect()->back(),
        };
    }

    private function generateTeacherSummaryReport()
    {
        $query = TeachingJournal::with(['teacher', 'schoolClass', 'subject'])
            ->whereBetween('date', [$this->reportStartDate, $this->reportEndDate]);

        if ($this->reportTeacher !== 'all') {
            $query->where('teacher_id', $this->reportTeacher);
        }

        $journals = $query->get();
        
        // Group by teacher
        $summary = $journals->groupBy('teacher_id')->map(function($teacherJournals) {
            $teacher = $teacherJournals->first()->teacher;
            return [
                'teacher' => $teacher,
                'total_journals' => $teacherJournals->count(),
                'total_hours' => $teacherJournals->count() * 2, // Asumsi 2 JP per jurnal
                'classes' => $teacherJournals->pluck('schoolClass.name')->unique()->sort()->values(),
                'subjects' => $teacherJournals->pluck('subject.name')->unique()->sort()->values(),
            ];
        });

        $pdf = Pdf::loadView('reports.teaching-journal.teacher-summary', [
            'summary' => $summary,
            'startDate' => Carbon::parse($this->reportStartDate),
            'endDate' => Carbon::parse($this->reportEndDate),
            'generatedAt' => now(),
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_Jurnal_Per_Guru_' . now()->format('Ymd_His') . '.pdf');
    }

    private function generateAttendanceRecapReport()
    {
        $query = TeachingJournal::with(['attendances.student', 'schoolClass', 'subject'])
            ->whereBetween('date', [$this->reportStartDate, $this->reportEndDate]);

        if ($this->reportClass !== 'all') {
            $query->where('class_id', $this->reportClass);
        }

        $journals = $query->get();
        
        // Collect all attendance records
        $attendanceData = [];
        foreach ($journals as $journal) {
            foreach ($journal->attendances as $attendance) {
                $studentId = $attendance->student_id;
                if (!isset($attendanceData[$studentId])) {
                    $attendanceData[$studentId] = [
                        'student' => $attendance->student,
                        'hadir' => 0,
                        'sakit' => 0,
                        'izin' => 0,
                        'alpha' => 0,
                        'total' => 0,
                    ];
                }
                
                $attendanceData[$studentId][$attendance->status]++;
                $attendanceData[$studentId]['total']++;
            }
        }

        $pdf = Pdf::loadView('reports.teaching-journal.attendance-recap', [
            'attendanceData' => collect($attendanceData)->sortBy('student.name'),
            'startDate' => Carbon::parse($this->reportStartDate),
            'endDate' => Carbon::parse($this->reportEndDate),
            'className' => $this->reportClass !== 'all' ? SchoolClass::find($this->reportClass)->name : 'Semua Kelas',
            'generatedAt' => now(),
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_Kehadiran_Siswa_' . now()->format('Ymd_His') . '.pdf');
    }

    private function generateMaterialRecapReport()
    {
        $query = TeachingJournal::with(['teacher', 'schoolClass', 'subject'])
            ->whereBetween('date', [$this->reportStartDate, $this->reportEndDate]);

        if ($this->reportClass !== 'all') {
            $query->where('class_id', $this->reportClass);
        }

        $journals = $query->orderBy('date')->get();
        
        // Group by class and subject
        $materialsGrouped = $journals->groupBy(function($journal) {
            return $journal->schoolClass->name . ' - ' . $journal->subject->name;
        });

        $pdf = Pdf::loadView('reports.teaching-journal.material-recap', [
            'materialsGrouped' => $materialsGrouped,
            'startDate' => Carbon::parse($this->reportStartDate),
            'endDate' => Carbon::parse($this->reportEndDate),
            'generatedAt' => now(),
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_Materi_Ajar_' . now()->format('Ymd_His') . '.pdf');
    }

    private function generateMissingJournalsReport()
    {
        // Get all teachers
        $teachers = User::where('role', 'guru')->with('subjects')->get();
        
        $missingData = [];
        foreach ($teachers as $teacher) {
            $journalCount = TeachingJournal::where('teacher_id', $teacher->id)
                ->whereBetween('date', [$this->reportStartDate, $this->reportEndDate])
                ->count();
            
            $missingData[] = [
                'teacher' => $teacher,
                'journal_count' => $journalCount,
                'subjects' => $teacher->subjects->pluck('name')->join(', '),
            ];
        }

        // Sort by journal count (ascending - yang paling sedikit di atas)
        $missingData = collect($missingData)->sortBy('journal_count')->values();

        $pdf = Pdf::loadView('reports.teaching-journal.missing-journals', [
            'missingData' => $missingData,
            'startDate' => Carbon::parse($this->reportStartDate),
            'endDate' => Carbon::parse($this->reportEndDate),
            'generatedAt' => now(),
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Monitoring_Jurnal_' . now()->format('Ymd_His') . '.pdf');
    }

    private function generateMyJournalsReport()
    {
        $journals = TeachingJournal::with(['schoolClass', 'subject', 'attendances.student'])
            ->where('teacher_id', auth()->id())
            ->whereBetween('date', [$this->reportStartDate, $this->reportEndDate])
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('reports.teaching-journal.my-journals', [
            'journals' => $journals,
            'teacher' => auth()->user(),
            'startDate' => Carbon::parse($this->reportStartDate),
            'endDate' => Carbon::parse($this->reportEndDate),
            'generatedAt' => now(),
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Jurnal_Mengajar_Saya_' . now()->format('Ymd_His') . '.pdf');
    }

    public function delete($id)
    {
        $journal = TeachingJournal::findOrFail($id);
        
        // Check authorization
        if (!auth()->user()->isAdmin() && $journal->teacher_id !== auth()->id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus jurnal ini.');
            return;
        }

        $journal->delete();
        session()->flash('success', 'Jurnal mengajar berhasil dihapus!');
    }

    public function openCopyModal($journalId)
    {
        $this->copySourceJournal = TeachingJournal::with(['schoolClass', 'subject'])->findOrFail($journalId);
        
        // Set default date (same as source)
        $this->copyDate = $this->copySourceJournal->date->format('Y-m-d');
        
        // Load available time slots for the date
        $this->loadTimeSlots();
        
        // Reset selections
        $this->copyTargetClasses = [];
        $this->copyTimeSlot = '';
        
        $this->showCopyModal = true;
    }

    public function updatedCopyDate()
    {
        $this->loadTimeSlots();
        $this->copyTimeSlot = ''; // Reset time slot when date changes
    }

    private function loadTimeSlots()
    {
        if ($this->copyDate) {
            $dayOfWeekEnglish = date('l', strtotime($this->copyDate));
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
            
            $this->availableTimeSlots = \App\Models\TimeSlot::active()
                ->forDay($dayOfWeek)
                ->ordered()
                ->get();
        }
    }

    public function closeCopyModal()
    {
        $this->showCopyModal = false;
        $this->reset(['copySourceJournal', 'copyTargetClasses', 'copyDate', 'copyTimeSlot', 'availableTimeSlots']);
    }

    public function executeCopy()
    {
        // Validate
        $this->validate([
            'copyTargetClasses' => 'required|array|min:1',
            'copyDate' => 'required|date',
            'copyTimeSlot' => 'required|string',
        ], [
            'copyTargetClasses.required' => 'Pilih minimal 1 kelas tujuan',
            'copyTargetClasses.min' => 'Pilih minimal 1 kelas tujuan',
            'copyDate.required' => 'Tanggal harus diisi',
            'copyTimeSlot.required' => 'Jam mengajar harus dipilih',
        ]);

        $source = $this->copySourceJournal;
        $successCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($this->copyTargetClasses as $targetClassId) {
            try {
                // Check for duplicate - use whereJsonContains for JSON array field
                $exists = TeachingJournal::where('teacher_id', auth()->id())
                    ->where('class_id', $targetClassId)
                    ->where('date', $this->copyDate)
                    ->whereJsonContains('time_slot', $this->copyTimeSlot)
                    ->exists();

                if ($exists) {
                    $class = SchoolClass::find($targetClassId);
                    $skippedCount++;
                    $errors[] = $class->name . ' (sudah ada jurnal)';
                    continue;
                }

                // Create new journal
                $newJournal = $source->replicate();
                $newJournal->class_id = $targetClassId;
                $newJournal->date = $this->copyDate;
                $newJournal->time_slot = [$this->copyTimeSlot]; // Single time slot as array
                $newJournal->activity_photo = null; // Don't copy photo
                $newJournal->save();

                // Create default attendance (all present)
                $students = \App\Models\User::where('role', 'siswa')
                    ->where('is_active', true)
                    ->whereHas('enrollments', function($q) use ($targetClassId) {
                        $q->where('class_id', $targetClassId);
                    })
                    ->get();

                foreach ($students as $student) {
                    \App\Models\StudentAttendance::create([
                        'teaching_journal_id' => $newJournal->id,
                        'student_id' => $student->id,
                        'status' => 'hadir',
                    ]);
                }

                // Update stats
                $newJournal->updateAttendanceStats();
                $successCount++;

            } catch (\Exception $e) {
                $class = SchoolClass::find($targetClassId);
                $errors[] = $class->name . ' (error: ' . $e->getMessage() . ')';
            }
        }

        // Close modal
        $this->closeCopyModal();

        // Show result message
        if ($successCount > 0) {
            $message = "Jurnal berhasil di-copy ke {$successCount} kelas";
            if ($skippedCount > 0) {
                $message .= ". {$skippedCount} kelas dilewati: " . implode(', ', $errors);
            }
            session()->flash('success', $message);
        } else {
            session()->flash('error', 'Gagal copy jurnal: ' . implode(', ', $errors));
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Jurnal Mengajar - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $query = TeachingJournal::with(['teacher', 'schoolClass', 'subject', 'academicYear'])
            ->orderBy('date', 'desc');

        // Filter by teacher (if not admin/waka/kepsek)
        if (!auth()->user()->canManageUsers() && !auth()->user()->isWakaKurikulum()) {
            $query->where('teacher_id', auth()->id());
        }

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('topic', 'like', '%' . $this->search . '%')
                  ->orWhere('learning_objective', 'like', '%' . $this->search . '%')
                  ->orWhereHas('teacher', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filter by class
        if ($this->filterClass !== 'all') {
            $query->where('class_id', $this->filterClass);
        }

        // Filter by subject
        if ($this->filterSubject !== 'all') {
            $query->where('subject_id', $this->filterSubject);
        }

        // Filter by date
        if ($this->filterDate) {
            $query->whereDate('date', $this->filterDate);
        }

        $journals = $query->paginate(15);
        
        // Get active academic year
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        
        // Data for filters - only show classes from active academic year
        $classes = SchoolClass::when($activeAcademicYear, function($q) use ($activeAcademicYear) {
                $q->where('academic_year_id', $activeAcademicYear->id);
            })
            ->orderBy('name')
            ->get();
        
        $subjects = auth()->user()->isGuru() 
            ? auth()->user()->subjects 
            : Subject::orderBy('name')->get();
        
        // Data for report modal
        $teachers = User::where('role', 'guru')->orderBy('name')->get();

        return view('livewire.teaching-journal.index', [
            'journals' => $journals,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }
}


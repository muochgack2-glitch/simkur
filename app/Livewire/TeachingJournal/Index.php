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
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class Index extends Component
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
        
        // Data for filters
        $classes = SchoolClass::orderBy('name')->get();
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

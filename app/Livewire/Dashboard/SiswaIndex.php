<?php

namespace App\Livewire\Dashboard;

use App\Models\StudentAttendance;
use App\Models\Assessment;
use App\Models\StudentLearningProfile;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class SiswaIndex extends Component
{
    public $attendanceThisMonth = [];
    public $attendancePercentage = 0;
    public $subjectsLearnedThisMonth = 0;
    public $totalSubjects = 0;
    public $availableAssessments = 0;
    public $completedAssessments = 0;
    public $attendanceChartData = [];
    public $myLearningProfile = null;

    public function mount()
    {
        $studentId = auth()->id();

        // Attendance this month
        $attendances = StudentAttendance::whereHas('teachingJournal', function($q) {
            $q->whereYear('date', now()->year)
              ->whereMonth('date', now()->month);
        })
        ->where('student_id', $studentId)
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get();

        $this->attendanceThisMonth = [
            'hadir' => $attendances->where('status', 'hadir')->first()->total ?? 0,
            'sakit' => $attendances->where('status', 'sakit')->first()->total ?? 0,
            'izin' => $attendances->where('status', 'izin')->first()->total ?? 0,
            'alpha' => $attendances->where('status', 'alpha')->first()->total ?? 0,
        ];

        $totalAttendance = array_sum($this->attendanceThisMonth);
        if ($totalAttendance > 0) {
            $this->attendancePercentage = round(($this->attendanceThisMonth['hadir'] / $totalAttendance) * 100, 1);
        }

        // Subjects learned this month
        $this->subjectsLearnedThisMonth = StudentAttendance::whereHas('teachingJournal', function($q) {
            $q->whereYear('date', now()->year)
              ->whereMonth('date', now()->month);
        })
        ->where('student_id', $studentId)
        ->distinct()
        ->count(DB::raw('DISTINCT teaching_journal_id'));

        // Total subjects (based on class if assigned)
        if (auth()->user()->class_id) {
            $this->totalSubjects = Subject::whereHas('teachingJournals', function($q) {
                $q->where('class_id', auth()->user()->class_id);
            })->distinct()->count();
        } else {
            $this->totalSubjects = Subject::count();
        }

        // Available assessments
        $user = auth()->user();
        $assessmentQuery = Assessment::where('is_published', true)
            ->where('is_active', true);

        // Filter by grade and major if applicable
        if ($user->grade && $user->major) {
            $assessmentQuery->where(function($q) use ($user) {
                $q->whereNull('target_grades')
                  ->orWhereJsonContains('target_grades', $user->grade);
            })->where(function($q) use ($user) {
                $q->whereNull('target_majors')
                  ->orWhereJsonContains('target_majors', $user->major);
            });
        }

        $this->availableAssessments = $assessmentQuery->count();

        // Completed assessments
        $this->completedAssessments = StudentLearningProfile::where('user_id', $studentId)->count();

        // My learning profile (most recent)
        $this->myLearningProfile = StudentLearningProfile::where('user_id', $studentId)
            ->with('assessment')
            ->orderBy('completed_at', 'desc')
            ->first();

        // Prepare attendance chart (last 6 months)
        $this->prepareAttendanceChartData($studentId);
    }

    private function prepareAttendanceChartData($studentId)
    {
        $months = [];
        $hadirData = [];
        $sakitData = [];
        $izinData = [];
        $alphaData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->locale('id')->isoFormat('MMM');
            
            $attendances = StudentAttendance::whereHas('teachingJournal', function($q) use ($date) {
                $q->whereYear('date', $date->year)
                  ->whereMonth('date', $date->month);
            })
            ->where('student_id', $studentId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

            $hadirData[] = $attendances->where('status', 'hadir')->first()->total ?? 0;
            $sakitData[] = $attendances->where('status', 'sakit')->first()->total ?? 0;
            $izinData[] = $attendances->where('status', 'izin')->first()->total ?? 0;
            $alphaData[] = $attendances->where('status', 'alpha')->first()->total ?? 0;
        }

        $this->attendanceChartData = [
            'labels' => $months,
            'hadir' => $hadirData,
            'sakit' => $sakitData,
            'izin' => $izinData,
            'alpha' => $alphaData,
        ];
    }

    #[Layout('components.layouts.app')]
    #[Title('Dashboard Siswa - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $studentId = auth()->id();

        // Recent subjects learned (last 5 unique subjects)
        $recentSubjects = StudentAttendance::with(['teachingJournal.subject', 'teachingJournal.teacher'])
            ->where('student_id', $studentId)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->unique('teachingJournal.subject_id')
            ->take(5);

        // Available assessments for me
        $user = auth()->user();
        $assessments = Assessment::where('is_published', true)
            ->where('is_active', true);

        if ($user->grade && $user->major) {
            $assessments->where(function($q) use ($user) {
                $q->whereNull('target_grades')
                  ->orWhereJsonContains('target_grades', $user->grade);
            })->where(function($q) use ($user) {
                $q->whereNull('target_majors')
                  ->orWhereJsonContains('target_majors', $user->major);
            });
        }

        $assessments = $assessments->limit(5)->get();

        return view('livewire.dashboard.siswa-index', [
            'recentSubjects' => $recentSubjects,
            'assessments' => $assessments,
        ]);
    }
}

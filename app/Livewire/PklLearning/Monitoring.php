<?php
namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;
use App\Models\PklSubmission;
use App\Models\PklQuizResponse;
use App\Models\SchoolClass;
use App\Models\User;

class Monitoring extends BaseComponent
{
    public $filterClass = '';
    public $filterTeacher = '';
    public $filterPeriod = '';
    
    public $pklClasses = [];
    public $teachers = [];
    public $periods = [];
    
    public $stats = [];
    public $courses = [];
    public $teacherGrid = [];
    
    // Detail
    public $selectedCourseId = null;
    public $courseDetail = null;
    public $studentDetails = [];

    public function mount()
    {
        $ay = AcademicYear::where('is_active', true)->first();
        if (!$ay) return;

        $pklClassIds = \App\Models\TeachingSchedule::where('academic_year_id', $ay->id)
            ->where('is_active', false)->pluck('class_id')->unique();
        
        $this->pklClasses = SchoolClass::whereIn('id', $pklClassIds)->orderBy('name')->get();
        $this->periods = \App\Models\PklPeriod::where('academic_year_id', $ay->id)
            ->where('is_active', true)->orderBy('period_number')->get();
        
        // All teachers assigned to PKL classes
        $teacherIds = \App\Models\TeachingSchedule::where('academic_year_id', $ay->id)
            ->whereIn('class_id', $pklClassIds)->pluck('teacher_id')->unique();
        $this->teachers = User::whereIn('id', $teacherIds)->orderBy('name')->get();

        $this->loadData();
    }

    public function updatedFilterClass() { $this->selectedCourseId = null; $this->loadData(); }
    public function updatedFilterTeacher() { $this->selectedCourseId = null; $this->loadData(); }
    public function updatedFilterPeriod() { $this->selectedCourseId = null; $this->loadData(); }

    public function loadData()
    {
        $ay = AcademicYear::where('is_active', true)->first();
        if (!$ay) return;

        $query = PklCourse::with(['subject', 'teacher', 'period', 'materials', 'assignments', 'quizzes'])
            ->where('academic_year_id', $ay->id)
            ->where('is_published', true);

        if ($this->filterTeacher) $query->where('teacher_id', $this->filterTeacher);
        if ($this->filterPeriod) $query->where('pkl_period_id', $this->filterPeriod);
        if ($this->filterClass) {
            $classId = (int) $this->filterClass;
            $query->whereJsonContains('target_classes', $classId);
        }

        $this->courses = $query->orderBy('order')->get();

        // Stats
        $courseIds = $this->courses->pluck('id');
        $subs = PklSubmission::whereHas('assignment', fn($q) => $q->whereIn('pkl_course_id', $courseIds));
        $quizR = PklQuizResponse::whereHas('quiz', fn($q) => $q->whereIn('pkl_course_id', $courseIds));

        $this->stats = [
            'total_courses' => $this->courses->count(),
            'total_materials' => $this->courses->sum(fn($c) => $c->materials->count()),
            'total_assignments' => $this->courses->sum(fn($c) => $c->assignments->count()),
            'total_quizzes' => $this->courses->sum(fn($c) => $c->quizzes->count()),
            'submissions' => (clone $subs)->whereNotNull('submitted_at')->count(),
            'graded' => (clone $subs)->whereNotNull('graded_at')->count(),
            'ungraded' => (clone $subs)->whereNotNull('submitted_at')->whereNull('graded_at')->count(),
            'late' => (clone $subs)->where('is_late', true)->count(),
            'quiz_done' => (clone $quizR)->whereNotNull('submitted_at')->count(),
            'avg_score' => (clone $subs)->whereNotNull('score')->avg('score'),
        ];

        // Teacher period grid
        $pklClassIds = $this->pklClasses->pluck('id');
        $schedules = \App\Models\TeachingSchedule::with(['teacher', 'subject'])
            ->where('academic_year_id', $ay->id)
            ->whereIn('class_id', $pklClassIds)
            ->get()->unique(fn($s) => $s->teacher_id . '-' . $s->subject_id);

        if ($this->filterTeacher) {
            $schedules = $schedules->where('teacher_id', $this->filterTeacher);
        }

        $allPublished = PklCourse::where('academic_year_id', $ay->id)->where('is_published', true)->get();
        
        $this->teacherGrid = [];
        foreach ($schedules as $s) {
            $ps = [];
            $total = 0;
            foreach ($this->periods as $p) {
                $has = $allPublished->filter(fn($c) => $c->teacher_id == $s->teacher_id && $c->pkl_period_id == $p->id)->count();
                $ps[$p->id] = $has;
                if ($has > 0) $total++;
            }
            $this->teacherGrid[] = [
                'name' => $s->teacher->name ?? '-',
                'subject' => $s->subject->name ?? '-',
                'periods' => $ps,
                'total' => $total,
                'max' => $this->periods->count(),
            ];
        }
        usort($this->teacherGrid, fn($a, $b) => $a['total'] <=> $b['total']);
    }

    public function showDetail($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->courseDetail = PklCourse::with(['subject', 'teacher', 'assignments', 'quizzes', 'materials'])->find($courseId);
        if (!$this->courseDetail) return;

        $targetClassIds = array_map('intval', $this->courseDetail->target_classes ?? []);
        if ($this->filterClass) $targetClassIds = [(int)$this->filterClass];

        $students = User::where('role', 'siswa')->whereIn('class_id', $targetClassIds)
            ->where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        $this->studentDetails = [];
        foreach ($students as $student) {
            $asgIds = $this->courseDetail->assignments->pluck('id');
            $submissions = PklSubmission::whereIn('pkl_assignment_id', $asgIds)->where('student_id', $student->id)->get();
            $submitted = $submissions->whereNotNull('submitted_at')->count();
            $graded = $submissions->whereNotNull('graded_at')->count();
            $avgScore = $submissions->whereNotNull('score')->avg('score');

            $quizIds = $this->courseDetail->quizzes->pluck('id');
            $quizResponses = PklQuizResponse::whereIn('pkl_quiz_id', $quizIds)->where('student_id', $student->id)->get();
            $quizDone = $quizResponses->whereNotNull('submitted_at')->count();
            $quizAvg = $quizResponses->whereNotNull('score')->avg('score');

            $this->studentDetails[] = [
                'name' => $student->name,
                'class' => $student->schoolClass->name ?? '-',
                'asg_done' => $submitted . '/' . $this->courseDetail->assignments->count(),
                'asg_graded' => $graded,
                'asg_avg' => $avgScore ? round($avgScore, 1) : '-',
                'quiz_done' => $quizDone . '/' . $this->courseDetail->quizzes->where('is_published', true)->count(),
                'quiz_avg' => $quizAvg ? round($quizAvg, 1) : '-',
            ];
        }
    }

    public function closeDetail()
    {
        $this->selectedCourseId = null;
        $this->courseDetail = null;
        $this->studentDetails = [];
    }

    public function render()
    {
        return view('livewire.pkl-learning.monitoring');
    }
}
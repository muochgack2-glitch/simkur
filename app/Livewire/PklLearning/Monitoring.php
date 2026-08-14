<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;
use App\Models\PklSubmission;
use App\Models\PklQuizResponse;
use App\Models\SchoolClass;
use App\Models\TeachingSchedule;
use App\Models\User;

class Monitoring extends BaseComponent
{
    public $courses = [];
    public $stats = [];
    public $filterTeacher = '';
    public $filterSubject = '';
    public $selectedCourseId = null;
    public $courseDetail = null;
    public $studentDetails = [];
    public $classProgress = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) return;

        $query = PklCourse::with(['subject', 'teacher', 'assignments', 'quizzes', 'materials'])
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('created_at', 'desc');

        if ($this->filterTeacher) {
            $query->where('teacher_id', $this->filterTeacher);
        }

        if ($this->filterSubject) {
            $query->where('subject_id', $this->filterSubject);
        }

        $this->courses = $query->get();

        // Global stats
        $courseIds = $this->courses->pluck('id');
        $allSubmissions = PklSubmission::whereHas('assignment', fn($q) => $q->whereIn('pkl_course_id', $courseIds));
        $allQuizResponses = PklQuizResponse::whereHas('quiz', fn($q) => $q->whereIn('pkl_course_id', $courseIds));

        $submittedSubs = (clone $allSubmissions)->whereNotNull('submitted_at');
        $gradedSubs = (clone $allSubmissions)->whereNotNull('graded_at');
        $lateSubs = (clone $allSubmissions)->where('is_late', true);
        $quizDone = (clone $allQuizResponses)->whereNotNull('submitted_at');

        // Average scores
        $avgAssignment = (clone $gradedSubs)->avg('score');
        $avgQuiz = (clone $quizDone)->avg('score');

        $this->stats = [
            'total_courses' => $this->courses->count(),
            'published' => $this->courses->where('is_published', true)->count(),
            'total_submissions' => $submittedSubs->count(),
            'total_graded' => $gradedSubs->count(),
            'total_quiz_responses' => $quizDone->count(),
            'late_submissions' => $lateSubs->count(),
            'avg_assignment_score' => $avgAssignment ? round($avgAssignment, 1) : '-',
            'avg_quiz_score' => $avgQuiz ? round($avgQuiz, 1) : '-',
        ];

        $this->loadTeacherStats();
    }

    public function showDetail($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->courseDetail = PklCourse::with(['subject', 'teacher', 'assignments', 'quizzes', 'materials'])->find($courseId);

        if (!$this->courseDetail) return;

        $targetClassIds = array_map('intval', $this->courseDetail->target_classes ?? []);
        $students = User::where('role', 'siswa')
            ->whereIn('class_id', $targetClassIds)
            ->where('is_active', true)
            ->with('schoolClass')
            ->orderBy('name')
            ->get();

        // Per-student detail
        $this->studentDetails = [];
        foreach ($students as $student) {
            $progress = $this->courseDetail->getProgressForStudent($student->id);

            // Assignment details
            $assignmentIds = $this->courseDetail->assignments->pluck('id');
            $submissions = PklSubmission::whereIn('pkl_assignment_id', $assignmentIds)
                ->where('student_id', $student->id)
                ->get();
            $submitted = $submissions->whereNotNull('submitted_at')->count();
            $graded = $submissions->whereNotNull('graded_at')->count();
            $late = $submissions->where('is_late', true)->count();
            $avgScore = $submissions->whereNotNull('score')->avg('score');

            // Quiz details
            $quizIds = $this->courseDetail->quizzes->pluck('id');
            $quizResponses = PklQuizResponse::whereIn('pkl_quiz_id', $quizIds)
                ->where('student_id', $student->id)
                ->get();
            $quizDone = $quizResponses->whereNotNull('submitted_at')->count();
            $quizAvg = $quizResponses->whereNotNull('score')->avg('score');

            $this->studentDetails[] = [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->schoolClass->name ?? '-',
                'progress' => $progress['percentage'],
                'assignments_submitted' => $submitted,
                'assignments_total' => $this->courseDetail->assignments->count(),
                'assignments_graded' => $graded,
                'assignments_late' => $late,
                'assignment_avg' => $avgScore ? round($avgScore, 1) : '-',
                'quizzes_done' => $quizDone,
                'quizzes_total' => $this->courseDetail->quizzes->where('is_published', true)->count(),
                'quiz_avg' => $quizAvg ? round($quizAvg, 1) : '-',
            ];
        }

        // Per-class progress
        $this->classProgress = [];
        $classes = SchoolClass::whereIn('id', $targetClassIds)->get();
        foreach ($classes as $class) {
            $classStudents = collect($this->studentDetails)->where('class', $class->name);
            $avgProgress = $classStudents->avg('progress');
            $this->classProgress[] = [
                'name' => $class->name,
                'student_count' => $classStudents->count(),
                'avg_progress' => round($avgProgress ?? 0, 1),
                'completed' => $classStudents->where('progress', 100)->count(),
            ];
        }
    }

    public function closeDetail()
    {
        $this->selectedCourseId = null;
        $this->courseDetail = null;
        $this->studentDetails = [];
        $this->classProgress = [];

        $this->loadTeacherStats();
    }

    public $teacherStats = [];

    public function loadTeacherStats()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) return;

        // Get ALL teachers who have schedules in PKL classes (deactivated)
        $pklClassIds = \App\Models\TeachingSchedule::where('academic_year_id', $academicYear->id)
            ->where('is_active', false)
            ->pluck('class_id')
            ->unique();

        if ($pklClassIds->isEmpty()) {
            $pklClassIds = SchoolClass::where('academic_year_id', $academicYear->id)
                ->where('grade', 'XII')
                ->pluck('id');
        }

        // All teachers with schedules in PKL classes
        $pklSchedules = \App\Models\TeachingSchedule::where('academic_year_id', $academicYear->id)
            ->whereIn('class_id', $pklClassIds)
            ->with(['teacher', 'subject'])
            ->get();

        $teacherSubjects = $pklSchedules->groupBy('teacher_id');

        // All courses
        $allCourses = PklCourse::with(['assignments', 'quizzes', 'materials'])
            ->where('academic_year_id', $academicYear->id)
            ->get()
            ->groupBy('teacher_id');

        $this->teacherStats = [];

        foreach ($teacherSubjects as $teacherId => $schedules) {
            $teacher = $schedules->first()->teacher;
            if (!$teacher) continue;

            $mapelNames = $schedules->pluck('subject.name')->unique()->filter()->implode(', ');
            $teacherCourses = $allCourses->get($teacherId, collect());

            $totalMaterials = 0;
            $totalAssignments = 0;
            $totalQuizzes = 0;
            $ungradedCount = 0;

            foreach ($teacherCourses as $course) {
                $totalMaterials += $course->materials->count();
                $totalAssignments += $course->assignments->count();
                $totalQuizzes += $course->quizzes->count();

                $assignmentIds = $course->assignments->pluck('id');
                $ungradedCount += PklSubmission::whereIn('pkl_assignment_id', $assignmentIds)
                    ->whereNotNull('submitted_at')
                    ->whereNull('graded_at')
                    ->count();
            }

            $this->teacherStats[] = [
                'name' => $teacher->name,
                'mapel' => $mapelNames,
                'courses' => $teacherCourses->count(),
                'published' => $teacherCourses->where('is_published', true)->count(),
                'materials' => $totalMaterials,
                'assignments' => $totalAssignments,
                'quizzes' => $totalQuizzes,
                'ungraded' => $ungradedCount,
                'has_course' => $teacherCourses->count() > 0,
            ];
        }

        // Sort: yang belum buat course di atas
        usort($this->teacherStats, function($a, $b) {
            if ($a['has_course'] === $b['has_course']) return strcmp($a['name'], $b['name']);
            return $a['has_course'] ? 1 : -1;
        });
    }

    public function updatedFilterTeacher() { $this->loadData(); }
    public function updatedFilterSubject() { $this->loadData(); }

    public function render()
    {
        return view('livewire.pkl-learning.monitoring');
    }
}
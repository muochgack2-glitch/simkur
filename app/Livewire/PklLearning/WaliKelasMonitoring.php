<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;
use App\Models\SchoolClass;
use App\Models\User;

class WaliKelasMonitoring extends BaseComponent
{
    public $classes = [];
    public $selectedClassId = null;
    public $students = [];
    public $courseStats = [];
    public $teacherStats = [];
    public $studentProgress = [];

    public function mount()
    {
        $user = auth()->user();
        $this->classes = $user->homeroomPklClasses();

        if ($this->classes->isNotEmpty()) {
            $this->selectedClassId = $this->classes->first()->id;
            $this->loadData();
        }
    }

    public function updatedSelectedClassId()
    {
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->selectedClassId) return;

        $ay = AcademicYear::where('is_active', true)->first();
        if (!$ay) return;

        $class = SchoolClass::with('students')->find($this->selectedClassId);
        if (!$class) return;

        $this->students = $class->students;

        // Get courses targeting this class
        $courses = PklCourse::with(['subject', 'teacher', 'period', 'materials', 'assignments', 'quizzes'])
            ->where('academic_year_id', $ay->id)
            ->where('is_published', true)
            ->whereJsonContains('target_classes', (int) $class->id)
            ->orderBy('order')
            ->get();

        // Course stats (guru side)
        $this->courseStats = [];
        foreach ($courses as $course) {
            $this->courseStats[] = [
                'id' => $course->id,
                'title' => $course->title,
                'teacher' => $course->teacher->name ?? '-',
                'subject' => $course->subject->name ?? '-',
                'period' => $course->period ? 'P' . $course->period->period_number : '-',
                'materials_count' => $course->materials->count(),
                'assignments_count' => $course->assignments->count(),
                'quizzes_count' => $course->quizzes->count(),
                'has_empty_material' => $course->materials->where('file_path', null)->where('external_url', null)->count() > 0,
            ];
        }

        // Teacher stats - all teachers assigned to this class
        $schedules = \App\Models\TeachingSchedule::with(['teacher', 'subject'])
            ->where('academic_year_id', $ay->id)
            ->where('class_id', $class->id)
            ->get()
            ->unique(fn($s) => $s->teacher_id . '-' . $s->subject_id);

        $this->teacherStats = [];
        foreach ($schedules as $schedule) {
            $teacherCourses = $courses->filter(fn($c) => $c->teacher_id == $schedule->teacher_id);
            $publishedCount = $teacherCourses->count();
            $materialsCount = $teacherCourses->sum(fn($c) => $c->materials->count());
            $assignmentsCount = $teacherCourses->sum(fn($c) => $c->assignments->count());
            $quizzesCount = $teacherCourses->sum(fn($c) => $c->quizzes->count());
            $hasEmptyFiles = $teacherCourses->contains(fn($c) => $c->materials->where('file_path', null)->where('external_url', null)->count() > 0);

            $this->teacherStats[] = [
                'teacher_name' => $schedule->teacher->name ?? '-',
                'subject' => $schedule->subject->name ?? '-',
                'published_courses' => $publishedCount,
                'materials_count' => $materialsCount,
                'assignments_count' => $assignmentsCount,
                'quizzes_count' => $quizzesCount,
                'has_empty_files' => $hasEmptyFiles,
                'status' => $publishedCount > 0 ? 'published' : 'not_published',
            ];
        }

        // Sort: belum publish di atas
        usort($this->teacherStats, fn($a, $b) => ($a['status'] === 'not_published' ? 0 : 1) <=> ($b['status'] === 'not_published' ? 0 : 1));

        // Student progress
        $this->studentProgress = [];
        foreach ($this->students as $student) {
            $totalAssignments = 0;
            $submittedAssignments = 0;
            $totalQuizzes = 0;
            $submittedQuizzes = 0;
            $totalScore = 0;
            $gradedCount = 0;

            foreach ($courses as $course) {
                foreach ($course->assignments as $asg) {
                    $totalAssignments++;
                    $sub = $asg->getSubmissionForStudent($student->id);
                    if ($sub && $sub->isSubmitted()) {
                        $submittedAssignments++;
                        if ($sub->isGraded()) {
                            $totalScore += $sub->score;
                            $gradedCount++;
                        }
                    }
                }
                foreach ($course->quizzes->where('is_published', true) as $quiz) {
                    $totalQuizzes++;
                    $resp = $quiz->getResponseForStudent($student->id);
                    if ($resp && $resp->isSubmitted()) {
                        $submittedQuizzes++;
                        if ($resp->isGraded()) {
                            $totalScore += $resp->score;
                            $gradedCount++;
                        }
                    }
                }
            }

            $this->studentProgress[] = [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis ?? '-',
                'total_assignments' => $totalAssignments,
                'submitted_assignments' => $submittedAssignments,
                'total_quizzes' => $totalQuizzes,
                'submitted_quizzes' => $submittedQuizzes,
                'avg_score' => $gradedCount > 0 ? round($totalScore / $gradedCount, 1) : null,
                'completion' => ($totalAssignments + $totalQuizzes) > 0
                    ? round(($submittedAssignments + $submittedQuizzes) / ($totalAssignments + $totalQuizzes) * 100)
                    : 0,
            ];
        }

        // Sort by completion asc (yang belum selesai di atas)
        usort($this->studentProgress, fn($a, $b) => $a['completion'] <=> $b['completion']);
    }

    public function render()
    {
        return view('livewire.pkl-learning.wali-kelas-monitoring');
    }
}
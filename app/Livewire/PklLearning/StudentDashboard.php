<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;

class StudentDashboard extends BaseComponent
{
    public $progress = [];

    public function render()
    {
        $user = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        $courses = collect();
        $periods = collect();
        $groupedCourses = collect();

        if ($academicYear && $user->class_id) {
            $courses = PklCourse::with(['subject', 'teacher', 'materials', 'assignments', 'quizzes', 'period'])
                ->where('academic_year_id', $academicYear->id)
                ->where('is_published', true)
                ->whereJsonContains('target_classes', (int) $user->class_id)
                ->where(function($q) {
                    $q->whereNull('pkl_period_id')
                      ->orWhereHas('pklPeriod', fn($p) => $p->where('is_active', true));
                })
                ->orderBy('order')
                ->get();

            foreach ($courses as $course) {
                $this->progress[$course->id] = $course->getProgressForStudent($user->id);
            }

            $periods = \App\Models\PklPeriod::where('academic_year_id', $academicYear->id)
                ->where('is_active', true)->orderBy('period_number')->get();

            $groupedCourses = $courses->groupBy('pkl_period_id');
        }

        // Stats
        $totalCourses = $courses->count();
        $totalAssignments = $courses->sum(fn($c) => $c->assignments->count());
        $totalQuizzes = $courses->sum(fn($c) => $c->quizzes->where('is_published', true)->count());
        $doneAssignments = 0; $doneQuizzes = 0; $scores = [];
        foreach ($courses as $c) {
            foreach ($c->assignments as $a) {
                $sub = \App\Models\PklSubmission::where('pkl_assignment_id', $a->id)->where('student_id', $user->id)->whereNotNull('submitted_at')->first();
                if ($sub) { $doneAssignments++; if ($sub->score !== null) $scores[] = $sub->score; }
            }
            foreach ($c->quizzes->where('is_published', true) as $q) {
                $resp = \App\Models\PklQuizResponse::where('pkl_quiz_id', $q->id)->where('student_id', $user->id)->whereNotNull('submitted_at')->first();
                if ($resp) { $doneQuizzes++; if ($resp->score !== null) $scores[] = $resp->score; }
            }
        }
        $stats = [
            'courses' => $totalCourses,
            'asg_done' => $doneAssignments, 'asg_total' => $totalAssignments,
            'quiz_done' => $doneQuizzes, 'quiz_total' => $totalQuizzes,
            'avg_score' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : null,
        ];

        return view('livewire.pkl-learning.student-dashboard', [
            'stats' => $stats,
            'courses' => $courses,
            'periods' => $periods,
            'groupedCourses' => $groupedCourses,
        ]);
    }
}
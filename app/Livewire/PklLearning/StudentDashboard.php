<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;
use App\Models\PklSubmission;
use App\Models\PklQuizResponse;
use Carbon\Carbon;

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
        $urgentAssignments = collect();
        $pendingPerCourse = [];
        $revisionPerCourse = [];

        if ($academicYear && $user->class_id) {
            $courses = PklCourse::with(['subject', 'teacher', 'materials', 'assignments', 'quizzes', 'pklPeriod'])
                ->where('academic_year_id', $academicYear->id)
                ->where('is_published', true)
                ->whereJsonContains('target_classes', (int) $user->class_id)
                ->orderBy('order')
                ->get();

            foreach ($courses as $course) {
                $this->progress[$course->id] = $course->getProgressForStudent($user->id);

                // Count pending assignments per course
                $pending = 0;
                $revisions = 0;
                foreach ($course->assignments as $asg) {
                    $sub = PklSubmission::where('pkl_assignment_id', $asg->id)
                        ->where('student_id', $user->id)
                        ->whereNotNull('submitted_at')->first();
                    if (!$sub) $pending++;
                    if ($sub && $sub->revision_requested) { $pending++; $revisions++; }

                    // Urgent: deadline within 7 days and not submitted
                    if (!$sub && $asg->deadline) {
                        $deadlineDay = Carbon::parse($asg->deadline)->startOfDay();
                        $today = now()->startOfDay();
                        // Only show if deadline is today or in future
                        if ($deadlineDay->gte($today)) {
                            $daysLeft = (int) $today->diffInDays($deadlineDay);
                            if ($daysLeft <= 7) {
                                $urgentAssignments->push([
                                    'title' => $asg->title,
                                    'course' => $course->title,
                                    'deadline' => $deadlineDay,
                                    'days_left' => $daysLeft,
                                    'course_id' => $course->id,
                                ]);
                            }
                        }
                    }
                }
                $pendingPerCourse[$course->id] = $pending;
                $revisionPerCourse[$course->id] = $revisions;
            }

            $periods = \App\Models\PklPeriod::where('academic_year_id', $academicYear->id)
                ->orderBy('period_number')->get();

            $groupedCourses = $courses->groupBy('pkl_period_id');

            // Sort urgent by deadline
            $urgentAssignments = $urgentAssignments->sortBy('deadline')->values();
        }

        // Stats
        $totalCourses = $courses->count();
        $totalAssignments = $courses->sum(fn($c) => $c->assignments->count());
        $totalQuizzes = $courses->sum(fn($c) => $c->quizzes->where('is_published', true)->count());
        $doneAssignments = 0; $doneQuizzes = 0; $scores = [];
        foreach ($courses as $c) {
            foreach ($c->assignments as $a) {
                $sub = PklSubmission::where('pkl_assignment_id', $a->id)->where('student_id', $user->id)->whereNotNull('submitted_at')->first();
                if ($sub) { $doneAssignments++; if ($sub->score !== null) $scores[] = $sub->score; }
            }
            foreach ($c->quizzes->where('is_published', true) as $q) {
                $resp = PklQuizResponse::where('pkl_quiz_id', $q->id)->where('student_id', $user->id)->whereNotNull('submitted_at')->first();
                if ($resp) { $doneQuizzes++; if ($resp->score !== null) $scores[] = $resp->score; }
            }
        }
        $totalProgress = $totalCourses > 0 ? round(collect($this->progress)->avg('percentage')) : 0;
        $stats = [
            'courses' => $totalCourses,
            'asg_done' => $doneAssignments, 'asg_total' => $totalAssignments,
            'quiz_done' => $doneQuizzes, 'quiz_total' => $totalQuizzes,
            'avg_score' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : null,
            'total_progress' => $totalProgress,
        ];

        return view('livewire.pkl-learning.student-dashboard', [
            'stats' => $stats,
            'courses' => $courses,
            'periods' => $periods,
            'groupedCourses' => $groupedCourses,
            'urgentAssignments' => $urgentAssignments,
            'pendingPerCourse' => $pendingPerCourse,
            'revisionPerCourse' => $revisionPerCourse,
            'user' => $user,
        ]);
    }
}


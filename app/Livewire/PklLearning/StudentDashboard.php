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
                ->orderBy('order')
                ->get();

            foreach ($courses as $course) {
                $this->progress[$course->id] = $course->getProgressForStudent($user->id);
            }

            $periods = \App\Models\PklPeriod::where('academic_year_id', $academicYear->id)
                ->where('is_active', true)->orderBy('period_number')->get();

            $groupedCourses = $courses->groupBy('pkl_period_id');
        }

        return view('livewire.pkl-learning.student-dashboard', [
            'courses' => $courses,
            'periods' => $periods,
            'groupedCourses' => $groupedCourses,
        ]);
    }
}
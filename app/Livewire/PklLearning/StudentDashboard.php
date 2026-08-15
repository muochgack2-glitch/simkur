<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;

class StudentDashboard extends BaseComponent
{
    public $courses = [];
    public $progress = [];
    public $periods = [];
    public $groupedCourses = [];

    public function mount()
    {
        $user = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear || !$user->class_id) return;

        $this->courses = PklCourse::with(['subject', 'teacher', 'materials', 'assignments', 'quizzes'])
            ->where('academic_year_id', $academicYear->id)
            ->where('is_published', true)
            ->whereJsonContains('target_classes', (int) $user->class_id)
            ->orderBy('order')
            ->get();

        foreach ($this->courses as $course) {
            $this->progress[$course->id] = $course->getProgressForStudent($user->id);
        }

        // Group by period
        $this->periods = \App\Models\PklPeriod::where('academic_year_id', $academicYear->id)
            ->where('is_active', true)->orderBy('period_number')->get();

        $this->groupedCourses = $this->courses->groupBy('pkl_period_id');
    }

    public function render()
    {
        return view('livewire.pkl-learning.student-dashboard');
    }
}

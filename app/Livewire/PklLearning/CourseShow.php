<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklCourse;
use App\Models\User;

class CourseShow extends BaseComponent
{
    public PklCourse $course;
    public $students = [];
    public $studentProgress = [];

    public function mount(PklCourse $course)
    {
        $this->course = $course->load(['materials', 'assignments.submissions', 'quizzes.responses', 'subject', 'teacher', 'activity']);

        // Get target students - cast class IDs to int for proper matching
        $targetClassIds = array_map('intval', $this->course->target_classes ?? []);
        $this->students = User::where('role', 'siswa')
            ->whereIn('class_id', $targetClassIds)
            ->where('is_active', true)
            ->with('schoolClass')
            ->orderBy('name')
            ->get();

        // Calculate progress per student
        foreach ($this->students as $student) {
            $this->studentProgress[$student->id] = $this->course->getProgressForStudent($student->id);
        }
    }

    public function render()
    {
        return view('livewire.pkl-learning.course-show');
    }
}

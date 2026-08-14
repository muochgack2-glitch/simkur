<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\PklCourse;
use App\Models\PklSubmission;
use App\Models\PklQuizResponse;

class Monitoring extends BaseComponent
{
    public $courses = [];
    public $stats = [];
    public $filterTeacher = '';
    public $filterSubject = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) return;

        $query = PklCourse::with(['subject', 'teacher', 'assignments', 'quizzes'])
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
        $this->stats = [
            'total_courses' => $this->courses->count(),
            'published' => $this->courses->where('is_published', true)->count(),
            'total_submissions' => PklSubmission::whereHas('assignment', fn($q) => $q->whereIn('pkl_course_id', $courseIds))->whereNotNull('submitted_at')->count(),
            'total_graded' => PklSubmission::whereHas('assignment', fn($q) => $q->whereIn('pkl_course_id', $courseIds))->whereNotNull('graded_at')->count(),
            'total_quiz_responses' => PklQuizResponse::whereHas('quiz', fn($q) => $q->whereIn('pkl_course_id', $courseIds))->whereNotNull('submitted_at')->count(),
        ];
    }

    public function updatedFilterTeacher() { $this->loadData(); }
    public function updatedFilterSubject() { $this->loadData(); }

    public function render()
    {
        return view('livewire.pkl-learning.monitoring');
    }
}

<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\PklCourse;
use App\Models\SchoolClass;
use App\Models\TeachingSchedule;
use Livewire\Component;

class Dashboard extends BaseComponent
{
    public $courses = [];
    public $pklActivity = null;
    public $stats = [];

    public function mount()
    {
        $user = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) return;

        // Get active PKL activity
        $this->pklActivity = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->whereHas('activityType', fn($q) => $q->where('code', 'pkl'))
            ->first();

        // Load courses for this teacher
        $query = PklCourse::with(['subject', 'activity', 'assignments', 'quizzes', 'materials'])
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        if ($user->role === 'guru') {
            $query->where('teacher_id', $user->id);
        }

        $this->courses = $query->get();

        // Stats
        $this->stats = [
            'total_courses' => $this->courses->count(),
            'published' => $this->courses->where('is_published', true)->count(),
            'draft' => $this->courses->where('is_published', false)->count(),
            'total_assignments' => $this->courses->sum(fn($c) => $c->assignments->count()),
            'total_quizzes' => $this->courses->sum(fn($c) => $c->quizzes->count()),
        ];
    }

    public function deleteCourse($courseId)
    {
        $user = auth()->user();
        if ($user->role === 'guru') {
            $course = PklCourse::where('teacher_id', $user->id)->findOrFail($courseId);
        } else {
            $course = PklCourse::findOrFail($courseId);
        }
        $course->delete();
        session()->flash('success', "Course \"{$course->title}\" berhasil dihapus");
        return redirect()->route('pkl-learning.dashboard');
    }

    public function togglePublish($courseId)
    {
        $user = auth()->user();
        if ($user->role === 'guru') {
            $course = PklCourse::where('teacher_id', $user->id)->findOrFail($courseId);
        } else {
            $course = PklCourse::findOrFail($courseId);
        }
        $course->update(['is_published' => !$course->is_published]);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.pkl-learning.dashboard');
    }
}

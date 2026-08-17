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
    public $filterPeriod = '';
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
        $query = PklCourse::with(['subject', 'activity', 'assignments', 'quizzes', 'materials', 'pklPeriod'])
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        if ($user->role === 'guru') {
            $query->where('teacher_id', $user->id);
        }

        if ($this->filterPeriod !== '') {
            $query->where('pkl_period_id', $this->filterPeriod ?: null);
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
        $status = $course->is_published ? 'dipublikasikan' : 'dijadikan draft';
        session()->flash('success', "Materi berhasil {$status}");
        return redirect()->route('pkl-learning.dashboard');
    }

    public function render()
    {
        // Map class IDs to class info for display
        $allClassIds = collect();
        foreach ($this->courses as $c) {
            $allClassIds = $allClassIds->merge($c->target_classes ?? []);
        }
        $classMap = SchoolClass::whereIn('id', $allClassIds->unique())
            ->withCount(['students' => fn($q) => $q->where('is_active', true)])
            ->get()
            ->keyBy('id');

        return view('livewire.pkl-learning.dashboard', [
            'pklPeriods' => \App\Models\PklPeriod::orderBy('name')->get(),
            'classMap' => $classMap,
        ]);
    }
}

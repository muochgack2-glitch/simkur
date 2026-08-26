<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\PklCourse;
use App\Models\SchoolClass;
use App\Models\TeachingSchedule;
use Livewire\Component;
use App\Services\WhatsAppService;

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
        $wasDraft = !$course->is_published;
        $course->update(['is_published' => !$course->is_published]);
        $status = $course->is_published ? 'dipublikasikan' : 'dijadikan draft';
        // Kirim WA notif hanya saat draft → publish
        if ($wasDraft && $course->is_published) {
            $this->sendWaNotification($course->fresh());
        }
        session()->flash('success', "Materi berhasil {$status}");
        return redirect()->route('pkl-learning.dashboard');
    }


    protected function sendWaNotification(\App\Models\PklCourse $course): void
    {
        try {
            $groupId = \App\Models\Setting::getValue('wa_pkl_group_id');
            if (!$groupId) return;

            $teacher = $course->teacher?->name ?? auth()->user()->name;
            $classes = \App\Models\SchoolClass::whereIn('id', $course->target_classes)->pluck('name')->join(', ');
            $mapel = $course->subject?->name ?? '-';
            $assignments = $course->assignments()->orderBy('deadline')->get();
            if ($assignments->isNotEmpty()) {
                $deadlineTugas = $assignments->map(function ($asg, $i) {
                    $tgl = \Carbon\Carbon::parse($asg->deadline)->translatedFormat('d F Y');
                    return "  " . ($i + 1) . ". {$asg->title}: {$tgl}";
                })->join("\n");
            } else {
                $deadlineTugas = \Carbon\Carbon::parse($course->deadline)->translatedFormat('d F Y');
            }
            $defaultTpl = "Materi PKL Dipublikasikan\n\nJudul: {judul}\nMapel: {mapel}\nGuru: {guru}\nKelas: {kelas}\nDeadline Tugas: {deadline_tugas}\n\nSegera cek di SIM Kurikulum:\n{link}";
            $template = \App\Models\Setting::getValue('wa_pkl_template', $defaultTpl) ?: $defaultTpl;
            $message = str_replace(
                ['{judul}', '{mapel}', '{guru}', '{kelas}', '{deadline_tugas}', '{link}'],
                [$course->title, $mapel, $teacher, $classes, $deadlineTugas, url('/pkl-learning/student')],
                $template
            );
            (new WhatsAppService())->sendToGroup($groupId, $message);
        } catch (\Throwable $e) {
            \Log::error('WA notification failed (Dashboard): ' . $e->getMessage());
        }
    }
    public function render()
    {
        $user = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();

        $courses = collect();
        if ($academicYear) {
            $query = PklCourse::with(['subject', 'activity', 'assignments', 'quizzes', 'materials', 'pklPeriod'])
                ->where('academic_year_id', $academicYear->id)
                ->orderBy('order')
                ->orderBy('created_at', 'desc');

            if ($user->role === 'guru') {
                $query->where('teacher_id', $user->id);
            }

            if ($this->filterPeriod !== '') {
                $query->where('pkl_period_id', $this->filterPeriod === 'null' ? null : $this->filterPeriod);
            }

            $courses = $query->get();
        }

        $this->courses = $courses;
        $this->stats = [
            'total_courses' => $courses->count(),
            'published' => $courses->where('is_published', true)->count(),
            'draft' => $courses->where('is_published', false)->count(),
            'total_assignments' => $courses->sum(fn($c) => $c->assignments->count()),
            'total_quizzes' => $courses->sum(fn($c) => $c->quizzes->count()),
        ];

        $allClassIds = collect();
        foreach ($courses as $c) {
            $allClassIds = $allClassIds->merge($c->target_classes ?? []);
        }
        $classMap = SchoolClass::whereIn('id', $allClassIds->unique())
            ->withCount(['students' => fn($q) => $q->where('is_active', true)])
            ->get()
            ->keyBy('id');

        return view('livewire.pkl-learning.dashboard', [
            'pklPeriods' => \App\Models\PklPeriod::orderBy('period_number')->get(),
            'classMap' => $classMap,
        ]);
    }
}


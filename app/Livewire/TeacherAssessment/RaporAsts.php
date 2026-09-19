<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\TeachingSchedule;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RaporAsts extends Component
{
    public ?SchoolClass $myClass = null;
    public ?Semester $semester = null;
    public ?AcademicYear $academicYear = null;

    public function mount(): void
    {
        $this->myClass = SchoolClass::where('homeroom_teacher_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$this->myClass) {
            abort(403, 'Anda bukan wali kelas aktif.');
        }

        $this->academicYear = AcademicYear::where('is_active', true)->first();
        $semType = (now()->month >= 7) ? 'ganjil' : 'genap';
        $this->semester = $this->academicYear
            ? Semester::with('academicYear')
                ->where('academic_year_id', $this->academicYear->id)
                ->where('type', $semType)
                ->first()
              ?? Semester::with('academicYear')->where('academic_year_id', $this->academicYear->id)->orderByDesc('id')->first()
            : Semester::with('academicYear')->where('type', $semType)->orderByDesc('id')->first()
              ?? Semester::with('academicYear')->orderByDesc('id')->first();
    }

    #[Computed]
    public function students()
    {
        return User::where('class_id', $this->myClass->id)
            ->where('role', 'siswa')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function subjects()
    {
        $all = TeachingSchedule::with('subject')
            ->where('class_id', $this->myClass->id)
            ->where('is_active', true)
            ->when($this->academicYear?->id, fn($q) => $q->where('academic_year_id', $this->academicYear->id))
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $agamaSubjects = $all->whereNotNull('agama_filter')->values();
        $nonAgama      = $all->whereNull('agama_filter')->values();

        if ($agamaSubjects->isNotEmpty()) {
            $virtualAgama = (object)[
                'id'           => 'agama_merged',
                'name'         => 'Pendidikan Agama',
                'agama_filter' => 'merged',
                '_agama_ids'   => $agamaSubjects->pluck('id')->toArray(),
            ];
            return $nonAgama->push($virtualAgama)->values();
        }

        return $nonAgama;
    }

    #[Computed]
    public function assessments()
    {
        $grade = $this->myClass->grade;
        $major = $this->myClass->major;

        // Ambil pasangan (subject_id, teacher_id) yang terjadwal di kelas ini
        // agar rapor hanya menghitung soal dari guru yang memang mengajar mapel itu di kelas ini.
        $validPairs = TeachingSchedule::where('class_id', $this->myClass->id)
            ->where('is_active', true)
            ->when($this->academicYear?->id, fn($q) => $q->where('academic_year_id', $this->academicYear->id))
            ->distinct()->get(['subject_id', 'teacher_id']);

        return Assessment::with(['subject', 'assessmentLabel'])
            ->whereHas('assessmentLabel', fn($q) => $q->where('name', 'like', '%ASTS%'))
            ->when($this->semester?->id, fn($q) => $q->where('semester_id', $this->semester->id))
            ->where(function ($q) use ($grade) {
                $q->whereJsonContains('target_grades', $grade)->orWhereNull('target_grades');
            })
            ->where(function ($q) use ($major) {
                $q->whereJsonContains('target_majors', $major)->orWhereNull('target_majors');
            })
            ->where(function ($q) use ($validPairs) {
                // Hanya assessment yang teacher_id-nya sesuai jadwal di kelas ini
                foreach ($validPairs as $pair) {
                    $q->orWhere(function ($q2) use ($pair) {
                        $q2->where('subject_id', $pair->subject_id)
                           ->where('teacher_id', $pair->teacher_id);
                    });
                }
                // Fallback: assessment tanpa teacher_id (dibuat admin) tetap masuk
                $q->orWhereNull('teacher_id');
            })
            ->get();
    }

    public function getNilai(int $studentId, $subjectId): int
    {
        if ($subjectId === 'agama_merged') {
            $student = $this->students()->firstWhere('id', $studentId);
            $agama   = $student?->agama;
            if (!$agama) return 0;

            $matchingSubject = TeachingSchedule::with('subject')
                ->where('class_id', $this->myClass->id)
                ->where('is_active', true)
                ->when($this->academicYear?->id, fn($q) => $q->where('academic_year_id', $this->academicYear->id))
                ->get()
                ->pluck('subject')
                ->filter()
                ->firstWhere('agama_filter', $agama);

            if (!$matchingSubject) return 0;
            $subjectId = $matchingSubject->id;
        }

        $assessmentIds = $this->assessments()
            ->where('subject_id', $subjectId)
            ->pluck('id');

        if ($assessmentIds->isEmpty()) return 0;

        $sessions = AssessmentStudentSession::whereIn('assessment_id', $assessmentIds)
            ->where('user_id', $studentId)
            ->whereNotNull('submitted_at')
            ->get();

        if ($sessions->isEmpty()) return 0;

        $total = $sessions->sum(function ($s) {
            if ($s->max_score > 0) {
                return round($s->total_score / $s->max_score * 100);
            }
            return min(100, max(0, (int) round((float) $s->total_score)));
        });

        return (int) round($total / $sessions->count());
    }

    public function render()
    {
        return view('livewire.teacher-assessment.rapor-asts')
            ->layout('components.layouts.app');
    }
}
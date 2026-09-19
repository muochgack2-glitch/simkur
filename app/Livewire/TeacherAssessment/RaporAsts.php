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
        // Deteksi kelas wali kelas yang login
        $this->myClass = SchoolClass::where('homeroom_teacher_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$this->myClass) {
            abort(403, 'Anda bukan wali kelas aktif.');
        }

        // Semester aktif: deteksi dari bulan (7-12=Gasal, 1-6=Genap)
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

    /**
     * Semua mapel yang diajarkan di kelas ini (dari jadwal mengajar),
     * diurutkan berdasarkan nama. Termasuk mapel yang belum ada ASTS-nya.
     */
    #[Computed]
    public function subjects()
    {
        return TeachingSchedule::with('subject')
            ->where('class_id', $this->myClass->id)
            ->where('is_active', true)
            ->when($this->academicYear?->id, fn($q) => $q->where('academic_year_id', $this->academicYear->id))
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    /**
     * Semua asesmen ASTS semester aktif yang berlaku untuk kelas ini.
     * Digunakan oleh getNilai() - tetap filter by ASTS.
     */
    #[Computed]
    public function assessments()
    {
        $grade = $this->myClass->grade;
        $major = $this->myClass->major;

        return Assessment::with(['subject', 'assessmentLabel'])
            ->whereHas('assessmentLabel', fn($q) => $q->where('name', 'like', '%ASTS%'))
            ->when($this->semester?->id, fn($q) => $q->where('semester_id', $this->semester->id))
            ->where(function ($q) use ($grade) {
                $q->whereJsonContains('target_grades', $grade)
                  ->orWhereNull('target_grades');
            })
            ->where(function ($q) use ($major) {
                $q->whereJsonContains('target_majors', $major)
                  ->orWhereNull('target_majors');
            })
            ->get();
    }

    /**
     * Nilai siswa untuk satu mapel (rata-rata semua ASTS mapel itu).
     * Skala 0-100. Belum mengerjakan = 0.
     */
    public function getNilai(int $studentId, int $subjectId): int
    {
        $assessmentIds = $this->assessments()
            ->where('subject_id', $subjectId)
            ->pluck('id');

        if ($assessmentIds->isEmpty()) {
            return 0;
        }

        $sessions = AssessmentStudentSession::whereIn('assessment_id', $assessmentIds)
            ->where('user_id', $studentId)
            ->whereNotNull('submitted_at')
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $total = $sessions->sum(function ($s) {
            if ($s->max_score > 0) {
                return round($s->total_score / $s->max_score * 100);
            }
            return min(100, max(0, (int) round((float) $s->total_score)));
        });

        return (int) round($total / $assessmentIds->count());
    }

    public function render()
    {
        return view('livewire.teacher-assessment.rapor-asts')
            ->layout('components.layouts.app');
    }
}
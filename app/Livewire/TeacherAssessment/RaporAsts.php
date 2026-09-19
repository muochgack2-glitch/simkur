<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RaporAsts extends Component
{
    public ?SchoolClass $myClass = null;
    public ?Semester $semester = null;

    public function mount(): void
    {
        // Deteksi kelas wali kelas yang login
        $this->myClass = SchoolClass::where('homeroom_teacher_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$this->myClass) {
            abort(403, 'Anda bukan wali kelas aktif.');
        }

        // Semester aktif dari tahun ajaran aktif
        $academicYear = AcademicYear::where('is_active', true)->first();
        $this->semester = $academicYear
            ? Semester::where('academic_year_id', $academicYear->id)->orderByDesc('id')->first()
            : Semester::orderByDesc('id')->first();
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
     * Subjects dengan kolom: subject_id, subject_name
     * Satu baris per mapel unik yang punya asesmen ASTS untuk kelas ini.
     */
    #[Computed]
    public function subjects()
    {
        return $this->assessments()
            ->groupBy('subject_id')
            ->map(fn($group) => $group->first()->subject)
            ->filter()
            ->sortBy('name')
            ->values();
    }

    /**
     * Semua asesmen ASTS semester aktif yang berlaku untuk kelas ini.
     */
    #[Computed]
    public function assessments()
    {
        $grade = $this->myClass->grade;
        $major = $this->myClass->major;

        $academicYearId = $this->semester?->academic_year_id
            ?? AcademicYear::where('is_active', true)->value('id');

        return Assessment::with(['subject', 'assessmentLabel'])
            ->whereHas('assessmentLabel', fn($q) => $q->where('name', 'like', '%ASTS%'))
            // Filter by academic_year (lebih reliable dari semester_id)
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            // Tidak filter is_published — tampilkan semua asesmen ASTS meski belum published
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
            return 0;
        });

        return (int) round($total / $assessmentIds->count());
    }

    public function render()
    {
        return view('livewire.teacher-assessment.rapor-asts')
            ->layout('components.layouts.app');
    }
}
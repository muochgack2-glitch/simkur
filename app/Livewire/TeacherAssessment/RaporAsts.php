<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\TeachingSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class RaporAsts extends Component
{
    use WithFileUploads;

    public ?SchoolClass $myClass = null;
    public ?Semester $semester = null;
    public ?AcademicYear $academicYear = null;
    public $kopSuratFile = null;

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
    public function kopSuratUrl(): ?string
    {
        $path = Setting::getValue('kop_surat_rapor', '');
        if (!$path) return null;
        if (!Storage::disk('public')->exists($path)) return null;
        return Storage::disk('public')->url($path);
    }

    public function uploadKopSurat(): void
    {
        $this->validate([
            'kopSuratFile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'kopSuratFile.required' => 'Pilih file gambar terlebih dahulu.',
            'kopSuratFile.image'    => 'File harus berupa gambar (JPG/PNG).',
            'kopSuratFile.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $oldPath = Setting::getValue('kop_surat_rapor', '');
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $ext  = $this->kopSuratFile->getClientOriginalExtension();
        $path = $this->kopSuratFile->storeAs('kop-surat', 'kop_rapor.' . $ext, 'public');

        Setting::setValue('kop_surat_rapor', $path, 'string', 'rapor');

        $this->kopSuratFile = null;
        unset($this->kopSuratUrl);
        session()->flash('kop_success', 'Kop surat berhasil diupload.');
    }

    public function deleteKopSurat(): void
    {
        $path = Setting::getValue('kop_surat_rapor', '');
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        Setting::setValue('kop_surat_rapor', '', 'string', 'rapor');
        unset($this->kopSuratUrl);
        session()->flash('kop_success', 'Kop surat dihapus.');
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

    public function getNilai(int $studentId, int $subjectId): int
    {
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
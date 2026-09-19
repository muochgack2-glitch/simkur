<?php

namespace App\Livewire\Assessment;

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

class AdminRaporAsts extends Component
{
    use WithFileUploads;

    public ?int $selectedClassId = null;
    public $kopSuratFile = null;
    public string $tanggalCetak = '';

    public function mount(): void
    {
        // Default ke kelas pertama aktif
        $first = SchoolClass::where('is_active', true)->orderBy('name')->first();
        $this->selectedClassId = $first?->id;
        $this->tanggalCetak = Setting::getValue('rapor_tanggal_cetak', '');
    }

    #[Computed]
    public function classes()
    {
        return SchoolClass::where('is_active', true)->orderBy('grade')->orderBy('name')->get()->unique('id')->values();
    }

    #[Computed]
    public function selectedClass()
    {
        return $this->selectedClassId
            ? SchoolClass::find($this->selectedClassId)
            : null;
    }

    #[Computed]
    public function academicYear()
    {
        return AcademicYear::where('is_active', true)->first();
    }

    #[Computed]
    public function semester()
    {
        $ay = $this->academicYear();
        $semType = (now()->month >= 7) ? 'ganjil' : 'genap';
        return $ay
            ? Semester::with('academicYear')
                ->where('academic_year_id', $ay->id)
                ->where('type', $semType)
                ->first()
              ?? Semester::with('academicYear')->where('academic_year_id', $ay->id)->orderByDesc('id')->first()
            : Semester::with('academicYear')->where('type', $semType)->orderByDesc('id')->first()
              ?? Semester::with('academicYear')->orderByDesc('id')->first();
    }



    public function saveTanggalCetak(): void
    {
        Setting::setValue('rapor_tanggal_cetak', trim($this->tanggalCetak), 'string', 'rapor');
        session()->flash('kop_success', 'Tanggal cetak rapor disimpan.');
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
        $filename = 'kop_rapor_' . now()->timestamp . '.' . $ext;
        $path = $this->kopSuratFile->storeAs('kop-surat', $filename, 'public');
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
        if (!$this->selectedClassId) return collect();
        return User::where('class_id', $this->selectedClassId)
            ->where('role', 'siswa')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function subjects()
    {
        if (!$this->selectedClassId) return collect();
        $ay = $this->academicYear();
        return TeachingSchedule::with('subject')
            ->where('class_id', $this->selectedClassId)
            ->where('is_active', true)
            ->when($ay?->id, fn($q) => $q->where('academic_year_id', $ay->id))
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
        $class = $this->selectedClass();
        if (!$class) return collect();

        $grade = $class->grade;
        $major = $class->major;
        $sem   = $this->semester();

        return Assessment::with(['subject', 'assessmentLabel'])
            ->whereHas('assessmentLabel', fn($q) => $q->where('name', 'like', '%ASTS%'))
            ->when($sem?->id, fn($q) => $q->where('semester_id', $sem->id))
            ->where(function ($q) use ($grade) {
                $q->whereJsonContains('target_grades', $grade)->orWhereNull('target_grades');
            })
            ->where(function ($q) use ($major) {
                $q->whereJsonContains('target_majors', $major)->orWhereNull('target_majors');
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
        return view('livewire.assessment.admin-rapor-asts')
            ->layout('components.layouts.app');
    }
}
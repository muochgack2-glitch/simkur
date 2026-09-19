<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\AssessmentLabel;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;

class CreateEdit extends Component
{
    public ?int $assessmentId = null;

    // Form fields
    public ?int $assessmentLabelId = null;
    public string $title = ''; // Diisi otomatis dari label
    public string $description = '';
    public string $startDate = '';
    public string $startTime = '07:00';
    public string $endDate = '';
    public string $endTime = '23:59';
    public bool $allowRetry = false;
    public bool $shuffleQuestions = true;
    public bool $shuffleOptions = true;
    public bool $isPublished = false;
    public ?int $subjectId = null;
    public ?int $teacherId = null; // guru pemilik soal (admin pilih)

    // Target siswa
    public array $targetGrades  = []; // ['X','XI','XII']
    public array $targetMajors  = []; // ['MPLB','AKL','BUSANA']

    public array $gradeOptions = ['X', 'XI', 'XII'];
    public array $majorOptions = [
        'MPLB'   => 'MPLB - Manajemen Perkantoran',
        'AKL'    => 'AKL - Akuntansi',
        'BUSANA' => 'BUSANA - Tata Busana',
    ];

    public function mount(?int $id = null): void
    {
        $this->assessmentId = $id;

        // Default: pemilik soal adalah diri sendiri
        $this->teacherId = auth()->id();

        if ($id) {
            $isAdmin      = auth()->user()->role === 'admin';
            $isWaka       = auth()->user()->role === 'waka_kurikulum';
            $canEditAll   = $isAdmin || $isWaka;

            $assessment = Assessment::where('id', $id)
                ->when(!$canEditAll, fn($q) => $q->where('teacher_id', auth()->id()))
                ->firstOrFail();

            $this->assessmentLabelId = $assessment->assessment_label_id;
            $this->title             = $assessment->title;
            $this->description       = $assessment->description ?? '';
            $this->startDate         = $assessment->start_date->toDateString();
            $this->startTime         = substr($assessment->start_time ?? '07:00', 0, 5);
            $this->endDate           = $assessment->end_date->toDateString();
            $this->endTime           = substr($assessment->end_time ?? '23:59', 0, 5);
            $this->allowRetry        = $assessment->allow_retry ?? false;
            $this->shuffleQuestions  = $assessment->shuffle_questions ?? true;
            $this->shuffleOptions    = $assessment->shuffle_options ?? true;
            $this->isPublished       = $assessment->is_published ?? false;
            $this->targetGrades      = $assessment->target_grades ?? [];
            $this->targetMajors      = $assessment->target_majors ?? [];
            $this->subjectId         = $assessment->subject_id;
            $this->teacherId         = $assessment->teacher_id ?? auth()->id();
        }
    }

    protected function rules(): array
    {
        $isAdmin = auth()->user()->role === 'admin';
        return [
            'assessmentLabelId' => 'required|integer|exists:assessment_labels,id',
            'title'             => 'nullable|string|max:255',
            'description'       => 'nullable|string|max:2000',
            'startDate'         => 'required|date',
            'startTime'         => 'required|date_format:H:i',
            'endDate'           => 'required|date|after_or_equal:startDate',
            'endTime'           => 'required|date_format:H:i',
            'allowRetry'        => 'boolean',
            'shuffleQuestions'  => 'boolean',
            'shuffleOptions'    => 'boolean',
            'isPublished'       => 'boolean',
            'targetGrades'      => 'array',
            'targetMajors'      => 'array',
            'subjectId'         => 'nullable|integer|exists:subjects,id',
            'teacherId'         => $isAdmin ? 'required|integer|exists:users,id' : 'nullable',
        ];
    }

    protected function messages(): array
    {
        return [
            'assessmentLabelId.required' => 'Jenis asesmen wajib dipilih.',
            'assessmentLabelId.exists'   => 'Jenis asesmen tidak valid.',
            'title.required'             => 'Judul asesmen wajib diisi.',
            'startDate.required'         => 'Tanggal mulai wajib diisi.',
            'endDate.required'           => 'Tanggal berakhir wajib diisi.',
            'endDate.after_or_equal'     => 'Tanggal berakhir tidak boleh sebelum tanggal mulai.',
            'startTime.date_format'      => 'Format jam mulai tidak valid.',
            'endTime.date_format'        => 'Format jam berakhir tidak valid.',
            'teacherId.required'         => 'Guru pemilik soal wajib dipilih.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $academicYear = AcademicYear::where('is_active', true)->first();
        // Semester tidak punya kolom is_active — ambil dari tahun ajaran aktif
        $semester = $academicYear
            ? Semester::where('academic_year_id', $academicYear->id)->orderByDesc('id')->first()
            : Semester::orderByDesc('id')->first();

        // Kosong = berlaku untuk semua
        $grades = !empty($this->targetGrades) ? $this->targetGrades : null;
        $majors = !empty($this->targetMajors) ? $this->targetMajors : null;

        // Title diisi otomatis dari nama label yang dipilih
        $label = AssessmentLabel::find($this->assessmentLabelId);
        $this->title = $label?->name ?? $this->title;

        // Tentukan guru pemilik soal
        $isAdmin   = auth()->user()->role === 'admin';
        $teacherId = $isAdmin ? ($this->teacherId ?? auth()->id()) : auth()->id();

        $data = [
            'assessment_label_id' => $this->assessmentLabelId,
            'title'               => $this->title,
            'description'         => $this->description ?: null,
            'assessment_type'     => 'quiz',
            'academic_year_id'    => $academicYear?->id ?? 1,
            'semester_id'         => $semester?->id ?? 1,
            'start_date'          => $this->startDate,
            'start_time'          => $this->startTime . ':00',
            'end_date'            => $this->endDate,
            'end_time'            => $this->endTime . ':59',
            'allow_retry'         => $this->allowRetry,
            'shuffle_questions'   => $this->shuffleQuestions,
            'shuffle_options'     => $this->shuffleOptions,
            'is_active'           => true,
            'is_published'        => $this->isPublished,
            'created_by'          => auth()->id(),
            'teacher_id'          => $teacherId,
            'target_grades'       => $grades,
            'target_majors'       => $majors,
            'subject_id'          => $this->subjectId ?: null,
        ];

        if ($this->assessmentId) {
            $isWaka     = auth()->user()->role === 'waka_kurikulum';
            $canEditAll = $isAdmin || $isWaka;

            $assessment = Assessment::where('id', $this->assessmentId)
                ->when(!$canEditAll, fn($q) => $q->where('teacher_id', auth()->id()))
                ->firstOrFail();
            $assessment->update($data);
            session()->flash('success', 'Asesmen berhasil diperbarui.');
            $this->redirect(route('teacher.assessment.questions', $assessment->id), navigate: true);
        } else {
            $assessment = Assessment::create($data);
            session()->flash('success', 'Asesmen berhasil dibuat. Silakan tambahkan soal.');
            $this->redirect(route('teacher.assessment.questions', $assessment->id), navigate: true);
        }
    }

    public function getAssessmentLabelsProperty()
    {
        return AssessmentLabel::where('is_active', true)->orderBy('name')->get();
    }

    // Reset subjectId ketika admin ganti pilihan guru
    public function updatedTeacherId(): void
    {
        $this->subjectId = null;
    }

    public function getSubjectsProperty()
    {
        // Tentukan guru target: admin pakai teacherId yang dipilih, guru pakai diri sendiri
        $isAdmin = auth()->user()->role === 'admin';
        $targetTeacherId = ($isAdmin && $this->teacherId) ? $this->teacherId : auth()->id();

        // Filter mapel dari jadwal mengajar guru yang dipilih
        $subjects = Subject::whereHas('teachingSchedules', function ($q) use ($targetTeacherId) {
                $q->where('teacher_id', $targetTeacherId)->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Fallback: jika guru tidak punya jadwal aktif, tampilkan semua mapel aktif
        return $subjects->isNotEmpty() ? $subjects : Subject::where('is_active', true)->orderBy('name')->get();
    }

    public function getTeachersProperty()
    {
        // Hanya untuk admin — daftar semua guru aktif
        if (auth()->user()->role !== 'admin') {
            return collect();
        }
        return User::where('role', 'guru')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.teacher-assessment.create-edit')
            ->layout('components.layouts.app');
    }
}
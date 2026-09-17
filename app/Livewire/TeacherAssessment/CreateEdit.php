<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AcademicYear;
use App\Models\Semester;
use Livewire\Component;

class CreateEdit extends Component
{
    public ?int $assessmentId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $startDate = '';
    public string $startTime = '07:00';
    public string $endDate = '';
    public string $endTime = '23:59';
    public bool $allowRetry = false;
    public bool $shuffleQuestions = true;
    public bool $shuffleOptions = true;
    public bool $isPublished = false;

    public function mount(?int $id = null): void
    {
        $this->assessmentId = $id;

        if ($id) {
            $assessment = Assessment::where('id', $id)
                ->where('created_by', auth()->id())
                ->firstOrFail();

            $this->title            = $assessment->title;
            $this->description      = $assessment->description ?? '';
            $this->startDate        = $assessment->start_date->toDateString();
            $this->startTime        = $assessment->start_time ?? '07:00';
            $this->endDate          = $assessment->end_date->toDateString();
            $this->endTime          = $assessment->end_time ?? '23:59';
            $this->allowRetry       = $assessment->allow_retry ?? false;
            $this->shuffleQuestions = $assessment->shuffle_questions ?? true;
            $this->shuffleOptions   = $assessment->shuffle_options ?? true;
            $this->isPublished      = $assessment->is_published ?? false;
        }
    }

    protected function rules(): array
    {
        return [
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'startDate'       => 'required|date',
            'startTime'       => 'required|date_format:H:i',
            'endDate'         => 'required|date|after_or_equal:startDate',
            'endTime'         => 'required|date_format:H:i',
            'allowRetry'      => 'boolean',
            'shuffleQuestions'=> 'boolean',
            'shuffleOptions'  => 'boolean',
            'isPublished'     => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'       => 'Judul asesmen wajib diisi.',
            'startDate.required'   => 'Tanggal mulai wajib diisi.',
            'endDate.required'     => 'Tanggal berakhir wajib diisi.',
            'endDate.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai.',
            'startTime.date_format'=> 'Format jam mulai tidak valid.',
            'endTime.date_format'  => 'Format jam berakhir tidak valid.',
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

        $data = [
            'title'            => $this->title,
            'description'      => $this->description ?: null,
            'assessment_type'  => 'quiz',
            'academic_year_id' => $academicYear?->id ?? 1,
            'semester_id'      => $semester?->id ?? 1,
            'start_date'       => $this->startDate,
            'start_time'       => $this->startTime . ':00',
            'end_date'         => $this->endDate,
            'end_time'         => $this->endTime . ':59',
            'allow_retry'      => $this->allowRetry,
            'shuffle_questions'=> $this->shuffleQuestions,
            'shuffle_options'  => $this->shuffleOptions,
            'is_active'        => true,
            'is_published'     => $this->isPublished,
            'created_by'       => auth()->id(),
        ];

        if ($this->assessmentId) {
            $assessment = Assessment::where('id', $this->assessmentId)
                ->where('created_by', auth()->id())
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

    public function render()
    {
        return view('livewire.teacher-assessment.create-edit')
            ->layout('components.layouts.app');
    }
}

<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\PklAssignment;
use App\Models\PklSubmission;
use Livewire\WithFileUploads;

class StudentSubmission extends BaseComponent
{
    use WithFileUploads;

    public PklAssignment $assignment;
    public ?PklSubmission $submission = null;
    public $content = '';
    public $file;

    public function mount(PklAssignment $assignment)
    {
        $user = auth()->user();
        $course = $assignment->course;

        if (!in_array((int) $user->class_id, $course->target_classes ?? [])) {
            abort(403);
        }

        $this->assignment = $assignment->load('course.subject');
        $this->submission = PklSubmission::where('pkl_assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->first();

        if ($this->submission) {
            $this->content = $this->submission->content ?? '';
        }
    }

    public function submit()
    {
        // Block if period is future
        if ($this->assignment->course && $this->assignment->course->isPeriodLocked()) {
            session()->flash('error', 'Periode belum dimulai!');
            return;
        }
        $this->validate([
            'content' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240',
        ]);

        if (empty($this->content) && !$this->file) {
            session()->flash('error', 'Isi jawaban atau upload file');
            return;
        }

        $filePath = null;
        $fileName = null;
        if ($this->file) {
            $fileName = $this->file->getClientOriginalName();
            $filePath = $this->file->store('pkl-submissions', 'public');
        }

        $isLate = now()->gt($this->assignment->deadline);

        if (!$this->assignment->allow_late && $isLate) {
            session()->flash('error', 'Batas waktu sudah lewat, tugas tidak bisa dikumpulkan');
            return;
        }

        PklSubmission::updateOrCreate(
            [
                'pkl_assignment_id' => $this->assignment->id,
                'student_id' => auth()->id(),
            ],
            [
                'content' => $this->content,
                'file_path' => $filePath ?? $this->submission?->file_path,
                'file_name' => $fileName ?? $this->submission?->file_name,
                'submitted_at' => now(),
                'is_late'               => $isLate,
                'revision_requested'    => false,
                'revision_note'         => null,
                'revision_requested_at' => null,
            ]
        );

        session()->flash('success', 'Tugas berhasil dikumpulkan!');
        return redirect()->route('pkl-learning.student.course', $this->assignment->course);
    }

    public function render()
    {
        return view('livewire.pkl-learning.student-submission');
    }
}


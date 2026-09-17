<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use Livewire\Component;

class Preview extends Component
{
    public Assessment $assessment;

    public function mount(int $id): void
    {
        $assessment = Assessment::with(['questions.options', 'creator'])
            ->where('id', $id)
            ->where('assessment_type', 'quiz')
            ->where('is_published', true)
            ->firstOrFail();

        // Pastikan siswa ini memang berhak lihat asesmen ini
        // (bisa cek target_class_ids jika sudah diimplementasi)
        $this->assessment = $assessment;
    }

    public function render()
    {
        return view('livewire.teacher-assessment.preview', [
            'questions' => $this->assessment->questions()->with('options')->orderBy('order_number')->get(),
        ])->layout('components.layouts.app');
    }
}

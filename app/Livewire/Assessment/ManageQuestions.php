<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use Livewire\Component;

class ManageQuestions extends Component
{
    public Assessment $assessment;
    public $showAddModal = false;
    public $showEditModal = false;
    public $editingQuestion = null;

    // Form fields - common
    public $question_text = '';
    public $question_type = 'multiple_choice';
    public $order_number = 1;
    public $weight = 1;
    
    // VARK-specific fields
    public $learning_style_indicator = 'visual';
    public $option_0_text = '';
    public $option_0_score = 3;
    public $option_1_text = '';
    public $option_1_score = 0;
    public $option_2_text = '';
    public $option_2_score = 0;
    public $option_3_text = '';
    public $option_3_score = 0;
    
    // Diagnostic-specific fields
    public $aspect = 'kesiapan';
    public $aspect_weight = 30;

    public function mount($id)
    {
        try {
            $this->assessment = Assessment::with(['questions' => function($query) {
                $query->orderBy('order_number');
            }, 'questions.options' => function($query) {
                $query->orderBy('order_number');
            }])->findOrFail($id);
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading assessment: ' . $e->getMessage());
            return redirect()->route('assessment.index');
        }
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->order_number = $this->assessment->questions()->count() + 1;
        $this->showAddModal = true;
        
        // Force re-render
        $this->dispatch('modal-opened');
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetForm();
    }

    public function openEditModal($questionId)
    {
        $question = AssessmentQuestion::with('options')->findOrFail($questionId);
        $this->editingQuestion = $question;
        
        $this->question_text = $question->question_text;
        $this->question_type = $question->question_type;
        $this->order_number = $question->order_number;
        $this->weight = $question->weight;
        
        if ($this->assessment->isVark()) {
            // Load VARK-specific data
            $this->learning_style_indicator = $question->learning_style_indicator;
            
            $options = $question->options;
            if (isset($options[0])) {
                $this->option_0_text = $options[0]->option_text;
                $this->option_0_score = $options[0]->score_value;
            }
            if (isset($options[1])) {
                $this->option_1_text = $options[1]->option_text;
                $this->option_1_score = $options[1]->score_value;
            }
            if (isset($options[2])) {
                $this->option_2_text = $options[2]->option_text;
                $this->option_2_score = $options[2]->score_value;
            }
            if (isset($options[3])) {
                $this->option_3_text = $options[3]->option_text;
                $this->option_3_score = $options[3]->score_value;
            }
        } else {
            // Load Diagnostic-specific data
            $this->aspect = $question->aspect;
            $this->aspect_weight = $question->aspect_weight;
        }
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function saveQuestion()
    {
        if ($this->assessment->isVark()) {
            // VARK validation
            $this->validate([
                'question_text' => 'required|string',
                'learning_style_indicator' => 'required|in:visual,auditory,kinesthetic,reading_writing',
                'order_number' => 'required|integer|min:1',
                'option_0_text' => 'required|string',
                'option_1_text' => 'required|string',
                'option_2_text' => 'required|string',
                'option_3_text' => 'required|string',
                'option_0_score' => 'required|integer',
                'option_1_score' => 'required|integer',
                'option_2_score' => 'required|integer',
                'option_3_score' => 'required|integer',
            ], [
                'question_text.required' => 'Pertanyaan harus diisi',
                'option_0_text.required' => 'Pilihan A harus diisi',
                'option_1_text.required' => 'Pilihan B harus diisi',
                'option_2_text.required' => 'Pilihan C harus diisi',
                'option_3_text.required' => 'Pilihan D harus diisi',
            ]);

            $question = AssessmentQuestion::create([
                'assessment_id' => $this->assessment->id,
                'question_text' => $this->question_text,
                'question_type' => 'multiple_choice',
                'learning_style_indicator' => $this->learning_style_indicator,
                'order_number' => $this->order_number,
                'weight' => $this->weight,
            ]);

            // Create 4 options for VARK
            AssessmentQuestionOption::create([
                'assessment_question_id' => $question->id,
                'option_text' => $this->option_0_text,
                'score_value' => $this->option_0_score,
                'order_number' => 1,
            ]);
            AssessmentQuestionOption::create([
                'assessment_question_id' => $question->id,
                'option_text' => $this->option_1_text,
                'score_value' => $this->option_1_score,
                'order_number' => 2,
            ]);
            AssessmentQuestionOption::create([
                'assessment_question_id' => $question->id,
                'option_text' => $this->option_2_text,
                'score_value' => $this->option_2_score,
                'order_number' => 3,
            ]);
            AssessmentQuestionOption::create([
                'assessment_question_id' => $question->id,
                'option_text' => $this->option_3_text,
                'score_value' => $this->option_3_score,
                'order_number' => 4,
            ]);
        } else {
            // Diagnostic validation
            $this->validate([
                'question_text' => 'required|string',
                'aspect' => 'required|in:kesiapan,motivasi,kemandirian,kolaborasi,preferensi,dunia_kerja',
                'aspect_weight' => 'required|integer|min:0|max:100',
                'order_number' => 'required|integer|min:1',
            ], [
                'question_text.required' => 'Pertanyaan harus diisi',
                'aspect.required' => 'Aspek harus dipilih',
            ]);

            $question = AssessmentQuestion::create([
                'assessment_id' => $this->assessment->id,
                'question_text' => $this->question_text,
                'question_type' => 'likert',
                'aspect' => $this->aspect,
                'aspect_weight' => $this->aspect_weight,
                'order_number' => $this->order_number,
                'weight' => $this->weight,
            ]);

            // Create 5 Likert scale options automatically
            $likertOptions = [
                ['text' => '1 - Sangat Tidak Sesuai', 'score' => 1],
                ['text' => '2 - Tidak Sesuai', 'score' => 2],
                ['text' => '3 - Cukup Sesuai', 'score' => 3],
                ['text' => '4 - Sesuai', 'score' => 4],
                ['text' => '5 - Sangat Sesuai', 'score' => 5],
            ];

            foreach ($likertOptions as $index => $option) {
                AssessmentQuestionOption::create([
                    'assessment_question_id' => $question->id,
                    'option_text' => $option['text'],
                    'score_value' => $option['score'],
                    'order_number' => $index + 1,
                ]);
            }
        }

        session()->flash('success', 'Pertanyaan berhasil ditambahkan!');
        $this->closeAddModal();
        $this->assessment->load(['questions' => function($query) {
            $query->orderBy('order_number');
        }, 'questions.options' => function($query) {
            $query->orderBy('order_number');
        }]);
    }

    public function updateQuestion()
    {
        if ($this->assessment->isVark()) {
            // VARK validation
            $this->validate([
                'question_text' => 'required|string',
                'learning_style_indicator' => 'required|in:visual,auditory,kinesthetic,reading_writing',
                'order_number' => 'required|integer|min:1',
                'option_0_text' => 'required|string',
                'option_1_text' => 'required|string',
                'option_2_text' => 'required|string',
                'option_3_text' => 'required|string',
                'option_0_score' => 'required|integer',
                'option_1_score' => 'required|integer',
                'option_2_score' => 'required|integer',
                'option_3_score' => 'required|integer',
            ]);

            $this->editingQuestion->update([
                'question_text' => $this->question_text,
                'question_type' => 'multiple_choice',
                'learning_style_indicator' => $this->learning_style_indicator,
                'order_number' => $this->order_number,
                'weight' => $this->weight,
            ]);

            // Update options
            $options = $this->editingQuestion->options;
            if (isset($options[0])) {
                $options[0]->update([
                    'option_text' => $this->option_0_text,
                    'score_value' => $this->option_0_score,
                ]);
            }
            if (isset($options[1])) {
                $options[1]->update([
                    'option_text' => $this->option_1_text,
                    'score_value' => $this->option_1_score,
                ]);
            }
            if (isset($options[2])) {
                $options[2]->update([
                    'option_text' => $this->option_2_text,
                    'score_value' => $this->option_2_score,
                ]);
            }
            if (isset($options[3])) {
                $options[3]->update([
                    'option_text' => $this->option_3_text,
                    'score_value' => $this->option_3_score,
                ]);
            }
        } else {
            // Diagnostic validation
            $this->validate([
                'question_text' => 'required|string',
                'aspect' => 'required|in:kesiapan,motivasi,kemandirian,kolaborasi,preferensi,dunia_kerja',
                'aspect_weight' => 'required|integer|min:0|max:100',
                'order_number' => 'required|integer|min:1',
            ]);

            $this->editingQuestion->update([
                'question_text' => $this->question_text,
                'question_type' => 'likert',
                'aspect' => $this->aspect,
                'aspect_weight' => $this->aspect_weight,
                'order_number' => $this->order_number,
                'weight' => $this->weight,
            ]);

            // Likert options are fixed, no need to update
        }

        session()->flash('success', 'Pertanyaan berhasil diperbarui!');
        $this->closeEditModal();
        $this->assessment->load(['questions' => function($query) {
            $query->orderBy('order_number');
        }, 'questions.options' => function($query) {
            $query->orderBy('order_number');
        }]);
    }

    public function deleteQuestion($questionId)
    {
        $question = AssessmentQuestion::findOrFail($questionId);
        
        // Check if has responses
        if ($question->responses()->exists()) {
            session()->flash('error', 'Tidak dapat menghapus pertanyaan yang sudah dijawab siswa.');
            return;
        }

        $question->delete();
        session()->flash('success', 'Pertanyaan berhasil dihapus!');
        $this->assessment->load(['questions' => function($query) {
            $query->orderBy('order_number');
        }, 'questions.options' => function($query) {
            $query->orderBy('order_number');
        }]);
    }

    public function duplicateQuestion($questionId)
    {
        $original = AssessmentQuestion::with('options')->findOrFail($questionId);
        
        $newQuestion = AssessmentQuestion::create([
            'assessment_id' => $this->assessment->id,
            'question_text' => $original->question_text . ' (Copy)',
            'question_type' => $original->question_type,
            'learning_style_indicator' => $original->learning_style_indicator,
            'order_number' => $this->assessment->questions()->count() + 1,
            'weight' => $original->weight,
        ]);

        foreach ($original->options as $option) {
            AssessmentQuestionOption::create([
                'assessment_question_id' => $newQuestion->id,
                'option_text' => $option->option_text,
                'score_value' => $option->score_value,
                'order_number' => $option->order_number,
            ]);
        }

        session()->flash('success', 'Pertanyaan berhasil diduplikasi!');
        $this->assessment->load(['questions' => function($query) {
            $query->orderBy('order_number');
        }, 'questions.options' => function($query) {
            $query->orderBy('order_number');
        }]);
    }

    private function resetForm()
    {
        $this->question_text = '';
        $this->question_type = 'multiple_choice';
        $this->learning_style_indicator = 'visual';
        $this->weight = 1;
        $this->editingQuestion = null;
        
        // Reset VARK options
        $this->option_0_text = '';
        $this->option_0_score = 3;
        $this->option_1_text = '';
        $this->option_1_score = 0;
        $this->option_2_text = '';
        $this->option_2_score = 0;
        $this->option_3_text = '';
        $this->option_3_score = 0;
        
        // Reset Diagnostic fields
        $this->aspect = 'kesiapan';
        $this->aspect_weight = 30;
    }

    public function render()
    {
        return view('livewire.assessment.manage-questions')
            ->layout('components.layouts.app');
    }
}

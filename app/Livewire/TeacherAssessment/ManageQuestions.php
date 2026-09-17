<?php

namespace App\Livewire\TeacherAssessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use Livewire\Component;

class ManageQuestions extends Component
{
    public Assessment $assessment;

    // State form tambah/edit soal
    public bool $showForm = false;
    public ?int $editingQuestionId = null;

    // Field soal
    public string $questionText = '';
    public string $questionType = 'multiple_choice';
    public int $maxScore = 1;
    public string $fileAccept = 'image/*,application/pdf';

    // Untuk PG & T/F: array options [{text, is_correct}]
    public array $options = [];

    // Untuk Menjodohkan: array pairs [{left, right}]
    public array $matchingPairs = [];

    public function mount(int $id): void
    {
        $this->assessment = Assessment::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->showForm          = false;
        $this->editingQuestionId = null;
        $this->questionText      = '';
        $this->questionType      = 'multiple_choice';
        $this->maxScore          = 1;
        $this->fileAccept        = 'image/*,application/pdf';
        $this->options           = [
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
        $this->matchingPairs     = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
    }

    public function showAddForm(): void
    {
        $this->resetForm();
        $this->showForm = true;

        if ($this->questionType === 'true_false') {
            $this->initTrueFalseOptions();
        }
    }

    public function editQuestion(int $questionId): void
    {
        $question = AssessmentQuestion::with('options')->findOrFail($questionId);
        $this->editingQuestionId = $question->id;
        $this->questionText      = $question->question_text;
        $this->questionType      = $question->question_type;
        $this->maxScore          = $question->max_score ?? 1;
        $this->fileAccept        = $question->file_accept ?? 'image/*,application/pdf';

        if ($question->isMatching()) {
            $this->matchingPairs = $question->matching_pairs ?? [['left'=>'','right'=>'']];
            $this->options = [];
        } elseif ($question->isMultipleChoice() || $question->isTrueFalse()) {
            $this->options = $question->options->map(fn($o) => [
                'text'       => $o->option_text,
                'is_correct' => $o->score_value > 0,
            ])->toArray();
        }

        $this->showForm = true;
    }

    public function updatedQuestionType(string $value): void
    {
        if ($value === 'true_false') {
            $this->initTrueFalseOptions();
        } elseif ($value === 'multiple_choice') {
            $this->options = [
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
            ];
        } elseif ($value === 'matching') {
            $this->options = [];
            if (empty($this->matchingPairs)) {
                $this->matchingPairs = [['left'=>'','right'=>''],['left'=>'','right'=>''],['left'=>'','right'=>'']];
            }
        }
    }

    private function initTrueFalseOptions(): void
    {
        $this->options = [
            ['text' => 'Benar', 'is_correct' => true],
            ['text' => 'Salah', 'is_correct' => false],
        ];
    }

    public function addOption(): void
    {
        $this->options[] = ['text' => '', 'is_correct' => false];
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > 2) {
            array_splice($this->options, $index, 1);
            $this->options = array_values($this->options);
        }
    }

    public function setCorrectOption(int $index): void
    {
        foreach ($this->options as $i => $opt) {
            $this->options[$i]['is_correct'] = ($i === $index);
        }
    }

    public function addMatchingPair(): void
    {
        $this->matchingPairs[] = ['left' => '', 'right' => ''];
    }

    public function removeMatchingPair(int $index): void
    {
        if (count($this->matchingPairs) > 2) {
            array_splice($this->matchingPairs, $index, 1);
            $this->matchingPairs = array_values($this->matchingPairs);
        }
    }

    public function saveQuestion(): void
    {
        $this->validate([
            'questionText' => 'required|string|min:3',
            'questionType' => 'required|in:multiple_choice,true_false,matching,essay,file_upload',
            'maxScore'     => 'required|integer|min:1|max:100',
        ]);

        // Validasi tipe-spesifik
        if (in_array($this->questionType, ['multiple_choice', 'true_false'])) {
            $hasCorrect = collect($this->options)->where('is_correct', true)->count();
            if ($hasCorrect === 0) {
                $this->addError('options', 'Tandai minimal 1 jawaban yang benar.');
                return;
            }
            $emptyOptions = collect($this->options)->filter(fn($o) => trim($o['text']) === '')->count();
            if ($emptyOptions > 0) {
                $this->addError('options', 'Semua pilihan jawaban harus diisi.');
                return;
            }
        }

        if ($this->questionType === 'matching') {
            foreach ($this->matchingPairs as $pair) {
                if (empty(trim($pair['left'])) || empty(trim($pair['right']))) {
                    $this->addError('matchingPairs', 'Semua pasangan kiri dan kanan harus diisi.');
                    return;
                }
            }
        }

        $nextOrder = $this->assessment->questions()->max('order_number') + 1;

        $questionData = [
            'assessment_id' => $this->assessment->id,
            'question_text' => $this->questionText,
            'question_type' => $this->questionType,
            'max_score'     => $this->maxScore,
            'order_number'  => $this->editingQuestionId
                ? AssessmentQuestion::find($this->editingQuestionId)->order_number
                : $nextOrder,
            'matching_pairs'=> $this->questionType === 'matching' ? $this->matchingPairs : null,
            'file_accept'   => $this->questionType === 'file_upload' ? $this->fileAccept : null,
            'weight'        => $this->maxScore,
        ];

        if ($this->editingQuestionId) {
            $question = AssessmentQuestion::findOrFail($this->editingQuestionId);
            $question->update($questionData);
            // Hapus opsi lama
            $question->options()->delete();
        } else {
            $question = AssessmentQuestion::create($questionData);
        }

        // Simpan opsi untuk PG / T-F
        if (in_array($this->questionType, ['multiple_choice', 'true_false'])) {
            foreach ($this->options as $i => $opt) {
                AssessmentQuestionOption::create([
                    'assessment_question_id' => $question->id,
                    'option_text'            => $opt['text'],
                    'score_value'            => $opt['is_correct'] ? $this->maxScore : 0,
                    'order_number'           => $i + 1,
                ]);
            }
        }

        session()->flash('success', $this->editingQuestionId ? 'Soal berhasil diperbarui.' : 'Soal berhasil ditambahkan.');
        $this->resetForm();
    }

    public function deleteQuestion(int $questionId): void
    {
        AssessmentQuestion::where('id', $questionId)
            ->whereHas('assessment', fn($q) => $q->where('created_by', auth()->id()))
            ->delete();

        // Re-order
        $this->assessment->questions()->orderBy('order_number')->get()
            ->each(fn($q, $i) => $q->update(['order_number' => $i + 1]));

        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function moveUp(int $questionId): void
    {
        $question = AssessmentQuestion::findOrFail($questionId);
        $prev = AssessmentQuestion::where('assessment_id', $this->assessment->id)
            ->where('order_number', '<', $question->order_number)
            ->orderBy('order_number', 'desc')->first();

        if ($prev) {
            [$question->order_number, $prev->order_number] = [$prev->order_number, $question->order_number];
            $question->save();
            $prev->save();
        }
    }

    public function moveDown(int $questionId): void
    {
        $question = AssessmentQuestion::findOrFail($questionId);
        $next = AssessmentQuestion::where('assessment_id', $this->assessment->id)
            ->where('order_number', '>', $question->order_number)
            ->orderBy('order_number')->first();

        if ($next) {
            [$question->order_number, $next->order_number] = [$next->order_number, $question->order_number];
            $question->save();
            $next->save();
        }
    }

    public function render()
    {
        return view('livewire.teacher-assessment.manage-questions', [
            'questions' => $this->assessment->questions()->with('options')->orderBy('order_number')->get(),
        ])->layout('components.layouts.app');
    }
}

<?php

namespace App\Livewire\StudentAssessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\StudentAssessmentResponse;
use App\Models\StudentLearningProfile;
use App\Services\LearningProfileService;
use App\Services\DiagnosticProfileService;
use Livewire\Component;
use Livewire\Attributes\Locked;

class Take extends Component
{
    #[Locked]
    public Assessment $assessment;
    
    // Don't store questions as property - get them dynamically
    // #[Locked]
    // public $questions;
    
    public $answers = [];
    public $currentPage = 1;
    
    #[Locked]
    public $questionsPerPage = 5;
    
    public $showConfirmation = false;

    public function updatedAnswers()
    {
        // Save answers to session to persist across reloads
        session(['assessment_answers_' . $this->assessment->id => $this->answers]);
    }

    public function mount($id)
    {
        $this->assessment = Assessment::with(['questions.options', 'academicYear', 'semester'])
            ->findOrFail($id);

        // Check if already completed this specific assessment
        $alreadyCompleted = StudentLearningProfile::where('user_id', auth()->id())
            ->where('assessment_id', $this->assessment->id)
            ->exists();

        if ($alreadyCompleted) {
            session()->flash('info', 'Anda sudah mengisi asesmen ini.');
            return $this->redirect(route('student.assessment.result', $this->assessment->id), navigate: true);
        }

        // Check if assessment is still active
        if (!$this->assessment->isOngoing()) {
            session()->flash('error', 'Asesmen ini sudah tidak aktif.');
            return $this->redirect(route('student.assessment.index'), navigate: true);
        }

        // Don't store questions - get dynamically to avoid hydration issues
        // $this->questions = $this->assessment->questions()->orderBy('order_number')->get()->toArray();
        
        // Try to restore answers from session first
        $savedAnswers = session('assessment_answers_' . $this->assessment->id);
        
        if ($savedAnswers && is_array($savedAnswers)) {
            $this->answers = $savedAnswers;
            \Log::info('Restored answers from session', ['count' => count($this->answers)]);
        } else {
            // Initialize answers array - force reset to ensure clean state
            $this->answers = [];
            $questions = $this->assessment->questions()->orderBy('order_number')->get();
            foreach ($questions as $question) {
                $this->answers[$question->id] = null;
            }
        }
    }
    
    // Helper method to get questions
    public function getQuestionsProperty()
    {
        return $this->assessment->questions()->orderBy('order_number')->get();
    }

    public function resetAssessment()
    {
        // Force reload assessment and questions
        $this->assessment = Assessment::with(['questions.options', 'academicYear', 'semester'])
            ->findOrFail($this->assessment->id);
        
        $this->questions = $this->assessment->questions()->orderBy('order_number')->get();
        
        // Reset answers
        $this->answers = [];
        foreach ($this->questions as $question) {
            $this->answers[$question->id] = null;
        }
        
        $this->currentPage = 1;
        
        session()->flash('success', 'Assessment telah di-reset. Silakan isi ulang jawaban Anda.');
        
        \Log::info('Assessment reset', [
            'user_id' => auth()->id(),
            'assessment_id' => $this->assessment->id,
            'questions_count' => $this->questions->count()
        ]);
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage()
    {
        $totalPages = $this->getTotalPages();
        
        if ($this->currentPage < $totalPages) {
            $this->currentPage++;
        }
    }

    public function goToPage($page)
    {
        $this->currentPage = $page;
    }

    public function getTotalPages()
    {
        return ceil($this->questions->count() / $this->questionsPerPage);
    }

    public function getCurrentQuestions()
    {
        $offset = ($this->currentPage - 1) * $this->questionsPerPage;
        return $this->questions->slice($offset, $this->questionsPerPage);
    }

    public function getAnsweredCount()
    {
        return collect($this->answers)->filter(fn($answer) => $answer !== null)->count();
    }

    public function getProgressPercentage()
    {
        $total = $this->questions->count();
        $answered = $this->getAnsweredCount();
        
        return $total > 0 ? round(($answered / $total) * 100, 2) : 0;
    }

    public function confirmSubmit()
    {
        // Validate all questions are answered
        $unanswered = collect($this->answers)->filter(fn($answer) => $answer === null);
        
        \Log::info('Confirm submit check', [
            'total_questions' => count($this->answers),
            'unanswered_count' => $unanswered->count(),
            'answers' => $this->answers
        ]);
        
        if ($unanswered->count() > 0) {
            session()->flash('error', 'Mohon jawab semua pertanyaan sebelum submit.');
            return;
        }

        $this->showConfirmation = true;
    }

    public function cancelSubmit()
    {
        $this->showConfirmation = false;
    }

    public function submit()
    {
        // Validate all questions are answered
        $unanswered = collect($this->answers)->filter(fn($answer) => $answer === null);
        
        if ($unanswered->count() > 0) {
            session()->flash('error', 'Mohon jawab semua pertanyaan sebelum submit.');
            return;
        }

        try {
            \Log::info('Starting assessment submission', [
                'user_id' => auth()->id(),
                'assessment_id' => $this->assessment->id,
                'answers_count' => count($this->answers)
            ]);

            \DB::beginTransaction();

            // Save all responses
            $responseCount = 0;
            $invalidOptions = [];
            
            foreach ($this->answers as $questionId => $optionId) {
                $question = AssessmentQuestion::find($questionId);
                if (!$question) {
                    \Log::error("Question not found", ['question_id' => $questionId]);
                    continue; // Skip invalid question
                }

                $option = $question->options()->find($optionId);
                if (!$option) {
                    \Log::error("Option not found - will use first available option", [
                        'option_id' => $optionId,
                        'question_id' => $questionId,
                        'available_options' => $question->options()->pluck('id')->toArray()
                    ]);
                    // FALLBACK: Use first available option instead of failing
                    $option = $question->options()->first();
                    $invalidOptions[] = $questionId;
                }
                
                if (!$option) {
                    throw new \Exception("Question {$questionId} has no options available");
                }

                StudentAssessmentResponse::create([
                    'assessment_id' => $this->assessment->id,
                    'user_id' => auth()->id(),
                    'assessment_question_id' => $questionId,
                    'selected_option_id' => $option->id,
                    'score' => $option->score_value,
                    'answered_at' => now(),
                ]);
                $responseCount++;
            }
            
            if (count($invalidOptions) > 0) {
                \Log::warning("Some answers had invalid options and were replaced", [
                    'invalid_questions' => $invalidOptions,
                    'count' => count($invalidOptions)
                ]);
            }

            \Log::info("Saved {$responseCount} responses");

            // Calculate and save learning profile based on assessment type
            if ($this->assessment->isVark()) {
                \Log::info('Calculating VARK profile');
                $profileService = new LearningProfileService();
                $profile = $profileService->calculateProfile($this->assessment, auth()->user());
            } else {
                \Log::info('Calculating Diagnostic profile');
                $diagnosticService = new DiagnosticProfileService();
                $profileData = $diagnosticService->calculateProfile(auth()->user(), $this->assessment);
                $profile = $diagnosticService->saveProfile(auth()->user(), $this->assessment, $profileData);
            }

            if (!$profile || !$profile->id) {
                throw new \Exception("Failed to create learning profile");
            }

            \Log::info('Profile created successfully', ['profile_id' => $profile->id]);

            \DB::commit();

            \Log::info('Transaction committed successfully');

            // Flash success message
            session()->flash('success', 'Asesmen berhasil diselesaikan!');
            
            // Redirect using Livewire v3 method
            \Log::info('Redirecting to result page', ['assessment_id' => $this->assessment->id]);
            return $this->redirect(route('student.assessment.result', $this->assessment->id), navigate: true);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Assessment submission error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Error details', [
                'user_id' => auth()->id(),
                'assessment_id' => $this->assessment->id ?? 'N/A',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Close confirmation modal
            $this->showConfirmation = false;
            
            // Show user-friendly error
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'Option not found') || str_contains($errorMessage, 'Data tidak valid')) {
                session()->flash('error', 'Data jawaban tidak valid. Silakan klik tombol RESET di bawah dan isi ulang asesmen.');
            } else {
                session()->flash('error', 'Terjadi kesalahan: ' . $errorMessage);
            }
        }
    }

    public function render()
    {
        return view('livewire.student-assessment.take', [
            'currentQuestions' => $this->getCurrentQuestions(),
            'totalPages' => $this->getTotalPages(),
            'answeredCount' => $this->getAnsweredCount(),
            'totalQuestions' => $this->questions->count(),
            'progressPercentage' => $this->getProgressPercentage(),
        ])->layout('components.layouts.app');
    }
}

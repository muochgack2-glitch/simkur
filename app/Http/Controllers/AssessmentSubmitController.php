<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\StudentAssessmentResponse;
use App\Models\StudentLearningProfile;
use App\Services\LearningProfileService;
use App\Services\DiagnosticProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentSubmitController extends Controller
{
    public function show($id)
    {
        $assessment = Assessment::with(['questions.options', 'academicYear', 'semester'])
            ->findOrFail($id);

        // Check if already completed
        $existingProfile = StudentLearningProfile::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->first();

        if ($existingProfile) {
            return redirect()->route('student.assessment.result', $assessment->id)
                ->with('info', 'Anda sudah mengisi asesmen ini.');
        }

        // Check if assessment is still active
        if (!$assessment->isOngoing()) {
            return redirect()->route('student.assessment.index')
                ->with('error', 'Asesmen ini sudah tidak aktif.');
        }

        $questions = $assessment->questions()->orderBy('order_number')->get();

        return view('student-assessment.take-simple', compact('assessment', 'questions'));
    }

    public function submit(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);

        // Check if already submitted
        $existingProfile = StudentLearningProfile::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->first();

        if ($existingProfile) {
            return redirect()->route('student.assessment.result', $assessment->id)
                ->with('info', 'Anda sudah menyelesaikan asesmen ini sebelumnya.');
        }

        // Validate
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer|exists:assessment_question_options,id'
        ]);

        try {
            DB::beginTransaction();

            $answers = $request->input('answers');

            // Delete any previous responses (in case of partial submission)
            StudentAssessmentResponse::where('assessment_id', $assessment->id)
                ->where('user_id', auth()->id())
                ->delete();

            // Save responses
            foreach ($answers as $questionId => $optionId) {
                $question = AssessmentQuestion::find($questionId);
                if (!$question) continue;

                $option = $question->options()->find($optionId);
                if (!$option) {
                    // Fallback: use first option
                    $option = $question->options()->first();
                }

                if (!$option) continue;

                StudentAssessmentResponse::create([
                    'assessment_id' => $assessment->id,
                    'user_id' => auth()->id(),
                    'assessment_question_id' => $questionId,
                    'selected_option_id' => $option->id,
                    'score' => $option->score_value,
                    'answered_at' => now(),
                ]);
            }

            // Calculate profile
            if ($assessment->isVark()) {
                $profileService = new LearningProfileService();
                $profile = $profileService->calculateProfile($assessment, auth()->user());
            } else {
                $diagnosticService = new DiagnosticProfileService();
                $profileData = $diagnosticService->calculateProfile(auth()->user(), $assessment);
                $profile = $diagnosticService->saveProfile(auth()->user(), $assessment, $profileData);
            }

            DB::commit();

            return redirect()->route('student.assessment.result', $assessment->id)
                ->with('success', 'Asesmen berhasil diselesaikan!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Assessment submission error: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}

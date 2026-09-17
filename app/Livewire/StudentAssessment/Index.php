<?php

namespace App\Livewire\StudentAssessment;

use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\StudentLearningProfile;
use Livewire\Component;
use App\Livewire\BaseComponent;

class Index extends BaseComponent
{
    public function render()
    {
        $student = auth()->user();

        // ── VARK & Diagnostik (logika lama, tidak diubah) ─────────────────────
        $varkDiagnosticAssessments = Assessment::with(['academicYear', 'semester'])
            ->where('is_published', true)
            ->where('is_active', true)
            ->whereIn('assessment_type', ['vark', 'diagnostic'])
            ->ongoing()
            ->forGrade($student->grade)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($assessment) use ($student) {
                $profile = StudentLearningProfile::where('user_id', $student->id)
                    ->where('assessment_id', $assessment->id)
                    ->first();

                $assessment->is_completed    = $profile !== null;
                $assessment->completion_date = $profile?->completed_at;
                $assessment->profile         = $profile;

                if ($assessment->assessment_type === 'diagnostic' && $student->major) {
                    $questionCount = $assessment->questions()->where(function($q) use ($student) {
                        $q->whereNull('major')->orWhere('major', '')->orWhere('major', $student->major);
                    })->count();
                    $assessment->actual_question_count = $questionCount;
                } else {
                    $assessment->actual_question_count = $assessment->questions()->count();
                }

                return $assessment;
            });

        // ── QUIZ (dari guru) — SEMUA status, urut start_date + start_time ─────
        $quizAssessments = Assessment::with(['creator'])
            ->where('assessment_type', 'quiz')
            ->where('is_published', true)
            ->where('is_active', true)
            ->orderBy('start_date', 'asc')
            ->orderByRaw("COALESCE(start_time, '00:00:00') ASC")
            ->get()
            ->filter(fn($a) => $a->isForStudent($student))
            ->values()
            ->map(function ($assessment) use ($student) {
                // Status asesmen
                $status = $assessment->status; // upcoming | ongoing | closed

                // Cek apakah siswa sudah submit
                $latestSession = AssessmentStudentSession::where('assessment_id', $assessment->id)
                    ->where('user_id', $student->id)
                    ->orderByDesc('attempt_number')
                    ->first();

                $assessment->student_status = match(true) {
                    $status === 'upcoming'                                    => 'upcoming',
                    $latestSession?->isSubmitted() && $status === 'closed'    => 'closed_submitted',
                    !$latestSession && $status === 'closed'                   => 'closed_missed',
                    $latestSession?->isSubmitted()                            => 'submitted',
                    $latestSession?->isInProgress()                           => 'in_progress',
                    default                                                   => 'open',
                };

                $assessment->latest_session    = $latestSession;
                $assessment->questions_count   = $assessment->questions()->count();

                return $assessment;
            });

        return view('livewire.student-assessment.index', [
            'assessments'         => $varkDiagnosticAssessments,
            'quizAssessments'     => $quizAssessments,
        ])->layout('components.layouts.app');
    }
}

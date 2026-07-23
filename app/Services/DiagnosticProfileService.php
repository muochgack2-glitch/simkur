<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\StudentAssessmentResponse;
use App\Models\StudentLearningProfile;
use App\Models\User;

class DiagnosticProfileService
{
    /**
     * Calculate diagnostic profile from student responses
     */
    public function calculateProfile(User $student, Assessment $assessment): array
    {
        // Get all responses for this student and assessment
        $responses = StudentAssessmentResponse::where('user_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->with(['question', 'selectedOption'])
            ->get();

        // Group responses by aspect
        $aspectScores = [];
        $aspectQuestionCounts = [];

        foreach ($responses as $response) {
            $question = $response->question;
            $aspect = $question->aspect;

            if (!isset($aspectScores[$aspect])) {
                $aspectScores[$aspect] = 0;
                $aspectQuestionCounts[$aspect] = 0;
            }

            // For Likert scale, score_value is 1-5
            $aspectScores[$aspect] += $response->selectedOption->score_value ?? 0;
            $aspectQuestionCounts[$aspect]++;
        }

        // Calculate percentage for each aspect
        $aspectPercentages = [];
        foreach ($aspectScores as $aspect => $totalScore) {
            $questionCount = $aspectQuestionCounts[$aspect];
            $maxScore = $questionCount * 5; // Max Likert score is 5
            $aspectPercentages[$aspect] = round(($totalScore / $maxScore) * 100, 2);
        }

        // Determine areas needing support (< 60%)
        $needsSupport = [];
        foreach ($aspectPercentages as $aspect => $percentage) {
            if ($percentage < 60) {
                $needsSupport[] = $aspect;
            }
        }

        // Determine overall category
        $averageScore = count($aspectPercentages) > 0 
            ? array_sum($aspectPercentages) / count($aspectPercentages) 
            : 0;

        $category = $this->determineCategory($averageScore);

        // Generate recommendations
        $recommendations = $this->generateRecommendations($aspectPercentages, $needsSupport);

        return [
            'aspect_scores' => $aspectPercentages,
            'needs_support_in' => $needsSupport,
            'diagnostic_category' => $category,
            'diagnostic_recommendations' => $recommendations,
            'average_score' => round($averageScore, 2),
        ];
    }

    /**
     * Determine category based on average score
     */
    private function determineCategory(float $averageScore): string
    {
        if ($averageScore >= 86) {
            return 'sangat_baik';
        } elseif ($averageScore >= 71) {
            return 'baik';
        } elseif ($averageScore >= 56) {
            return 'cukup';
        } else {
            return 'perlu_pendampingan';
        }
    }

    /**
     * Generate recommendations based on scores
     */
    private function generateRecommendations(array $aspectScores, array $needsSupport): array
    {
        $recommendations = [];

        // If student needs support
        if (!empty($needsSupport)) {
            $recommendations['areas_to_improve'] = array_map(
                fn($aspect) => $this->getAspectLabel($aspect),
                $needsSupport
            );

            $recommendations['suggestions'] = $this->getSuggestionsForWeakAspects($needsSupport);
        } else {
            $recommendations['status'] = 'Siswa menunjukkan kesiapan belajar yang baik';
            $recommendations['suggestions'] = [
                'Pertahankan konsistensi belajar',
                'Terus kembangkan kemandirian',
                'Bantu teman yang membutuhkan',
            ];
        }

        // Add aspect-specific tips
        foreach ($aspectScores as $aspect => $score) {
            if ($score < 70) {
                $recommendations['aspect_tips'][$aspect] = $this->getTipsForAspect($aspect);
            }
        }

        return $recommendations;
    }

    /**
     * Get suggestions for weak aspects
     */
    private function getSuggestionsForWeakAspects(array $weakAspects): array
    {
        $suggestions = [];

        foreach ($weakAspects as $aspect) {
            switch ($aspect) {
                case 'kesiapan':
                    $suggestions[] = 'Buat checklist persiapan belajar harian';
                    $suggestions[] = 'Siapkan materi sebelum pelajaran dimulai';
                    break;
                case 'motivasi':
                    $suggestions[] = 'Tentukan target belajar yang jelas';
                    $suggestions[] = 'Cari inspirasi dari kakak kelas atau alumni';
                    break;
                case 'kemandirian':
                    $suggestions[] = 'Buat jadwal belajar mandiri';
                    $suggestions[] = 'Cari sumber belajar tambahan dari internet';
                    break;
                case 'kolaborasi':
                    $suggestions[] = 'Aktif dalam diskusi kelompok';
                    $suggestions[] = 'Latihan komunikasi dengan teman';
                    break;
                case 'dunia_kerja':
                    $suggestions[] = 'Ikuti pelatihan soft skills';
                    $suggestions[] = 'Pelajari budaya kerja industri';
                    break;
            }
        }

        return $suggestions;
    }

    /**
     * Get tips for specific aspect
     */
    private function getTipsForAspect(string $aspect): array
    {
        return match($aspect) {
            'kesiapan' => [
                'Datang tepat waktu',
                'Lengkapi perlengkapan belajar',
                'Review materi kemarin',
            ],
            'motivasi' => [
                'Tetapkan tujuan jangka pendek',
                'Rayakan pencapaian kecil',
                'Ingat alasan masuk SMK',
            ],
            'kemandirian' => [
                'Belajar tanpa disuruh',
                'Evaluasi hasil belajar sendiri',
                'Atasi masalah secara mandiri dulu',
            ],
            'kolaborasi' => [
                'Dengarkan pendapat orang lain',
                'Berikan kontribusi dalam kelompok',
                'Bantu teman yang kesulitan',
            ],
            'preferensi' => [
                'Kenali cara belajar yang cocok',
                'Eksplorasi berbagai metode',
                'Gunakan teknologi untuk belajar',
            ],
            'dunia_kerja' => [
                'Latih kedisiplinan',
                'Kembangkan problem solving',
                'Pelajari tools industri',
            ],
            default => ['Terus tingkatkan kemampuan'],
        };
    }

    /**
     * Get aspect label
     */
    private function getAspectLabel(string $aspect): string
    {
        return match($aspect) {
            'kesiapan' => 'Kesiapan Belajar',
            'motivasi' => 'Motivasi Belajar',
            'kemandirian' => 'Kemandirian Belajar',
            'kolaborasi' => 'Kolaborasi & Komunikasi',
            'dunia_kerja' => 'Kesiapan Dunia Kerja',
            'preferensi' => 'Preferensi Belajar',
            default => 'Unknown',
        };
    }

    /**
     * Save diagnostic profile to database
     */
    public function saveProfile(User $student, Assessment $assessment, array $profileData): StudentLearningProfile
    {
        return StudentLearningProfile::create([
            'user_id' => $student->id,
            'assessment_id' => $assessment->id,
            'academic_year_id' => $assessment->academic_year_id,
            'semester_id' => $assessment->semester_id,
            'aspect_scores' => $profileData['aspect_scores'],
            'needs_support_in' => $profileData['needs_support_in'],
            'diagnostic_category' => $profileData['diagnostic_category'],
            'diagnostic_recommendations' => $profileData['diagnostic_recommendations'],
            'completed_at' => now(),
        ]);
    }

    /**
     * Get class-wide diagnostic report
     */
    public function getClassReport(Assessment $assessment, ?string $grade = null, ?string $major = null): array
    {
        $query = StudentLearningProfile::where('assessment_id', $assessment->id)
            ->with('student');

        if ($grade) {
            $query->whereHas('student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        if ($major && $major !== 'all') {
            $query->whereHas('student', function($q) use ($major) {
                $q->where('major', $major);
            });
        }

        $profiles = $query->get();

        if ($profiles->isEmpty()) {
            return [
                'total_students' => 0,
                'average_aspects' => [],
                'category_distribution' => [],
                'students_need_support' => [],
            ];
        }

        // Calculate average for each aspect
        $aspectAverages = [];
        $aspectKeys = array_keys($profiles->first()->aspect_scores ?? []);

        foreach ($aspectKeys as $aspect) {
            $scores = $profiles->pluck('aspect_scores')->pluck($aspect)->filter();
            $aspectAverages[$aspect] = round($scores->avg(), 2);
        }

        // Category distribution
        $categoryDistribution = $profiles->groupBy('diagnostic_category')
            ->map->count()
            ->toArray();

        // Students needing support
        $studentsNeedSupport = $profiles->filter(function($profile) {
            return $profile->diagnostic_category === 'perlu_pendampingan';
        })->map(function($profile) {
            return [
                'name' => $profile->student->name,
                'grade' => $profile->student->grade,
                'needs_support_in' => $profile->needs_support_in,
                'average' => collect($profile->aspect_scores)->avg(),
            ];
        })->values();

        return [
            'total_students' => $profiles->count(),
            'average_aspects' => $aspectAverages,
            'category_distribution' => $categoryDistribution,
            'students_need_support' => $studentsNeedSupport,
            'recommendations' => $this->getClassRecommendations($aspectAverages, $studentsNeedSupport->count()),
        ];
    }

    /**
     * Get recommendations for class
     */
    private function getClassRecommendations(array $aspectAverages, int $studentsNeedSupport): array
    {
        $recommendations = [];

        // Find weak aspects in class
        $weakAspects = collect($aspectAverages)->filter(fn($score) => $score < 70)->keys()->toArray();

        if (!empty($weakAspects)) {
            $recommendations['focus_areas'] = 'Kelas perlu perhatian pada: ' . implode(', ', array_map(
                fn($aspect) => $this->getAspectLabel($aspect),
                $weakAspects
            ));
        }

        if ($studentsNeedSupport > 0) {
            $recommendations['support_needed'] = "$studentsNeedSupport siswa memerlukan pendampingan khusus";
            $recommendations['actions'] = [
                'Lakukan konseling individual',
                'Berikan target belajar yang jelas',
                'Pantau perkembangan secara berkala',
            ];
        } else {
            $recommendations['status'] = 'Kelas menunjukkan kesiapan belajar yang baik';
            $recommendations['actions'] = [
                'Pertahankan kualitas pembelajaran',
                'Berikan tantangan lebih',
                'Fokus pada pengembangan karir',
            ];
        }

        return $recommendations;
    }
}

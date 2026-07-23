<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\StudentAssessmentResponse;
use App\Models\StudentLearningProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LearningProfileService
{
    /**
     * Calculate and save student learning profile based on assessment responses
     */
    public function calculateProfile(Assessment $assessment, User $student): StudentLearningProfile
    {
        // Get all responses from this student for this assessment
        $responses = StudentAssessmentResponse::where('assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->with('question')
            ->get();

        // Initialize scores
        $scores = [
            'visual' => 0,
            'auditory' => 0,
            'kinesthetic' => 0,
            'reading_writing' => 0,
        ];

        // Calculate scores based on responses
        foreach ($responses as $response) {
            $indicator = $response->question->learning_style_indicator;
            $scores[$indicator] += $response->score;
        }

        // Determine dominant style
        $dominantStyle = array_keys($scores, max($scores))[0];

        // Calculate total score
        $totalScore = array_sum($scores);

        // Generate recommendations
        $recommendations = $this->generateRecommendations($dominantStyle, $scores, $totalScore);

        // Save or update profile
        return StudentLearningProfile::updateOrCreate(
            [
                'user_id' => $student->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'semester_id' => $assessment->semester_id,
                'academic_year_id' => $assessment->academic_year_id,
                'dominant_style' => $dominantStyle,
                'visual_score' => $scores['visual'],
                'auditory_score' => $scores['auditory'],
                'kinesthetic_score' => $scores['kinesthetic'],
                'reading_writing_score' => $scores['reading_writing'],
                'total_score' => $totalScore,
                'recommendations' => $recommendations,
                'completed_at' => now(),
            ]
        );
    }

    /**
     * Generate learning recommendations based on dominant style
     */
    private function generateRecommendations(string $dominantStyle, array $scores, int $totalScore): array
    {
        $recommendations = [
            'visual' => [
                'title' => 'Visual Learner',
                'description' => 'Kamu belajar paling baik dengan melihat gambar, diagram, dan visualisasi.',
                'tips' => [
                    'Gunakan mind map atau diagram alur untuk memahami konsep',
                    'Highlight catatan dengan warna berbeda untuk topik berbeda',
                    'Tonton video tutorial saat belajar hal baru',
                    'Buat sketsa atau gambar untuk mengingat informasi',
                    'Gunakan flashcard dengan gambar untuk menghafal',
                    'Duduk di depan kelas agar bisa melihat papan tulis dengan jelas',
                ],
                'study_methods' => [
                    'Buat infografis atau poster untuk materi yang sulit',
                    'Gunakan aplikasi mind mapping (MindMeister, Canva)',
                    'Screenshot atau foto materi penting',
                    'Gunakan warna konsisten untuk kategori materi',
                ],
            ],
            'auditory' => [
                'title' => 'Auditory Learner',
                'description' => 'Kamu belajar paling baik dengan mendengar penjelasan dan diskusi.',
                'tips' => [
                    'Rekam penjelasan guru dan dengarkan ulang saat belajar',
                    'Diskusikan materi dengan teman atau kelompok belajar',
                    'Baca materi dengan suara keras',
                    'Dengarkan podcast atau audiobook edukatif',
                    'Jelaskan materi ke orang lain untuk memahami lebih dalam',
                    'Gunakan musik instrumental saat belajar (jika membantu)',
                ],
                'study_methods' => [
                    'Buat rekaman voice note tentang ringkasan materi',
                    'Join diskusi online atau forum belajar',
                    'Gunakan metode belajar dengan menceritakan kembali',
                    'Ikuti webinar atau online class dengan penjelasan verbal',
                ],
            ],
            'kinesthetic' => [
                'title' => 'Kinesthetic Learner',
                'description' => 'Kamu belajar paling baik dengan praktik langsung dan bergerak.',
                'tips' => [
                    'Praktikkan langsung setiap materi yang dipelajari',
                    'Gunakan alat peraga atau benda nyata saat belajar',
                    'Buat proyek atau simulasi dari materi pembelajaran',
                    'Belajar sambil berjalan atau bergerak ringan',
                    'Role play atau simulasi untuk memahami konsep',
                    'Ikuti praktikum dan kegiatan hands-on sebanyak mungkin',
                ],
                'study_methods' => [
                    'Buat prototype atau model dari konsep yang dipelajari',
                    'Gunakan teknik belajar dengan menulis sambil berdiri',
                    'Break time setiap 25 menit untuk peregangan',
                    'Praktik coding/eksperimen langsung daripada hanya baca teori',
                ],
            ],
            'reading_writing' => [
                'title' => 'Reading/Writing Learner',
                'description' => 'Kamu belajar paling baik dengan membaca dan menulis.',
                'tips' => [
                    'Buat catatan lengkap dengan kata-kata sendiri',
                    'Baca buku, artikel, dan referensi sebanyak mungkin',
                    'Tulis rangkuman setelah belajar setiap topik',
                    'Buat daftar (list) atau outline materi',
                    'Tulis jurnal refleksi tentang apa yang sudah dipelajari',
                    'Gunakan metode Cornell Note-taking',
                ],
                'study_methods' => [
                    'Buat essay atau artikel tentang materi yang dipelajari',
                    'Gunakan aplikasi note-taking (Notion, Obsidian)',
                    'Baca dokumentasi resmi saat belajar programming',
                    'Buat FAQ (Frequently Asked Questions) sendiri',
                ],
            ],
        ];

        $result = $recommendations[$dominantStyle];

        // Add secondary style if close
        $secondaryStyle = $this->getSecondaryStyle($scores, $dominantStyle);
        if ($secondaryStyle) {
            $result['secondary_style'] = [
                'name' => $secondaryStyle,
                'label' => $recommendations[$secondaryStyle]['title'],
                'tip' => 'Kamu juga cukup kuat di gaya belajar ' . $recommendations[$secondaryStyle]['title'] . '. Kombinasikan kedua metode untuk hasil optimal!',
            ];
        }

        return $result;
    }

    /**
     * Get secondary learning style if score is close to dominant
     */
    private function getSecondaryStyle(array $scores, string $dominantStyle): ?string
    {
        $dominantScore = $scores[$dominantStyle];
        
        // Find second highest score
        arsort($scores);
        $sortedStyles = array_keys($scores);
        $secondStyle = $sortedStyles[1] ?? null;
        
        if (!$secondStyle) {
            return null;
        }

        $secondScore = $scores[$secondStyle];
        
        // If difference is less than 20%, consider it secondary
        $difference = (($dominantScore - $secondScore) / $dominantScore) * 100;
        
        return $difference < 20 ? $secondStyle : null;
    }

    /**
     * Get class statistics for learning styles
     */
    public function getClassStatistics(string $grade, int $semesterId): array
    {
        // Get the most recent VARK assessment for this semester
        $varkAssessment = Assessment::where('assessment_type', 'vark')
            ->where('semester_id', $semesterId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$varkAssessment) {
            return [
                'total_students' => 0,
                'completed_students' => 0,
                'completion_percentage' => 0,
                'distribution' => [],
                'dominant_style' => null,
                'recommendations' => null,
            ];
        }

        $profiles = StudentLearningProfile::whereHas('student', function ($query) use ($grade) {
                $query->where('grade', $grade);
            })
            ->where('semester_id', $semesterId)
            ->where('assessment_id', $varkAssessment->id) // Filter by specific assessment
            ->get();

        if ($profiles->isEmpty()) {
            return [
                'total_students' => 0,
                'completed_students' => 0,
                'completion_percentage' => 0,
                'distribution' => [],
                'dominant_style' => null,
                'recommendations' => null,
            ];
        }

        $distribution = $profiles->groupBy('dominant_style')->map(function ($group) {
            return [
                'count' => $group->count(),
                'percentage' => 0, // Will be calculated below
            ];
        })->toArray();

        $totalCompleted = $profiles->count();
        $totalStudents = User::where('role', 'siswa')->where('grade', $grade)->count();

        // Calculate percentages
        foreach ($distribution as $style => &$data) {
            $data['percentage'] = round(($data['count'] / $totalCompleted) * 100, 2);
        }

        // Sort by count
        uasort($distribution, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        $dominantStyle = array_key_first($distribution);

        return [
            'total_students' => $totalStudents,
            'completed_students' => $totalCompleted,
            'completion_percentage' => round(($totalCompleted / max($totalStudents, 1)) * 100, 2),
            'distribution' => $distribution,
            'dominant_style' => $dominantStyle,
            'recommendations' => $this->getTeachingRecommendations($dominantStyle, $distribution),
        ];
    }

    /**
     * Generate teaching recommendations for teachers based on class distribution
     */
    private function getTeachingRecommendations(string $dominantStyle, array $distribution): array
    {
        $recommendations = [
            'visual' => [
                'priority' => [
                    'Gunakan presentasi dengan banyak diagram dan infografis',
                    'Sediakan video tutorial untuk praktikum',
                    'Gunakan color coding untuk konsep berbeda',
                    'Demo visual sebelum praktik',
                ],
                'balance' => 'Seimbangkan dengan metode lain untuk siswa dengan gaya belajar berbeda',
            ],
            'auditory' => [
                'priority' => [
                    'Berikan penjelasan verbal yang detail',
                    'Perbanyak diskusi kelas dan tanya jawab',
                    'Gunakan metode storytelling',
                    'Sediakan podcast atau audio materi',
                ],
                'balance' => 'Tambahkan visual dan praktik untuk melengkapi',
            ],
            'kinesthetic' => [
                'priority' => [
                    'Perbanyak praktik hands-on',
                    'Gunakan simulasi dan role play',
                    'Buat project-based learning',
                    'Demo yang bisa diikuti langsung',
                ],
                'balance' => 'Sertakan penjelasan verbal dan visual untuk pemahaman konsep',
            ],
            'reading_writing' => [
                'priority' => [
                    'Sediakan modul dan handout lengkap',
                    'Berikan tugas menulis laporan',
                    'Gunakan metode jurnal refleksi',
                    'Sediakan referensi bacaan yang banyak',
                ],
                'balance' => 'Kombinasikan dengan praktik dan diskusi',
            ],
        ];

        return $recommendations[$dominantStyle] ?? [];
    }
}

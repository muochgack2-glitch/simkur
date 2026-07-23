<?php

namespace App\Services;

class DiagnosticAssessmentInterpreter
{
    /**
     * Interpret overall diagnostic assessment score
     * 
     * @param float $totalScore Average score (1-5)
     * @param array $aspectScores Scores per aspect
     * @return array
     */
    public static function interpret(float $totalScore, array $aspectScores): array
    {
        $interpretation = [];

        // Overall interpretation
        if ($totalScore >= 4.5) {
            $interpretation['kategori'] = 'Sangat Siap';
            $interpretation['warna'] = 'green';
            $interpretation['emoji'] = '🌟';
            $interpretation['deskripsi'] = 'Anda memiliki kesiapan belajar yang sangat baik. Pertahankan dan kembangkan terus!';
        } elseif ($totalScore >= 4.0) {
            $interpretation['kategori'] = 'Siap';
            $interpretation['warna'] = 'blue';
            $interpretation['emoji'] = '✅';
            $interpretation['deskripsi'] = 'Anda siap untuk belajar dengan baik. Tingkatkan konsistensi untuk hasil optimal.';
        } elseif ($totalScore >= 3.5) {
            $interpretation['kategori'] = 'Cukup Siap';
            $interpretation['warna'] = 'yellow';
            $interpretation['emoji'] = '⚠️';
            $interpretation['deskripsi'] = 'Anda cukup siap belajar, namun masih ada beberapa aspek yang perlu ditingkatkan.';
        } elseif ($totalScore >= 3.0) {
            $interpretation['kategori'] = 'Kurang Siap';
            $interpretation['warna'] = 'orange';
            $interpretation['emoji'] = '🔶';
            $interpretation['deskripsi'] = 'Kesiapan belajar Anda perlu ditingkatkan. Konsultasi dengan guru BK disarankan.';
        } else {
            $interpretation['kategori'] = 'Perlu Perhatian Khusus';
            $interpretation['warna'] = 'red';
            $interpretation['emoji'] = '🆘';
            $interpretation['deskripsi'] = 'Kesiapan belajar Anda memerlukan perhatian segera. Segera konsultasi dengan guru BK dan wali kelas.';
        }

        // General recommendations based on total score
        $interpretation['saran_umum'] = self::getGeneralRecommendations($totalScore);

        // Specific recommendations based on aspect scores
        $interpretation['saran_per_aspek'] = self::getAspectRecommendations($aspectScores);

        // Strengths and weaknesses
        $interpretation['kekuatan'] = self::getStrengths($aspectScores);
        $interpretation['kelemahan'] = self::getWeaknesses($aspectScores);

        return $interpretation;
    }

    /**
     * Get general recommendations based on total score
     */
    private static function getGeneralRecommendations(float $score): array
    {
        if ($score >= 4.5) {
            return [
                'Pertahankan semangat dan konsistensi belajar Anda',
                'Bantu teman yang mengalami kesulitan sebagai tutor sebaya',
                'Ikuti kompetisi atau kegiatan ekstrakurikuler untuk pengembangan diri',
                'Mulai persiapkan portofolio untuk PKL/magang di industri',
                'Pelajari skill tambahan yang relevan dengan jurusan Anda',
            ];
        } elseif ($score >= 4.0) {
            return [
                'Tingkatkan konsistensi dalam mengerjakan tugas',
                'Perbanyak latihan mandiri di rumah',
                'Aktif bertanya kepada guru jika ada kesulitan',
                'Ikuti pembelajaran praktik dengan serius',
                'Bergabung dengan project atau komunitas jurusan',
            ];
        } elseif ($score >= 3.5) {
            return [
                'Buat jadwal belajar yang lebih teratur dan patuhi',
                'Cari motivasi belajar yang lebih kuat (tujuan karir)',
                'Bergabung dengan kelompok belajar atau study group',
                'Manfaatkan sumber belajar digital (YouTube, e-learning)',
                'Konsultasi dengan guru untuk metode belajar yang lebih efektif',
            ];
        } elseif ($score >= 3.0) {
            return [
                'Konsultasi dengan guru BK untuk bantuan belajar',
                'Identifikasi kendala utama dalam belajar (waktu, motivasi, metode)',
                'Mulai dengan target kecil yang mudah dicapai',
                'Minta bantuan teman atau kakak kelas untuk mentoring',
                'Libatkan orang tua dalam memantau proses belajar',
            ];
        } else {
            return [
                'Segera konsultasi dengan guru BK dan wali kelas',
                'Identifikasi masalah mendasar (keluarga, ekonomi, kesehatan)',
                'Buat program pendampingan khusus bersama guru',
                'Libatkan orang tua secara aktif dalam pembelajaran',
                'Pertimbangkan konseling atau bantuan profesional',
            ];
        }
    }

    /**
     * Get specific recommendations based on aspect scores
     */
    private static function getAspectRecommendations(array $aspectScores): array
    {
        $recommendations = [];

        foreach ($aspectScores as $aspect => $score) {
            if ($score < 3.5) {
                switch ($aspect) {
                    case 'kesiapan':
                        $recommendations[] = [
                            'aspek' => 'Kesiapan Belajar',
                            'status' => 'Perlu Ditingkatkan',
                            'saran' => [
                                'Buat checklist harian untuk persiapan belajar',
                                'Siapkan bahan ajar dan perlengkapan malam sebelumnya',
                                'Baca rangkuman materi 10 menit sebelum kelas',
                                'Pastikan tidur cukup (7-8 jam) untuk konsentrasi optimal',
                            ],
                        ];
                        break;

                    case 'motivasi':
                        $recommendations[] = [
                            'aspek' => 'Motivasi Belajar',
                            'status' => 'Perlu Ditingkatkan',
                            'saran' => [
                                'Diskusikan tujuan karir dengan guru BK',
                                'Cari role model sukses di jurusan Anda',
                                'Buat vision board untuk tujuan jangka panjang',
                                'Ikuti kunjungan industri atau company visit',
                            ],
                        ];
                        break;

                    case 'kemandirian':
                        $recommendations[] = [
                            'aspek' => 'Kemandirian Belajar',
                            'status' => 'Perlu Ditingkatkan',
                            'saran' => [
                                'Latihan belajar mandiri 30 menit setiap hari',
                                'Gunakan timer Pomodoro (25 menit fokus, 5 menit istirahat)',
                                'Buat catatan pribadi dengan gaya Anda sendiri',
                                'Cari tutorial online untuk belajar mandiri',
                            ],
                        ];
                        break;

                    case 'kolaborasi':
                        $recommendations[] = [
                            'aspek' => 'Kolaborasi & Komunikasi',
                            'status' => 'Perlu Ditingkatkan',
                            'saran' => [
                                'Bergabung dengan project kelompok atau organisasi',
                                'Latih skill komunikasi dengan presentasi kecil',
                                'Dengarkan aktif saat teman berbicara',
                                'Ikuti kegiatan team building di sekolah',
                            ],
                        ];
                        break;

                    case 'preferensi':
                        $recommendations[] = [
                            'aspek' => 'Preferensi Belajar',
                            'status' => 'Eksplorasi Diperlukan',
                            'saran' => [
                                'Coba berbagai metode belajar (visual, audio, kinestetik)',
                                'Identifikasi gaya belajar yang paling cocok untuk Anda',
                                'Kombinasikan berbagai media pembelajaran',
                                'Diskusikan dengan guru metode yang paling efektif',
                            ],
                        ];
                        break;

                    case 'dunia_kerja':
                        $recommendations[] = [
                            'aspek' => 'Kesiapan Dunia Kerja',
                            'status' => 'Perlu Ditingkatkan',
                            'saran' => [
                                'Latih kedisiplinan waktu mulai dari sekarang',
                                'Ikuti magang atau PKL dengan serius',
                                'Pelajari etika kerja dan profesionalisme',
                                'Bangun networking dengan alumni atau industri',
                            ],
                        ];
                        break;
                }
            }
        }

        return $recommendations;
    }

    /**
     * Identify strengths from aspect scores
     */
    private static function getStrengths(array $aspectScores): array
    {
        $strengths = [];

        foreach ($aspectScores as $aspect => $score) {
            if ($score >= 4.0) {
                $strengths[] = [
                    'aspek' => self::getAspectName($aspect),
                    'skor' => round($score, 2),
                    'keterangan' => self::getAspectDescription($aspect, $score),
                ];
            }
        }

        // Sort by score descending
        usort($strengths, fn($a, $b) => $b['skor'] <=> $a['skor']);

        return $strengths;
    }

    /**
     * Identify weaknesses from aspect scores
     */
    private static function getWeaknesses(array $aspectScores): array
    {
        $weaknesses = [];

        foreach ($aspectScores as $aspect => $score) {
            if ($score < 3.5) {
                $weaknesses[] = [
                    'aspek' => self::getAspectName($aspect),
                    'skor' => round($score, 2),
                    'keterangan' => self::getAspectDescription($aspect, $score),
                ];
            }
        }

        // Sort by score ascending
        usort($weaknesses, fn($a, $b) => $a['skor'] <=> $b['skor']);

        return $weaknesses;
    }

    /**
     * Get human-readable aspect name
     */
    private static function getAspectName(string $aspect): string
    {
        return match ($aspect) {
            'kesiapan' => 'Kesiapan Belajar',
            'motivasi' => 'Motivasi Belajar',
            'kemandirian' => 'Kemandirian Belajar',
            'kolaborasi' => 'Kolaborasi & Komunikasi',
            'preferensi' => 'Preferensi Belajar',
            'dunia_kerja' => 'Kesiapan Dunia Kerja',
            'kompetensi_jurusan' => 'Kompetensi Jurusan',
            default => ucfirst($aspect),
        };
    }

    /**
     * Get aspect description based on score
     */
    private static function getAspectDescription(string $aspect, float $score): string
    {
        if ($score >= 4.5) {
            return 'Sangat Baik - Pertahankan dan kembangkan terus';
        } elseif ($score >= 4.0) {
            return 'Baik - Tingkatkan konsistensi';
        } elseif ($score >= 3.5) {
            return 'Cukup - Perlu peningkatan lebih lanjut';
        } elseif ($score >= 3.0) {
            return 'Kurang - Memerlukan perhatian khusus';
        } else {
            return 'Sangat Kurang - Butuh intervensi segera';
        }
    }
}

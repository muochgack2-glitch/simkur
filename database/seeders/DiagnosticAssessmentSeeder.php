<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiagnosticAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            $this->command->warn('No active academic year found. Please create one first.');
            return;
        }

        $activeSemester = $activeYear->semesters()->first();
        
        if (!$activeSemester) {
            $this->command->warn('No semester found for active academic year.');
            return;
        }

        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = User::where('role', 'waka_kurikulum')->first();
        }

        if (!$admin) {
            $this->command->warn('No admin or waka_kurikulum user found.');
            return;
        }

        // Create Assessment
        $assessment = Assessment::create([
            'title' => 'Asesmen Diagnostik Kesiapan Belajar',
            'description' => 'Asesmen diagnostik untuk mengukur kesiapan belajar siswa SMK meliputi: Kesiapan Belajar (25%), Motivasi (20%), Kemandirian (15%), Kolaborasi (15%), Preferensi Belajar (15%), Kesiapan Dunia Kerja (10%), dan Kompetensi Jurusan (bonus). Total 43 soal termasuk soal spesifik untuk MPLB, AKL, dan BUSANA.',
            'assessment_type' => 'diagnostic',
            'academic_year_id' => $activeYear->id,
            'semester_id' => $activeSemester->id,
            'target_grades' => ['X', 'XI', 'XII'],
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'created_by' => $admin->id,
        ]);

        $this->command->info('Diagnostic Assessment created: ' . $assessment->title);

        // Get questions grouped by aspect
        $questions = $this->getQuestions();

        foreach ($questions as $index => $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'question_text' => $questionData['text'],
                'question_type' => 'likert',
                'aspect' => $questionData['aspect'],
                'aspect_weight' => $questionData['weight'],
                'order_number' => $index + 1,
                'weight' => 1,
            ]);

            // Create 5 Likert scale options
            $likertOptions = [
                ['text' => '1 - Sangat Tidak Sesuai', 'score' => 1],
                ['text' => '2 - Tidak Sesuai', 'score' => 2],
                ['text' => '3 - Cukup Sesuai', 'score' => 3],
                ['text' => '4 - Sesuai', 'score' => 4],
                ['text' => '5 - Sangat Sesuai', 'score' => 5],
            ];

            foreach ($likertOptions as $optIndex => $option) {
                AssessmentQuestionOption::create([
                    'assessment_question_id' => $question->id,
                    'option_text' => $option['text'],
                    'score_value' => $option['score'],
                    'order_number' => $optIndex + 1,
                ]);
            }
        }

        $this->command->info(count($questions) . ' diagnostic questions created successfully!');
    }

    /**
     * Get all diagnostic questions grouped by aspect
     * Total: 43 questions (28 umum + 15 spesifik jurusan)
     */
    private function getQuestions(): array
    {
        return [
            // A. Kesiapan Belajar (25%) - 6 questions
            [
                'text' => 'Saya membaca materi pelajaran sebelum kelas dimulai',
                'aspect' => 'kesiapan',
                'weight' => 25,
            ],
            [
                'text' => 'Saya membawa perlengkapan belajar (alat tulis, laptop, modul) dengan lengkap setiap hari',
                'aspect' => 'kesiapan',
                'weight' => 25,
            ],
            [
                'text' => 'Saya memahami tujuan pembelajaran yang dijelaskan oleh guru',
                'aspect' => 'kesiapan',
                'weight' => 25,
            ],
            [
                'text' => 'Saya siap mengikuti pelajaran dan fokus saat guru menjelaskan',
                'aspect' => 'kesiapan',
                'weight' => 25,
            ],
            [
                'text' => 'Saya berani bertanya kepada guru jika ada materi yang belum saya pahami',
                'aspect' => 'kesiapan',
                'weight' => 25,
            ],
            [
                'text' => 'Saya mengerjakan PR atau tugas yang diberikan guru dengan lengkap',
                'aspect' => 'kesiapan',
                'weight' => 25,
            ],

            // B. Motivasi Belajar (20%) - 5 questions
            [
                'text' => 'Saya ingin memperoleh nilai yang baik di semua mata pelajaran',
                'aspect' => 'motivasi',
                'weight' => 20,
            ],
            [
                'text' => 'Saya tetap belajar walaupun tidak ada tugas atau PR',
                'aspect' => 'motivasi',
                'weight' => 20,
            ],
            [
                'text' => 'Saya ingin menguasai keterampilan sesuai dengan jurusan yang saya pilih',
                'aspect' => 'motivasi',
                'weight' => 20,
            ],
            [
                'text' => 'Saya senang mempelajari hal-hal baru yang berhubungan dengan jurusan saya',
                'aspect' => 'motivasi',
                'weight' => 20,
            ],
            [
                'text' => 'Saya memiliki target atau rencana yang jelas setelah lulus SMK',
                'aspect' => 'motivasi',
                'weight' => 20,
            ],

            // C. Kemandirian Belajar (15%) - 5 questions
            [
                'text' => 'Saya dapat belajar tanpa selalu harus dibimbing oleh guru',
                'aspect' => 'kemandirian',
                'weight' => 15,
            ],
            [
                'text' => 'Saya menyelesaikan tugas tepat waktu tanpa menunda-nunda',
                'aspect' => 'kemandirian',
                'weight' => 15,
            ],
            [
                'text' => 'Saya mencari sumber belajar tambahan (video, artikel, tutorial) sendiri di internet',
                'aspect' => 'kemandirian',
                'weight' => 15,
            ],
            [
                'text' => 'Saya membuat jadwal belajar dan berusaha mengikutinya',
                'aspect' => 'kemandirian',
                'weight' => 15,
            ],
            [
                'text' => 'Saya mengevaluasi hasil belajar saya sendiri dan mencari cara untuk perbaikan',
                'aspect' => 'kemandirian',
                'weight' => 15,
            ],

            // D. Kolaborasi & Komunikasi (15%) - 5 questions
            [
                'text' => 'Saya senang bekerja dalam kelompok untuk menyelesaikan tugas',
                'aspect' => 'kolaborasi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya menghargai dan mendengarkan pendapat teman saat berdiskusi',
                'aspect' => 'kolaborasi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya aktif berdiskusi dan memberikan kontribusi dalam kelompok',
                'aspect' => 'kolaborasi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya membantu teman yang mengalami kesulitan dalam belajar',
                'aspect' => 'kolaborasi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya dapat membagi tugas dengan adil dalam kerja kelompok',
                'aspect' => 'kolaborasi',
                'weight' => 15,
            ],

            // E. Preferensi Belajar (15%) - 5 questions
            [
                'text' => 'Saya lebih mudah memahami materi melalui gambar, diagram, atau video',
                'aspect' => 'preferensi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya lebih mudah memahami materi melalui praktik langsung daripada teori',
                'aspect' => 'preferensi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya senang berdiskusi dengan teman untuk memahami materi',
                'aspect' => 'preferensi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya lebih suka belajar dengan mencoba sendiri sebelum diberi contoh',
                'aspect' => 'preferensi',
                'weight' => 15,
            ],
            [
                'text' => 'Saya merasa lebih paham jika belajar menggunakan teknologi (komputer, aplikasi, simulasi)',
                'aspect' => 'preferensi',
                'weight' => 15,
            ],

            // F. Kesiapan Dunia Kerja (10%) - 3 questions
            [
                'text' => 'Saya senang memecahkan masalah nyata yang ada di kehidupan sehari-hari',
                'aspect' => 'dunia_kerja',
                'weight' => 10,
            ],
            [
                'text' => 'Saya mampu bekerja dalam tim dengan orang yang baru saya kenal',
                'aspect' => 'dunia_kerja',
                'weight' => 10,
            ],
            [
                'text' => 'Saya disiplin terhadap waktu dan jarang terlambat',
                'aspect' => 'dunia_kerja',
                'weight' => 10,
            ],

            // G. Kompetensi Jurusan MPLB - Manajemen Perkantoran dan Layanan Bisnis (5 soal)
            [
                'text' => '[MPLB] Saya senang mengatur dan mengelola dokumen atau data',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'MPLB',
            ],
            [
                'text' => '[MPLB] Saya tertarik dengan pekerjaan administrasi perkantoran',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'MPLB',
            ],
            [
                'text' => '[MPLB] Saya senang menggunakan aplikasi komputer (Word, Excel, PowerPoint)',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'MPLB',
            ],
            [
                'text' => '[MPLB] Saya mampu berkomunikasi dengan baik secara lisan maupun tulisan',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'MPLB',
            ],
            [
                'text' => '[MPLB] Saya tertarik belajar tentang manajemen bisnis dan pelayanan pelanggan',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'MPLB',
            ],

            // H. Kompetensi Jurusan AKL - Akuntansi dan Keuangan Lembaga (5 soal)
            [
                'text' => '[AKL] Saya senang bekerja dengan angka dan perhitungan',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'AKL',
            ],
            [
                'text' => '[AKL] Saya teliti dan detail dalam mengerjakan sesuatu',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'AKL',
            ],
            [
                'text' => '[AKL] Saya tertarik dengan pembukuan dan laporan keuangan',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'AKL',
            ],
            [
                'text' => '[AKL] Saya suka menganalisis data dan mencari pola',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'AKL',
            ],
            [
                'text' => '[AKL] Saya tertarik belajar tentang perpajakan dan sistem keuangan',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'AKL',
            ],

            // I. Kompetensi Jurusan BUSANA - Tata Busana (5 soal)
            [
                'text' => '[BUSANA] Saya senang membuat karya dengan tangan sendiri',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'BUSANA',
            ],
            [
                'text' => '[BUSANA] Saya tertarik dengan dunia fashion dan desain',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'BUSANA',
            ],
            [
                'text' => '[BUSANA] Saya sabar dan teliti dalam mengerjakan detail kecil',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'BUSANA',
            ],
            [
                'text' => '[BUSANA] Saya memiliki kreativitas dalam memadukan warna dan pola',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'BUSANA',
            ],
            [
                'text' => '[BUSANA] Saya tertarik belajar menjahit dan membuat pakaian',
                'aspect' => 'kompetensi_jurusan',
                'weight' => 0,
                'major' => 'BUSANA',
            ],
        ];
    }
}

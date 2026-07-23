<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class VARKAssessmentRevisedSeeder extends Seeder
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
            'title' => 'Asesmen Gaya Belajar VARK (Revisi)',
            'description' => 'Asesmen untuk mengidentifikasi gaya belajar siswa berdasarkan model VARK (Visual, Auditory, Reading/Writing, Kinesthetic) - Cocok untuk semua jurusan SMK',
            'academic_year_id' => $activeYear->id,
            'semester_id' => $activeSemester->id,
            'target_grades' => ['X', 'XI', 'XII'],
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'created_by' => $admin->id,
        ]);

        $this->command->info('Assessment created: ' . $assessment->title);

        // 24 Questions for VARK Assessment (Universal SMK Context)
        $questions = $this->getQuestions();

        foreach ($questions as $index => $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'question_text' => $questionData['text'],
                'question_type' => 'multiple_choice',
                'order_number' => $index + 1,
                'learning_style_indicator' => $questionData['indicator'],
                'weight' => 1,
            ]);

            foreach ($questionData['options'] as $optIndex => $option) {
                AssessmentQuestionOption::create([
                    'assessment_question_id' => $question->id,
                    'option_text' => $option['text'],
                    'score_value' => $option['score'],
                    'order_number' => $optIndex + 1,
                ]);
            }
        }

        $this->command->info('24 VARK questions (revised) created successfully!');
    }

    private function getQuestions(): array
    {
        return [
            // Visual Questions (6 questions)
            [
                'text' => 'Saat guru menjelaskan materi baru, saya lebih mudah paham jika:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Guru menggunakan diagram, grafik, atau gambar di papan/slide', 'score' => 3],
                    ['text' => 'Guru menjelaskan dengan kata-kata yang jelas', 'score' => 0],
                    ['text' => 'Guru memberikan handout atau modul untuk dibaca', 'score' => 0],
                    ['text' => 'Guru memberikan contoh praktik langsung', 'score' => 0],
                ],
            ],
            [
                'text' => 'Untuk mengingat langkah-langkah mengerjakan tugas, saya biasanya:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Membuat flowchart atau diagram alur kerja', 'score' => 3],
                    ['text' => 'Mengulangi langkah-langkahnya dalam hati', 'score' => 0],
                    ['text' => 'Menulis daftar langkah-langkahnya', 'score' => 0],
                    ['text' => 'Langsung praktik berulang kali', 'score' => 0],
                ],
            ],
            [
                'text' => 'Cara paling efektif untuk saya memahami materi pelajaran:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Menonton video pembelajaran atau tutorial', 'score' => 3],
                    ['text' => 'Mendengarkan penjelasan guru dengan fokus', 'score' => 0],
                    ['text' => 'Membaca buku atau referensi tertulis', 'score' => 0],
                    ['text' => 'Mengerjakan latihan soal atau praktikum', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat mencatat pelajaran, saya lebih suka:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Membuat mind map dengan warna dan gambar', 'score' => 3],
                    ['text' => 'Mendengarkan saja dan mengingat poin pentingnya', 'score' => 0],
                    ['text' => 'Menulis catatan rapi dengan poin-poin lengkap', 'score' => 0],
                    ['text' => 'Tidak mencatat, langsung praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Dalam tugas kelompok, kontribusi terbaik saya adalah:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Membuat presentasi visual atau poster', 'score' => 3],
                    ['text' => 'Menjelaskan ide ke anggota kelompok', 'score' => 0],
                    ['text' => 'Menulis laporan atau dokumentasi', 'score' => 0],
                    ['text' => 'Mengerjakan bagian praktik atau demo', 'score' => 0],
                ],
            ],
            [
                'text' => 'Ketika belajar prosedur baru (seperti membuat laporan keuangan, membuat pola, atau mengetik surat), saya lebih paham jika:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Melihat contoh jadi atau video tutorial', 'score' => 3],
                    ['text' => 'Mendengar penjelasan langkah demi langkah', 'score' => 0],
                    ['text' => 'Membaca panduan atau instruksi tertulis', 'score' => 0],
                    ['text' => 'Langsung mencoba sambil dipandu', 'score' => 0],
                ],
            ],

            // Auditory Questions (6 questions)
            [
                'text' => 'Saya paling mudah memahami pelajaran ketika:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Guru menulis atau menggambar di papan tulis', 'score' => 0],
                    ['text' => 'Guru menjelaskan secara lisan dengan detail', 'score' => 3],
                    ['text' => 'Guru memberikan materi tertulis untuk dibaca', 'score' => 0],
                    ['text' => 'Guru memberi praktik langsung', 'score' => 0],
                ],
            ],
            [
                'text' => 'Cara belajar mandiri yang paling efektif buat saya:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Melihat video atau animasi pembelajaran', 'score' => 0],
                    ['text' => 'Mendengarkan rekaman penjelasan atau diskusi online', 'score' => 3],
                    ['text' => 'Membaca buku atau modul sendiri', 'score' => 0],
                    ['text' => 'Mengerjakan latihan praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saya lebih mudah mengingat:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Wajah atau penampilan seseorang', 'score' => 0],
                    ['text' => 'Nama atau suara seseorang', 'score' => 3],
                    ['text' => 'Tulisan atau nama yang tertulis', 'score' => 0],
                    ['text' => 'Kegiatan yang dilakukan bersama', 'score' => 0],
                ],
            ],
            [
                'text' => 'Ketika menghadapi materi yang sulit, saya biasanya:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Mencari video atau gambar penjelasan', 'score' => 0],
                    ['text' => 'Bertanya dan diskusi dengan guru atau teman', 'score' => 3],
                    ['text' => 'Membaca ulang buku atau catatan', 'score' => 0],
                    ['text' => 'Mencoba praktik sampai paham', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat presentasi di depan kelas, saya paling percaya diri ketika:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Menampilkan slide yang menarik', 'score' => 0],
                    ['text' => 'Menjelaskan materi secara lisan', 'score' => 3],
                    ['text' => 'Membagikan handout tertulis', 'score' => 0],
                    ['text' => 'Melakukan demonstrasi praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Untuk mengingat rumus atau formula (akuntansi, matematika, dll), saya:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Membuat catatan visual berwarna', 'score' => 0],
                    ['text' => 'Membaca dengan suara keras atau menghafalnya', 'score' => 3],
                    ['text' => 'Menulis berulang-ulang', 'score' => 0],
                    ['text' => 'Mengerjakan soal latihan', 'score' => 0],
                ],
            ],

            // Kinesthetic Questions (6 questions)
            [
                'text' => 'Saya belajar paling efektif ketika:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Melihat contoh atau demonstrasi', 'score' => 0],
                    ['text' => 'Mendengar penjelasan detail', 'score' => 0],
                    ['text' => 'Membaca materi tertulis', 'score' => 0],
                    ['text' => 'Langsung praktik dan mencoba sendiri', 'score' => 3],
                ],
            ],
            [
                'text' => 'Saat praktikum (di lab, workshop, atau ruang praktik), saya:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Melihat dulu step-by-step di slide', 'score' => 0],
                    ['text' => 'Mendengar instruksi guru dengan teliti', 'score' => 0],
                    ['text' => 'Membaca panduan praktikum dulu', 'score' => 0],
                    ['text' => 'Langsung coba sambil dibimbing guru', 'score' => 3],
                ],
            ],
            [
                'text' => 'Metode belajar yang paling cocok untuk saya:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Presentasi dengan slide visual menarik', 'score' => 0],
                    ['text' => 'Ceramah atau diskusi kelas', 'score' => 0],
                    ['text' => 'Baca buku atau e-book', 'score' => 0],
                    ['text' => 'Workshop atau praktikum langsung', 'score' => 3],
                ],
            ],
            [
                'text' => 'Saat ujian praktik, saya merasa:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Lebih tenang jika ada diagram atau gambar panduan', 'score' => 0],
                    ['text' => 'Lebih tenang jika bisa tanya instruksi dulu', 'score' => 0],
                    ['text' => 'Lebih tenang jika ada petunjuk tertulis lengkap', 'score' => 0],
                    ['text' => 'Ini saat saya paling jago, langsung praktik!', 'score' => 3],
                ],
            ],
            [
                'text' => 'Untuk belajar keterampilan baru (menjahit, mengetik, menghitung, dll), saya lebih suka:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Melihat video tutorial dulu', 'score' => 0],
                    ['text' => 'Mendengar penjelasan cara kerjanya', 'score' => 0],
                    ['text' => 'Membaca buku panduan lengkap', 'score' => 0],
                    ['text' => 'Langsung praktik berulang kali', 'score' => 3],
                ],
            ],
            [
                'text' => 'Saat belajar mengoperasikan alat atau mesin (mesin jahit, komputer, kalkulator, dll), saya:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Melihat diagram atau gambar cara pakainya', 'score' => 0],
                    ['text' => 'Mendengar penjelasan cara menggunakannya', 'score' => 0],
                    ['text' => 'Membaca manual atau petunjuk penggunaan', 'score' => 0],
                    ['text' => 'Langsung coba pakai dan eksplorasi fiturnya', 'score' => 3],
                ],
            ],

            // Reading/Writing Questions (6 questions)
            [
                'text' => 'Saat dapat materi pelajaran baru, hal pertama yang saya lakukan:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Cari gambar atau video tentang materi itu', 'score' => 0],
                    ['text' => 'Tanya guru atau teman tentang materi itu', 'score' => 0],
                    ['text' => 'Membaca buku atau artikel tentang materi tersebut', 'score' => 3],
                    ['text' => 'Langsung praktik atau coba contoh soal', 'score' => 0],
                ],
            ],
            [
                'text' => 'Cara saya mempersiapkan ujian:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Melihat kembali slide atau gambar materi', 'score' => 0],
                    ['text' => 'Belajar kelompok dan diskusi', 'score' => 0],
                    ['text' => 'Membaca catatan dan membuat rangkuman tertulis', 'score' => 3],
                    ['text' => 'Mengerjakan latihan soal praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Jenis tugas yang paling saya sukai:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Membuat poster, infografis, atau karya visual', 'score' => 0],
                    ['text' => 'Presentasi lisan di depan kelas', 'score' => 0],
                    ['text' => 'Menulis essay, artikel, atau laporan tertulis', 'score' => 3],
                    ['text' => 'Membuat project atau karya praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Untuk mengingat informasi penting (tanggal, rumus, prosedur), saya:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Membuat catatan visual berwarna', 'score' => 0],
                    ['text' => 'Mengulang-ulang dalam hati atau suara keras', 'score' => 0],
                    ['text' => 'Menulis berulang kali sampai hafal', 'score' => 3],
                    ['text' => 'Praktik langsung menggunakannya', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat membaca buku pelajaran, saya:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Fokus pada gambar, grafik, atau diagram', 'score' => 0],
                    ['text' => 'Membaca dengan suara pelan dalam hati', 'score' => 0],
                    ['text' => 'Membuat catatan atau highlight poin penting', 'score' => 3],
                    ['text' => 'Langsung cari latihan praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat menerima kritik atau saran dari guru tentang tugas, saya lebih suka:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Guru menunjuk atau memberi tanda di tugas saya', 'score' => 0],
                    ['text' => 'Guru menjelaskan secara lisan', 'score' => 0],
                    ['text' => 'Guru menulis komentar detail di tugas saya', 'score' => 3],
                    ['text' => 'Guru memberi contoh praktik yang benar', 'score' => 0],
                ],
            ],
        ];
    }
}

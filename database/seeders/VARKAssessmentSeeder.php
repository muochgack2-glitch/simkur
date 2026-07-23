<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class VARKAssessmentSeeder extends Seeder
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
            'title' => 'Asesmen Gaya Belajar VARK',
            'description' => 'Asesmen diagnostik untuk mengidentifikasi gaya belajar siswa berdasarkan model VARK (Visual, Auditory, Reading/Writing, Kinesthetic)',
            'academic_year_id' => $activeYear->id,
            'semester_id' => $activeSemester->id,
            'target_grades' => ['X', 'XI', 'XII'],
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'created_by' => $admin->id,
        ]);

        $this->command->info('Assessment created: ' . $assessment->title);

        // 24 Questions for VARK Assessment (SMK Context)
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

        $this->command->info('24 VARK questions created successfully!');
    }

    private function getQuestions(): array
    {
        return [
            // Visual Questions (6 questions)
            [
                'text' => 'Saat guru menjelaskan materi baru, saya lebih suka:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Melihat diagram, flowchart, atau gambar di slide presentasi', 'score' => 3],
                    ['text' => 'Mendengarkan penjelasan guru dengan seksama', 'score' => 0],
                    ['text' => 'Membaca handout atau modul yang diberikan', 'score' => 0],
                    ['text' => 'Langsung praktik mengikuti demo guru', 'score' => 0],
                ],
            ],
            [
                'text' => 'Ketika belajar coding atau program baru, saya lebih mudah paham jika:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Melihat video tutorial atau screen recording', 'score' => 3],
                    ['text' => 'Mendengar penjelasan step-by-step', 'score' => 0],
                    ['text' => 'Membaca dokumentasi atau tutorial tertulis', 'score' => 0],
                    ['text' => 'Langsung coba-coba sendiri', 'score' => 0],
                ],
            ],
            [
                'text' => 'Untuk mengingat materi pelajaran, saya biasanya:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Membuat mind map atau diagram warna-warni', 'score' => 3],
                    ['text' => 'Merekam suara penjelasan dan mendengarnya ulang', 'score' => 0],
                    ['text' => 'Menulis rangkuman berkali-kali', 'score' => 0],
                    ['text' => 'Membuat flashcard dan menghafalnya', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat mengerjakan tugas kelompok, kontribusi terbaik saya adalah:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Membuat presentasi visual atau infografis', 'score' => 3],
                    ['text' => 'Menjelaskan konsep ke anggota kelompok', 'score' => 0],
                    ['text' => 'Menulis laporan atau dokumentasi', 'score' => 0],
                    ['text' => 'Mengerjakan bagian praktik atau prototype', 'score' => 0],
                ],
            ],
            [
                'text' => 'Ketika mencari solusi error di program, saya lebih suka:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Lihat screenshot atau video tutorial', 'score' => 3],
                    ['text' => 'Tanya teman atau guru secara langsung', 'score' => 0],
                    ['text' => 'Baca forum atau dokumentasi', 'score' => 0],
                    ['text' => 'Trial and error sampai ketemu', 'score' => 0],
                ],
            ],
            [
                'text' => 'Cara paling efektif buat saya memahami konsep database adalah:',
                'indicator' => 'visual',
                'options' => [
                    ['text' => 'Melihat ERD (Entity Relationship Diagram)', 'score' => 3],
                    ['text' => 'Mendengar penjelasan tentang relasi tabel', 'score' => 0],
                    ['text' => 'Membaca query SQL dan penjelasannya', 'score' => 0],
                    ['text' => 'Langsung praktik buat database', 'score' => 0],
                ],
            ],

            // Auditory Questions (6 questions)
            [
                'text' => 'Saya paling paham materi jika:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Guru menggambar di papan tulis', 'score' => 0],
                    ['text' => 'Guru menjelaskan secara verbal dengan detail', 'score' => 3],
                    ['text' => 'Diberi handout untuk dibaca sendiri', 'score' => 0],
                    ['text' => 'Langsung praktik di lab komputer', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat belajar mandiri, metode yang paling efektif buat saya:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Nonton video animasi pembelajaran', 'score' => 0],
                    ['text' => 'Mendengarkan podcast atau audio pembelajaran', 'score' => 3],
                    ['text' => 'Baca buku atau e-book', 'score' => 0],
                    ['text' => 'Mengerjakan latihan soal', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saya lebih mudah mengingat:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Wajah orang', 'score' => 0],
                    ['text' => 'Nama orang atau suara mereka', 'score' => 3],
                    ['text' => 'Tulisan atau teks yang saya baca', 'score' => 0],
                    ['text' => 'Apa yang saya lakukan bersama mereka', 'score' => 0],
                ],
            ],
            [
                'text' => 'Cara terbaik saya memahami algoritma pemrograman:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Lihat flowchart algoritmanya', 'score' => 0],
                    ['text' => 'Dengarin penjelasan logika step-by-step', 'score' => 3],
                    ['text' => 'Baca pseudocode dan penjelasannya', 'score' => 0],
                    ['text' => 'Langsung coding dan testing', 'score' => 0],
                ],
            ],
            [
                'text' => 'Ketika ada materi sulit, saya biasanya:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Cari video tutorial', 'score' => 0],
                    ['text' => 'Diskusi dengan teman atau tanya guru', 'score' => 3],
                    ['text' => 'Baca ulang buku atau catatan', 'score' => 0],
                    ['text' => 'Coba praktik sampai paham', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat presentasi proyek, saya paling percaya diri di bagian:',
                'indicator' => 'auditory',
                'options' => [
                    ['text' => 'Menampilkan slide atau demo visual', 'score' => 0],
                    ['text' => 'Menjelaskan konsep secara lisan', 'score' => 3],
                    ['text' => 'Menyerahkan laporan tertulis', 'score' => 0],
                    ['text' => 'Demo langsung cara kerja aplikasi', 'score' => 0],
                ],
            ],

            // Kinesthetic Questions (6 questions)
            [
                'text' => 'Saya belajar paling efektif ketika:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Melihat contoh visual', 'score' => 0],
                    ['text' => 'Mendengar penjelasan', 'score' => 0],
                    ['text' => 'Membaca materi', 'score' => 0],
                    ['text' => 'Langsung praktik dan mencoba sendiri', 'score' => 3],
                ],
            ],
            [
                'text' => 'Cara terbaik saya memahami cara kerja hardware komputer:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Lihat diagram komponen komputer', 'score' => 0],
                    ['text' => 'Dengarin penjelasan fungsi tiap komponen', 'score' => 0],
                    ['text' => 'Baca spesifikasi dan manual', 'score' => 0],
                    ['text' => 'Bongkar pasang komputer langsung', 'score' => 3],
                ],
            ],
            [
                'text' => 'Saat belajar jaringan komputer, saya lebih suka:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Lihat topologi jaringan', 'score' => 0],
                    ['text' => 'Dengarin cara kerja protokol', 'score' => 0],
                    ['text' => 'Baca teori OSI Layer', 'score' => 0],
                    ['text' => 'Praktik crimping kabel dan setting router', 'score' => 3],
                ],
            ],
            [
                'text' => 'Metode belajar yang paling cocok untuk saya:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Slide presentasi dengan gambar menarik', 'score' => 0],
                    ['text' => 'Webinar atau podcast edukatif', 'score' => 0],
                    ['text' => 'E-book atau artikel lengkap', 'score' => 0],
                    ['text' => 'Workshop atau praktikum langsung', 'score' => 3],
                ],
            ],
            [
                'text' => 'Saat ujian praktik, saya merasa:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Lebih baik kalau ada gambar atau diagram', 'score' => 0],
                    ['text' => 'Lebih baik kalau bisa tanya-tanya dulu', 'score' => 0],
                    ['text' => 'Lebih baik kalau dikasih soal tertulis lengkap', 'score' => 0],
                    ['text' => 'Ini saat saya paling jago, hands-on!', 'score' => 3],
                ],
            ],
            [
                'text' => 'Untuk memahami konsep OOP (Object Oriented Programming), saya:',
                'indicator' => 'kinesthetic',
                'options' => [
                    ['text' => 'Lihat diagram class dan relationship', 'score' => 0],
                    ['text' => 'Dengarin penjelasan konsep inheritance, polymorphism', 'score' => 0],
                    ['text' => 'Baca dokumentasi dan contoh code', 'score' => 0],
                    ['text' => 'Langsung bikin class dan coba sendiri', 'score' => 3],
                ],
            ],

            // Reading/Writing Questions (6 questions)
            [
                'text' => 'Saat dapat materi baru, hal pertama yang saya lakukan:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Cari video atau gambar tentang topik itu', 'score' => 0],
                    ['text' => 'Tanya guru atau teman', 'score' => 0],
                    ['text' => 'Baca buku atau artikel tentang topik tersebut', 'score' => 3],
                    ['text' => 'Langsung coba praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Cara saya mempersiapkan ujian:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Review slide presentasi guru', 'score' => 0],
                    ['text' => 'Study group dengan teman', 'score' => 0],
                    ['text' => 'Baca catatan dan buat rangkuman', 'score' => 3],
                    ['text' => 'Kerjain latihan soal praktik', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saya paling suka tugas berupa:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Membuat poster atau infografis', 'score' => 0],
                    ['text' => 'Presentasi lisan di depan kelas', 'score' => 0],
                    ['text' => 'Menulis essay, artikel, atau laporan', 'score' => 3],
                    ['text' => 'Membuat project atau prototype', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat belajar bahasa pemrograman baru, saya:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Nonton video tutorial', 'score' => 0],
                    ['text' => 'Ikut bootcamp atau kelas online', 'score' => 0],
                    ['text' => 'Baca dokumentasi resmi dan contoh code', 'score' => 3],
                    ['text' => 'Langsung bikin project mini', 'score' => 0],
                ],
            ],
            [
                'text' => 'Untuk mengingat syntax code, saya biasanya:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Lihat cheat sheet bergambar', 'score' => 0],
                    ['text' => 'Hafal dengan cara dibaca berulang', 'score' => 0],
                    ['text' => 'Tulis berulang kali di notepad', 'score' => 3],
                    ['text' => 'Sering-sering praktik coding', 'score' => 0],
                ],
            ],
            [
                'text' => 'Saat dapat feedback dari guru tentang tugas, saya lebih suka:',
                'indicator' => 'reading_writing',
                'options' => [
                    ['text' => 'Lihat highlight atau tanda di tugas saya', 'score' => 0],
                    ['text' => 'Dengarin penjelasan guru secara langsung', 'score' => 0],
                    ['text' => 'Baca komentar tertulis yang detail', 'score' => 3],
                    ['text' => 'Langsung revisi dan praktik lagi', 'score' => 0],
                ],
            ],
        ];
    }
}

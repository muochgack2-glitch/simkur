<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 ">
            Asesmen Gaya Belajar
        </h1>
        <p class="mt-2 text-gray-800 ">
            Isi asesmen untuk mengetahui gaya belajar Anda
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    
                    @if (session()->has('success'))
                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 " role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 " role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Daftar Asesmen Tersedia</h3>
                        <p class="mt-1 text-sm text-gray-800 ">
                            Isi asesmen untuk mengetahui gaya belajar Anda
                        </p>
                    </div>

                    @if($assessments->isEmpty())
                        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center ">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 ">Tidak ada asesmen aktif</h3>
                            <p class="mt-1 text-sm text-gray-700 ">
                                Belum ada asesmen yang tersedia untuk Anda saat ini.
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($assessments as $assessment)
                                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm ">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <h4 class="text-lg font-semibold text-gray-900 ">
                                                    {{ $assessment->title }}
                                                </h4>
                                                @if($assessment->is_completed)
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800 ">
                                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        Sudah Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-sm font-medium text-yellow-800 ">
                                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                        Belum Dikerjakan
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="mt-2 text-sm text-gray-800 ">
                                                {{ $assessment->description }}
                                            </p>

                                            <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-700 ">
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $assessment->start_date->format('d M Y') }} - {{ $assessment->end_date->format('d M Y') }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ $assessment->actual_question_count ?? $assessment->total_questions }} Pertanyaan
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    ± 10-15 menit
                                                </div>
                                            </div>

                                            @if($assessment->is_completed)
                                                <div class="mt-3 text-sm text-gray-700 ">
                                                    Diselesaikan pada: {{ $assessment->completion_date->format('d M Y H:i') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="ml-4">
                                            @if($assessment->is_completed)
                                                <a href="{{ route('student.assessment.result', $assessment->id) }}" 
                                                   class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 ">
                                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    Lihat Hasil
                                                </a>
                                            @else
                                                <a href="{{ route('student.assessment.take', $assessment->id) }}" 
                                                   class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 ">
                                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                    Mulai Asesmen
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SEKSI ASESMEN GURU (QUIZ) — semua status, urut tanggal+jam   --}}
    {{-- ============================================================ --}}
    @if($quizAssessments->isNotEmpty())
        <div class="mt-8">
            <div class="mb-4 flex items-center gap-3">
                <h2 class="text-lg font-bold text-gray-800">Soal / Kuis dari Guru</h2>
                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                    {{ $quizAssessments->count() }} asesmen
                </span>
            </div>
            <div class="space-y-4">
                @foreach($quizAssessments as $quiz)
                    @php
                        $statusConfig = match($quiz->student_status) {
                            'upcoming'         => ['icon'=>'🔒','badge'=>'Belum Dimulai','bg'=>'bg-yellow-100','text'=>'text-yellow-800','border'=>'border-yellow-200'],
                            'open'             => ['icon'=>'🟢','badge'=>'Bisa Dikerjakan','bg'=>'bg-green-100','text'=>'text-green-800','border'=>'border-green-200'],
                            'in_progress'      => ['icon'=>'⏳','badge'=>'Sedang Dikerjakan','bg'=>'bg-blue-100','text'=>'text-blue-800','border'=>'border-blue-200'],
                            'submitted'        => ['icon'=>'✅','badge'=>'Selesai','bg'=>'bg-green-100','text'=>'text-green-800','border'=>'border-green-200'],
                            'closed_submitted' => ['icon'=>'✅','badge'=>'Selesai','bg'=>'bg-gray-100','text'=>'text-gray-600','border'=>'border-gray-200'],
                            'closed_missed'    => ['icon'=>'🔴','badge'=>'Ditutup','bg'=>'bg-red-50','text'=>'text-red-700','border'=>'border-red-200'],
                            default            => ['icon'=>'📋','badge'=>'—','bg'=>'bg-gray-100','text'=>'text-gray-600','border'=>'border-gray-200'],
                        };
                    @endphp
                    <div class="rounded-xl border {{ $statusConfig['border'] }} bg-white p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-gray-800">{{ $quiz->title }}</h3>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        {{ $statusConfig['icon'] }} {{ $statusConfig['badge'] }}
                                    </span>
                                </div>
                                @if($quiz->description)
                                    <p class="text-xs text-gray-500 mb-2">{{ Str::limit($quiz->description, 100) }}</p>
                                @endif
                                <div class="flex flex-wrap gap-4 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $quiz->start_date->translatedFormat('d M Y') }}
                                        {{ $quiz->start_time ? substr($quiz->start_time,0,5) : '' }}
                                        —
                                        {{ $quiz->end_date->translatedFormat('d M Y') }}
                                        {{ $quiz->end_time ? substr($quiz->end_time,0,5) : '' }}
                                    </span>
                                    <span>📋 {{ $quiz->questions_count }} soal</span>
                                    <span>👤 {{ $quiz->creator->name ?? '-' }}</span>
                                </div>
                                {{-- Countdown untuk asesmen upcoming --}}
                                @if($quiz->student_status === 'upcoming')
                                    <div id="countdown-quiz-{{ $quiz->id }}"
                                        class="mt-2 text-sm font-bold text-yellow-700"
                                        data-target="{{ $quiz->getOpenDatetime()->toIso8601String() }}">
                                    </div>
                                @endif
                                {{-- Nilai jika sudah selesai --}}
                                @if(in_array($quiz->student_status, ['submitted','closed_submitted']) && $quiz->latest_session)
                                    <div class="mt-2 text-sm">
                                        <span class="font-semibold text-blue-700">
                                            Nilai: {{ $quiz->latest_session->getScorePercentage() }}%
                                        </span>
                                        @if($quiz->latest_session->needsManualGrading())
                                            <span class="text-xs text-orange-500 ml-1">(menunggu penilaian esai)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="shrink-0">
                                @if($quiz->student_status === 'upcoming')
                                    <a href="{{ route('student.assessment.preview', $quiz->id) }}" wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-800 hover:bg-yellow-100 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat Soal
                                    </a>
                                @elseif($quiz->student_status === 'open' || $quiz->student_status === 'in_progress')
                                    <a href="{{ route('student.assessment.quiz', $quiz->id) }}" wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                                        {{ $quiz->student_status === 'in_progress' ? '▶ Lanjutkan' : '✏️ Kerjakan' }}
                                    </a>
                                @elseif(in_array($quiz->student_status, ['submitted','closed_submitted']))
                                    <a href="{{ route('student.assessment.result', $quiz->id) }}" wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                        📄 Lihat Hasil
                                    </a>
                                @elseif($quiz->student_status === 'closed_missed')
                                    <span class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-600">
                                        🔴 Ditutup
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Countdown script untuk semua quiz --}}
    @if($quizAssessments->where('student_status','upcoming')->isNotEmpty())
    <script>
    (function() {
        function startCountdown(el) {
            const target = new Date(el.dataset.target).getTime();
            function update() {
                const diff = target - Date.now();
                if (diff <= 0) {
                    el.innerHTML = '<a href="." class="text-blue-600 underline">Asesmen sudah dibuka — refresh halaman</a>';
                    return;
                }
                const d = Math.floor(diff / 86400000);
                const h = Math.floor((diff % 86400000) / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                el.textContent = 'Dimulai dalam: ' + (d > 0 ? d + ' hari ' : '')
                    + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                setTimeout(update, 1000);
            }
            update();
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[id^="countdown-quiz-"]').forEach(startCountdown);
        });
        document.addEventListener('livewire:navigated', function () {
            document.querySelectorAll('[id^="countdown-quiz-"]').forEach(startCountdown);
        });
    })();
    </script>
    @endif
</div>

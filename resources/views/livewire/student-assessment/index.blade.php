<div>
    @if(session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Asesmen dan Kuis</h1>
        <p class="mt-1 text-sm text-gray-500">Kerjakan asesmen dan kuis dari sekolah maupun guru Anda</p>
    </div>

    {{-- SECTION 1: Kuis dari Guru --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-3">
            <h2 class="text-base font-semibold text-gray-700">Kuis dari Guru</h2>
            @if($quizAssessments->isNotEmpty())
                <span class="rounded-full bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5">{{ $quizAssessments->count() }} kuis</span>
            @endif
        </div>

        @if($quizAssessments->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center">
                <p class="text-sm text-gray-400">Belum ada kuis dari guru saat ini</p>
            </div>
        @else
            @php
                $groupedQuizzes = $quizAssessments->groupBy(fn($q) => $q->start_date->format('Y-m-d'));
                $activeStatuses = ['open', 'in_progress', 'upcoming'];
            @endphp
            <div class="space-y-2">
            @foreach($groupedQuizzes as $dateKey => $dayQuizzes)
                @php
                    $hasActive = $dayQuizzes->contains(fn($q) => in_array($q->student_status, $activeStatuses));
                    $firstQuiz = $dayQuizzes->first();
                    $dayLabel  = $firstQuiz->start_date->translatedFormat('l, d F Y');
                    $openCount = $dayQuizzes->where('student_status', 'open')->count()
                               + $dayQuizzes->where('student_status', 'in_progress')->count();
                @endphp
                <div x-data="{ open: {{ $hasActive ? 'true' : 'false' }} }"
                     class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

                    {{-- Accordion Header --}}
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-2">
                            <svg :class="open ? 'rotate-90' : ''"
                                 class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">{{ $dayLabel }}</span>
                            <span class="rounded-full bg-gray-100 text-gray-500 text-xs px-2 py-0.5">
                                {{ $dayQuizzes->count() }} kuis
                            </span>
                            @if($openCount > 0)
                                <span class="rounded-full bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 animate-pulse">
                                    {{ $openCount }} aktif
                                </span>
                            @endif
                        </div>
                        <svg :class="open ? 'rotate-180' : ''"
                             class="w-4 h-4 text-gray-400 transition-transform duration-200"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Accordion Body --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="divide-y divide-gray-100 border-t border-gray-100">
                @foreach($dayQuizzes as $quiz)
                    @php
                        $cfg = match($quiz->student_status) {
                            'upcoming'         => ['badge' => 'Akan Dimulai',      'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                            'open'             => ['badge' => 'Bisa Dikerjakan',   'bg' => 'bg-green-100',  'text' => 'text-green-800'],
                            'in_progress'      => ['badge' => 'Sedang Dikerjakan', 'bg' => 'bg-blue-100',   'text' => 'text-blue-800'],
                            'submitted'        => ['badge' => 'Sudah Dikumpul',    'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                            'closed_submitted' => ['badge' => 'Selesai',           'bg' => 'bg-gray-100',   'text' => 'text-gray-600'],
                            'closed_missed'    => ['badge' => 'Tidak Dikerjakan',  'bg' => 'bg-red-100',    'text' => 'text-red-700'],
                            default            => ['badge' => $quiz->student_status,'bg' => 'bg-gray-100',  'text' => 'text-gray-600'],
                        };
                    @endphp
                    <div class="p-4 flex flex-wrap items-center justify-between gap-3 hover:bg-gray-50 transition">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h3 class="text-sm font-semibold text-gray-800">{{ $quiz->title }}</h3>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                    {{ $cfg['badge'] }}
                                </span>
                            </div>
                            @if($quiz->description)
                                <p class="text-xs text-gray-400 mb-1">{{ Str::limit($quiz->description, 80) }}</p>
                            @endif
                            <div class="flex flex-wrap gap-3 text-xs text-gray-400 mt-1">
                                <span>
                                    {{ $quiz->start_date->translatedFormat('d M Y') }}
                                    {{ $quiz->start_time ? substr($quiz->start_time, 0, 5) : '' }}
                                    &mdash;
                                    {{ $quiz->end_date->translatedFormat('d M Y') }}
                                    {{ $quiz->end_time ? substr($quiz->end_time, 0, 5) : '' }}
                                </span>
                                <span class="text-blue-600 font-medium">{{ $quiz->subject->name ?? '—' }}</span>
                                <span>&bull;</span>
                                <span>{{ $quiz->questions_count }} soal</span>
                                <span>{{ $quiz->teacher->name ?? $quiz->creator->name ?? '-' }}</span>
                            </div>
                            @if($quiz->student_status === 'upcoming')
                                <div id="countdown-quiz-{{ $quiz->id }}"
                                    class="mt-1.5 text-xs font-bold text-yellow-700"
                                    data-target="{{ $quiz->getOpenDatetime()->toIso8601String() }}">
                                </div>
                            @endif
                            @if(in_array($quiz->student_status, ['submitted', 'closed_submitted']) && $quiz->latest_session)
                                <div class="mt-1.5 text-xs">
                                    <span class="font-semibold text-blue-700">Nilai: {{ (int)round($quiz->latest_session->total_score ?? (($quiz->latest_session->auto_score ?? 0) + ($quiz->latest_session->manual_score ?? 0))) }}</span>
                                    @if($quiz->latest_session->needsManualGrading())
                                        <span class="text-orange-500 ml-1">(menunggu penilaian esai)</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="shrink-0">
                            @if($quiz->student_status === 'upcoming')
                                <a href="{{ route('student.assessment.preview', $quiz->id) }}" wire:navigate
                                   class="inline-flex items-center rounded-lg border border-yellow-300 bg-yellow-50 px-3 py-2 text-xs font-medium text-yellow-800 hover:bg-yellow-100 transition">
                                    Lihat Soal
                                </a>
                            @elseif($quiz->student_status === 'open')
                                <a href="{{ route('student.assessment.quiz', $quiz->id) }}" wire:navigate
                                   class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                                    Kerjakan
                                </a>
                            @elseif($quiz->student_status === 'in_progress' && $quiz->status === 'ongoing')
                                {{-- Quiz masih terbuka dan siswa sedang mengerjakan --}}
                                <a href="{{ route('student.assessment.quiz', $quiz->id) }}" wire:navigate
                                   class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                                    Lanjutkan
                                </a>
                            @elseif($quiz->student_status === 'in_progress' && $quiz->status !== 'ongoing')
                                {{-- Quiz sudah tutup, tapi siswa tidak sempat submit --}}
                                <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-400">
                                    Waktu Habis
                                </span>
                            @elseif(in_array($quiz->student_status, ['submitted', 'closed_submitted']))
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('student.assessment.result', $quiz->id) }}" wire:navigate
                                       class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                        Lihat Hasil
                                    </a>
                                    @if($quiz->allow_retry && $quiz->student_status === 'submitted' && $quiz->status === 'ongoing')
                                        <a href="{{ route('student.assessment.quiz', $quiz->id) }}?retry=1" wire:navigate
                                           class="inline-flex items-center rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700 transition">
                                            🔄 Kerjakan Ulang
                                        </a>
                                    @endif
                                </div>
                            @elseif($quiz->student_status === 'closed_missed')
                                <span class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-600">
                                    Ditutup
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
                    </div>{{-- /accordion body --}}
                </div>{{-- /accordion container --}}
            @endforeach
            </div>
        @endif
    </div>

    {{-- SECTION 2: Asesmen Gaya Belajar — tampil hanya jika ada --}}
    @if($assessments->isNotEmpty())
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
            <h2 class="text-base font-semibold text-gray-700">Asesmen Gaya Belajar</h2>
            <span class="rounded-full bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-0.5">
                {{ $assessments->count() }} asesmen
            </span>
        </div>
        <div class="space-y-3">
            @foreach($assessments as $assessment)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h4 class="text-sm font-semibold text-gray-900">{{ $assessment->title }}</h4>
                            @if($assessment->is_completed)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Sudah Selesai</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Belum Dikerjakan</span>
                            @endif
                        </div>
                        @if($assessment->description)
                            <p class="text-xs text-gray-400 mb-2">{{ $assessment->description }}</p>
                        @endif
                        <div class="flex flex-wrap gap-3 text-xs text-gray-400">
                            <span>{{ $assessment->actual_question_count ?? $assessment->questions()->count() }} soal</span>
                            @if($assessment->is_completed && $assessment->completion_date)
                                <span>Selesai {{ $assessment->completion_date->translatedFormat('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="shrink-0">
                        @if($assessment->is_completed)
                            <a href="{{ route('student.assessment.result', $assessment->id) }}" wire:navigate
                               class="inline-flex rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Lihat Hasil
                            </a>
                        @else
                            <a href="{{ route('student.assessment.take', $assessment->id) }}" wire:navigate
                               class="inline-flex rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-purple-700 transition">
                                Kerjakan
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Kosong semua --}}
    @if($assessments->isEmpty() && $quizAssessments->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-200 bg-white p-10 text-center">
            <p class="text-sm font-medium text-gray-500">Belum ada asesmen atau kuis untuk Anda saat ini</p>
            <p class="text-xs mt-1 text-gray-400">Silakan cek kembali nanti</p>
        </div>
    @endif

    {{-- Countdown script --}}
    @if($quizAssessments->where('student_status', 'upcoming')->isNotEmpty())
    <script>
    (function() {
        function startCountdown(el) {
            const target = new Date(el.dataset.target).getTime();
            function update() {
                const diff = target - Date.now();
                if (diff <= 0) {
                    el.innerHTML = '<a href="." class="text-blue-600 underline">Asesmen sudah dibuka, refresh halaman</a>';
                    return;
                }
                const d = Math.floor(diff / 86400000);
                const h = Math.floor((diff % 86400000) / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                el.textContent = 'Dimulai dalam: ' + (d > 0 ? d + ' hari ' : '')
                    + String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
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
<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Hasil Kuis</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $assessment->title }}</p>
    </div>

    @if(!$session)
        {{-- Sesi tidak ditemukan --}}
        <div class="mx-auto max-w-lg rounded-xl border border-red-200 bg-red-50 p-8 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h2 class="mt-4 text-lg font-bold text-red-700">Hasil Tidak Ditemukan</h2>
            <p class="mt-2 text-sm text-red-600">Jawaban Anda belum tercatat. Pastikan Anda sudah mengumpulkan jawaban.</p>
            <a href="{{ route('student.assessment.index') }}" wire:navigate
               class="mt-6 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                ← Kembali ke Daftar Asesmen
            </a>
        </div>
    @else
        <div class="mx-auto max-w-2xl space-y-4 sm:px-0">

            @if(session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            {{-- Score Card --}}
            @php
                $scorePercent    = $session->getScorePercentage();
                $needsGrading    = $session->needsManualGrading();
                $autoScore       = $session->auto_score ?? 0;
                $manualScore     = $session->manual_score;
                $totalScore      = $session->total_score;
                $maxScore        = $session->max_possible_score ?? 0;

                $grade = match(true) {
                    $scorePercent >= 90 => ['label' => 'Sangat Baik',  'color' => 'text-green-600',  'bg' => 'bg-green-50',  'border' => 'border-green-200'],
                    $scorePercent >= 75 => ['label' => 'Baik',         'color' => 'text-blue-600',   'bg' => 'bg-blue-50',   'border' => 'border-blue-200'],
                    $scorePercent >= 60 => ['label' => 'Cukup',        'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200'],
                    default             => ['label' => 'Perlu Belajar','color' => 'text-red-600',    'bg' => 'bg-red-50',    'border' => 'border-red-200'],
                };
            @endphp

            <div class="rounded-xl border {{ $grade['border'] }} {{ $grade['bg'] }} p-6 shadow-sm text-center">
                @if($needsGrading)
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-orange-700">Menunggu Penilaian Guru</span>
                    </div>
                    <p class="text-4xl font-bold text-gray-400">—</p>
                    <p class="mt-1 text-sm text-gray-500">Skor esai/file belum dinilai</p>
                @else
                    <p class="text-5xl font-bold {{ $grade['color'] }}">{{ $scorePercent }}%</p>
                    <p class="mt-1 text-sm font-medium text-gray-600">{{ $grade['label'] }}</p>
                @endif
            </div>

            {{-- Detail Skor --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Rincian Nilai</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Skor Otomatis (PG / B-S / Menjodohkan)</span>
                        <span class="font-semibold text-gray-800">{{ $autoScore }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Skor Manual (Esai / File)</span>
                        @if($manualScore !== null)
                            <span class="font-semibold text-gray-800">{{ $manualScore }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-700">
                                Belum dinilai
                            </span>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-sm font-semibold">
                        <span class="text-gray-700">Total</span>
                        <span class="text-gray-800">
                            {{ $totalScore ?? '—' }} / {{ $maxScore }}
                        </span>
                    </div>
                </div>

                {{-- Progress bar --}}
                @if(!$needsGrading && $maxScore > 0)
                    <div class="mt-4">
                        <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-blue-600 transition-all"
                                 style="width: {{ $scorePercent }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Info Waktu --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Info Pengerjaan</h3>
                <div class="space-y-2 text-sm text-gray-500">
                    <div class="flex justify-between">
                        <span>Mulai</span>
                        <span class="text-gray-700">{{ $session->started_at?->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dikumpulkan</span>
                        <span class="text-gray-700">{{ $session->submitted_at?->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    @if($session->started_at && $session->submitted_at)
                        <div class="flex justify-between">
                            <span>Durasi</span>
                            <span class="text-gray-700">{{ gmdate('H:i:s', $session->started_at->diffInSeconds($session->submitted_at)) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tombol kembali --}}
            <div class="flex justify-center pt-2">
                <a href="{{ route('student.assessment.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    ← Kembali ke Daftar Asesmen
                </a>
            </div>

        </div>
    @endif
</div>
<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Hasil Kuis</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $assessment->title }}</p>
    </div>

    @if(!$session)
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
        @php
            $scorePercent = $session->getScorePercentage();
            $needsGrading = $session->needsManualGrading();
            $autoScore    = $session->auto_score ?? 0;
            $manualScore  = $session->manual_score;
            $totalScore   = $session->total_score;
            $maxScore     = $session->max_possible_score ?? 0;

            $gradeConfig = match(true) {
                $scorePercent >= 90 => ['label' => 'Sangat Baik',   'color' => 'text-green-600',  'ring' => 'ring-green-200',  'bg' => 'bg-green-50'],
                $scorePercent >= 75 => ['label' => 'Baik',          'color' => 'text-blue-600',   'ring' => 'ring-blue-200',   'bg' => 'bg-blue-50'],
                $scorePercent >= 60 => ['label' => 'Cukup',         'color' => 'text-yellow-600', 'ring' => 'ring-yellow-200', 'bg' => 'bg-yellow-50'],
                default             => ['label' => 'Perlu Belajar', 'color' => 'text-red-600',    'ring' => 'ring-red-200',    'bg' => 'bg-red-50'],
            };
        @endphp

        <div class="mx-auto max-w-2xl space-y-4">

            {{-- ✅ HERO: Selesai --}}
            <div class="rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 p-8 text-center shadow-sm">
                {{-- Ikon centang besar --}}
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 ring-4 ring-green-200">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-green-800">Jawaban Berhasil Dikumpulkan!</h2>
                <p class="mt-1 text-sm text-green-700">
                    Kuis <span class="font-semibold">{{ $assessment->title }}</span> telah selesai dikerjakan.
                </p>

                @if($needsGrading)
                    {{-- Ada esai — tampilkan skor sementara + info menunggu --}}
                    <div class="mt-5 inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-1.5 text-sm font-medium text-amber-800 ring-1 ring-amber-200">
                        <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Sebagian soal esai sedang dinilai guru
                    </div>
                    <p class="mt-3 text-xs text-green-600">Hasil akhir akan tersedia setelah guru menyelesaikan penilaian</p>
                @else
                    {{-- Semua auto-scored — tampilkan nilai langsung --}}
                    <div class="mt-5">
                        <span class="text-5xl font-extrabold {{ $gradeConfig['color'] }}">{{ $scorePercent }}%</span>
                        <p class="mt-1 text-sm font-semibold {{ $gradeConfig['color'] }}">{{ $gradeConfig['label'] }}</p>
                    </div>
                    {{-- Progress bar --}}
                    @if($maxScore > 0)
                        <div class="mx-auto mt-4 max-w-xs">
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-green-100">
                                <div class="h-full rounded-full bg-green-500 transition-all duration-700"
                                     style="width: {{ $scorePercent }}%"></div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Rincian Nilai --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Rincian Nilai
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Otomatis <span class="text-xs text-gray-400">(PG / B-S / Menjodohkan)</span></span>
                        <span class="font-semibold text-gray-800">{{ number_format($autoScore, 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Esai / File <span class="text-xs text-gray-400">(dinilai guru)</span></span>
                        @if($manualScore !== null)
                            <span class="font-semibold text-gray-800">{{ number_format($manualScore, 0) }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu penilaian
                            </span>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Total</span>
                        <span class="text-base font-bold text-gray-800">
                            {{ $totalScore !== null ? number_format($totalScore, 0) : '—' }}
                            <span class="text-sm font-normal text-gray-400">/ {{ number_format($maxScore, 0) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info Pengerjaan --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Info Pengerjaan
                </h3>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-400 mb-1">Mulai</p>
                        <p class="text-xs font-semibold text-gray-700">{{ $session->started_at?->translatedFormat('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $session->started_at?->format('H:i') }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-400 mb-1">Dikumpulkan</p>
                        <p class="text-xs font-semibold text-gray-700">{{ $session->submitted_at?->translatedFormat('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $session->submitted_at?->format('H:i') }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3">
                        <p class="text-xs text-blue-400 mb-1">Durasi</p>
                        @if($session->started_at && $session->submitted_at)
                            <p class="text-sm font-bold text-blue-700">{{ gmdate('H:i:s', $session->started_at->diffInSeconds($session->submitted_at)) }}</p>
                        @else
                            <p class="text-sm font-bold text-gray-400">—</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="flex justify-center pt-2 pb-4">
                <a href="{{ route('student.assessment.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-blue-700 active:scale-95 transition-all">
                    ← Kembali ke Daftar Asesmen
                </a>
            </div>

        </div>
    @endif
</div>
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
                &larr; Kembali ke Daftar Asesmen
            </a>
        </div>
    @else
        @php
            $needsGrading = $session->needsManualGrading();
            $autoScore    = $session->auto_score ?? 0;
            $manualScore  = $session->manual_score;
            $totalScore   = $session->total_score;
            $maxScore     = $session->max_possible_score ?? 0;
            $displayScore = (int) round($totalScore ?? ($autoScore + ($manualScore ?? 0)));

            // Predikat berdasarkan angka mentah vs max
            $pct = $maxScore > 0 ? ($displayScore / $maxScore) * 100 : 0;
            $gradeConfig = match(true) {
                $pct >= 90 => ['label' => 'Sangat Baik',   'color' => 'text-emerald-600', 'badgeBg' => 'bg-emerald-100', 'badgeText' => 'text-emerald-800', 'badgeRing' => 'ring-emerald-200'],
                $pct >= 75 => ['label' => 'Baik',          'color' => 'text-blue-600',    'badgeBg' => 'bg-blue-100',    'badgeText' => 'text-blue-800',    'badgeRing' => 'ring-blue-200'],
                $pct >= 60 => ['label' => 'Cukup',         'color' => 'text-yellow-600',  'badgeBg' => 'bg-yellow-100',  'badgeText' => 'text-yellow-800',  'badgeRing' => 'ring-yellow-200'],
                default    => ['label' => 'Perlu Belajar', 'color' => 'text-red-600',     'badgeBg' => 'bg-red-100',     'badgeText' => 'text-red-800',     'badgeRing' => 'ring-red-200'],
            };
        @endphp

        <div class="mx-auto max-w-2xl space-y-4">

            {{-- HERO --}}
            <div class="rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 p-8 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 ring-4 ring-green-200">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-green-800">Jawaban Berhasil Dikumpulkan!</h2>
                <p class="mt-1 text-sm text-green-700">
                    Kuis <span class="font-semibold">{{ $assessment->title }}</span> telah selesai dikerjakan.
                </p>

                <div class="mt-5">
                    @if($needsGrading)
                        <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-1.5 text-sm font-medium text-amber-800 ring-1 ring-amber-200">
                            <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Sebagian soal esai sedang dinilai guru
                        </div>
                        <p class="mt-2 text-xs text-green-600">Hasil akhir akan tersedia setelah guru menyelesaikan penilaian</p>
                    @else
                        @if($assessment->show_score)
                            {{-- Angka mentah besar — hanya tampil jika guru aktifkan show_score --}}
                            <div class="inline-flex flex-col items-center gap-1">
                                <span class="text-6xl font-extrabold {{ $gradeConfig['color'] }}">{{ $displayScore }}</span>
                                @if($maxScore > 0)
                                    <span class="text-sm text-gray-500">dari {{ (int)$maxScore }}</span>
                                @endif
                                <span class="mt-1 inline-flex items-center rounded-full px-3 py-0.5 text-xs font-semibold ring-1
                                    {{ $gradeConfig['badgeBg'] }} {{ $gradeConfig['badgeText'] }} {{ $gradeConfig['badgeRing'] }}">
                                    {{ $gradeConfig['label'] }}
                                </span>
                            </div>
                        @else
                            {{-- Nilai disembunyikan --}}
                            <div class="inline-flex flex-col items-center gap-2">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 ring-4 ring-green-200">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Jawaban berhasil disimpan</p>
                                <p class="text-xs text-gray-400">Nilai akan diumumkan oleh guru</p>
                            </div>
                        @endif
                    @endif
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
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-center">
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
                    <div class="rounded-lg bg-blue-50 p-3 col-span-2 sm:col-span-1">
                        <p class="text-xs text-blue-400 mb-1">Durasi</p>
                        @if($session->started_at && $session->submitted_at)
                            <p class="text-sm font-bold text-blue-700">{{ gmdate('H:i:s', $session->started_at->diffInSeconds($session->submitted_at)) }}</p>
                        @else
                            <p class="text-sm font-bold text-gray-400">&mdash;</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="flex justify-center pt-2 pb-4">
                <a href="{{ route('student.assessment.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-blue-700 active:scale-95 transition-all">
                    &larr; Kembali ke Daftar Asesmen
                </a>
            </div>

        </div>
    @endif
</div>
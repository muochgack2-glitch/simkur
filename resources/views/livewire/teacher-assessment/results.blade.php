<div>
    {{-- Flash messages --}}
    @if(session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700 flex items-center gap-2">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('info'))
        <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700 flex items-center gap-2">
            <svg class="h-5 w-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.assessment.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Hasil Asesmen</h1>
                <p class="text-sm text-gray-500">{{ $assessment->title }}</p>
            </div>
        </div>
        <a href="{{ route('teacher.assessment.questions', $assessment->id) }}" wire:navigate
           class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 transition">
            Kelola Soal
        </a>
    </div>

    {{-- Force Submit All Banner --}}
    @if($isClosed && $totalInProgress > 0)
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="rounded-full bg-red-100 p-2 flex-shrink-0">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-red-800">Waktu ujian telah berakhir</p>
                    <p class="text-sm text-red-600 mt-0.5">
                        <span class="font-bold">{{ $totalInProgress }} siswa</span> masih berstatus "Mengerjakan".
                        Anda bisa force submit semua sekaligus &mdash; jawaban yang sudah tersimpan akan dinilai otomatis.
                    </p>
                </div>
            </div>
            <button
                wire:click="forceSubmitAll"
                wire:confirm="Yakin ingin force submit SEMUA siswa yang masih mengerjakan? Jawaban yang sudah tersimpan akan dinilai, soal yang belum dijawab mendapat skor 0."
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition disabled:opacity-50 flex-shrink-0"
            >
                <svg wire:loading.remove wire:target="forceSubmitAll" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <svg wire:loading wire:target="forceSubmitAll" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Force Submit Semua
            </button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $totalSubmitted }}</p>
            <p class="text-sm text-gray-500 mt-1">Sudah Mengumpulkan</p>
        </div>
        <div class="rounded-xl border {{ $totalInProgress > 0 ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-white' }} p-5 shadow-sm text-center">
            <p class="text-3xl font-bold {{ $totalInProgress > 0 ? 'text-yellow-600' : 'text-gray-800' }}">{{ $totalInProgress }}</p>
            <p class="text-sm {{ $totalInProgress > 0 ? 'text-yellow-500' : 'text-gray-500' }} mt-1">Sedang Mengerjakan</p>
        </div>
        <div class="rounded-xl border {{ $needsGrading > 0 ? 'border-orange-200 bg-orange-50' : 'border-gray-200 bg-white' }} p-5 shadow-sm text-center">
            <p class="text-3xl font-bold {{ $needsGrading > 0 ? 'text-orange-600' : 'text-gray-800' }}">{{ $needsGrading }}</p>
            <p class="text-sm {{ $needsGrading > 0 ? 'text-orange-500' : 'text-gray-500' }} mt-1">Menunggu Penilaian Guru</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $avgScore }}</p>
            <p class="text-sm text-gray-500 mt-1">Rata-rata Nilai</p>
        </div>
    </div>

    {{-- Filter status --}}
    <div class="mb-4 flex gap-2">
        <button wire:click="$set('filterStatus','')"
            class="rounded-lg px-3 py-1.5 text-xs font-medium border transition
                {{ $filterStatus === '' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50' }}">
            Semua
        </button>
        <button wire:click="$set('filterStatus','submitted')"
            class="rounded-lg px-3 py-1.5 text-xs font-medium border transition
                {{ $filterStatus === 'submitted' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50' }}">
            Sudah Kumpul
        </button>
        <button wire:click="$set('filterStatus','in_progress')"
            class="rounded-lg px-3 py-1.5 text-xs font-medium border transition
                {{ $filterStatus === 'in_progress' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50' }}">
            Sedang Mengerjakan
        </button>
    </div>

    {{-- Tabel hasil --}}
    @if($sessions->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
            <p class="mt-3 text-gray-500">Belum ada siswa yang mengerjakan asesmen ini.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu Kumpul</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Nilai</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sessions as $session)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $session->student->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $session->student->nis ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($session->isSubmitted())
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">&check; Selesai</span>
                                @elseif($session->isInProgress())
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">&#9203; Mengerjakan</span>
                                    @if($isClosed)
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 ml-1">Waktu habis</span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Belum mulai</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $session->submitted_at?->translatedFormat('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($session->isSubmitted())
                                    <span class="font-semibold text-gray-800">{{ (int)round($session->total_score ?? (($session->auto_score ?? 0) + ($session->manual_score ?? 0))) }}</span>
                                    @if($session->needsManualGrading())
                                        <span class="ml-1 text-xs text-orange-500">(belum lengkap)</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($session->isSubmitted())
                                    <a href="{{ route('teacher.assessment.grade', [$assessment->id, $session->user_id]) }}" wire:navigate
                                       class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                        {{ $session->needsManualGrading() ? '✏️ Nilai' : '👁 Lihat' }}
                                    </a>
                                @elseif($session->isInProgress() && $isClosed)
                                    <button
                                        wire:click="forceSubmit({{ $session->id }})"
                                        wire:confirm="Yakin force submit jawaban {{ $session->student->name ?? 'siswa ini' }}? Soal yang belum dijawab mendapat skor 0."
                                        wire:loading.attr="disabled"
                                        wire:target="forceSubmit({{ $session->id }})"
                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-100 transition disabled:opacity-50"
                                    >
                                        <svg wire:loading.remove wire:target="forceSubmit({{ $session->id }})" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <svg wire:loading wire:target="forceSubmit({{ $session->id }})" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Force Submit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

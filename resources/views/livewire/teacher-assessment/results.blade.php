<div>
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

    {{-- Statistik --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $totalSubmitted }}</p>
            <p class="text-sm text-gray-500 mt-1">Sudah Mengumpulkan</p>
        </div>
        <div class="rounded-xl border {{ $needsGrading > 0 ? 'border-orange-200 bg-orange-50' : 'border-gray-200 bg-white' }} p-5 shadow-sm text-center">
            <p class="text-3xl font-bold {{ $needsGrading > 0 ? 'text-orange-600' : 'text-gray-800' }}">{{ $needsGrading }}</p>
            <p class="text-sm {{ $needsGrading > 0 ? 'text-orange-500' : 'text-gray-500' }} mt-1">Menunggu Penilaian Guru</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $avgScore }}%</p>
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
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">✓ Selesai</span>
                                @elseif($session->isInProgress())
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">⏳ Mengerjakan</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Belum mulai</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $session->submitted_at?->translatedFormat('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($session->isSubmitted())
                                    <span class="font-semibold text-gray-800">{{ $session->getScorePercentage() }}%</span>
                                    @if($session->needsManualGrading())
                                        <span class="ml-1 text-xs text-orange-500">(belum lengkap)</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($session->isSubmitted())
                                    <a href="{{ route('teacher.assessment.grade', [$assessment->id, $session->user_id]) }}" wire:navigate
                                       class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                        {{ $session->needsManualGrading() ? '✏️ Nilai' : '👁 Lihat' }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

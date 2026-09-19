<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Asesmen Guru</h1>
            <p class="mt-1 text-sm text-gray-500">Buat dan kelola soal kuis / asesmen untuk siswa</p>
        </div>
        <a href="{{ route('teacher.assessment.create') }}" wire:navigate
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Asesmen Baru
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Filter tabs --}}
    <div class="mb-4 flex flex-wrap gap-2 border-b border-gray-200">
        @foreach(['all'=>'Semua','upcoming'=>'🔒 Akan Dimulai','ongoing'=>'🟢 Berlangsung','closed'=>'🔴 Selesai','draft'=>'📝 Draft'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition
                    {{ $tab === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul asesmen..."
            class="w-full max-w-sm rounded-lg border border-gray-300 px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    {{-- List --}}
    @if($assessments->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-3 text-gray-500">Belum ada asesmen. Klik <strong>Buat Asesmen Baru</strong> untuk memulai.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($assessments as $assessment)
                @php
                    if (!$assessment->is_published) {
                        $statusConfig = ['label'=>'Draft', 'bg'=>'bg-gray-100', 'text'=>'text-gray-500'];
                    } else {
                        $status = $assessment->status;
                        $statusConfig = match($status) {
                            'upcoming' => ['label'=>'Akan Dimulai', 'bg'=>'bg-yellow-100', 'text'=>'text-yellow-800'],
                            'ongoing'  => ['label'=>'Berlangsung',  'bg'=>'bg-green-100',  'text'=>'text-green-800'],
                            'closed'   => ['label'=>'Selesai',      'bg'=>'bg-gray-100',   'text'=>'text-gray-600'],
                            default      => ['label'=>'Tidak Diketahui', 'bg'=>'bg-gray-100', 'text'=>'text-gray-500'],
                        };
                    }
                @endphp
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h3 class="text-base font-semibold text-gray-800 truncate">{{ $assessment->title }}</h3>
                                @if($assessment->subject)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-medium text-emerald-700">
                                        📖 {{ $assessment->subject->name }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                            {{-- Badge kelas & jurusan target --}}
                            @if(!empty($assessment->target_grades) || !empty($assessment->target_majors))
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @foreach($assessment->target_grades ?? [] as $grade)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 border border-blue-200 rounded-lg text-xs font-medium text-blue-700">🎓 {{ $grade }}</span>
                                @endforeach
                                @foreach($assessment->target_majors ?? [] as $major)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 border border-purple-200 rounded-lg text-xs font-medium text-purple-700">🏫 {{ $major }}</span>
                                @endforeach
                            </div>
                            @endif
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 mt-2">
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $assessment->start_date->translatedFormat('d M Y') }}
                                    {{ $assessment->start_time ? substr($assessment->start_time,0,5) : '' }}
                                    &mdash;
                                    {{ $assessment->end_date->translatedFormat('d M Y') }}
                                    {{ $assessment->end_time ? substr($assessment->end_time,0,5) : '' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                    </svg>
                                    {{ $assessment->questions_count }} soal
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                    </svg>
                                    {{ $assessment->submitted_count }} mengerjakan
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $assessment->creator->name ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 sm:shrink-0">
                            <a href="{{ route('teacher.assessment.questions', $assessment->id) }}" wire:navigate
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition" title="Kelola Soal">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="hidden sm:inline">Kelola Soal</span>
                            </a>
                            <a href="{{ route('teacher.assessment.results', $assessment->id) }}" wire:navigate
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition" title="Hasil">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="hidden sm:inline">Hasil</span>
                            </a>
                            <a href="{{ route('teacher.assessment.edit', $assessment->id) }}" wire:navigate
                               class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                Edit
                            </a>
                            <button wire:click="deleteAssessment({{ $assessment->id }})"
                                    wire:confirm="Yakin hapus asesmen '{{ $assessment->title }}'? Tindakan ini tidak bisa dibatalkan."
                                    class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
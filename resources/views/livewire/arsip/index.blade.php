<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-800 dark:text-white flex items-center gap-2">
            &#128230; Arsip Tahun Ajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Lihat data dari tahun ajaran sebelumnya</p>
    </div>

    {{-- Pilih Tahun Ajaran --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-6">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih Tahun Ajaran</label>
        <div class="flex flex-wrap gap-3 items-center">
            <select wire:model.live="selectedYearId" class="border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">-- Pilih Tahun Ajaran --</option>
                @foreach($academicYears as $ay)
                <option value="{{ $ay->id }}">
                    {{ $ay->year }} {{ $ay->is_active ? '(Aktif)' : '(Arsip)' }}
                </option>
                @endforeach
            </select>
            @if($selectedYear)
            <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-full">
                {{ $selectedYear->start_date?->locale('id')->isoFormat('D MMM YYYY') }}
                &mdash;
                {{ $selectedYear->end_date?->locale('id')->isoFormat('D MMM YYYY') }}
            </span>
            @endif
        </div>
    </div>

    @if(!$selectedYear)
    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 shadow-sm">
        <div class="text-5xl mb-4">&#128230;</div>
        <h3 class="text-lg font-bold text-gray-500">Pilih tahun ajaran untuk melihat arsip</h3>
        <p class="text-sm text-gray-400 mt-2">Semua data tersimpan dan bisa diakses kapan saja</p>
    </div>
    @else

    {{-- Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        {{-- Tab Nav --}}
        <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            @foreach([
                ['pkl_journal',    '&#128203;', 'PKL Jurnal'],
                ['teaching_journal','&#128218;', 'Jurnal Mengajar'],
                ['assessment',     '&#128221;', 'Assessment'],
                ['pkl_placement',  '&#127970;', 'PKL Penempatan'],
            ] as [$tab, $icon, $label])
            <button wire:click="$set('activeTab','{{ $tab }}')"
                class="flex-shrink-0 px-5 py-3.5 text-sm font-semibold border-b-2 transition-colors
                    {{ $activeTab === $tab
                        ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/20'
                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <span>{!! $icon !!}</span> {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Tab: PKL Jurnal --}}
        @if($activeTab === 'pkl_journal')
        <div class="p-5">
            @if($pklData->isEmpty())
            <p class="text-center text-gray-400 py-10">Tidak ada data PKL untuk tahun ini</p>
            @else
            @foreach($pklData as $sup)
            <div class="mb-6">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-3">&#127970; {{ $sup->company->name }}</p>
                @foreach($sup->company->placements as $pl)
                @php
                    $jrnls = $pl->journals;
                    $total = $jrnls->count();
                    $hadir = $jrnls->where('attendance_status','hadir')->count();
                    $sakit = $jrnls->where('attendance_status','sakit')->count();
                    $izin  = $jrnls->where('attendance_status','izin')->count();
                    $alpha = $jrnls->where('attendance_status','alpha')->count();
                    $pct   = $total > 0 ? round($hadir/$total*100,1) : 0;
                    $firstDate = $jrnls->min('journal_date');
                @endphp
                <div class="flex items-start justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $pl->student->name ?? '-' }}</p>
                        @if($firstDate)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $total }} jurnal &middot; sejak {{ \Carbon\Carbon::parse($firstDate)->locale('id')->isoFormat('D MMM YYYY') }}</p>
                        @else
                        <p class="text-xs text-gray-400 italic mt-0.5">Tidak ada jurnal</p>
                        @endif
                        @if($total > 0)
                        <div class="flex gap-2 mt-1.5">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-bold">&#9989; {{ $hadir }}</span>
                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-bold">&#129314; {{ $sakit }}</span>
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-bold">&#128196; {{ $izin }}</span>
                            @if($alpha > 0)<span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-bold">&#10060; {{ $alpha }}</span>@endif
                        </div>
                        @endif
                    </div>
                    @if($total > 0)
                    <div class="text-right ml-4">
                        <span class="text-xl font-black {{ $pct >= 80 ? 'text-green-600' : ($pct >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $pct }}%</span>
                        <p class="text-xs text-gray-400">kehadiran</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
            @endif
        </div>

        {{-- Tab: Jurnal Mengajar --}}
        @elseif($activeTab === 'teaching_journal')
        <div class="p-5">
            @if($teachingJournals->isEmpty())
            <p class="text-center text-gray-400 py-10">Tidak ada jurnal mengajar untuk tahun ini</p>
            @else
            @foreach($teachingJournals as $teacherId => $journals)
            @php $guru = $journals->first()->teacher; @endphp
            <div class="mb-4 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 bg-indigo-50 dark:bg-indigo-900/20">
                    <p class="text-sm font-bold text-indigo-700 dark:text-indigo-300">{{ $guru->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $journals->count() }} entri jurnal</p>
                </div>
                <div class="px-4 py-2">
                    @foreach($journals->take(5) as $jrn)
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50 dark:border-gray-700 last:border-0">
                        <span class="text-xs text-gray-500 w-24">{{ \Carbon\Carbon::parse($jrn->date)->locale('id')->isoFormat('D MMM YYYY') }}</span>
                        <span class="text-xs text-gray-700 dark:text-gray-300 flex-1 px-2 truncate">{{ $jrn->topic ?? $jrn->learning_objective ?? '-' }}</span>
                        <span class="text-xs text-gray-400">{{ $jrn->subject->name ?? '-' }}</span>
                    </div>
                    @endforeach
                    @if($journals->count() > 5)
                    <p class="text-xs text-gray-400 text-center py-2">... dan {{ $journals->count() - 5 }} jurnal lainnya</p>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Tab: Assessment --}}
        @elseif($activeTab === 'assessment')
        <div class="p-5">
            @if($assessments->isEmpty())
            <p class="text-center text-gray-400 py-10">Tidak ada assessment untuk tahun ini</p>
            @else
            <div class="space-y-3">
                @foreach($assessments as $ass)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $ass->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $ass->questions_count }} soal &middot; {{ $ass->assessment_type }} &middot; oleh {{ $ass->creator->name ?? '-' }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $ass->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $ass->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Tab: PKL Penempatan --}}
        @elseif($activeTab === 'pkl_placement')
        <div class="p-5">
            @if($placements->isEmpty())
            <p class="text-center text-gray-400 py-10">Tidak ada data penempatan PKL untuk tahun ini</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase border-b border-gray-200">
                            <th class="text-left py-2 pr-4">Siswa</th>
                            <th class="text-left py-2 pr-4">Perusahaan</th>
                            <th class="text-left py-2 pr-4">Mulai</th>
                            <th class="text-left py-2">Selesai</th>
                            <th class="text-left py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($placements as $pl)
                        <tr class="border-b border-gray-50 dark:border-gray-700 last:border-0">
                            <td class="py-2 pr-4 font-medium text-gray-800 dark:text-gray-200">{{ $pl->student->name ?? '-' }}</td>
                            <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $pl->company->name ?? '-' }}</td>
                            <td class="py-2 pr-4 text-gray-500 text-xs">{{ $pl->start_date?->locale('id')->isoFormat('D MMM YY') ?? '-' }}</td>
                            <td class="py-2 pr-4 text-gray-500 text-xs">{{ $pl->end_date?->locale('id')->isoFormat('D MMM YY') ?? '-' }}</td>
                            <td class="py-2">
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                                    {{ $pl->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($pl->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif
</div>
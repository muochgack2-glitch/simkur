<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">📊 Monitoring Pembelajaran</h1>
    <p class="text-sm text-gray-500 mb-5">Pantau progress materi, tugas, dan kuis PKL</p>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Kelas</label>
                <select wire:model.live="filterClass" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($pklClasses as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Guru</label>
                <select wire:model.live="filterTeacher" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">Semua Guru</option>
                    @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Periode</label>
                <div class="flex gap-1.5 flex-wrap">
                    <button wire:click="$set('filterPeriod', '')" class="px-3 py-2 rounded-lg text-xs font-medium transition {{ $filterPeriod === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Semua</button>
                    @foreach($periods as $p)
                    <button wire:click="$set('filterPeriod', '{{ $p->id }}')" class="px-3 py-2 rounded-lg text-xs font-medium transition {{ (string)$filterPeriod === (string)$p->id ? 'bg-blue-600 text-white' : ($p->isCurrentPeriod() ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200') }}">P{{ $p->period_number }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_courses'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Materi</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['submissions'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Tugas Masuk</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['ungraded'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">⚠ Belum Dinilai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
            <p class="text-2xl font-bold text-red-500">{{ $stats['late'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Terlambat</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
            <p class="text-2xl font-bold text-green-600">{{ $stats['avg_score'] ? number_format($stats['avg_score'], 1) : '-' }}</p>
            <p class="text-xs text-gray-500">Rata-rata Nilai</p>
        </div>
    </div>

    <!-- Course Detail (if selected) -->
    @if($selectedCourseId && $courseDetail)
    <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-blue-300 p-5 mb-5">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $courseDetail->title }}</h2>
                <p class="text-sm text-gray-500">{{ $courseDetail->teacher->name ?? '-' }} - {{ $courseDetail->subject->name ?? '-' }}</p>
            </div>
            <button wire:click="closeDetail" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg">Tutup</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b text-xs uppercase text-gray-500">
                    <th class="text-left py-2 px-3">Siswa</th>
                    <th class="text-left py-2 px-3">Kelas</th>
                    <th class="text-center py-2 px-3">Tugas</th>
                    <th class="text-center py-2 px-3">Nilai Tugas</th>
                    <th class="text-center py-2 px-3">Kuis</th>
                    <th class="text-center py-2 px-3">Nilai Kuis</th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($studentDetails as $sd)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 font-medium">{{ $sd['name'] }}</td>
                        <td class="py-2 px-3 text-xs text-gray-500">{{ $sd['class'] }}</td>
                        <td class="py-2 px-3 text-center">{{ $sd['asg_done'] }}</td>
                        <td class="py-2 px-3 text-center font-medium {{ is_numeric($sd['asg_avg']) && $sd['asg_avg'] >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $sd['asg_avg'] }}</td>
                        <td class="py-2 px-3 text-center">{{ $sd['quiz_done'] }}</td>
                        <td class="py-2 px-3 text-center font-medium {{ is_numeric($sd['quiz_avg']) && $sd['quiz_avg'] >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $sd['quiz_avg'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Teacher Period Grid -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden mb-5">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <h2 class="font-bold text-gray-800">📊 Status Guru Per Periode</h2>
        </div>
        @if(count($teacherGrid) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b bg-gray-50/50 text-xs uppercase text-gray-500">
                    <th class="text-left py-2 px-4">Guru</th>
                    <th class="text-left py-2 px-3">Mapel</th>
                    @foreach($periods as $p)
                    <th class="text-center py-2 px-2 {{ $p->isCurrentPeriod() ? 'text-green-600 font-bold' : '' }}">P{{ $p->period_number }}</th>
                    @endforeach
                    <th class="text-center py-2 px-3">Total</th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($teacherGrid as $tg)
                    <tr class="hover:bg-gray-50 {{ $tg['total'] === 0 ? 'bg-red-50/50' : '' }}">
                        <td class="py-2 px-4 font-medium">{{ $tg['name'] }}</td>
                        <td class="py-2 px-3 text-xs text-gray-500">{{ $tg['subject'] }}</td>
                        @foreach($periods as $p)
                        <td class="py-2 px-2 text-center">@if($tg['periods'][$p->id] ?? 0)<span class="text-green-600 font-medium">{{ $tg['periods'][$p->id] }}</span>@else<span class="text-red-400">❌</span>@endif</td>
                        @endforeach
                        <td class="py-2 px-3 text-center"><span class="px-2 py-0.5 rounded text-xs font-bold {{ $tg['total'] === $tg['max'] ? 'bg-green-100 text-green-700' : ($tg['total'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">{{ $tg['total'] }}/{{ $tg['max'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Course List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <h2 class="font-bold text-gray-800">📚 Daftar Materi ({{ count($courses) }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b bg-gray-50/50 text-xs uppercase text-gray-500">
                    <th class="text-left py-2 px-4">Materi</th>
                    <th class="text-left py-2 px-3">Guru</th>
                    <th class="text-center py-2 px-2">Periode</th>
                    <th class="text-center py-2 px-2">File</th>
                    <th class="text-center py-2 px-2">Tugas</th>
                    <th class="text-center py-2 px-2">Kuis</th>
                    <th class="text-center py-2 px-3">Rincian</th>
                </tr></thead>
                <tbody class="divide-y">
                    @forelse($courses as $c)
                    <tr class="hover:bg-gray-50 {{ $selectedCourseId === $c->id ? 'bg-blue-50' : '' }}">
                        <td class="py-2 px-4">
                            <p class="font-medium text-gray-800">{{ $c->title }}</p>
                            <p class="text-xs text-gray-400">{{ $c->subject->name ?? '' }}</p>
                        </td>
                        <td class="py-2 px-3 text-xs text-gray-600">{{ $c->teacher->name ?? '-' }}</td>
                        <td class="py-2 px-2 text-center">
                            @if($c->period)<span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs font-medium">P{{ $c->period->period_number }}</span>@else<span class="text-gray-400">-</span>@endif
                        </td>
                        <td class="py-2 px-2 text-center text-gray-600">{{ $c->materials->count() }}</td>
                        <td class="py-2 px-2 text-center text-gray-600">{{ $c->assignments->count() }}</td>
                        <td class="py-2 px-2 text-center text-gray-600">{{ $c->quizzes->count() }}</td>
                        <td class="py-2 px-3 text-center">
                            <button wire:click="showDetail({{ $c->id }})" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">Lihat</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">Tidak ada materi ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
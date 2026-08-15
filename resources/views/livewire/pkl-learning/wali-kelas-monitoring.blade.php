<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">🎓 Monitoring Kelas Saya</h1>
            <p class="text-sm text-gray-500">Pantau progress pembelajaran PKL siswa di kelas Anda</p>
        </div>
        @if(count($classes) > 1)
        <select wire:model.live="selectedClassId" class="px-4 py-2 border rounded-xl text-sm">
            @foreach($classes as $cls)
            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
            @endforeach
        </select>
        @else
        <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium">{{ $classes->first()->name ?? '' }}</span>
        @endif
    </div>

    <!-- Guru Per Periode -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5 mb-4">
        <h2 class="text-base font-bold text-gray-800 dark:text-white mb-3">📚 Guru Pengajar & Status Per Periode</h2>
        @if(count($teacherStats) === 0)
            <p class="text-sm text-gray-400">Tidak ada guru yang terdaftar untuk kelas ini.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 text-xs uppercase">
                        <th class="text-left py-2 px-3">Guru</th>
                        <th class="text-left py-2 px-3">Mapel</th>
                        @foreach($activePeriods as $p)
                        <th class="text-center py-2 px-2">
                            <div class="{{ $p->isCurrentPeriod() ? 'text-green-600 font-bold' : '' }}">P{{ $p->period_number }}</div>
                        </th>
                        @endforeach
                        <th class="text-center py-2 px-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teacherStats as $ts)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 {{ $ts['total_published'] === 0 ? 'bg-red-50/50' : '' }}">
                        <td class="py-2.5 px-3 font-medium text-gray-800">{{ $ts['teacher_name'] }}</td>
                        <td class="py-2.5 px-3 text-gray-600 text-xs">{{ $ts['subject'] }}</td>
                        @foreach($activePeriods as $p)
                        <td class="py-2.5 px-2 text-center">
                            @if($ts['period_status'][$p->id] ?? false)
                            <span class="text-green-600">✅</span>
                            @else
                            <span class="text-red-400">❌</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="py-2.5 px-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $ts['total_published'] === $ts['total_periods'] ? 'bg-green-100 text-green-700' : ($ts['total_published'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                                {{ $ts['total_published'] }}/{{ $ts['total_periods'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Student Progress -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
        <h2 class="text-base font-bold text-gray-800 dark:text-white mb-3">🎓 Progress Siswa ({{ count($studentProgress) }} siswa)</h2>
        <div class="flex items-center gap-2 mb-3">
            <label class="text-xs text-gray-500">Filter Periode:</label>
            <select wire:model.live="selectedPeriodId" class="px-3 py-1.5 border rounded-lg text-xs">
                <option value="all">Semua Periode</option>
                @foreach($activePeriods as $p)
                <option value="{{ $p->id }}">Periode {{ $p->period_number }} - {{ $p->title }}</option>
                @endforeach
            </select>
        </div>
        @if(count($studentProgress) === 0)
            <p class="text-sm text-gray-400">Tidak ada siswa di kelas ini.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 text-xs uppercase">
                        <th class="text-left py-2 px-3">Siswa</th>
                        <th class="text-center py-2 px-3">Tugas</th>
                        <th class="text-center py-2 px-3">Kuis</th>
                        <th class="text-center py-2 px-3">Rata-rata</th>
                        <th class="text-center py-2 px-3">Penyelesaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentProgress as $sp)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 {{ $sp['completion'] < 50 ? 'bg-red-50/50' : '' }}">
                        <td class="py-2.5 px-3">
                            <p class="font-medium text-gray-800">{{ $sp['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $sp['nis'] }}</p>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="{{ $sp['submitted_assignments'] < $sp['total_assignments'] ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                {{ $sp['submitted_assignments'] }}/{{ $sp['total_assignments'] }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="{{ $sp['submitted_quizzes'] < $sp['total_quizzes'] ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                {{ $sp['submitted_quizzes'] }}/{{ $sp['total_quizzes'] }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @if($sp['avg_score'] !== null)
                            <span class="font-medium {{ $sp['avg_score'] >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $sp['avg_score'] }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $sp['completion'] >= 80 ? 'bg-green-500' : ($sp['completion'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $sp['completion'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium w-10 text-right {{ $sp['completion'] < 50 ? 'text-red-600' : 'text-gray-600' }}">{{ $sp['completion'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
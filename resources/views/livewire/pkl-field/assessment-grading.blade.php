<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📊 Penilaian Akhir PKL</h1>
            <p class="text-gray-500 mt-1 text-sm">Input & rekap nilai per siswa per komponen</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats -->
    @if($components->isNotEmpty())
    @php
        $totalStudents = $placements->count();
        $completedCount = collect($finalScores)->where('complete', true)->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-600 rounded-2xl p-4 text-white shadow-lg">
            <div class="text-2xl font-bold">{{ $totalStudents }}</div>
            <div class="text-blue-100 text-xs font-medium mt-1">Total Siswa</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $completedCount }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">✅ Nilai Lengkap</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-amber-600">{{ $totalStudents - $completedCount }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">⏳ Belum Lengkap</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-purple-600">{{ $components->count() }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">📋 Komponen</div>
        </div>
    </div>

    <!-- Progress -->
    @if($totalStudents > 0)
    <div class="bg-white rounded-xl border p-4 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-600">Progres Penilaian</span>
            <span class="text-xs font-bold text-green-600">{{ round(($completedCount / $totalStudents) * 100) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full bg-green-500 transition-all duration-500" style="width: {{ ($completedCount / $totalStudents) * 100 }}%"></div>
        </div>
    </div>
    @endif
    @endif

    <!-- Filter -->
    <div class="flex flex-wrap gap-3 mb-4">
        <select wire:model.live="filterCompany" class="px-4 py-2.5 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua DU/DI</option>
            @foreach($companies as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    @if($components->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border shadow-sm">
        <div class="text-5xl mb-4">⚙️</div>
        <h3 class="text-lg font-bold text-gray-400">Belum ada komponen penilaian</h3>
        <p class="text-sm text-gray-400 mt-2">Setting komponen di menu "Setting Penilaian" terlebih dahulu</p>
    </div>
    @else
    <!-- Grading Table -->
    <div class="bg-white rounded-xl border overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase sticky left-0 bg-gray-50 z-10 min-w-[180px]">Siswa</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase min-w-[120px]">DU/DI</th>
                        @foreach($components as $comp)
                        <th class="px-2 py-3 text-center min-w-[100px]">
                            <div class="text-xs font-semibold text-gray-600">{{ $comp->name }}</div>
                            <div class="flex items-center justify-center gap-1 mt-0.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $comp->category === 'school' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
                                    {{ $comp->category === 'school' ? 'SKL' : 'DU/DI' }}
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $comp->weight }}%</span>
                            </div>
                        </th>
                        @endforeach
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase min-w-[90px] bg-blue-50">Nilai Akhir</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase min-w-[80px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($placements as $p)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-3 py-3 sticky left-0 bg-white z-10">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($p->student->name ?? '?', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-xs">{{ $p->student->name ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $p->student->username ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-600">{{ Str::limit($p->company->name ?? '-', 20) }}</td>
                        @foreach($components as $comp)
                        @php $key = "{$p->id}_{$comp->id}"; @endphp
                        <td class="px-2 py-2 text-center" x-data="{ showNote: false }">
                            <div class="flex items-center justify-center gap-1">
                                <input type="number" wire:model.blur="scores.{{ $key }}" min="0" max="{{ $comp->max_score }}" step="0.01"
                                    class="w-16 px-2 py-1.5 border rounded-lg text-sm text-center transition-colors focus:ring-2 focus:ring-blue-500
                                    {{ isset($scores[$key]) && $scores[$key] !== null && $scores[$key] !== '' ? 'bg-green-50 border-green-300 font-semibold' : 'bg-white border-gray-300' }}"
                                    placeholder="0">
                                <button type="button" @click="showNote = !showNote" class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition" title="Catatan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                </button>
                            </div>
                            <div x-show="showNote" x-cloak class="mt-1">
                                <textarea wire:model.blur="notes.{{ $key }}" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-lg text-[11px] resize-none focus:ring-1 focus:ring-blue-500" placeholder="Catatan..."></textarea>
                            </div>
                        </td>
                        @endforeach
                        <td class="px-3 py-3 text-center bg-blue-50/50">
                            @php $fs = $finalScores[$p->id] ?? ['total' => 0, 'complete' => false]; @endphp
                            <div class="font-bold text-lg {{ $fs['complete'] ? ($fs['total'] >= 80 ? 'text-green-600' : ($fs['total'] >= 60 ? 'text-blue-600' : 'text-red-600')) : 'text-gray-300' }}">
                                {{ number_format($fs['total'], 1) }}
                            </div>
                            @if($fs['complete'])
                            <div class="text-[10px] font-semibold {{ $fs['total'] >= 80 ? 'text-green-500' : ($fs['total'] >= 60 ? 'text-blue-500' : 'text-red-500') }}">
                                {{ $fs['total'] >= 90 ? 'A' : ($fs['total'] >= 80 ? 'B' : ($fs['total'] >= 70 ? 'C' : ($fs['total'] >= 60 ? 'D' : 'E'))) }}
                            </div>
                            @else
                            <div class="text-[10px] text-amber-500">belum lengkap</div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            <button wire:click="saveScores({{ $p->id }})" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold shadow-sm hover:shadow transition-all" wire:loading.attr="disabled" wire:target="saveScores({{ $p->id }})">
                                <span wire:loading.remove wire:target="saveScores({{ $p->id }})">💾</span>
                                <span wire:loading wire:target="saveScores({{ $p->id }})">⏳</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $components->count() + 4 }}" class="px-4 py-16 text-center">
                            <div class="text-4xl mb-3">👥</div>
                            <h3 class="text-lg font-bold text-gray-400">Belum ada siswa ditempatkan</h3>
                            <p class="text-sm text-gray-400 mt-1">Tempatkan siswa di menu "Penempatan" terlebih dahulu</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
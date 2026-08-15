<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📊 Penilaian Akhir PKL</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Input nilai per siswa per komponen</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Filter -->
    <div class="flex gap-3 mb-6">
        <select wire:model.live="filterCompany" class="px-4 py-2.5 border rounded-xl bg-white text-sm">
            <option value="">Semua DU/DI</option>
            @foreach($companies as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    @if($components->isEmpty())
    <div class="text-center py-16 bg-white rounded-xl border">
        <h3 class="text-lg font-semibold text-gray-400">Belum ada komponen penilaian</h3>
        <p class="text-sm text-gray-400 mt-1">Setting komponen di menu "Setting Penilaian" dulu</p>
    </div>
    @else
    <!-- Grading Table -->
    <div class="bg-white rounded-xl border overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 sticky left-0 bg-gray-50 min-w-[180px]">Siswa</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 min-w-[120px]">DU/DI</th>
                        @foreach($components as $comp)
                        <th class="px-2 py-3 text-center font-semibold text-gray-600 min-w-[100px]">
                            <div class="text-xs">{{ $comp->name }}</div>
                            <div class="text-xs text-gray-400">({{ $comp->weight }}%)</div>
                        </th>
                        @endforeach
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 min-w-[80px] bg-blue-50">Nilai Akhir</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 min-w-[70px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($placements as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 sticky left-0 bg-white">
                            <div class="font-semibold text-gray-800">{{ $p->student->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $p->student->username ?? '' }}</div>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-600">{{ $p->company->name ?? '-' }}</td>
                        @foreach($components as $comp)
                        @php $key = "{$p->id}_{$comp->id}"; @endphp
                        <td class="px-2 py-2 text-center">
                            <input type="number" wire:model.blur="scores.{{ $key }}" min="0" max="{{ $comp->max_score }}" step="0.01"
                                class="w-20 px-2 py-1.5 border rounded-lg text-sm text-center {{ isset($scores[$key]) && $scores[$key] !== null && $scores[$key] !== '' ? 'bg-green-50 border-green-300' : 'bg-white border-gray-300' }}">
                        </td>
                        @endforeach
                        <td class="px-3 py-3 text-center bg-blue-50">
                            @php $fs = $finalScores[$p->id] ?? ['total' => 0, 'complete' => false]; @endphp
                            <span class="font-bold text-lg {{ $fs['complete'] ? 'text-blue-700' : 'text-gray-400' }}">{{ $fs['total'] }}</span>
                            @if(!$fs['complete'])
                            <div class="text-xs text-amber-500">belum lengkap</div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            <button wire:click="saveScores({{ $p->id }})" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-semibold">💾 Simpan</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $components->count() + 4 }}" class="px-4 py-12 text-center text-gray-400">Belum ada siswa yang ditempatkan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
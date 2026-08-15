<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">👥 Penempatan Siswa PKL</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Assign siswa ke tempat DU/DI</p>
        </div>
        <button wire:click="openAssign()" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-2.5 px-5 rounded-xl transition shadow-lg">
            + Tempatkan Siswa
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-5 py-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $stats['placed'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Sudah Ditempatkan</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold {{ $stats['unplaced'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ $stats['unplaced'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Belum Ditempatkan</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['companies'] }}</div>
            <div class="text-xs text-gray-500 mt-1">DU/DI Aktif</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <select wire:model.live="academicYearId" class="px-4 py-2.5 border border-gray-300 rounded-xl bg-white text-sm">
            @foreach($academicYears as $act)
            <option value="{{ $act->id }}">{{ $act->year_name ?? $act->year }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterCompany" class="px-4 py-2.5 border border-gray-300 rounded-xl bg-white text-sm">
            <option value="">Semua DU/DI</option>
            @foreach($companies as $c)
            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->availableCapacity($academicYearId) }} sisa)</option>
            @endforeach
        </select>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari siswa..." class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl bg-white text-sm">
    </div>

    <!-- Placements Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Siswa</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">DU/DI</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($placements as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $p->student->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $p->student->username ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-700">{{ $p->company->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $p->company->address ?? '' }}</div>
                        @if($p->moves->isNotEmpty())
                        <span class="text-xs text-amber-600">↺ Pindah {{ $p->moves->count() }}x</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $p->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button wire:click="openMove({{ $p->id }})" class="text-amber-600 hover:text-amber-800 text-xs font-medium mr-2">Pindah</button>
                        @if($confirmDelete === $p->id)
                        <button wire:click="removePlacement({{ $p->id }})" class="text-red-600 font-bold text-xs">Yakin?</button>
                        <button wire:click="$set('confirmDelete', null)" class="text-gray-400 text-xs ml-1">Batal</button>
                        @else
                        <button wire:click="$set('confirmDelete', {{ $p->id }})" class="text-red-500 text-xs">Batal</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada penempatan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Unplaced Students -->
    @if($unplacedStudents->isNotEmpty())
    <div class="mt-6 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200 p-4">
        <h3 class="font-bold text-amber-800 mb-2">⚠️ Siswa Belum Ditempatkan ({{ $unplacedStudents->count() }})</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($unplacedStudents as $s)
            <span class="px-3 py-1 bg-white rounded-lg text-sm border border-amber-200 text-gray-700">{{ $s->name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Assign Modal -->
    @if($showAssign)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showAssign', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4">Tempatkan Siswa ke DU/DI</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Siswa <span class="text-red-500">*</span></label>
                    <select wire:model="assignStudentId" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                        <option value="">Pilih siswa...</option>
                        @foreach($unplacedStudents as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DU/DI <span class="text-red-500">*</span></label>
                    <select wire:model="assignCompanyId" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        @if(!$c->isFull($academicYearId))
                        <option value="{{ $c->id }}">{{ $c->name }} (sisa {{ $c->availableCapacity($academicYearId) }})</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea wire:model="assignNotes" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showAssign', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="assignStudent" class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold">Tempatkan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Move Modal -->
    @if($showMove)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showMove', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4">Pindah Siswa ke DU/DI Lain</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DU/DI Tujuan <span class="text-red-500">*</span></label>
                    <select wire:model="moveToCompanyId" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        @if(!$c->isFull($academicYearId))
                        <option value="{{ $c->id }}">{{ $c->name }} (sisa {{ $c->availableCapacity($academicYearId) }})</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pindah <span class="text-red-500">*</span></label>
                    <textarea wire:model="moveReason" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none" placeholder="Jelaskan alasan perpindahan..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showMove', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="moveStudent" class="px-5 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-semibold">Pindahkan</button>
            </div>
        </div>
    </div>
    @endif
</div>
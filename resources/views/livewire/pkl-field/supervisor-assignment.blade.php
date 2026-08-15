<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">👨‍🏫 Pembimbing DU/DI</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Assign guru pembimbing ke tempat PKL</p>
        </div>
        <button wire:click="openAssign()" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl transition shadow-lg">
            + Assign Pembimbing
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Activity Filter -->
    <div class="mb-6">
        <select wire:model.live="academicYearId" class="px-4 py-2.5 border border-gray-300 rounded-xl bg-white text-sm">
            @foreach($academicYears as $act)
            <option value="{{ $act->id }}">{{ $act->year_name ?? $act->year }}</option>
            @endforeach
        </select>
    </div>

    <!-- Assignments by Teacher -->
    <div class="space-y-4">
        @forelse($assignments as $teacherId => $items)
        @php $teacher = $items->first()->teacher; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white">{{ $teacher->name }}</h3>
                        <p class="text-xs text-gray-500">Membimbing {{ $items->count() }} DU/DI</p>
                    </div>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                        {{ $items->sum(fn($i) => $i->company->activePlacements($academicYearId)->count()) }} siswa
                    </span>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($items as $item)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <span class="font-medium text-gray-700">{{ $item->company->name }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $item->company->address }}</span>
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">
                            {{ $item->company->activePlacements($academicYearId)->count() }}/{{ $item->company->capacity }} siswa
                        </span>
                    </div>
                    @if($confirmDelete === $item->id)
                    <div>
                        <button wire:click="removeAssignment({{ $item->id }})" class="text-red-600 font-bold text-xs">Yakin hapus?</button>
                        <button wire:click="$set('confirmDelete', null)" class="text-gray-400 text-xs ml-2">Batal</button>
                    </div>
                    @else
                    <button wire:click="$set('confirmDelete', {{ $item->id }})" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-400">Belum ada pembimbing yang di-assign</h3>
            <p class="text-sm text-gray-400 mt-1">Klik "Assign Pembimbing" untuk mulai</p>
        </div>
        @endforelse
    </div>

    <!-- Assign Modal -->
    @if($showAssign)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showAssign', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4">Assign Guru ke DU/DI</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guru Pembimbing <span class="text-red-500">*</span></label>
                    <select wire:model="assignTeacherId" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                        <option value="">Pilih guru...</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">DU/DI (pilih satu atau lebih) <span class="text-red-500">*</span></label>
                    <div class="space-y-2 max-h-60 overflow-y-auto border rounded-xl p-3">
                        @foreach($companies as $c)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" wire:model="assignCompanyIds" value="{{ $c->id }}" class="rounded">
                            <span class="text-sm">{{ $c->name }}</span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $c->activePlacements($academicYearId)->count() }}/{{ $c->capacity }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showAssign', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="assignSupervisor" class="px-5 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-semibold">Assign</button>
            </div>
        </div>
    </div>
    @endif
</div>
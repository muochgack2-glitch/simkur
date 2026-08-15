<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">👥 Penempatan Siswa PKL</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola penempatan siswa ke DU/DI</p>
        </div>
        <button wire:click="$set('showAssign', true)" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
            + Tempatkan Siswa
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-5 py-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-600 rounded-2xl p-4 text-white shadow-lg">
            <div class="text-2xl font-bold">{{ $placements->count() }}</div>
            <div class="text-blue-100 text-xs font-medium mt-1">Sudah Ditempatkan</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-amber-600">{{ $unplacedStudents->count() }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">⚠️ Belum Ditempatkan</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-gray-600">{{ $placements->count() + $unplacedStudents->count() }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">Total Siswa XII</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-4">
        <select wire:model.live="filterCompany" class="px-4 py-2.5 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua DU/DI</option>
            @foreach($companies as $c)
            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->placements_count ?? 0 }}/{{ $c->capacity }})</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
            <option value="moved">Moved</option>
        </select>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari siswa..." class="flex-1 min-w-[200px] px-4 py-2.5 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Siswa</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">DU/DI</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($placements as $i => $p)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($p->student->name ?? '?', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $p->student->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $p->student->schoolClass->name ?? '' }} · {{ $p->student->username ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-700">{{ $p->company->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ Str::limit($p->company->address ?? '', 40) }}</div>
                            @if($p->company->contact_person)
                            <div class="text-xs text-gray-500 mt-0.5">👤 {{ $p->company->contact_person }}</div>
                            @endif
                            @if($p->moves->isNotEmpty())
                            <span class="inline-flex items-center gap-1 text-xs text-amber-600 mt-1">🔄 Pindah {{ $p->moves->count() }}x</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $p->status === 'active' ? 'bg-green-100 text-green-700' : ($p->status === 'completed' ? 'bg-blue-100 text-blue-700' : ($p->status === 'moved' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')) }}">
                                {{ $p->status === 'active' ? '✅ Aktif' : ($p->status === 'completed' ? '🎓 Selesai' : ($p->status === 'moved' ? '🔄 Pindah' : '❌ Batal')) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if($p->status === 'active')
                                <button wire:click="openMove({{ $p->id }})" class="px-3 py-1.5 border border-amber-300 hover:bg-amber-50 rounded-lg text-xs font-medium text-amber-600 transition-colors" title="Pindah DU/DI">
                                    🔄 Pindah
                                </button>
                                @endif
                                @if($confirmDelete === $p->id)
                                <button wire:click="removePlacement({{ $p->id }})" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-bold">Yakin hapus?</button>
                                <button wire:click="$set('confirmDelete', null)" class="px-2 py-1.5 text-gray-400 text-xs">✕</button>
                                @else
                                <button wire:click="$set('confirmDelete', {{ $p->id }})" class="px-3 py-1.5 border border-red-200 hover:bg-red-50 rounded-lg text-xs text-red-500 transition-colors" title="Hapus">
                                    🗑️
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <div class="text-4xl mb-3">👥</div>
                            <h3 class="text-lg font-bold text-gray-400">Belum ada penempatan</h3>
                            <p class="text-sm text-gray-400 mt-1">Klik "+ Tempatkan Siswa" untuk mulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Unplaced Students -->
    @if($unplacedStudents->isNotEmpty())
    <div class="mt-6 bg-amber-50 rounded-2xl border border-amber-200 p-5">
        <h3 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
            ⚠️ Siswa Belum Ditempatkan
            <span class="px-2 py-0.5 bg-amber-200 text-amber-800 rounded-full text-xs">{{ $unplacedStudents->count() }}</span>
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach($unplacedStudents as $s)
            <span class="px-3 py-1.5 bg-white rounded-lg text-sm border border-amber-200 text-gray-700 font-medium">{{ $s->name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Assign Modal -->
    @if($showAssign)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showAssign', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-5 text-white">
                <h2 class="text-lg font-bold">👥 Tempatkan Siswa ke DU/DI</h2>
                <p class="text-blue-100 text-sm mt-1">Pilih siswa dan DU/DI tujuan</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Siswa <span class="text-red-500">*</span></label>
                    <select wire:model="assignStudentId" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih siswa...</option>
                        @foreach($unplacedStudents as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? '' }})</option>
                        @endforeach
                    </select>
                    @error('assignStudentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">DU/DI <span class="text-red-500">*</span></label>
                    <select wire:model="assignCompanyId" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        @if(!$c->isFull($academicYearId))
                        <option value="{{ $c->id }}">{{ $c->name }} (sisa {{ $c->availableCapacity($academicYearId) }})</option>
                        @endif
                        @endforeach
                    </select>
                    @error('assignCompanyId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="assignNotes" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500" placeholder="Catatan penempatan..."></textarea>
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('showAssign', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="assignStudent" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="assignStudent">👥 Tempatkan</span>
                    <span wire:loading wire:target="assignStudent">⏳ Menempatkan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Move Modal -->
    @if($showMove)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showMove', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-amber-500 px-6 py-5 text-white">
                <h2 class="text-lg font-bold">🔄 Pindah Siswa</h2>
                <p class="text-amber-100 text-sm mt-1">Pindahkan ke DU/DI lain</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">DU/DI Tujuan <span class="text-red-500">*</span></label>
                    <select wire:model="moveToCompanyId" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        @if(!$c->isFull($academicYearId))
                        <option value="{{ $c->id }}">{{ $c->name }} (sisa {{ $c->availableCapacity($academicYearId) }})</option>
                        @endif
                        @endforeach
                    </select>
                    @error('moveToCompanyId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alasan Pindah <span class="text-red-500">*</span></label>
                    <textarea wire:model="moveReason" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-amber-500" placeholder="Jelaskan alasan perpindahan..."></textarea>
                    @error('moveReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('showMove', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="moveStudent" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="moveStudent">🔄 Pindahkan</span>
                    <span wire:loading wire:target="moveStudent">⏳ Memindahkan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
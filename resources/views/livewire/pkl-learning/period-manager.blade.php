<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📅 Periode Pembelajaran PKL</h1>
            <p class="text-sm text-gray-500">Kelola periode pembelajaran selama masa PKL</p>
        </div>
        <button wire:click="create" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl text-sm font-medium hover:shadow-lg transition">➕ Tambah Periode</button>
    </div>

    @if(session('success'))<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>@endif

    <!-- Form -->
    @if($showForm)
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl border-2 border-blue-300 p-5">
        <h2 class="font-bold text-gray-800 dark:text-white mb-4">{{ $editingId ? '✏ Edit' : '➕ Tambah' }} Periode</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">No. Periode</label>
                <input type="number" wire:model="period_number" class="w-full px-3 py-2 border rounded-lg text-sm" min="1">
            </div>
            <div class="sm:col-span-3 lg:col-span-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Judul Periode</label>
                <input type="text" wire:model="title" placeholder="Contoh: Orientasi & Adaptasi" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                <input type="date" wire:model="start_date" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Selesai</label>
                <input type="date" wire:model="end_date" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi (opsional)</label>
            <textarea wire:model="description" rows="2" placeholder="Deskripsi singkat periode ini..." class="w-full px-3 py-2 border rounded-lg text-sm resize-none"></textarea>
        </div>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active" class="rounded"> Aktif</label>
            <div class="flex-1"></div>
            <button wire:click="cancel" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
            <button wire:click="save" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan</button>
        </div>
    </div>
    @endif

    <!-- Period Cards -->
    <div class="space-y-3">
        @forelse($periods as $period)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5 hover:shadow-md transition
            {{ $period->isCurrentPeriod() ? 'ring-2 ring-green-400' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg font-bold flex-shrink-0
                        {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700') }}">
                        {{ $period->period_number }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ $period->title }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700') }}">
                                {{ $period->getStatusLabel() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">📅 {{ $period->getDateRangeLabel() }} <span class="text-gray-400">({{ $period->start_date->diffInDays($period->end_date) + 1 }} hari)</span></p>
                        @if($period->description)<p class="text-xs text-gray-400 mt-0.5 truncate">{{ $period->description }}</p>@endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-medium">{{ $period->courses_count }} materi</span>
                    <button wire:click="edit({{ $period->id }})" class="px-3 py-1.5 text-xs bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg font-medium">✏</button>
                    @if($period->courses_count === 0)
                    <button wire:click="delete({{ $period->id }})" wire:confirm="Yakin hapus periode ini?" class="px-3 py-1.5 text-xs bg-red-50 text-red-500 hover:bg-red-100 rounded-lg font-medium">🗑</button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border p-8 text-center text-gray-400">
            <p class="text-lg mb-2">📅</p>
            <p>Belum ada periode. Klik "Tambah Periode" untuk mulai.</p>
        </div>
        @endforelse
    </div>
</div>
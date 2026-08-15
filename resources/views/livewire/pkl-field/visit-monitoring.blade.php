<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📅 Kunjungan Monitoring</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Jadwal kunjungan guru ke DU/DI (1x/bulan)</p>
        </div>
        <div class="flex gap-2">
            @if($isAdmin)
            <button wire:click="$set('showGenerate', true)" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg">
                ⚡ Generate Jadwal
            </button>
            @endif
            <button wire:click="openForm()" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg">
                + Tambah Kunjungan
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</div>
            <div class="text-xs text-gray-500">Terjadwal</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
            <div class="text-xs text-gray-500">Selesai</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-red-600">{{ $stats['missed'] }}</div>
            <div class="text-xs text-gray-500">Terlewat</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-3 mb-6">
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border rounded-xl bg-white text-sm">
            <option value="">Semua Status</option>
            <option value="scheduled">Terjadwal</option>
            <option value="completed">Selesai</option>
            <option value="missed">Terlewat</option>
        </select>
    </div>

    <!-- Visits Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    @if($isAdmin)<th class="px-4 py-3 text-left font-semibold text-gray-600">Guru</th>@endif
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">DU/DI</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Jadwal</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Tanggal Aktual</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($visits as $v)
                <tr class="hover:bg-gray-50">
                    @if($isAdmin)<td class="px-4 py-3 text-gray-700">{{ $v->teacher->name ?? '-' }}</td>@endif
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $v->company->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $v->company->address ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $v->scheduled_date->translatedFormat('d M Y') }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $v->actual_date ? $v->actual_date->translatedFormat('d M Y') : '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $v->status === 'completed' ? 'bg-green-100 text-green-700' : ($v->status === 'missed' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $v->status === 'completed' ? '✅ Selesai' : ($v->status === 'missed' ? '❌ Terlewat' : '📅 Terjadwal') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($v->status === 'scheduled')
                        <button wire:click="markCompleted({{ $v->id }})" class="text-green-600 text-xs font-medium mr-2">✅ Selesai</button>
                        @endif
                        <button wire:click="openForm({{ $v->id }})" class="text-blue-600 text-xs font-medium">Edit</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-4 py-12 text-center text-gray-400">Belum ada jadwal kunjungan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-lg font-bold mb-4">{{ $editingId ? ($status === 'completed' ? 'Laporan Kunjungan' : 'Edit Kunjungan') : 'Tambah Kunjungan' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DU/DI <span class="text-red-500">*</span></label>
                    <select wire:model="form_company_id" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jadwal <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="scheduled_date" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Aktual</label>
                        <input type="date" wire:model="actual_date" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model="status" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                        <option value="scheduled">Terjadwal</option>
                        <option value="completed">Selesai</option>
                        <option value="missed">Terlewat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none"></textarea>
                </div>
                @if($status === 'completed')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temuan</label>
                    <textarea wire:model="findings" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none" placeholder="Kondisi siswa, lingkungan kerja, dll"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi</label>
                    <textarea wire:model="recommendations" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none"></textarea>
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="save" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Generate Modal -->
    @if($showGenerate)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showGenerate', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h2 class="text-lg font-bold mb-4">Generate Jadwal Bulanan</h2>
            <p class="text-sm text-gray-500 mb-4">Otomatis buat jadwal kunjungan untuk setiap guru pembimbing ke DU/DI yang di-assign.</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <input type="month" wire:model="generateMonth" class="w-full px-4 py-2.5 border rounded-xl text-sm">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showGenerate', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="generateSchedule" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold">⚡ Generate</button>
            </div>
        </div>
    </div>
    @endif
</div>
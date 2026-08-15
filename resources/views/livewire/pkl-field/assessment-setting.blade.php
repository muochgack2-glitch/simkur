<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">⚙️ Komponen Penilaian PKL</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Setting komponen & bobot penilaian akhir PKL</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="seedDefaults" wire:confirm="Buat 6 komponen default?" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
                ⚡ Komponen Default
            </button>
            <button wire:click="openForm()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
                + Tambah
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Total Weight -->
    <div class="mb-6 p-4 rounded-xl border {{ $totalWeight == 100 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <span class="font-bold {{ $totalWeight == 100 ? 'text-green-700' : 'text-red-700' }}">Total Bobot: {{ $totalWeight }}%</span>
        @if($totalWeight != 100)
        <span class="text-red-600 text-sm ml-2">⚠️ Harus 100%!</span>
        @else
        <span class="text-green-600 text-sm ml-2">✅ OK</span>
        @endif
    </div>

    <!-- Components Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Urutan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Komponen</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Kategori</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Bobot</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Nilai Max</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($components as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $c->sort_order }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $c->category === 'company' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $c->getCategoryLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $c->weight }}%</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $c->max_score }}</td>
                    <td class="px-4 py-3 text-center">
                        <button wire:click="openForm({{ $c->id }})" class="text-blue-600 text-xs font-medium mr-2">Edit</button>
                        @if($confirmDelete === $c->id)
                        <button wire:click="delete({{ $c->id }})" class="text-red-600 font-bold text-xs">Yakin?</button>
                        <button wire:click="$set('confirmDelete', null)" class="text-gray-400 text-xs ml-1">Batal</button>
                        @else
                        <button wire:click="$set('confirmDelete', {{ $c->id }})" class="text-red-500 text-xs">Hapus</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada komponen. Klik "Komponen Default" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4">{{ $editingId ? 'Edit Komponen' : 'Tambah Komponen' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Komponen <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2.5 border rounded-xl text-sm" placeholder="Disiplin & Kehadiran">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select wire:model="category" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                            <option value="company">DU/DI</option>
                            <option value="school">Sekolah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bobot (%) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="weight" min="0" max="100" step="0.01" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Maksimal</label>
                        <input type="number" wire:model="max_score" min="1" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                        <input type="number" wire:model="sort_order" min="0" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="save" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all">💾 Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
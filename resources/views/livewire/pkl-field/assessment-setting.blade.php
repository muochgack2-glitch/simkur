<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">⚙️ Setting Komponen Penilaian</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola komponen dan bobot penilaian akhir PKL</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="seedDefaults" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm" wire:loading.attr="disabled">
                📋 Komponen Default
            </button>
            <button wire:click="openForm()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
                + Tambah
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Weight Summary -->
    @php
        $totalWeight = $components->sum('weight');
        $schoolWeight = $components->where('category', 'school')->sum('weight');
        $companyWeight = $components->where('category', 'company')->sum('weight');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold {{ $totalWeight == 100 ? 'text-green-600' : 'text-red-600' }}">{{ $totalWeight }}%</div>
                    <div class="text-gray-500 text-xs font-medium mt-1">Total Bobot {{ $totalWeight == 100 ? '✅' : '⚠️ Harus 100%' }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $totalWeight == 100 ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center text-lg">⚖️</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $schoolWeight }}%</div>
                    <div class="text-gray-500 text-xs font-medium mt-1">🏫 Bobot Sekolah</div>
                </div>
                <div class="text-xs text-gray-400">{{ $components->where('category', 'school')->count() }} komponen</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ $companyWeight }}%</div>
                    <div class="text-gray-500 text-xs font-medium mt-1">🏭 Bobot DU/DI</div>
                </div>
                <div class="text-xs text-gray-400">{{ $components->where('category', 'company')->count() }} komponen</div>
            </div>
        </div>
    </div>

    <!-- Components Table -->
    <div class="bg-white rounded-xl border overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase w-10">#</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Komponen</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Bobot</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Nilai Maks</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($components as $c)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $c->sort_order }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $c->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $c->category === 'school' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $c->category === 'school' ? '🏫 Sekolah' : '🏭 DU/DI' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold text-gray-800">{{ $c->weight }}%</span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $c->max_score }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openForm({{ $c->id }})" class="px-3 py-1.5 border border-blue-300 hover:bg-blue-50 rounded-lg text-xs font-medium text-blue-600 transition-colors">
                                    ✏️ Edit
                                </button>
                                @if($confirmDelete === $c->id)
                                <button wire:click="delete({{ $c->id }})" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-bold">Yakin?</button>
                                <button wire:click="$set('confirmDelete', null)" class="px-2 py-1.5 text-gray-400 text-xs">✕</button>
                                @else
                                <button wire:click="$set('confirmDelete', {{ $c->id }})" class="px-3 py-1.5 border border-red-200 hover:bg-red-50 rounded-lg text-xs text-red-500 transition-colors">
                                    🗑️
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="text-4xl mb-3">⚙️</div>
                            <h3 class="text-lg font-bold text-gray-400">Belum ada komponen</h3>
                            <p class="text-sm text-gray-400 mt-1">Klik "📋 Komponen Default" untuk mulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-5 text-white">
                <h2 class="text-lg font-bold">{{ $editingId ? '✏️ Edit Komponen' : '➕ Tambah Komponen' }}</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Komponen <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Disiplin & Kehadiran">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                        <select wire:model="category" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="company">🏭 DU/DI</option>
                            <option value="school">🏫 Sekolah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bobot (%) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="weight" min="0" max="100" step="0.01" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nilai Maksimal</label>
                        <input type="number" wire:model="max_score" min="1" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Urutan</label>
                        <input type="number" wire:model="sort_order" min="0" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="save" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">💾 Simpan</span>
                    <span wire:loading wire:target="save">⏳ Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
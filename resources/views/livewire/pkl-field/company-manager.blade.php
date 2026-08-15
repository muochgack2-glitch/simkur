<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">🏭 DU/DI (Tempat PKL)</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data tempat Praktik Kerja Lapangan</p>
        </div>
        <button wire:click="openForm()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl transition flex items-center space-x-2 shadow-lg">
            <span>+ Tambah DU/DI</span>
        </button>
    </div>

    <!-- Flash -->
    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-800 dark:text-green-300 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-800 dark:text-red-300 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, alamat, PIC..." class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Non-aktif</option>
            <option value="blacklisted">Blacklist</option>
        </select>
        <select wire:model.live="filterDept" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
            <option value="">Semua Jurusan</option>
            @foreach($this->departments as $dept)
            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Nama DU/DI</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Alamat</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">PIC</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Kapasitas</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($companies as $c)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $c->name }}</div>
                            <div class="text-xs text-gray-400">{{ $c->business_field }}</div>
                            @if($c->suitable_departments)
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($c->suitable_departments as $dept)
                                <span class="px-1.5 py-0.5 text-xs rounded bg-blue-100 text-blue-700">{{ $dept }}</span>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs max-w-xs truncate">{{ $c->address }}</td>
                        <td class="px-4 py-3">
                            <div class="text-gray-800 dark:text-white text-xs">{{ $c->contact_person }}</div>
                            <div class="text-gray-400 text-xs">{{ $c->contact_phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $used = $currentActivity ? $c->activePlacements($currentActivity->id)->count() : 0; @endphp
                            <span class="font-bold {{ $used >= $c->capacity ? 'text-red-600' : 'text-green-600' }}">{{ $used }}</span>
                            <span class="text-gray-400">/{{ $c->capacity }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $c->status === 'active' ? 'bg-green-100 text-green-700' : ($c->status === 'blacklisted' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $c->status === 'active' ? 'Aktif' : ($c->status === 'blacklisted' ? 'Blacklist' : 'Non-aktif') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="openForm({{ $c->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium mr-2">Edit</button>
                            @if($confirmDelete === $c->id)
                            <button wire:click="delete({{ $c->id }})" class="text-red-600 font-bold text-xs">Yakin?</button>
                            <button wire:click="$set('confirmDelete', null)" class="text-gray-400 text-xs ml-1">Batal</button>
                            @else
                            <button wire:click="$set('confirmDelete', {{ $c->id }})" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada data DU/DI</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $companies->links() }}</div>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="closeForm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">{{ $editingId ? 'Edit DU/DI' : 'Tambah DU/DI Baru' }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama DU/DI <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm" placeholder="PT. Contoh Perusahaan">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea wire:model="address" rows="2" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm resize-none" placeholder="Jl. Contoh No. 123"></textarea>
                        @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telepon</label>
                        <input type="text" wire:model="phone" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bidang Usaha</label>
                        <input type="text" wire:model="business_field" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm" placeholder="Manufaktur, Jasa, dll">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama PIC</label>
                        <input type="text" wire:model="contact_person" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">HP PIC</label>
                        <input type="text" wire:model="contact_phone" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kapasitas Siswa <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="capacity" min="1" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
                            <option value="active">Aktif</option>
                            <option value="inactive">Non-aktif</option>
                            <option value="blacklisted">Blacklist</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jurusan yang Cocok</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($this->departments as $dept)
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer text-sm
                                {{ in_array($dept->name, $suitable_departments ?? []) ? 'bg-blue-100 border-blue-400 text-blue-700' : 'bg-white dark:bg-gray-700 border-gray-300 text-gray-600' }}">
                                <input type="checkbox" wire:model="suitable_departments" value="{{ $dept->name }}" class="rounded">
                                {{ $dept->name }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                        <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeForm" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-semibold shadow-lg">{{ $editingId ? 'Simpan' : 'Tambah' }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
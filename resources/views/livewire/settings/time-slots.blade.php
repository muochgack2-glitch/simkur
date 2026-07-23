<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">⏰ Pengaturan Jam Mengajar</h1>
        <p class="mt-1 text-sm text-gray-800">Kelola jadwal jam mengajar di sekolah</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Action Bar -->
    <div class="mb-4 flex justify-between items-center">
        <div class="text-sm text-gray-800">
            Total: <span class="font-semibold">{{ $timeSlots->count() }}</span> jam mengajar
        </div>
        <button 
            wire:click="openCreate"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Jam Mengajar
        </button>
    </div>

    <!-- Time Slots Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hari</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Jam Mulai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Jam Selesai</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($timeSlots as $slot)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $slot->order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $slot->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            @php
                                $dayLabels = [
                                    'monday' => '🟦 Senin',
                                    'tuesday' => '🟩 Selasa',
                                    'wednesday' => '🟨 Rabu',
                                    'thursday' => '🟧 Kamis',
                                    'friday' => '🟪 Jumat',
                                    'saturday' => '🟥 Sabtu',
                                    'all' => '⭐ Semua Hari',
                                ];
                                $day = $slot->day_of_week ?? 'all';
                            @endphp
                            <span class="text-xs font-medium">{{ $dayLabels[$day] ?? '⭐ Semua Hari' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            {{ date('H:i', strtotime($slot->start_time)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            {{ date('H:i', strtotime($slot->end_time)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button 
                                wire:click="toggleActive({{ $slot->id }})"
                                class="px-3 py-1 rounded-full text-xs font-medium {{ $slot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}"
                            >
                                {{ $slot->is_active ? '✓ Aktif' : '○ Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button 
                                wire:click="openEdit({{ $slot->id }})"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="delete({{ $slot->id }})"
                                wire:confirm="Hapus jam mengajar '{{ $slot->name }}'?"
                                class="text-red-600 hover:text-red-900"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-700">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm">Belum ada jam mengajar</p>
                                <button 
                                    wire:click="openCreate"
                                    class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    + Tambah Jam Mengajar
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Create/Edit -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index: 9999;">
            <!-- Backdrop -->
            <div 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                wire:click="closeModal"
                style="z-index: 9999;"
            ></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full" style="z-index: 10000;">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $editingId ? '✏️ Edit Jam Mengajar' : '➕ Tambah Jam Mengajar' }}
                        </h3>
                        <button 
                            wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-800"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form wire:submit="save">
                        <div class="p-6 space-y-4">
                            <!-- Nama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Jam Mengajar <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    placeholder="Contoh: Jam ke-1"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Jam Mulai & Jam Selesai -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Jam Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="time" 
                                        wire:model="start_time"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Jam Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="time" 
                                        wire:model="end_time"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Urutan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    wire:model="order"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-700">Urutan penampilan jam mengajar</p>
                            </div>

                            <!-- Hari -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Berlaku Untuk Hari <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model="day_of_week"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all">⭐ Semua Hari</option>
                                    <option value="monday">🟦 Senin</option>
                                    <option value="tuesday">🟩 Selasa</option>
                                    <option value="wednesday">🟨 Rabu</option>
                                    <option value="thursday">🟧 Kamis</option>
                                    <option value="friday">🟪 Jumat</option>
                                    <option value="saturday">🟥 Sabtu</option>
                                </select>
                                @error('day_of_week') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-700">Jam mengajar akan muncul sesuai hari yang dipilih</p>
                            </div>

                            <!-- Status Aktif -->
                            <div>
                                <label class="flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model="is_active"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Aktif (tampil di form jurnal mengajar)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-end gap-3 px-6 py-4 bg-white border-t border-gray-200">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                            >
                                <span wire:loading.remove>{{ $editingId ? 'Update' : 'Simpan' }}</span>
                                <span wire:loading>Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

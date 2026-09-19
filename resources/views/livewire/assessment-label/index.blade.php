<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Jenis Asesmen</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola daftar jenis asesmen yang dapat dipilih guru (ASTS, ASAS, ASAT, dll)</p>
        </div>
        <button wire:click="openCreate"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Tambah Jenis Asesmen
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800" role="alert">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800" role="alert">{{ session('error') }}</div>
    @endif

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama..."
            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-72">
        <select wire:model.live="filterStatus"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="all">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
        </select>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama Jenis Asesmen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Digunakan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($labels as $label)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration + ($labels->currentPage() - 1) * $labels->perPage() }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $label->name }}</td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleStatus({{ $label->id }})"
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold transition-colors {{ $label->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $label->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $label->assessments()->count() }} asesmen</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="openEdit({{ $label->id }})"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Edit</button>
                                <button wire:click="delete({{ $label->id }})" wire:confirm="Yakin hapus jenis asesmen ini?"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            Belum ada jenis asesmen. Klik "+ Tambah Jenis Asesmen" untuk menambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($labels->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">{{ $labels->links() }}</div>
        @endif
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit Jenis Asesmen' : 'Tambah Jenis Asesmen' }}</h2>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Asesmen <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text" placeholder="Contoh: ASTS, ASAS, Ulangan Harian"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-3">
                        <input wire:model="isActive" type="checkbox" id="isActiveCheck" class="h-4 w-4 rounded border-gray-300 text-blue-600">
                        <label for="isActiveCheck" class="text-sm text-gray-700">Aktif (tampil di dropdown guru)</label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
<div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 ">📚 Master Data Mata Pelajaran</h1>
            <p class="mt-1 text-sm text-gray-800 ">Kelola data mata pelajaran yang diajarkan di sekolah</p>
        </div>
        <a href="{{ route('subjects.create') }}" 
           class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 ">
            + Tambah Mata Pelajaran
        </a>
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 " role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 " role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 rounded-lg bg-white p-4 shadow-sm ">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Cari Mata Pelajaran</label>
                <input type="text" wire:model.live="search" 
                       class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 "
                       placeholder="Cari nama atau kode...">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Filter Status</label>
                <select wire:model.live="filterStatus" 
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 ">
                <thead class="bg-white text-xs uppercase text-gray-700 ">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Kode</th>
                        <th scope="col" class="px-6 py-3">Nama Mata Pelajaran</th>
                        <th scope="col" class="px-6 py-3">Deskripsi</th>
                        <th scope="col" class="px-6 py-3">Jumlah Guru</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $index => $subject)
                        <tr class="border-b bg-white hover:bg-white ">
                            <td class="px-6 py-4">{{ $subjects->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 ">
                                {{ $subject->code ?: '-' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 ">
                                {{ $subject->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ Str::limit($subject->description, 50) ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 ">
                                    {{ $subject->teachers()->count() }} guru
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $subject->id }})" 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $subject->getStatusLabel() }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('subjects.edit', $subject->id) }}" 
                                       class="font-medium text-blue-600 hover:underline ">
                                        Edit
                                    </a>
                                    <button wire:click="delete({{ $subject->id }})" 
                                            wire:confirm="Yakin ingin menghapus mata pelajaran {{ $subject->name }}?"
                                            class="font-medium text-red-600 hover:underline ">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-700 ">
                                Tidak ada data mata pelajaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $subjects->links() }}
        </div>
    </div>
</div>

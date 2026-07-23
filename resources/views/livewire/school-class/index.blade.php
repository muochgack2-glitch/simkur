<div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 ">🏫 Master Data Kelas</h1>
            <p class="mt-1 text-sm text-gray-800 ">Kelola data kelas dan wali kelas</p>
        </div>
        <div class="flex space-x-2">
            @if($activeAcademicYear)
                <button wire:click="autoGenerate" 
                        wire:confirm="Generate 9 kelas standar untuk tahun ajaran {{ $activeAcademicYear->year }}?"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300">
                    ⚡ Auto-Generate Kelas
                </button>
            @endif
            <a href="{{ route('classes.create') }}" 
               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300">
                + Tambah Kelas Manual
            </a>
        </div>
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
        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Cari Kelas</label>
                <input type="text" wire:model.live="search" 
                       class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 "
                       placeholder="Cari nama kelas...">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Tingkat</label>
                <select wire:model.live="filterGrade" 
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                    <option value="all">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Jurusan</label>
                <select wire:model.live="filterMajor" 
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                    <option value="all">Semua Jurusan</option>
                    <option value="MPLB">MPLB</option>
                    <option value="AKL">AKL</option>
                    <option value="BUSANA">BUSANA</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Tahun Ajaran</label>
                <select wire:model.live="filterAcademicYear" 
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                    <option value="current">Tahun Ajaran Aktif</option>
                    <option value="all">Semua Tahun Ajaran</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                    @endforeach
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
                        <th scope="col" class="px-6 py-3">Nama Kelas</th>
                        <th scope="col" class="px-6 py-3">Tingkat</th>
                        <th scope="col" class="px-6 py-3">Jurusan</th>
                        <th scope="col" class="px-6 py-3">Tahun Ajaran</th>
                        <th scope="col" class="px-6 py-3">Wali Kelas</th>
                        <th scope="col" class="px-6 py-3">Siswa</th>
                        <th scope="col" class="px-6 py-3">Ruangan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $index => $class)
                        <tr class="border-b bg-white hover:bg-white ">
                            <td class="px-6 py-4">{{ $classes->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 ">
                                {{ $class->name }}
                            </td>
                            <td class="px-6 py-4">{{ $class->grade }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    {{ $class->major === 'MPLB' ? 'bg-blue-100 text-blue-800 : '' }}
                                    {{ $class->major === 'AKL' ? 'bg-green-100 text-green-800 : '' }}
                                    {{ $class->major === 'BUSANA' ? 'bg-purple-100 text-purple-800 : '' }}">
                                    {{ $class->major }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $class->academicYear->year ?? '-' }}</td>
                            <td class="px-6 py-4">
                                {{ $class->homeroomTeacher?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 ">
                                    {{ $class->getStudentCount() }}/{{ $class->capacity }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $class->room ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('classes.edit', $class->id) }}" 
                                       class="font-medium text-blue-600 hover:underline ">
                                        Edit
                                    </a>
                                    <button wire:click="delete({{ $class->id }})" 
                                            wire:confirm="Yakin ingin menghapus kelas {{ $class->name }}?"
                                            class="font-medium text-red-600 hover:underline ">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-700 ">
                                Tidak ada data kelas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $classes->links() }}
        </div>
    </div>
</div>

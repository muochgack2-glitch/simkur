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
        <div class="grid gap-4 md:grid-cols-5">
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
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Per halaman</label>
                <select wire:model.live="perPage" 
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                    <option value="10">10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                    <option value="100">100 data</option>
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
                                    {{ $class->major === 'MPLB' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $class->major === 'AKL' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $class->major === 'BUSANA' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ $class->major }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $class->academicYear->year ?? '-' }}</td>
                            <td class="px-6 py-4">
                                {{ $class->homeroomTeacher?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($class->getStudentCount() > 0)
                                    <button wire:click="viewStudents({{ $class->id }})"
                                            class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 hover:bg-blue-200 transition cursor-pointer">
                                        👥 {{ $class->getStudentCount() }}/{{ $class->capacity }}
                                    </button>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800">
                                        0/{{ $class->capacity }}
                                    </span>
                                @endif
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

    <!-- Student List Modal -->
    @if($showStudentModal && $selectedClass)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50 p-4">
            <!-- Modal Container -->
            <div class="relative w-full max-w-5xl rounded-lg bg-white shadow-2xl" style="max-height: 90vh;">
                <!-- Header -->
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-6 py-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                👥 Daftar Siswa - {{ $selectedClass->name }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $selectedClass->academicYear->year ?? '-' }} • 
                                Wali Kelas: {{ $selectedClass->homeroomTeacher?->name ?? 'Belum ditentukan' }}
                            </p>
                        </div>
                        <button wire:click="closeStudentModal" type="button"
                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="overflow-y-auto bg-gray-50 px-6 py-4" style="max-height: calc(90vh - 180px);">
                    @if($students->count() > 0)
                        <!-- Summary -->
                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-white p-4 shadow-sm">
                                <div class="text-sm text-gray-600">Total Siswa</div>
                                <div class="mt-1 text-2xl font-bold text-blue-600">{{ $students->count() }}</div>
                            </div>
                            <div class="rounded-lg bg-white p-4 shadow-sm">
                                <div class="text-sm text-gray-600">Kapasitas</div>
                                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $selectedClass->capacity }}</div>
                            </div>
                            <div class="rounded-lg bg-white p-4 shadow-sm">
                                <div class="text-sm text-gray-600">Sisa Kuota</div>
                                <div class="mt-1 text-2xl font-bold text-green-600">
                                    {{ $selectedClass->capacity - $students->count() }}
                                </div>
                            </div>
                        </div>

                        <!-- Student List Table -->
                        <div class="overflow-hidden rounded-lg bg-white shadow">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-700">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-700">NIS</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-700">Nama Siswa</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-700">Username</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-700">Email</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($students as $index => $student)
                                        <tr class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                <span class="rounded bg-gray-100 px-2 py-1 text-xs font-mono font-semibold text-gray-800">
                                                    {{ $student->nisn ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 flex-shrink-0 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $student->name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                <span class="text-xs text-gray-600">{{ $student->username }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs text-gray-600">{{ $student->email ?? '-' }}</span>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                @if($student->is_active)
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="rounded-lg bg-white py-12 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="mt-4 text-gray-600">Belum ada siswa di kelas ini</p>
                            <p class="mt-2 text-sm text-gray-500">Assign siswa ke kelas melalui menu Data Master Pengguna</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 rounded-b-lg border-t border-gray-200 bg-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">💡 Tips:</span> Edit siswa melalui menu Data Master Pengguna
                        </div>
                        <button wire:click="closeStudentModal" type="button"
                                class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>



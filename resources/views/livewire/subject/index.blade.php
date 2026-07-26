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
        <div class="grid gap-4 md:grid-cols-3">
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
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-900 ">Tampilkan per halaman</label>
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
                                @if($subject->teachers()->count() > 0)
                                    <button wire:click="viewTeachers({{ $subject->id }})"
                                            class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 hover:bg-blue-200 transition cursor-pointer">
                                        👨‍🏫 {{ $subject->teachers()->count() }} guru
                                    </button>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800">
                                        0 guru
                                    </span>
                                @endif
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

    <!-- Teacher List Modal -->
    @if($showTeacherModal && $selectedSubject)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50 p-4">
            <!-- Modal Container -->
            <div class="relative w-full max-w-4xl rounded-lg bg-white shadow-2xl" style="max-height: 90vh;">
                <!-- Header -->
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-6 py-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                👨‍🏫 Daftar Guru Pengajar
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Mata Pelajaran: <span class="font-semibold">{{ $selectedSubject->name }}</span>
                                @if($selectedSubject->code)
                                    ({{ $selectedSubject->code }})
                                @endif
                            </p>
                        </div>
                        <button wire:click="closeTeacherModal" type="button"
                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="overflow-y-auto bg-gray-50 px-6 py-4" style="max-height: calc(90vh - 180px);">
                    @if($teachers->count() > 0)
                        <!-- Summary -->
                        <div class="mb-4 rounded-lg bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-600">Total Guru Pengajar</div>
                                    <div class="mt-1 text-3xl font-bold text-blue-600">{{ $teachers->count() }}</div>
                                </div>
                                <div class="rounded-full bg-blue-100 p-3">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Cards -->
                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($teachers as $teacher)
                                <div class="rounded-lg bg-white p-4 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-start">
                                        <!-- Avatar -->
                                        <div class="h-12 w-12 flex-shrink-0 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-lg">
                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                        </div>
                                        
                                        <!-- Info -->
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-base font-semibold text-gray-900">
                                                    {{ $teacher->name }}
                                                </h4>
                                                @if($teacher->is_active)
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">
                                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-2 space-y-1">
                                                @if($teacher->nip_nuptk)
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                                        </svg>
                                                        NIP: {{ $teacher->nip_nuptk }}
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center text-xs text-gray-600">
                                                    <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $teacher->username }}
                                                </div>
                                                
                                                @if($teacher->email)
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ $teacher->email }}
                                                    </div>
                                                @endif

                                                @if($teacher->taught_majors && count($teacher->taught_majors) > 0)
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        @foreach($teacher->taught_majors as $major)
                                                            <span class="inline-flex rounded bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-800">
                                                                {{ $major }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="rounded-lg bg-white py-12 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="mt-4 text-gray-600">Belum ada guru yang mengajar mata pelajaran ini</p>
                            <p class="mt-2 text-sm text-gray-500">Assign guru melalui menu Data Master Pengguna</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 rounded-b-lg border-t border-gray-200 bg-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">💡 Tips:</span> Edit guru melalui menu Data Master Pengguna
                        </div>
                        <button wire:click="closeTeacherModal" type="button"
                                class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>



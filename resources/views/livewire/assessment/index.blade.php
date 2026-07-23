<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 ">
            Kelola Asesmen Gaya Belajar
        </h1>
        <p class="mt-2 text-gray-800 ">
            Kelola asesmen gaya belajar siswa
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    
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

                    <!-- Header Actions -->
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-medium">Daftar Asesmen</h3>
                            <p class="mt-1 text-sm text-gray-800 ">
                                Kelola asesmen gaya belajar siswa
                            </p>
                        </div>
                        @if(auth()->user()->canManageAssessments())
                            <a href="{{ route('assessment.create') }}" 
                               class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 ">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Buat Asesmen Baru
                            </a>
                        @endif
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Cari asesmen..."
                                   class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                        </div>
                        <div>
                            <select wire:model.live="filterType"
                                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="all">Semua Tipe</option>
                                <option value="vark">VARK</option>
                                <option value="diagnostic">Diagnostik</option>
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="filterStatus"
                                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="all">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-800 ">
                            <thead class="bg-white text-xs uppercase text-gray-700 ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Judul Asesmen</th>
                                    <th scope="col" class="px-6 py-3">Tipe</th>
                                    <th scope="col" class="px-6 py-3">Tahun Ajaran</th>
                                    <th scope="col" class="px-6 py-3">Periode</th>
                                    <th scope="col" class="px-6 py-3">Target</th>
                                    <th scope="col" class="px-6 py-3">Progress</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessments as $assessment)
                                    <tr class="border-b bg-white hover:bg-white ">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 ">
                                                {{ $assessment->title }}
                                            </div>
                                            <div class="text-xs text-gray-800 ">
                                                {{ $assessment->total_questions }} pertanyaan
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($assessment->isVark())
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 ">
                                                    VARK
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 ">
                                                    Diagnostik
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $assessment->academicYear->year }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs">
                                                {{ $assessment->start_date->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-gray-800">
                                                s/d {{ $assessment->end_date->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 ">
                                                {{ $assessment->getTargetGradesLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 w-20 overflow-hidden rounded-full bg-gray-200 ">
                                                    <div class="h-full bg-blue-600 " 
                                                         style="width: {{ $assessment->completion_percentage }}%"></div>
                                                </div>
                                                <span class="text-xs">{{ round($assessment->completion_percentage) }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($assessment->is_active)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 ">
                                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-900 ">
                                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('assessment.monitoring', $assessment->id) }}" 
                                                   class="font-medium text-blue-600 hover:underline "
                                                   title="Monitoring">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                </a>
                                                
                                                @if(auth()->user()->canManageAssessments())
                                                    <a href="{{ route('assessment.questions', $assessment->id) }}"
                                                       class="font-medium text-purple-600 hover:underline "
                                                       title="Kelola Soal">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </a>

                                                    <a href="{{ route('assessment.edit', $assessment->id) }}"
                                                       class="font-medium text-green-600 hover:underline "
                                                       title="Edit">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                
                                                    <button wire:click="toggleStatus({{ $assessment->id }})"
                                                            class="font-medium text-yellow-600 hover:underline "
                                                            title="Toggle Status">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <button wire:click="delete({{ $assessment->id }})"
                                                            wire:confirm="Apakah Anda yakin ingin menghapus asesmen ini?"
                                                            class="font-medium text-red-600 hover:underline "
                                                            title="Hapus">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="mb-3 h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-gray-800 ">Tidak ada asesmen ditemukan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $assessments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

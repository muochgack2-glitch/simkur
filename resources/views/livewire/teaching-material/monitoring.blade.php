<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
            Monitoring Kelengkapan Perangkat Ajar
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Pantau kelengkapan 7 dokumen perencanaan wajib per guru per mata pelajaran
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Tugas</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Complete -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Lengkap</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['complete'] }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Incomplete -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Belum Lengkap</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['incomplete'] }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Completion -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Rata-rata</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['avg_completion'] }}%</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Academic Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tahun Akademik
                </label>
                <select wire:model.live="academicYearId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->year }} - {{ $year->semester }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select wire:model.live="statusFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="all">Semua</option>
                    <option value="complete">Lengkap</option>
                    <option value="incomplete">Belum Lengkap</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cari
                </label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Nama guru atau mapel..." 
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-2 flex items-end gap-2">
                <button wire:click="syncRequirements" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Sync dari Jadwal
                </button>
                <button wire:click="refreshAll" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Guru
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Mata Pelajaran
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Kelengkapan
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Dokumen
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Upload Terakhir
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($requirements as $requirement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <!-- Guru -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr($requirement->teacher->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                            {{ $requirement->teacher->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $requirement->teacher->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Mata Pelajaran -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                    {{ $requirement->subject->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $requirement->subject->code }}
                                </div>
                            </td>

                            <!-- Progress -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <div class="w-full max-w-xs">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                {{ $requirement->completion_percentage }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                            <div class="bg-{{ $requirement->status_color }}-600 h-2.5 rounded-full" 
                                                style="width: {{ $requirement->completion_percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Documents Checklist -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 justify-center">
                                    @foreach(\App\Models\TeacherSubjectRequirement::REQUIRED_DOCUMENTS as $key => $label)
                                        @php
                                            $hasDoc = $requirement->{'has_' . $key};
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                            {{ $hasDoc ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}"
                                            title="{{ $label }}">
                                            @if($hasDoc)
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                            {{ strtoupper(str_replace('_', '', $key)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Last Upload -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ $requirement->last_upload_at ? $requirement->last_upload_at->diffForHumans() : '-' }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    bg-{{ $requirement->status_color }}-100 text-{{ $requirement->status_color }}-800 
                                    dark:bg-{{ $requirement->status_color }}-900 dark:text-{{ $requirement->status_color }}-200">
                                    {{ $requirement->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-4 text-lg">Tidak ada data</p>
                                <p class="mt-2 text-sm">Klik tombol "Sync dari Jadwal" untuk menggenerate data dari jadwal mengajar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $requirements->links() }}
        </div>
    </div>
</div>

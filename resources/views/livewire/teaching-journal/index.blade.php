<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📓 Jurnal Mengajar</h1>
            <p class="text-gray-600 mt-1">Catat kehadiran siswa dan materi ajar</p>
        </div>
        
        <div class="flex gap-2">
            <!-- Button Laporan -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    @click="open = !open"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>📊 Laporan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                >
                    @if(auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum() || auth()->user()->isKepalaSekolah())
                        <button wire:click="openReportModal('teacher-summary')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>👨‍🏫</span>
                            <span class="text-sm">Rekap Jurnal Per Guru</span>
                        </button>
                        <button wire:click="openReportModal('attendance-recap')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>📊</span>
                            <span class="text-sm">Rekap Kehadiran Siswa</span>
                        </button>
                        <button wire:click="openReportModal('material-recap')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>📚</span>
                            <span class="text-sm">Rekap Materi Ajar</span>
                        </button>
                        <button wire:click="openReportModal('missing-journals')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>⚠️</span>
                            <span class="text-sm">Monitoring Jurnal Kosong</span>
                        </button>
                        <hr class="my-2">
                    @endif
                    <button wire:click="openReportModal('my-journals')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                        <span>📄</span>
                        <span class="text-sm">Export Jurnal Saya</span>
                    </button>
                </div>
            </div>

            <!-- Button Buat Jurnal Baru -->
            <a href="{{ route('teaching-journal.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Jurnal Baru</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="md:col-span-2">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Cari materi atau nama guru..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Filter Kelas -->
            <select wire:model.live="filterClass" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>

            <!-- Filter Mata Pelajaran -->
            <select wire:model.live="filterSubject" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua Mapel</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>

            <!-- Filter Tanggal -->
            <input 
                type="date" 
                wire:model.live="filterDate"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas & Mapel</th>
                        @if(auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum())
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guru</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kehadiran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $journal->date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $journal->time_slot }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $journal->schoolClass->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $journal->subject->name }}</div>
                                </div>
                            </td>
                            @if(auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum())
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $journal->teacher->name }}</div>
                                </td>
                            @endif
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($journal->topic, 50) }}</div>
                                @if($journal->learning_objective)
                                    <div class="text-xs text-gray-500 mt-1">Tujuan: {{ Str::limit($journal->learning_objective, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-600 font-medium">✓ {{ $journal->present_count }}</span>
                                        <span class="text-yellow-600">⚠ {{ $journal->sick_count }}</span>
                                        <span class="text-blue-600">ⓘ {{ $journal->permission_count }}</span>
                                        <span class="text-red-600">✗ {{ $journal->absent_count }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">Total: {{ $journal->total_students }} siswa</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('teaching-journal.edit', $journal->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if(auth()->user()->isAdmin() || $journal->teacher_id === auth()->id())
                                        <button 
                                            wire:click="delete({{ $journal->id }})"
                                            wire:confirm="Hapus jurnal mengajar tanggal {{ $journal->date->format('d/m/Y') }}?"
                                            class="text-red-600 hover:text-red-900"
                                            title="Hapus"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">Belum ada jurnal mengajar</p>
                                <a href="{{ route('teaching-journal.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Buat jurnal pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($journals->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $journals->links() }}
            </div>
        @endif
    </div>

    <!-- Legend -->
    <div class="mt-4 bg-gray-50 rounded-lg p-4">
        <p class="text-sm text-gray-600 font-medium mb-2">Keterangan Kehadiran:</p>
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="text-green-600">✓ Hadir</span>
            <span class="text-yellow-600">⚠ Sakit</span>
            <span class="text-blue-600">ⓘ Izin</span>
            <span class="text-red-600">✗ Alpha</span>
        </div>
    </div>

    <!-- Report Modal -->
    @if($showReportModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-1">
                    @switch($reportType)
                        @case('teacher-summary')
                            👨‍🏫 Rekap Jurnal Per Guru
                            @break
                        @case('attendance-recap')
                            📊 Rekap Kehadiran Siswa
                            @break
                        @case('material-recap')
                            📚 Rekap Materi Ajar
                            @break
                        @case('missing-journals')
                            ⚠️ Monitoring Jurnal Kosong
                            @break
                        @case('my-journals')
                            📄 Export Jurnal Saya
                            @break
                    @endswitch
                </h3>
                <p class="text-sm text-gray-600">Pilih periode laporan yang ingin di-export</p>
            </div>

            <div class="space-y-4">
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        wire:model="reportStartDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    @error('reportStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input 
                        type="date" 
                        wire:model="reportEndDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    @error('reportEndDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Filter Guru (untuk report teacher-summary) -->
                @if($reportType === 'teacher-summary' && (auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum()))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guru (opsional)</label>
                        <select wire:model="reportTeacher" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Guru</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Kelas (untuk attendance-recap & material-recap) -->
                @if(in_array($reportType, ['attendance-recap', 'material-recap']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas (opsional)</label>
                        <select wire:model="reportClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Quick Select Period -->
                <div class="flex gap-2">
                    <button 
                        type="button"
                        wire:click="$set('reportStartDate', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('reportEndDate', '{{ now()->endOfMonth()->format('Y-m-d') }}')"
                        class="flex-1 px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded"
                    >
                        Bulan Ini
                    </button>
                    <button 
                        type="button"
                        wire:click="$set('reportStartDate', '{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}'); $set('reportEndDate', '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')"
                        class="flex-1 px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded"
                    >
                        Bulan Lalu
                    </button>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button 
                    wire:click="closeReportModal"
                    class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition"
                >
                    Batal
                </button>
                <button 
                    wire:click="generateReport"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                >
                    📄 Generate PDF
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🎓 Manajemen Jadwal PKL</h1>
        <p class="text-gray-600 mt-1">Kelola jadwal kelas yang sedang PKL (Praktik Kerja Lapangan)</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if (!$academicYear)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
            <p class="font-semibold">⚠️ Tidak ada tahun ajaran aktif</p>
            <p class="text-sm mt-1">Aktifkan tahun ajaran terlebih dahulu di menu Academic Years.</p>
        </div>
    @else
        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="text-blue-600 text-2xl">ℹ️</div>
                <div class="flex-1">
                    <h3 class="font-semibold text-blue-900">Tahun Ajaran: {{ $academicYear->year }}</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Nonaktifkan jadwal kelas yang sedang PKL agar tidak muncul di monitoring jurnal.
                        Jadwal dapat diaktifkan kembali setelah PKL selesai.
                    </p>
                    <div class="mt-2 text-xs text-blue-600">
                        Total kelas: {{ count($classes) }} | 
                        Kelas XII: {{ collect($classes)->filter(fn($c) => $c['is_xii'])->count() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow mb-6 p-4" wire:key="action-buttons">
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $xiiClasses = collect($classes)->filter(fn($c) => $c['is_xii']);
                    $hasXiiClasses = $xiiClasses->count() > 0;
                @endphp
                
                @if ($hasXiiClasses)
                    <button wire:click="selectAllXII" 
                            wire:key="btn-select-xii"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                        ✓ Pilih Semua Kelas XII ({{ $xiiClasses->count() }})
                    </button>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-2 rounded-lg text-sm" wire:key="no-xii-warning">
                        ⚠️ Tidak ada kelas XII ditemukan (Total kelas: {{ count($classes) }})
                    </div>
                @endif
                
                <button wire:click="deselectAll" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                    ✗ Batal Pilihan
                </button>
                
                <div class="flex-1"></div>
                
                <button wire:click="prepareDeactivate" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ empty($selectedClasses) ? 'disabled' : '' }}>
                    🔴 Nonaktifkan Jadwal
                </button>
                <button wire:click="prepareActivate" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ empty($selectedClasses) ? 'disabled' : '' }}>
                    🟢 Aktifkan Jadwal
                </button>
            </div>
            
            @if (count($selectedClasses) > 0)
                <div class="mt-3 text-sm text-gray-600">
                    <span class="font-semibold">{{ count($selectedClasses) }} kelas</span> dipilih
                </div>
            @endif
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($classes as $class)
                <div class="bg-white rounded-lg shadow hover:shadow-md transition cursor-pointer border-2 {{ in_array($class['id'], $selectedClasses) ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200' }}"
                     wire:click="toggleClass({{ $class['id'] }})"
                     wire:key="class-card-{{ $class['id'] }}">
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    {{ $class['name'] }}
                                    @if ($class['is_xii'])
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">XII</span>
                                    @endif
                                </h3>
                            </div>
                            <div class="flex items-center gap-2">
                                @if (in_array($class['id'], $selectedClasses))
                                    <span class="text-blue-500 text-2xl">☑</span>
                                @else
                                    <span class="text-gray-300 text-2xl">☐</span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2">
                            <!-- Status Badge -->
                            <div class="flex items-center gap-2">
                                @if ($class['is_active'])
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        🟢 Aktif
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        🔴 Nonaktif
                                    </span>
                                @endif
                            </div>

                            <!-- Schedule Count -->
                            <div class="text-sm text-gray-600">
                                <p class="flex items-center gap-2">
                                    <span class="font-semibold">Total Jadwal:</span>
                                    <span>{{ $class['total_schedules'] }}</span>
                                </p>
                                <div class="flex items-center gap-4 mt-1 text-xs">
                                    <span class="text-green-600">✓ Aktif: {{ $class['active_schedules'] }}</span>
                                    <span class="text-red-600">✗ Nonaktif: {{ $class['inactive_schedules'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (count($classes) === 0)
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <div class="text-gray-400 text-5xl mb-3">📚</div>
                <p class="text-gray-600">Tidak ada kelas yang ditemukan</p>
            </div>
        @endif
    @endif

    <!-- Confirmation Modal -->
    @if ($showConfirmation)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r {{ $action === 'deactivate' ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' }} text-white px-6 py-4">
                    <h3 class="text-xl font-bold">
                        {{ $action === 'deactivate' ? '🔴 Nonaktifkan Jadwal' : '🟢 Aktifkan Jadwal' }}
                    </h3>
                    <p class="text-sm mt-1 opacity-90">Konfirmasi perubahan status jadwal</p>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Kelas yang akan diubah:</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach (App\Models\SchoolClass::whereIn('id', $selectedClasses)->get() as $class)
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $class->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-yellow-800 mb-2">📊 Dampak Perubahan:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• <span class="font-semibold">{{ $affectedSchedulesCount }} jadwal</span> akan {{ $action === 'deactivate' ? 'dinonaktifkan' : 'diaktifkan' }}</li>
                            <li>• <span class="font-semibold">{{ count($affectedTeachers) }} guru</span> terpengaruh</li>
                            @if ($action === 'deactivate')
                                <li>• Jadwal tidak akan muncul di monitoring jurnal</li>
                                <li>• Guru tidak perlu mengisi jurnal untuk jadwal ini</li>
                            @else
                                <li>• Jadwal akan kembali muncul di monitoring jurnal</li>
                                <li>• Guru wajib mengisi jurnal untuk jadwal ini</li>
                            @endif
                        </ul>
                    </div>

                    @if (count($affectedTeachers) > 0)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">👥 Guru yang Terpengaruh:</h4>
                            <div class="max-h-40 overflow-y-auto">
                                <ul class="text-sm text-gray-700 space-y-1">
                                    @foreach ($affectedTeachers as $teacher)
                                        <li>• {{ $teacher }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button wire:click="cancelAction" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                        Batal
                    </button>
                    <button wire:click="confirmAction" 
                            class="bg-{{ $action === 'deactivate' ? 'red' : 'green' }}-600 hover:bg-{{ $action === 'deactivate' ? 'red' : 'green' }}-700 text-white px-4 py-2 rounded-lg transition font-semibold">
                        {{ $action === 'deactivate' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

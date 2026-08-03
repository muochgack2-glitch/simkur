<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jadwal Mengajar</h1>
        <p class="text-sm text-gray-800 mt-1">Kelola jadwal mengajar guru (Tahun Ajaran: {{ $academicYear->year ?? '-' }})</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Day Filter Tabs -->
    <div class="bg-white rounded-lg shadow mb-4 overflow-x-auto">
        <div class="flex border-b">
            <button wire:click="$set('filterDay', '')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === '' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Semua Hari
            </button>
            <button wire:click="$set('filterDay', 'Senin')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === 'Senin' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Senin
            </button>
            <button wire:click="$set('filterDay', 'Selasa')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === 'Selasa' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Selasa
            </button>
            <button wire:click="$set('filterDay', 'Rabu')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === 'Rabu' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Rabu
            </button>
            <button wire:click="$set('filterDay', 'Kamis')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === 'Kamis' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Kamis
            </button>
            <button wire:click="$set('filterDay', 'Jumat')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === 'Jumat' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Jumat
            </button>
            <button wire:click="$set('filterDay', 'Sabtu')" 
                class="px-6 py-3 text-sm font-medium transition {{ $filterDay === 'Sabtu' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Sabtu
            </button>
        </div>
    </div>

    <!-- Other Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input wire:model.live="search" type="text" placeholder="Cari guru, kelas, atau mata pelajaran..." 
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select wire:model.live="filterTeacher" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Guru</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterClass" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button wire:click="create" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Jadwal
                    </span>
                </button>
            </div>
        </div>
        
        <!-- Active Filters Display -->
        @if($filterDay || $filterTeacher || $filterClass || $search)
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="text-sm text-gray-600">Filter aktif:</span>
            
            @if($filterDay)
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                Hari: {{ $filterDay }}
                <button wire:click="$set('filterDay', '')" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
            @endif
            
            @if($filterTeacher)
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                Guru: {{ $teachers->firstWhere('id', $filterTeacher)->name ?? '' }}
                <button wire:click="$set('filterTeacher', '')" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
            @endif
            
            @if($filterClass)
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                Kelas: {{ $classes->firstWhere('id', $filterClass)->name ?? '' }}
                <button wire:click="$set('filterClass', '')" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
            @endif
            
            @if($search)
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                Pencarian: "{{ $search }}"
                <button wire:click="$set('search', '')" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
            @endif
            
            <button wire:click="$set('search', ''); $set('filterDay', ''); $set('filterTeacher', ''); $set('filterClass', '')" 
                class="text-xs text-red-600 hover:text-red-800 font-medium">
                Hapus Semua Filter
            </button>
        </div>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Summary Stats -->
        <div class="bg-gray-50 px-4 py-3 border-b">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-700">
                        <span class="font-semibold text-gray-800">{{ $schedules->total() }}</span> jadwal ditemukan
                    </span>
                    @if($filterDay)
                    <span class="text-gray-600">|</span>
                    <span class="text-gray-700">
                        Hari: <span class="font-semibold text-blue-600">{{ $filterDay }}</span>
                    </span>
                    @endif
                </div>
                <div class="text-xs text-gray-600">
                    Halaman {{ $schedules->currentPage() }} dari {{ $schedules->lastPage() }}
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Mata Pelajaran</th>
                        @if(!$filterDay)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Hari</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Jam</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $schedule->teacher->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $schedule->schoolClass->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $schedule->subject->name }}</td>
                            @if(!$filterDay)
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    {{ $schedule->day_of_week === 'Monday' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $schedule->day_of_week === 'Tuesday' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $schedule->day_of_week === 'Wednesday' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $schedule->day_of_week === 'Thursday' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $schedule->day_of_week === 'Friday' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $schedule->day_of_week === 'Saturday' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $schedule->day_of_week === 'Sunday' ? 'bg-pink-100 text-pink-800' : '' }}">
                                    {{ $schedule->getDayLabel() }}
                                </span>
                            </td>
                            @endif
                            <td class="px-4 py-3 text-sm text-gray-800">
                                @if(is_array($schedule->time_slot_id) && count($schedule->time_slot_id) > 0)
                                    <span 
                                        class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded cursor-help font-medium"
                                        title="{{ $schedule->detailed_time_slots }}"
                                    >
                                        {{ $schedule->compact_time_slots }}
                                    </span>
                                @elseif($schedule->timeSlot)
                                    <span 
                                        class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded cursor-help font-medium"
                                        title="{{ $schedule->timeSlot->name }} ({{ $schedule->timeSlot->time_range }})"
                                    >
                                        {{ $schedule->compact_time_slots }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($schedule->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="toggleActive({{ $schedule->id }})" 
                                        class="text-blue-600 hover:text-blue-800" title="Toggle Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $schedule->id }})" 
                                        class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $schedule->id }})" 
                                        wire:confirm="Yakin ingin menghapus jadwal ini?"
                                        class="text-red-600 hover:text-red-800" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $filterDay ? '6' : '7' }}" class="px-4 py-8 text-center text-gray-700">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-700">Tidak ada jadwal ditemukan</p>
                                    @if($filterDay || $filterTeacher || $filterClass || $search)
                                    <button wire:click="$set('search', ''); $set('filterDay', ''); $set('filterTeacher', ''); $set('filterClass', '')" 
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Hapus semua filter
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t">
            {{ $schedules->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $editMode ? 'Edit Jadwal' : 'Tambah Jadwal' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="text-gray-700 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Guru <span class="text-red-500">*</span></label>
                        <select wire:model="teacher_id" class="w-full px-3 py-2 border rounded-md @error('teacher_id') border-red-500 @enderror">
                            <option value="">Pilih Guru</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Kelas <span class="text-red-500">*</span></label>
                        <select wire:model="class_id" class="w-full px-3 py-2 border rounded-md @error('class_id') border-red-500 @enderror">
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select wire:model="subject_id" class="w-full px-3 py-2 border rounded-md @error('subject_id') border-red-500 @enderror">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Hari <span class="text-red-500">*</span></label>
                        <select wire:model.live="day_of_week" class="w-full px-3 py-2 border rounded-md @error('day_of_week') border-red-500 @enderror">
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                        @error('day_of_week') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        @if($day_of_week)
                            <select wire:model.live="start_time_slot_id" class="w-full px-3 py-2 border rounded-md @error('start_time_slot_id') border-red-500 @enderror">
                                <option value="">Pilih Jam Mulai</option>
                                @forelse($timeSlots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->display_name }}</option>
                                @empty
                                    <option value="" disabled>Tidak ada jam tersedia untuk hari ini</option>
                                @endforelse
                            </select>
                        @else
                            <select disabled class="w-full px-3 py-2 border rounded-md bg-gray-100 text-gray-700 cursor-not-allowed">
                                <option value="">Pilih hari terlebih dahulu</option>
                            </select>
                            <p class="text-xs text-blue-600 mt-1">💡 Silakan pilih hari terlebih dahulu</p>
                        @endif
                        @error('start_time_slot_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        @if($start_time_slot_id)
                            <select wire:model.live="end_time_slot_id" class="w-full px-3 py-2 border rounded-md @error('end_time_slot_id') border-red-500 @enderror">
                                <option value="">Pilih Jam Selesai</option>
                                @forelse($endTimeSlots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->display_name }}</option>
                                @empty
                                    <option value="" disabled>Tidak ada jam tersedia</option>
                                @endforelse
                            </select>
                        @else
                            <select disabled class="w-full px-3 py-2 border rounded-md bg-gray-100 text-gray-700 cursor-not-allowed">
                                <option value="">Pilih jam mulai terlebih dahulu</option>
                            </select>
                            <p class="text-xs text-blue-600 mt-1">💡 Pilih jam mulai terlebih dahulu</p>
                        @endif
                        @error('end_time_slot_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($totalJP > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Total Jam Pelajaran</p>
                                <p class="text-lg font-bold text-blue-600">{{ $totalJP }} JP</p>
                                <p class="text-xs text-blue-700 mt-1">Istirahat akan otomatis di-skip</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="mr-2">
                        <label for="is_active" class="text-sm text-gray-800">Jadwal Aktif</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" wire:click="$set('showModal', false)" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

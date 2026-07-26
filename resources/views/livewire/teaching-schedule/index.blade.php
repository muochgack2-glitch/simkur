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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input wire:model.live="search" type="text" placeholder="Cari..." 
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
                <select wire:model.live="filterDay" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Hari</option>
                    <option value="Monday">Senin</option>
                    <option value="Tuesday">Selasa</option>
                    <option value="Wednesday">Rabu</option>
                    <option value="Thursday">Kamis</option>
                    <option value="Friday">Jumat</option>
                    <option value="Saturday">Sabtu</option>
                    <option value="Sunday">Minggu</option>
                </select>
            </div>
            <div>
                <button wire:click="create" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                    + Tambah Jadwal
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Hari</th>
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
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $schedule->getDayLabel() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <span class="font-medium">{{ $schedule->timeSlot->name }}</span><br>
                                <span class="text-xs text-gray-700">{{ $schedule->timeSlot->time_range }}</span>
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
                            <td colspan="7" class="px-4 py-8 text-center text-gray-700">
                                Tidak ada data jadwal
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
                        <select wire:model="day_of_week" class="w-full px-3 py-2 border rounded-md @error('day_of_week') border-red-500 @enderror">
                            <option value="">Pilih Hari</option>
                            <option value="Monday">Senin</option>
                            <option value="Tuesday">Selasa</option>
                            <option value="Wednesday">Rabu</option>
                            <option value="Thursday">Kamis</option>
                            <option value="Friday">Jumat</option>
                            <option value="Saturday">Sabtu</option>
                            <option value="Sunday">Minggu</option>
                        </select>
                        @error('day_of_week') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Jam Pelajaran <span class="text-red-500">*</span></label>
                        <select wire:model="time_slot_id" class="w-full px-3 py-2 border rounded-md @error('time_slot_id') border-red-500 @enderror">
                            <option value="">Pilih Jam</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot->id }}">{{ $slot->display_name }}</option>
                            @endforeach
                        </select>
                        @error('time_slot_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

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

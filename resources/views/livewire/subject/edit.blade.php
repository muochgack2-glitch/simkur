<div>
    <div class="mb-6">
        <a href="{{ route('subjects.index') }}" class="text-gray-800 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Mata Pelajaran
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 ">Edit Mata Pelajaran</h1>
        <p class="mt-1 text-sm text-gray-800 ">Perbarui informasi mata pelajaran</p>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm ">
        <form wire:submit="save">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <!-- Nama Mata Pelajaran -->
                <div class="md:col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Nama Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                           placeholder="Contoh: Matematika, Bahasa Indonesia">
                    @error('name') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Kode Mata Pelajaran -->
                <div>
                    <label for="code" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Kode Mata Pelajaran
                    </label>
                    <input type="text" id="code" wire:model="code" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                           placeholder="Contoh: MTK, BIND">
                    @error('code') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Status
                    </label>
                    <select id="is_active" wire:model="is_active" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Deskripsi
                    </label>
                    <textarea id="description" wire:model="description" rows="3"
                              class="block w-full p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 "
                              placeholder="Deskripsi mata pelajaran (opsional)"></textarea>
                </div>

                <!-- Guru yang Mengajar Mapel Ini -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 ">
                        👨‍🏫 Guru yang Mengajar Mata Pelajaran Ini
                    </label>
                    @php
                        $teachers = $subject->teachers ?? collect();
                    @endphp
                    
                    @if($teachers->count() > 0)
                        <div class="border border-gray-300 rounded-lg p-3 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                @foreach($teachers as $teacher)
                                    <div class="bg-white p-2 rounded border border-gray-200 flex items-center">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-2">
                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $teacher->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $teacher->nip_nuptk ?: '-' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-3">
                                💡 <strong>Info:</strong> Untuk menambah/mengubah guru, silakan edit melalui menu 
                                <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline">Data Pengguna</a> → Edit Guru → Centang mata pelajaran
                            </p>
                        </div>
                    @else
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-600 mb-2">Belum ada guru yang mengajar mata pelajaran ini</p>
                            <a href="{{ route('users.index') }}" class="text-xs text-blue-600 hover:underline">
                                → Assign guru ke mata pelajaran ini
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Kelas yang Menggunakan Mapel Ini (dari Jadwal) -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 ">
                        🏫 Kelas yang Menggunakan Mata Pelajaran Ini
                    </label>
                    @php
                        $schedules = \App\Models\TeachingSchedule::where('subject_id', $subject->id)
                            ->with(['schoolClass', 'teacher'])
                            ->get()
                            ->groupBy('class_id');
                    @endphp
                    
                    @if($schedules->count() > 0)
                        <div class="border border-gray-300 rounded-lg p-3 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($schedules as $classSchedules)
                                    @php
                                        $class = $classSchedules->first()->schoolClass;
                                        $classTeachers = $classSchedules->pluck('teacher')->unique('id');
                                    @endphp
                                    <div class="bg-white p-3 rounded border border-gray-200">
                                        <div class="font-semibold text-gray-900 mb-1">
                                            🏫 {{ $class->name }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Guru: 
                                            @foreach($classTeachers as $t)
                                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-800 rounded mr-1 mb-1">
                                                    {{ $t->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-3">
                                💡 <strong>Info:</strong> Data dari 
                                <a href="{{ route('teaching-schedule.index') }}" class="text-blue-600 hover:underline">Jadwal Mengajar</a>
                            </p>
                        </div>
                    @else
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-600 mb-2">Mata pelajaran ini belum dijadwalkan di kelas manapun</p>
                            <a href="{{ route('teaching-schedule.index') }}" class="text-xs text-blue-600 hover:underline">
                                → Tambahkan ke jadwal mengajar
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
                    Simpan Perubahan
                </button>
                <a href="{{ route('subjects.index') }}"
                   class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>



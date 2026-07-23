<div>
    <div class="mb-6">
        <a href="{{ route('teaching-journal.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Jurnal
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">✏️ Edit Jurnal Mengajar</h1>
        <p class="mt-1 text-sm text-gray-600">Perbarui data mengajar dan kehadiran siswa</p>
    </div>

    <form wire:submit="update">
        <!-- Info Mengajar -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Informasi Mengajar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Jam Mengajar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Mengajar <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="time_slot"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Jam Mengajar</option>
                        @foreach($timeSlots as $slot)
                            <option value="{{ $slot->display_name }}">{{ $slot->display_name }}</option>
                        @endforeach
                    </select>
                    @error('time_slot') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="class_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear->name }})</option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="subject_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Tujuan Pembelajaran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tujuan Pembelajaran
                    </label>
                    <input 
                        type="text" 
                        wire:model="learning_objective"
                        placeholder="Contoh: Peserta didik mampu memahami konsep..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Materi Pokok -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Materi Pokok <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        wire:model="topic"
                        rows="3"
                        placeholder="Jelaskan materi yang diajarkan hari ini..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    @error('topic') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Metode Pembelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Metode Pembelajaran
                    </label>
                    <select 
                        wire:model="teaching_method"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Metode</option>
                        <option value="Ceramah">Ceramah</option>
                        <option value="Diskusi">Diskusi</option>
                        <option value="Praktik">Praktik</option>
                        <option value="Presentasi">Presentasi</option>
                        <option value="Problem Based Learning">Problem Based Learning</option>
                        <option value="Project Based Learning">Project Based Learning</option>
                        <option value="Discovery Learning">Discovery Learning</option>
                    </select>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Khusus
                    </label>
                    <textarea 
                        wire:model="notes"
                        rows="3"
                        placeholder="Catatan tambahan (opsional)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Daftar Hadir Siswa -->
        @if(count($students) > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">✅ Daftar Hadir Siswa ({{ count($students) }} siswa)</h2>
                
                <!-- Quick Actions -->
                <div class="flex gap-2 mb-4">
                    <button 
                        type="button"
                        wire:click="$set('attendances', {{ json_encode(array_combine(array_column($students->toArray(), 'id'), array_fill(0, count($students), 'hadir'))) }})"
                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200"
                    >
                        Semua Hadir
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $student->nisn ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-2">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    wire:model="attendances.{{ $student->id }}"
                                                    value="hadir"
                                                    class="w-4 h-4 text-green-600 focus:ring-green-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">✓ Hadir</span>
                                            </label>
                                            
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    wire:model="attendances.{{ $student->id }}"
                                                    value="sakit"
                                                    class="w-4 h-4 text-yellow-600 focus:ring-yellow-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">⚠ Sakit</span>
                                            </label>
                                            
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    wire:model="attendances.{{ $student->id }}"
                                                    value="izin"
                                                    class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">ⓘ Izin</span>
                                            </label>
                                            
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    wire:model="attendances.{{ $student->id }}"
                                                    value="alpha"
                                                    class="w-4 h-4 text-red-600 focus:ring-red-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">✗ Alpha</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-end gap-3">
            <a 
                href="{{ route('teaching-journal.index') }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
            >
                Batal
            </a>
            <button 
                type="submit"
                wire:loading.attr="disabled"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
                <span wire:loading.remove>💾 Update Jurnal</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

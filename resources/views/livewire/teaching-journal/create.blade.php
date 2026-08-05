<div>
    <!-- Inline script loaded immediately with HTML -->
    <script>
        // Declare preview functions in global scope IMMEDIATELY
        window.previewPhoto = function(input) {
            console.log('[PHOTO CREATE] Function called');
            const preview = document.getElementById('jsPreview');
            const previewImage = document.getElementById('jsPreviewImage');
            const photoSize = document.getElementById('photoSize');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                console.log('[PHOTO CREATE] File:', file.name, file.size, 'bytes');
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.classList.remove('hidden');
                    const sizeKB = (file.size / 1024).toFixed(2);
                    photoSize.textContent = file.name + ' (' + sizeKB + ' KB)';
                    console.log('[PHOTO CREATE] Preview displayed');
                };
                
                reader.readAsDataURL(file);
            }
        };
        
        window.clearPhoto = function() {
            const input = document.getElementById('photoInput');
            const preview = document.getElementById('jsPreview');
            
            if (input) input.value = '';
            if (preview) preview.classList.add('hidden');
            
            if (window.Livewire) {
                Livewire.find('{{ $_instance->getId() }}').set('activity_photo', null);
            }
        };
        
        console.log('[PHOTO CREATE] Functions loaded:', typeof window.previewPhoto);
    </script>

    <div class="mb-6">
        <a href="{{ route('teaching-journal.index') }}" class="text-gray-800 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Jurnal
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">📝 Buat Jurnal Mengajar</h1>
        <p class="mt-1 text-sm text-gray-800">Isi data mengajar dan catat kehadiran siswa</p>
    </div>

    <form wire:submit="save">
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
                        wire:model.blur="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="class_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }} ({{ $class->students->count() }} siswa)
                            </option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Jam Mengajar -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Mengajar <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-700 mb-3">
                        💡 Centang beberapa jam jika Anda mengajar berturut-turut di kelas & mapel yang sama
                    </p>
                    
                    @if($class_id)
                        @if(count($timeSlots) > 0)
                            <div class="border border-gray-300 rounded-lg p-4 bg-white max-h-60 overflow-y-auto">
                                <div class="space-y-2">
                                    @foreach($timeSlots as $slot)
                                        <label class="flex items-center p-2 hover:bg-white rounded cursor-pointer transition">
                                            <input 
                                                type="checkbox" 
                                                wire:model="selectedTimeSlots"
                                                value="{{ $slot->display_name }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                            >
                                            <span class="ml-3 text-sm text-gray-700 font-medium">
                                                {{ $slot->display_name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            @if(count($selectedTimeSlots) > 0)
                                <p class="mt-2 text-sm text-blue-600 font-medium">
                                    ✓ {{ count($selectedTimeSlots) }} jam terpilih
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-gray-700 italic">Tidak ada jam mengajar untuk tanggal ini</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-500 italic">Pilih kelas terlebih dahulu</p>
                    @endif
                    
                    @error('selectedTimeSlots') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Mata Pelajaran -->
                <div class="md:col-span-2">
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

                <!-- Foto Kegiatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📸 Foto Kegiatan (Opsional)
                    </label>
                    <p class="text-xs text-gray-600 mb-3">
                        💡 Unggah foto dokumentasi kegiatan pembelajaran. Max 10MB, format: JPG, JPEG, PNG, WEBP
                    </p>
                    
                    <div class="flex items-start gap-4">
                        <!-- Upload Button -->
                        <div class="flex-shrink-0">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Pilih Foto</span>
                                <input 
                                    type="file" 
                                    wire:model="activity_photo"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    capture="environment"
                                    class="hidden"
                                    id="photoInput"
                                    onchange="if(window.previewPhoto){window.previewPhoto(this)}else{console.error('previewPhoto not loaded')}"
                                >
                            </label>
                        </div>

                        <!-- Preview Container -->
                        <div class="flex-1">
                            <!-- JavaScript Preview (shown immediately) -->
                            <div id="jsPreview" class="hidden" wire:ignore>
                                <div class="relative inline-block">
                                    <img 
                                        id="jsPreviewImage"
                                        alt="Preview" 
                                        class="w-32 h-32 object-cover rounded-lg border-2 border-green-300"
                                    >
                                    <button 
                                        type="button"
                                        onclick="clearPhoto()"
                                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg"
                                        title="Hapus foto"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-green-600 mt-2 font-semibold">✓ Foto siap diupload</p>
                                <p class="text-xs text-gray-500 mt-1" id="photoSize"></p>
                            </div>

                            <!-- Loading Indicator -->
                            <div wire:loading wire:target="activity_photo" class="flex items-center text-blue-600">
                                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm">Mengupload foto...</span>
                            </div>
                        </div>
                    </div>
                    
                    @error('activity_photo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nama Siswa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">NIS</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $student->nisn ?? '-' }}</td>
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
        @elseif($class_id)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-yellow-800">⚠️ Tidak ada siswa di kelas ini. Pastikan siswa sudah di-assign ke kelas.</p>
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
                <span wire:loading.remove>💾 Simpan Jurnal</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>


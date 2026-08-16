<div>
    <!-- Inline script loaded immediately with HTML -->
    <script>
        // Compress image before sending to Livewire
        function compressImage(file, maxWidth, maxHeight, quality) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Calculate new dimensions
                        let width = img.width;
                        let height = img.height;
                        
                        if (width > height) {
                            if (width > maxWidth) {
                                height = Math.round(height * maxWidth / width);
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width = Math.round(width * maxHeight / height);
                                height = maxHeight;
                            }
                        }
                        
                        // Create canvas and resize
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Convert to JPEG with compression
                        const compressedBase64 = canvas.toDataURL('image/jpeg', quality);
                        resolve(compressedBase64);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }
        
        // Declare preview functions in global scope IMMEDIATELY
        window.previewPhoto = async function(input) {
            console.log('[PHOTO CREATE] Function called');
            const preview = document.getElementById('jsPreview');
            const previewImage = document.getElementById('jsPreviewImage');
            const photoSize = document.getElementById('photoSize');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                console.log('[PHOTO CREATE] Original file:', file.name, file.size, 'bytes');
                
                try {
                    // Compress image to max 1024x1024 at 75% quality
                    const compressedBase64 = await compressImage(file, 1024, 1024, 0.75);
                    
                    // Calculate compressed size
                    const compressedSize = Math.round((compressedBase64.length - 'data:image/jpeg;base64,'.length) * 0.75);
                    const compressedSizeKB = (compressedSize / 1024).toFixed(2);
                    
                    console.log('[PHOTO CREATE] Compressed size:', compressedSizeKB, 'KB');
                    
                    // Show preview
                    previewImage.src = compressedBase64;
                    preview.classList.remove('hidden');
                    photoSize.textContent = file.name + ' (compressed: ' + compressedSizeKB + ' KB)';
                    console.log('[PHOTO CREATE] Preview displayed');
                    
                    // Send Base64 to Livewire
                    if (window.Livewire) {
                        Livewire.find('{{ $_instance->getId() }}').set('photo_base64', compressedBase64);
                        console.log('[PHOTO CREATE] Base64 sent to Livewire');
                    }
                } catch (error) {
                    console.error('[PHOTO CREATE] Compression failed:', error);
                    alert('Gagal memproses foto. Silakan coba lagi.');
                }
            }
        };
        
        window.clearPhoto = function() {
            const input = document.getElementById('photoInput');
            const preview = document.getElementById('jsPreview');
            
            if (input) input.value = '';
            if (preview) preview.classList.add('hidden');
            
            if (window.Livewire) {
                Livewire.find('{{ $_instance->getId() }}').set('activity_photo', null);
                Livewire.find('{{ $_instance->getId() }}').set('photo_base64', null);
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
                        wire:model.live="date"
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
                        💡 Pilih rentang jam dari jam mulai sampai jam selesai (istirahat akan otomatis di-skip)
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Jam Mulai -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jam Mulai</label>
                            @if($date && count($timeSlots) > 0)
                                <select 
                                    wire:model.live="start_time_slot_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('start_time_slot_id') border-red-500 @enderror"
                                >
                                    <option value="">Pilih Jam Mulai</option>
                                    @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}">{{ $slot->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('start_time_slot_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @else
                                <select disabled class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed">
                                    <option value="">{{ $date ? 'Tidak ada jam tersedia' : 'Pilih tanggal terlebih dahulu' }}</option>
                                </select>
                                @if(!$date)
                                <p class="text-xs text-blue-600 mt-1">💡 Silakan pilih tanggal terlebih dahulu</p>
                                @endif
                            @endif
                        </div>

                        <!-- Jam Selesai -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jam Selesai</label>
                            @if($start_time_slot_id)
                                <select 
                                    wire:model.live="end_time_slot_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('end_time_slot_id') border-red-500 @enderror"
                                >
                                    <option value="">Pilih Jam Selesai</option>
                                    @forelse($endTimeSlots as $slot)
                                        <option value="{{ $slot->id }}">{{ $slot->display_name }}</option>
                                    @empty
                                        <option value="" disabled>Tidak ada jam tersedia</option>
                                    @endforelse
                                </select>
                                @error('end_time_slot_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @else
                                <select disabled class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed">
                                    <option value="">Pilih jam mulai terlebih dahulu</option>
                                </select>
                                <p class="text-xs text-blue-600 mt-1">💡 Pilih jam mulai terlebih dahulu</p>
                            @endif
                        </div>
                    </div>

                    @if($totalJP > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mt-3">
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
                </div>

                <!-- Mata Pelajaran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="subject_id"
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

                <!-- Legenda Scan QR -->
                <div class="flex flex-wrap items-center gap-3 mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <span class="text-xs font-semibold text-blue-800 mr-1">Keterangan Scan QR:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">&#x2705; 06:45</span>
                    <span class="text-xs text-gray-600">= Sudah scan</span>
                    <span class="text-gray-300">|</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">&#x26A0;&#xFE0F; 07:15</span>
                    <span class="text-xs text-gray-600">= Terlambat</span>
                    <span class="text-gray-300">|</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">&#x274C; Blm scan</span>
                    <span class="text-xs text-gray-600">= Belum scan</span>
                    <span class="text-gray-300">|</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">&#x1F4DD; Izin</span>
                    <span class="text-xs text-gray-600">= Izin</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nama Siswa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">NIS</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Status Kehadiran</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Scan QR</th>
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
                                    <td class="px-4 py-3 text-center text-sm">
                                        @if(isset($scanStatuses[$student->id]) && ($scanStatuses[$student->id]['source'] ?? '') === 'scan')
                                            @php
                                                $sStatus = $scanStatuses[$student->id]['status'] ?? 'unknown';
                                                $sTime = isset($scanStatuses[$student->id]['check_in_time']) && $scanStatuses[$student->id]['check_in_time'] ? \Carbon\Carbon::parse($scanStatuses[$student->id]['check_in_time'])->format('H:i') : null;
                                            @endphp
                                            @if($sStatus === 'hadir' && $sTime)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800" title="Scan pukul {{ $sTime }}">&#x2705; {{ $sTime }}</span>
                                            @elseif($sStatus === 'terlambat' && $sTime)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800" title="Terlambat {{ $sTime }}">&#x26A0;&#xFE0F; {{ $sTime }}</span>
                                            @elseif($sStatus === 'izin')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">&#x1F4DD; Izin</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">&#x274C; Blm scan</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-400">&mdash;</span>
                                        @endif
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
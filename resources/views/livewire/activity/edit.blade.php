<div>
    <div class="max-w-3xl">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Kegiatan</h1>
            <p class="text-gray-600 mt-1">Ubah data kegiatan {{ $activity->name }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="update" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Activity Type -->
                <div>
                    <label for="activity_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="activity_type_id"
                        wire:model.live="activity_type_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('activity_type_id') border-red-300 @enderror"
                    >
                        <option value="">-- Pilih Jenis Kegiatan --</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('activity_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="start_date"
                            wire:model.live="start_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-300 @enderror"
                        >
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="end_date"
                            wire:model.live="end_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('end_date') border-red-300 @enderror"
                        >
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="semester_id"
                        wire:model="semester_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('semester_id') border-red-300 @enderror"
                    >
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                        @endforeach
                    </select>
                    @error('semester_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Tingkat Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Target Tingkat Kelas <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <!-- All Grades Checkbox -->
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model.live="targetAllGrades"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                            >
                            <span class="text-sm font-semibold text-gray-900">Semua Kelas (X, XI, XII)</span>
                        </label>

                        <div class="border-t border-gray-300 my-2 pt-2">
                            <p class="text-xs text-gray-600 mb-2">Atau pilih tingkat tertentu:</p>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <!-- Kelas X -->
                                <label class="flex items-center space-x-2 cursor-pointer bg-white px-3 py-2 rounded border border-gray-300 hover:bg-blue-50 transition @if($targetGradeX) ring-2 ring-blue-500 bg-blue-50 @endif">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="targetGradeX"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                    >
                                    <span class="text-sm font-medium text-gray-700">Kelas X</span>
                                </label>

                                <!-- Kelas XI -->
                                <label class="flex items-center space-x-2 cursor-pointer bg-white px-3 py-2 rounded border border-gray-300 hover:bg-yellow-50 transition @if($targetGradeXI) ring-2 ring-yellow-500 bg-yellow-50 @endif">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="targetGradeXI"
                                        class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-2 focus:ring-yellow-500"
                                    >
                                    <span class="text-sm font-medium text-gray-700">Kelas XI</span>
                                </label>

                                <!-- Kelas XII -->
                                <label class="flex items-center space-x-2 cursor-pointer bg-white px-3 py-2 rounded border border-gray-300 hover:bg-purple-50 transition @if($targetGradeXII) ring-2 ring-purple-500 bg-purple-50 @endif">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="targetGradeXII"
                                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                    >
                                    <span class="text-sm font-medium text-gray-700">Kelas XII</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    @error('targetGrades')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pilih tingkat kelas yang akan melihat kegiatan ini di kalender mereka.
                        Contoh: MPLS untuk Kelas X saja, PKL untuk Kelas XII saja.
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea 
                        id="description"
                        wire:model="description"
                        rows="3"
                        placeholder="Keterangan tambahan tentang kegiatan ini..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-300 @enderror"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color (Optional Override) -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Warna Kustom (Opsional)
                    </label>
                    <div class="flex items-center space-x-4">
                        <input 
                            type="color" 
                            id="color"
                            wire:model.live="color"
                            class="h-10 w-16 border border-gray-300 rounded-lg cursor-pointer"
                        >
                        <input 
                            type="text" 
                            wire:model.live="color"
                            placeholder="#3B82F6"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Preview:</span>
                            <div class="w-10 h-10 rounded-lg border-2 border-gray-300" style="background-color: {{ $color ?: '#3B82F6' }}"></div>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan warna default dari jenis kegiatan</p>
                </div>

                <!-- Weekend Warning -->
                @if($weekendWarning && $hasWeekendDays)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-red-800">Peringatan: Kegiatan di Hari Libur</p>
                                <p class="text-sm text-red-700 mt-1">
                                    Tanggal yang Anda pilih mencakup <strong>hari Sabtu atau Minggu</strong>. 
                                    Kegiatan sekolah tidak boleh dijadwalkan di hari weekend.
                                </p>
                                <p class="text-sm text-red-700 mt-2">
                                    Silakan pilih tanggal di hari kerja (Senin-Jumat) saja.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Conflict Warning -->
                @if(count($conflicts) > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-yellow-800">Peringatan: Bentrok Tanggal</p>
                                <p class="text-sm text-yellow-700 mt-1">Terdapat {{ count($conflicts) }} kegiatan yang bertabrakan tanggal:</p>
                                <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                                    @foreach($conflicts as $conflict)
                                        <li>{{ $conflict['name'] }} ({{ $conflict['activity_type']['name'] }})</li>
                                    @endforeach
                                </ul>
                                <p class="text-xs text-yellow-600 mt-2">Anda masih dapat menyimpan perubahan ini.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Activity Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Informasi Kegiatan</p>
                            <div class="text-sm text-blue-700 mt-1 space-y-1">
                                <p>Dibuat oleh: <strong>{{ $activity->creator?->name ?? 'Sistem' }}</strong></p>
                                <p>Dibuat pada: <strong>{{ $activity->created_at->format('d M Y H:i') }}</strong></p>
                                @if($activity->updated_at != $activity->created_at)
                                    <p>Terakhir diubah: <strong>{{ $activity->updated_at->format('d M Y H:i') }}</strong></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @error('error')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a 
                        href="{{ route('activities.index') }}"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                    >
                        Batal
                    </a>
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2"
                    >
                        <span wire:loading.remove>
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Perbarui
                        </span>
                        <span wire:loading class="flex items-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memperbarui...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

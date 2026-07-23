<div>
    <div class="max-w-3xl">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Jenis Kegiatan</h1>
            <p class="text-gray-800 mt-1">Ubah data jenis kegiatan</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="update" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name"
                        placeholder="Contoh: Penilaian Tengah Semester"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="code"
                        wire:model="code"
                        placeholder="PTS"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono uppercase @error('code') border-red-300 @enderror"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-700">Kode singkat untuk identifikasi (2-5 karakter)</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        id="description"
                        wire:model="description"
                        rows="3"
                        placeholder="Deskripsi singkat tentang jenis kegiatan ini..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-300 @enderror"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color Picker -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Warna <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-4">
                        <input 
                            type="color" 
                            id="color"
                            wire:model.live="color"
                            class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer"
                        >
                        <div class="flex-1">
                            <input 
                                type="text" 
                                wire:model.live="color"
                                placeholder="#3B82F6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('color') border-red-300 @enderror"
                            >
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-800">Preview:</span>
                            <div class="w-12 h-12 rounded-lg border-2 border-gray-300" style="background-color: {{ $color }}"></div>
                        </div>
                    </div>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-700">Warna akan digunakan di kalender (format HEX: #RRGGBB)</p>
                </div>

                <!-- Preset Colors -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Warna Preset
                    </label>
                    <div class="grid grid-cols-8 gap-2">
                        @foreach([
                            '#EF4444' => 'Merah',
                            '#F59E0B' => 'Oranye',
                            '#EAB308' => 'Kuning',
                            '#10B981' => 'Hijau',
                            '#3B82F6' => 'Biru',
                            '#8B5CF6' => 'Ungu',
                            '#EC4899' => 'Pink',
                            '#6B7280' => 'Abu'
                        ] as $presetColor => $colorName)
                            <button 
                                type="button"
                                wire:click="$set('color', '{{ $presetColor }}')"
                                class="w-full h-10 rounded-lg border-2 hover:border-gray-400 transition {{ $color === $presetColor ? 'border-gray-900 ring-2 ring-offset-2 ring-gray-900' : 'border-gray-300' }}"
                                style="background-color: {{ $presetColor }}"
                                title="{{ $colorName }}"
                            ></button>
                        @endforeach
                    </div>
                </div>

                <!-- Flags -->
                <div class="space-y-3 border-t border-gray-200 pt-6">
                    <!-- Is Exam -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input 
                                type="checkbox" 
                                id="is_exam"
                                wire:model="is_exam"
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                        </div>
                        <div class="ml-3">
                            <label for="is_exam" class="text-sm font-medium text-gray-700">
                                Ujian / Penilaian
                            </label>
                            <p class="text-xs text-gray-700 mt-1">
                                Tandai jika kegiatan ini adalah ujian atau penilaian (PTS, PAS, PAT, dll)
                            </p>
                        </div>
                    </div>

                    <!-- Is Holiday -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input 
                                type="checkbox" 
                                id="is_holiday"
                                wire:model="is_holiday"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            >
                        </div>
                        <div class="ml-3">
                            <label for="is_holiday" class="text-sm font-medium text-gray-700">
                                Libur
                            </label>
                            <p class="text-xs text-gray-700 mt-1">
                                Tandai jika kegiatan ini adalah hari libur
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Usage Info -->
                @if($activityType->activities_count > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Informasi Penggunaan</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    Jenis kegiatan ini digunakan di <strong>{{ $activityType->activities_count }} kegiatan</strong>.
                                    Perubahan warna akan mempengaruhi tampilan kalender.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @error('error')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a 
                        href="{{ route('activity-types.index') }}"
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

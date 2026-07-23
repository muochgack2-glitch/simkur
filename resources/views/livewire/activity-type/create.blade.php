<div>
    <div class="max-w-3xl">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Jenis Kegiatan</h1>
            <p class="text-gray-800 mt-1">Buat jenis kegiatan baru untuk kalender akademik</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name"
                        wire:change="generateCode"
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
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            id="code"
                            wire:model="code"
                            placeholder="PTS"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono uppercase @error('code') border-red-300 @enderror"
                        >
                        <button 
                            type="button"
                            wire:click="generateCode"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition"
                            title="Generate kode dari nama"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
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
                            Simpan
                        </span>
                        <span wire:loading class="flex items-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

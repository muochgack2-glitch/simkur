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

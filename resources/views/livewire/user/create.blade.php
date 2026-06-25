<div>
    <div class="max-w-3xl">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Pengguna</h1>
            <p class="text-gray-600 mt-1">Buat akun pengguna baru untuk sistem E-KALDIK</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name"
                        wire:change="generateUsername"
                        placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            id="username"
                            wire:model="username"
                            placeholder="budisantoso"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono lowercase @error('username') border-red-300 @enderror"
                        >
                        <button 
                            type="button"
                            wire:click="generateUsername"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition"
                            title="Generate username dari nama"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Username untuk login (huruf, angka, underscore saja)</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        wire:model="email"
                        placeholder="budi.santoso@example.com"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-300 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="password"
                        wire:model="password"
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono @error('password') border-red-300 @enderror"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Default password: <span class="font-mono font-semibold">password</span> (user bisa ganti nanti)
                    </p>
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="role"
                        wire:model="role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-300 @enderror"
                    >
                        <option value="guru">Guru</option>
                        <option value="waka_kurikulum">Waka Kurikulum</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 space-y-1 text-xs text-gray-600">
                        <p><span class="font-semibold">Guru:</span> Hanya bisa melihat kalender dan hari efektif</p>
                        <p><span class="font-semibold">Waka Kurikulum:</span> Bisa mengelola kalender, kegiatan, dan hari efektif</p>
                        <p><span class="font-semibold">Admin:</span> Akses penuh ke semua fitur termasuk pengaturan dan user management</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-start border-t border-gray-200 pt-6">
                    <div class="flex items-center h-5">
                        <input 
                            type="checkbox" 
                            id="is_active"
                            wire:model="is_active"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        >
                    </div>
                    <div class="ml-3">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Aktifkan Akun
                        </label>
                        <p class="text-xs text-gray-500 mt-1">
                            User hanya bisa login jika akun dalam status aktif
                        </p>
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
                        href="{{ route('users.index') }}"
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

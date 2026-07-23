<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
    <p class="text-sm text-gray-600 mb-6">Silakan login untuk mengakses sistem</p>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-4">
        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                Username
            </label>
            <input 
                type="text" 
                id="username"
                wire:model="username"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('username') border-red-300 @enderror"
                placeholder="Masukkan username"
                autofocus
            >
            @error('username')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
            </label>
            <input 
                type="password" 
                id="password"
                wire:model="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('password') border-red-300 @enderror"
                placeholder="Masukkan password"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="remember"
                wire:model="remember"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <label for="remember" class="ml-2 text-sm text-gray-700">
                Ingat saya
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2"
        >
            <span wire:loading.remove>Masuk</span>
            <span wire:loading class="flex items-center space-x-2">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memproses...</span>
            </span>
        </button>
    </form>

    <!-- Default Credentials Info (Development) -->
    @if (app()->environment('local'))
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-xs font-semibold text-blue-800 mb-2">Default Credentials:</p>
            <div class="text-xs text-blue-700 space-y-1">
                <p><strong>Admin:</strong> admin / password</p>
                <p><strong>Waka:</strong> waka / password</p>
                <p><strong>Guru:</strong> guru1 / password</p>
            </div>
        </div>
    @endif
</div>

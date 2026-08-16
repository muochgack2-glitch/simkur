<div>
    <div class="mb-6">
        <h2 class="auth-font-display text-[1.7rem] leading-tight" style="color:var(--ink); font-weight:560;">
            Selamat datang kembali
        </h2>
        <p class="text-sm mt-1" style="color:#6B6A4E;">Isi data di bawah untuk masuk ke sistem</p>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-5 flex items-start gap-2.5 text-sm" style="color:#1F4E32;">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-start gap-2.5 text-sm" style="color:var(--stamp);">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M4.93 4.93l14.14 14.14"/>
                <circle cx="12" cy="12" r="9" stroke-linecap="round"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit="login" class="space-y-6" x-data="{ showPassword: false }">
        <!-- Username -->
        <div class="auth-field">
            <label for="username" class="auth-field-label block uppercase mb-1.5">
                Nama Pengguna
            </label>
            <input
                type="text"
                id="username"
                wire:model="username"
                class="w-full"
                style="{{ $errors->has('username') ? 'border-bottom-color:var(--stamp);' : '' }}"
                placeholder="cth. budi.santoso"
                autofocus
                autocomplete="username"
            >
            @error('username')
                <p class="mt-2 text-xs flex items-center gap-1.5" style="color:var(--stamp);">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M4.93 4.93l14.14 14.14"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div class="auth-field">
            <label for="password" class="auth-field-label block uppercase mb-1.5">
                Kata Sandi
            </label>
            <div class="relative flex items-center">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    wire:model="password"
                    class="w-full pr-8"
                    style="{{ $errors->has('password') ? 'border-bottom-color:var(--stamp);' : '' }}"
                    placeholder="Masukkan kata sandi"
                    autocomplete="current-password"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-0 p-1 text-[#8A8563] hover:text-[color:var(--ink)] transition"
                    :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                >
                    <svg x-show="!showPassword" class="w-4.5 h-4.5" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg x-show="showPassword" x-cloak class="w-4.5 h-4.5" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-xs flex items-center gap-1.5" style="color:var(--stamp);">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M4.93 4.93l14.14 14.14"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm cursor-pointer select-none" style="color:#4A4934;">
                <input
                    type="checkbox"
                    wire:model="remember"
                    class="w-4 h-4 rounded-sm"
                    style="accent-color: var(--green-800); border-color: var(--paper-line);"
                >
                Ingat saya
            </label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75 cursor-not-allowed"
            class="auth-btn w-full text-[color:var(--paper)] font-semibold py-3 px-4 rounded-md transition duration-200 flex items-center justify-center gap-2"
        >
            <span wire:loading.remove class="flex items-center justify-center gap-2">
                Masuk ke Sistem
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                </svg>
            </span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            </span>
        </button>
    </form>
</div>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - e-KALDIK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-lg mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">e-KALDIK</h1>
            <p class="text-gray-600">Sistem Kalender Pendidikan</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Login</h2>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Username Field -->
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('username') border-red-500 @enderror"
                        placeholder="Masukkan username"
                        required
                        autofocus
                    >
                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                        placeholder="Masukkan password"
                        required
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-200 transform hover:-translate-y-0.5"
                >
                    Login
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                &copy; {{ date('Y') }} e-KALDIK. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

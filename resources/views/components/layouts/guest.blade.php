<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - SIM Kurikulum SMK PGRI Blora</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 min-h-screen flex items-center justify-center p-4">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-5 rounded-full"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white opacity-5 rounded-full"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white opacity-3 rounded-full"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-2xl shadow-2xl mb-6 transform hover:scale-105 transition duration-300">
                <svg class="w-14 h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">SIM Kurikulum</h1>
            <div class="inline-block bg-white bg-opacity-20 backdrop-blur-sm px-6 py-2 rounded-full mb-2">
                <p class="text-xl font-semibold text-white">SMK PGRI Blora</p>
            </div>
            <p class="text-sm text-blue-100">Sistem Informasi Manajemen Kurikulum</p>
            <div class="flex items-center justify-center space-x-2 mt-3">
                <span class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-xs text-white font-medium">MPLB</span>
                <span class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-xs text-white font-medium">AKL</span>
                <span class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-xs text-white font-medium">BUSANA</span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-sm">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-white">
            <p class="font-medium">&copy; {{ date('Y') }} SMK PGRI Blora</p>
            <p class="text-xs text-blue-100 mt-1">All rights reserved</p>
        </div>
    </div>

    @livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - SIM Kurikulum SMK PGRI Blora</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">SIM Kurikulum</h1>
            <p class="text-lg font-semibold text-blue-600 mb-1">SMK PGRI Blora</p>
            <p class="text-sm text-gray-600">Sistem Informasi Manajemen Kurikulum</p>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} SMK PGRI Blora. All rights reserved.</p>
            <p class="text-xs text-gray-500 mt-1">MPLB • AKL • BUSANA</p>
        </div>
    </div>

    @livewireScripts
</body>
</html>

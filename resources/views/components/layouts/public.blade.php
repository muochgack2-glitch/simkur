<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ $title ?? 'SMK PGRI Blora' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Force reload CSS - cache buster */
        /* Version: {{ now()->timestamp }} */
        
        /* Ensure dark mode transitions work */
        html, body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>

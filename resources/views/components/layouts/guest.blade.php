<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - SIM Kurikulum SMK PGRI Blora</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        .login-bg {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
        }
        .login-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
        }
        .login-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: loginFloat 20s ease-in-out infinite;
        }
        .login-shape:nth-child(1) { width: 300px; height: 300px; top: -80px; right: -60px; }
        .login-shape:nth-child(2) { width: 200px; height: 200px; bottom: -50px; left: -40px; animation-delay: -7s; }
        .login-shape:nth-child(3) { width: 150px; height: 150px; top: 40%; right: 10%; animation-delay: -14s; }
        @keyframes loginFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .login-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center p-4">

    <div class="login-shape"></div>
    <div class="login-shape"></div>
    <div class="login-shape"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 shadow-lg mb-5">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">SIM Kurikulum</h1>
            <p class="text-blue-200 text-sm font-medium">SMK PGRI Blora</p>
        </div>

        <!-- Card -->
        <div class="login-card rounded-2xl px-8 py-8">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-xs text-blue-200/70">
            <p>&copy; {{ date('Y') }} SMK PGRI Blora</p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
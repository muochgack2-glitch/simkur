<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard - e-KALDIK' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Livewire CDN Fallback -->
    <script src="https://cdn.jsdelivr.net/gh/livewire/livewire@v3.x.x/dist/livewire.min.js" defer></script>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-800">e-KALDIK</span>
                    </div>

                    <!-- Main Menu -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            Dashboard
                        </a>
                        
                        @if(auth()->user()->canManageActivities() || auth()->user()->isGuru())
                            <a href="{{ route('activities.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('activities.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                Kalender
                            </a>
                            
                            <a href="{{ route('effective-days.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('effective-days.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                Hari Efektif
                            </a>
                            
                            <a href="{{ route('academic-years.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('academic-years.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                Tahun Pelajaran
                            </a>
                            
                            <a href="{{ route('activity-types.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('activity-types.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                Jenis Kegiatan
                            </a>
                        @endif
                        
                        @if(auth()->user()->canManageUsers())
                            <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                Pengguna
                            </a>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('settings.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                Pengaturan
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right Navigation -->
                <div class="flex items-center space-x-4">
                    <!-- User Info -->
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>

                    <!-- Dropdown Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200"
                             style="display: none;">
                            
                            <a href="{{ route('profile.change-password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Ganti Password
                                </span>
                            </a>
                            
                            <hr class="my-1">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - SIM Kurikulum SMK PGRI Blora</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0 flex items-center">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                        @elseif(file_exists(public_path('images/logo.jpg')))
                            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-8 h-8 object-contain">
                        @elseif(file_exists(public_path('images/logo.svg')))
                            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-8 h-8 object-contain">
                        @else
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        @endif
                        <div class="ml-2">
                            <div class="text-sm font-bold text-gray-800 leading-tight">SIM Kurikulum</div>
                            <div class="text-xs text-gray-600 leading-tight">SMK PGRI Blora</div>
                        </div>
                    </div>

                    <!-- Main Menu -->
                    <div class="hidden md:flex items-center space-x-1">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            📊 Dashboard
                        </a>
                        
                        <!-- Kalender Akademik (Dropdown for Admin/Guru) -->
                        @if(auth()->user()->canManageActivities() || auth()->user()->isGuru())
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('activities.*') || request()->routeIs('effective-days.*') || request()->routeIs('academic-years.*') || request()->routeIs('activity-types.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition flex items-center">
                                    📅 Kalender Akademik
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute left-0 mt-2 w-56 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <a href="{{ route('activities.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📆 Kalender Kegiatan
                                        </a>
                                        <a href="{{ route('effective-days.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📊 Hari Efektif
                                        </a>
                                        <a href="{{ route('academic-years.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📚 Tahun Pelajaran
                                        </a>
                                        <a href="{{ route('activity-types.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            🏷️ Jenis Kegiatan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Master Data (Admin & Kepsek) -->
                        @if(auth()->user()->canManageUsers())
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('subjects.*') || request()->routeIs('classes.*') || request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition flex items-center">
                                    📂 Master Data
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute left-0 mt-2 w-56 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            👥 Data Pengguna
                                        </a>
                                        <a href="{{ route('classes.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            🏫 Data Kelas
                                        </a>
                                        <a href="{{ route('subjects.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📚 Mata Pelajaran
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Jurnal Mengajar (Guru, Waka, Kepsek, Admin) -->
                        @if(auth()->user()->isGuru() || auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum())
                            <a href="{{ route('teaching-journal.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('teaching-journal.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                📓 Jurnal Mengajar
                            </a>
                        @endif
                        
                        <!-- Asesmen (Dropdown for Admin/Waka/Guru/Kepsek) -->
                        @if(auth()->user()->canManageAssessments() || auth()->user()->canViewAllStudentProfiles() || auth()->user()->isSiswa())
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('assessment.*') || request()->routeIs('student.assessment.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition flex items-center">
                                    📝 Asesmen
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute left-0 mt-2 w-56 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        @if(auth()->user()->isSiswa())
                                            <a href="{{ route('student.assessment.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                ✍️ Asesmen Saya
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->canManageAssessments())
                                            <a href="{{ route('assessment.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                ⚙️ Kelola Asesmen
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->canViewAllStudentProfiles())
                                            <a href="{{ route('assessment.class-report') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                📈 Profil Belajar Siswa
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Pengaturan (Admin only) -->
                        @if(auth()->user()->isAdmin())
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }} transition flex items-center">
                                    ⚙️ Pengaturan
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute left-0 mt-2 w-56 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            🏫 Pengaturan Umum
                                        </a>
                                        <a href="{{ route('settings.time-slots') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            ⏰ Jam Mengajar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Navigation -->
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button (hidden on desktop) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- User Info -->
                    <div class="text-right hidden sm:block">
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
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden pb-4"
                 style="display: none;">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📊 Dashboard
                    </a>
                    
                    <!-- Kalender Akademik -->
                    @if(auth()->user()->canManageActivities() || auth()->user()->isGuru())
                        <div class="border-l-2 border-gray-200 pl-2 ml-2 space-y-1">
                            <div class="text-xs font-semibold text-gray-500 px-3 py-1">📅 Kalender Akademik</div>
                            <a href="{{ route('activities.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                📆 Kalender Kegiatan
                            </a>
                            <a href="{{ route('effective-days.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                📊 Hari Efektif
                            </a>
                            <a href="{{ route('academic-years.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                📚 Tahun Pelajaran
                            </a>
                            <a href="{{ route('activity-types.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                🏷️ Jenis Kegiatan
                            </a>
                        </div>
                    @endif
                    
                    <!-- Master Data -->
                    @if(auth()->user()->canManageUsers())
                        <div class="border-l-2 border-gray-200 pl-2 ml-2 space-y-1">
                            <div class="text-xs font-semibold text-gray-500 px-3 py-1">📂 Master Data</div>
                            <a href="{{ route('users.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                👥 Data Pengguna
                            </a>
                            <a href="{{ route('classes.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                🏫 Data Kelas
                            </a>
                            <a href="{{ route('subjects.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                📚 Mata Pelajaran
                            </a>
                        </div>
                    @endif
                    
                    <!-- Jurnal Mengajar -->
                    @if(auth()->user()->isGuru() || auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum())
                        <a href="{{ route('teaching-journal.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('teaching-journal.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            📓 Jurnal Mengajar
                        </a>
                    @endif
                    
                    <!-- Asesmen -->
                    @if(auth()->user()->canManageAssessments() || auth()->user()->canViewAllStudentProfiles() || auth()->user()->isSiswa())
                        <div class="border-l-2 border-gray-200 pl-2 ml-2 space-y-1">
                            <div class="text-xs font-semibold text-gray-500 px-3 py-1">📝 Asesmen</div>
                            @if(auth()->user()->isSiswa())
                                <a href="{{ route('student.assessment.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                    ✍️ Asesmen Saya
                                </a>
                            @endif
                            @if(auth()->user()->canManageAssessments())
                                <a href="{{ route('assessment.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                    ⚙️ Kelola Asesmen
                                </a>
                            @endif
                            @if(auth()->user()->canViewAllStudentProfiles())
                                <a href="{{ route('assessment.class-report') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                    📈 Profil Belajar Siswa
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Pengaturan -->
                    @if(auth()->user()->isAdmin())
                        <div class="border-l-2 border-gray-200 pl-2 ml-2 space-y-1">
                            <div class="text-xs font-semibold text-gray-500 px-3 py-1">⚙️ Pengaturan</div>
                            <a href="{{ route('settings.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                🏫 Pengaturan Umum
                            </a>
                            <a href="{{ route('settings.time-slots') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                ⏰ Jam Mengajar
                            </a>
                        </div>
                    @endif
                    
                    <!-- User Actions -->
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <a href="{{ route('profile.change-password') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                            🔑 Ganti Password
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                                🚪 Logout
                            </button>
                        </form>
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

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="text-sm text-gray-600">
                        © {{ date('Y') }} <span class="font-semibold">SMK PGRI Blora</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Sistem Informasi Manajemen Kurikulum
                    </p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-xs text-gray-500">
                        Versi 2.0 • Jurusan: MPLB • AKL • BUSANA
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>

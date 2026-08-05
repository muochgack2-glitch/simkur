<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengguna</h1>
                <p class="text-gray-800 mt-1">Kelola pengguna sistem E-KALDIK</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <a href="{{ route('users.import') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span class="hidden sm:inline">Import Excel</span>
                    <span class="sm:hidden">Import</span>
                </a>
                
                <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">Tambah Pengguna</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tab Filters & Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <!-- Role Tabs -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex -mb-px whitespace-nowrap" aria-label="Tabs" style="min-width: min-content;">
                <button 
                    wire:click="$set('filterRole', 'all')"
                    class="group inline-flex items-center px-4 sm:px-6 py-3 border-b-2 font-medium text-xs sm:text-sm transition
                        @if($filterRole === 'all') border-blue-600 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2 @if($filterRole === 'all') text-blue-600 @else text-gray-400 group-hover:text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="hidden sm:inline">Semua</span>
                    <span class="ml-1 sm:ml-2 py-0.5 px-1.5 sm:px-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                        {{ \App\Models\User::count() }}
                    </span>
                </button>

                <button 
                    wire:click="$set('filterRole', 'admin')"
                    class="group inline-flex items-center px-4 sm:px-6 py-3 border-b-2 font-medium text-xs sm:text-sm transition
                        @if($filterRole === 'admin') border-red-600 text-red-600 @else border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2 @if($filterRole === 'admin') text-red-600 @else text-gray-400 group-hover:text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="hidden sm:inline">Admin</span>
                    <span class="ml-1 sm:ml-2 py-0.5 px-1.5 sm:px-2 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        {{ \App\Models\User::where('role', 'admin')->count() }}
                    </span>
                </button>

                <button 
                    wire:click="$set('filterRole', 'kepala_sekolah')"
                    class="group inline-flex items-center px-4 sm:px-6 py-3 border-b-2 font-medium text-xs sm:text-sm transition
                        @if($filterRole === 'kepala_sekolah') border-indigo-600 text-indigo-600 @else border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2 @if($filterRole === 'kepala_sekolah') text-indigo-600 @else text-gray-400 group-hover:text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="hidden sm:inline">Kepsek</span>
                    <span class="ml-1 sm:ml-2 py-0.5 px-1.5 sm:px-2 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                        {{ \App\Models\User::where('role', 'kepala_sekolah')->count() }}
                    </span>
                </button>

                <button 
                    wire:click="$set('filterRole', 'waka_kurikulum')"
                    class="group inline-flex items-center px-4 sm:px-6 py-3 border-b-2 font-medium text-xs sm:text-sm transition
                        @if($filterRole === 'waka_kurikulum') border-purple-600 text-purple-600 @else border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2 @if($filterRole === 'waka_kurikulum') text-purple-600 @else text-gray-400 group-hover:text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="hidden sm:inline">Waka</span>
                    <span class="ml-1 sm:ml-2 py-0.5 px-1.5 sm:px-2 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        {{ \App\Models\User::where('role', 'waka_kurikulum')->count() }}
                    </span>
                </button>

                <button 
                    wire:click="$set('filterRole', 'guru')"
                    class="group inline-flex items-center px-4 sm:px-6 py-3 border-b-2 font-medium text-xs sm:text-sm transition
                        @if($filterRole === 'guru') border-green-600 text-green-600 @else border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2 @if($filterRole === 'guru') text-green-600 @else text-gray-400 group-hover:text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="hidden sm:inline">Guru</span>
                    <span class="ml-1 sm:ml-2 py-0.5 px-1.5 sm:px-2 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        {{ \App\Models\User::where('role', 'guru')->count() }}
                    </span>
                </button>

                <button 
                    wire:click="$set('filterRole', 'siswa')"
                    class="group inline-flex items-center px-4 sm:px-6 py-3 border-b-2 font-medium text-xs sm:text-sm transition
                        @if($filterRole === 'siswa') border-blue-600 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2 @if($filterRole === 'siswa') text-blue-600 @else text-gray-400 group-hover:text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="hidden sm:inline">Siswa</span>
                    <span class="ml-1 sm:ml-2 py-0.5 px-1.5 sm:px-2 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        {{ \App\Models\User::where('role', 'siswa')->count() }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Search & Secondary Filters -->
        <div class="p-4">
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Search Bar -->
                <div class="flex-1">
                    <input 
                        type="text" 
                        wire:model.live="search"
                        placeholder="🔍 Cari nama, username, email, NIS, atau NIP..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Secondary Filters for Siswa -->
                @if($filterRole === 'siswa' || $filterRole === 'all')
                    <div class="flex flex-col sm:flex-row gap-2">
                        <select 
                            wire:model.live="filterGrade"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="all">Semua Kelas</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>

                        <select 
                            wire:model.live="filterMajor"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="all">Semua Jurusan</option>
                            <option value="MPLB">MPLB</option>
                            <option value="AKL">AKL</option>
                            <option value="BUSANA">BUSANA</option>
                        </select>
                    </div>
                @endif
                
                <!-- Per Page -->
                <div>
                    <select 
                        wire:model.live="perPage"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm w-full sm:w-auto"
                    >
                        <option value="10">10 per hal</option>
                        <option value="25">25 per hal</option>
                        <option value="50">50 per hal</option>
                        <option value="100">100 per hal</option>
                    </select>
                </div>
            </div>

            <!-- Active Filter Chips -->
            @if($search || $filterRole !== 'all' || $filterGrade !== 'all' || $filterMajor !== 'all')
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-600">Filter aktif:</span>
                    
                    @if($filterRole !== 'all')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Role: {{ ucfirst(str_replace('_', ' ', $filterRole)) }}
                            <button wire:click="$set('filterRole', 'all')" class="ml-1.5 text-blue-600 hover:text-blue-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if($filterGrade !== 'all')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Kelas: {{ $filterGrade }}
                            <button wire:click="$set('filterGrade', 'all')" class="ml-1.5 text-purple-600 hover:text-purple-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if($filterMajor !== 'all')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Jurusan: {{ $filterMajor }}
                            <button wire:click="$set('filterMajor', 'all')" class="ml-1.5 text-green-600 hover:text-green-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pencarian: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-1.5 text-yellow-600 hover:text-yellow-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    <button 
                        wire:click="$set('search', ''); $set('filterRole', 'all'); $set('filterGrade', 'all'); $set('filterMajor', 'all')"
                        class="text-xs text-gray-600 hover:text-gray-800 underline">
                        Reset semua filter
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Kelas/Jurusan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Login Terakhir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-700">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-800">
                                {{ $user->username }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Admin
                                </span>
                            @elseif($user->role === 'kepala_sekolah')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    Kepala Sekolah
                                </span>
                            @elseif($user->role === 'waka_kurikulum')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Waka Kurikulum
                                </span>
                            @elseif($user->role === 'guru')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Guru
                                </span>
                            @elseif($user->role === 'siswa')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Siswa
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(in_array($user->role, ['guru', 'waka_kurikulum', 'kepala_sekolah']))
                                <div class="text-sm">
                                    @if($user->nip_nuptk)
                                        <div class="font-medium text-gray-900">NIP: {{ $user->nip_nuptk }}</div>
                                    @endif
                                    @if($user->subjects && $user->subjects->count() > 0)
                                        <div class="text-xs text-gray-700">
                                            {{ $user->subjects->take(2)->pluck('name')->implode(', ') }}
                                            @if($user->subjects->count() > 2)
                                                <span class="text-blue-600">+{{ $user->subjects->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($user->taught_majors && count($user->taught_majors) > 0)
                                        <div class="text-xs text-gray-700 mt-1">
                                            <span class="inline-flex items-center">
                                                📍 {{ implode(', ', $user->taught_majors) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @elseif($user->role === 'siswa')
                                <div class="text-sm">
                                    @if($user->nisn)
                                        <div class="text-xs text-gray-700">NIS: {{ $user->nisn }}</div>
                                    @endif
                                    @if($user->schoolClass)
                                        <div class="font-medium text-gray-900">{{ $user->schoolClass->name }}</div>
                                        <div class="text-xs text-gray-700">{{ $user->schoolClass->academicYear->name }}</div>
                                    @elseif($user->grade && $user->major)
                                        <div class="font-medium text-gray-900">{{ $user->getFullClassLabel() }}</div>
                                        <div class="text-xs text-gray-700">(Belum di-assign kelas)</div>
                                    @endif
                                    @if($user->is_pkl || $user->is_teaching_factory)
                                        <div class="flex gap-1 mt-1">
                                            @if($user->is_pkl)
                                                <span class="px-1.5 py-0.5 text-xs rounded bg-orange-100 text-orange-800">PKL</span>
                                            @endif
                                            @if($user->is_teaching_factory)
                                                <span class="px-1.5 py-0.5 text-xs rounded bg-purple-100 text-purple-800">TeFa</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                <span class="text-gray-400">Belum pernah login</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <button 
                                    wire:click="resetPassword({{ $user->id }})"
                                    wire:confirm="Reset password user {{ $user->name }} ke 'password'?"
                                    class="text-orange-600 hover:text-orange-900"
                                    title="Reset Password"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                </button>

                                @if($user->id !== auth()->id())
                                    @if($user->activities()->count() === 0)
                                        <button 
                                            wire:click="delete({{ $user->id }})"
                                            wire:confirm="Hapus user {{ $user->name }}?"
                                            class="text-red-600 hover:text-red-900"
                                            title="Hapus"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-gray-400 cursor-not-allowed" title="Tidak dapat dihapus karena memiliki kegiatan terkait">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="text-gray-700">Tidak ada user yang ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>



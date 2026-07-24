<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengguna</h1>
            <p class="text-gray-800 mt-1">Kelola pengguna sistem E-KALDIK</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.import') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span>Import Excel</span>
            </a>
            
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Pengguna</span>
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button 
                    wire:click="setTab('simkur')"
                    class="group inline-flex items-center px-6 py-4 border-b-2 font-medium text-sm transition
                        {{ $activeTab === 'simkur' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-700 hover:text-gray-800 hover:border-gray-300' }}">
                    <svg class="w-5 h-5 mr-2 {{ $activeTab === 'simkur' ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    User SIMKUR SMK PGRI Blora
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab === 'simkur' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $simkurCount }}
                    </span>
                </button>

                <button 
                    wire:click="setTab('erapor')"
                    class="group inline-flex items-center px-6 py-4 border-b-2 font-medium text-sm transition
                        {{ $activeTab === 'erapor' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-700 hover:text-gray-800 hover:border-gray-300' }}">
                    <svg class="w-5 h-5 mr-2 {{ $activeTab === 'erapor' ? 'text-green-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    User e-Rapor
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab === 'erapor' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $eraporCount }}
                    </span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col space-y-3">
            <!-- Search Bar -->
            <div class="flex-1">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Cari nama, username, atau email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            
            <!-- Filters (Only show for SIMKUR tab) -->
            @if($activeTab === 'simkur')
            <div class="flex flex-wrap items-center gap-2">
                <select 
                    wire:model.live="filterRole"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="kepala_sekolah">Kepala Sekolah</option>
                    <option value="waka_kurikulum">Waka Kurikulum</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                </select>

                @if($filterRole === 'siswa' || $filterRole === 'all')
                    <select 
                        wire:model.live="filterGrade"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all">Semua Kelas</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>

                    <select 
                        wire:model.live="filterMajor"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all">Semua Jurusan</option>
                        <option value="MPLB">MPLB</option>
                        <option value="AKL">AKL</option>
                        <option value="BUSANA">BUSANA</option>
                    </select>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Content based on active tab -->
    @if($activeTab === 'simkur')
        <!-- SIMKUR Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Kelas/Jurusan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Login Terakhir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider sticky right-0 bg-gray-50">Aksi</th>
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
                            @if($user->role === 'guru')
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
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium sticky right-0 bg-white">
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

    @else
        <!-- e-Rapor Users Content -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Integrasi e-Rapor</h3>
            <p class="text-gray-700 mb-6">Fitur integrasi dengan sistem e-Rapor akan segera tersedia.</p>
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 max-w-2xl mx-auto text-left">
                <div class="flex">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-900 mb-1">Fitur yang Akan Datang:</p>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Sinkronisasi data guru dari e-Rapor</li>
                            <li>• Sinkronisasi data siswa dari e-Rapor</li>
                            <li>• Mapping otomatis ke struktur kelas SIMKUR</li>
                            <li>• Update berkala data pengguna</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">👨‍🎓 Daftar Alumni</h1>
        <p class="mt-1 text-sm text-gray-800">Alumni SMK PGRI Blora</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Alumni</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">MPLB</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['mplb'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Alumni</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">AKL</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['akl'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Alumni</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">BUSANA</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['busana'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Alumni</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🔍 Cari Alumni</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Nama, NIS, atau NISN..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📅 Tahun Lulus</label>
                <select 
                    wire:model.live="graduationYear"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Tahun</option>
                    @foreach($graduationYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🎓 Jurusan</label>
                <select 
                    wire:model.live="major"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Jurusan</option>
                    <option value="MPLB">MPLB</option>
                    <option value="AKL">AKL</option>
                    <option value="BUSANA">BUSANA</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Alumni List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">NIS/NISN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Jurusan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Tahun Lulus</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($alumni as $alumnus)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-semibold text-sm">
                                        {{ strtoupper(substr($alumnus->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $alumnus->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $alumnus->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $alumnus->nis ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $alumnus->nisn ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $alumnus->major === 'MPLB' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $alumnus->major === 'AKL' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $alumnus->major === 'BUSANA' ? 'bg-pink-100 text-pink-800' : '' }}">
                                {{ $alumnus->major }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                🎓 {{ $alumnus->graduation_year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-medium">
                            <button 
                                wire:click="viewProfile({{ $alumnus->id }})"
                                class="text-blue-600 hover:text-blue-900"
                            >
                                👁️ Profil
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            @if($search || $graduationYear || $major)
                                Tidak ada alumni yang sesuai dengan filter
                            @else
                                Belum ada data alumni
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $alumni->links() }}
        </div>
    </div>

    <!-- Profile Modal -->
    @if($selectedAlumni)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" wire:click="closeProfile">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="px-6 py-4 border-b border-gray-200 bg-purple-600 text-white rounded-t-lg">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold">👨‍🎓 Profil Alumni</h3>
                        <button wire:click="closeProfile" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 h-20 w-20 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-2xl">
                                {{ strtoupper(substr($selectedAlumni->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-bold text-gray-900">{{ $selectedAlumni->name }}</h3>
                            <p class="text-gray-600">Alumni Tahun {{ $selectedAlumni->graduation_year }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-600">NIS</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->nis ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">NISN</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->nisn ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Jurusan</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->getMajorLabel() }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Kelas Terakhir</div>
                            <div class="text-base font-semibold text-gray-900">XII {{ $selectedAlumni->major }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Email</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->email ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">No. HP</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->no_hp ?? '-' }}</div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-sm text-gray-600">Nama Orang Tua</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->parent_name ?? '-' }}</div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-sm text-gray-600">No. HP Orang Tua</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedAlumni->parent_phone ?? '-' }}</div>
                        </div>
                    </div>

                    @if($selectedAlumni->alumni_notes)
                        <div class="mt-4">
                            <div class="text-sm text-gray-600 mb-2">Catatan Alumni</div>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $selectedAlumni->alumni_notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button 
                        wire:click="closeProfile"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

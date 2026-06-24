<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Hari Efektif</h1>
        <p class="text-gray-600 mt-1">
            @if($activeYear)
                Perhitungan hari efektif belajar - Tahun Pelajaran: <span class="font-semibold">{{ $activeYear->year }}</span>
            @else
                <span class="text-red-600">Belum ada tahun pelajaran aktif</span>
            @endif
        </p>
    </div>

    @if(!$activeYear)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
            <p>Belum ada tahun pelajaran aktif. Silakan aktifkan tahun pelajaran terlebih dahulu.</p>
            <a href="{{ route('academic-years.index') }}" class="mt-2 inline-block text-yellow-900 underline">
                Kelola Tahun Pelajaran
            </a>
        </div>
    @else
        <!-- Action Buttons -->
        @if(auth()->user()->canManageActivities())
            <div class="mb-6 flex justify-end gap-3">
                <!-- Tombol Validasi -->
                <a 
                    href="{{ route('effective-days.validation') }}"
                    target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Lihat Validasi</span>
                </a>
                
                <!-- Tombol Hitung Ulang -->
                <button 
                    wire:click="recalculate"
                    wire:confirm="Hitung ulang semua hari efektif?"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Hitung Ulang Semua</span>
                </button>
            </div>
        @endif

        <!-- Semester Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($semesters as $semester)
                @php
                    $effectiveDay = $semester->effectiveDay;
                @endphp
                
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white">{{ $semester->name }}</h3>
                        <p class="text-blue-100 text-sm mt-1">
                            {{ $semester->start_date->format('d M Y') }} - {{ $semester->end_date->format('d M Y') }}
                        </p>
                    </div>

                    @if($effectiveDay)
                        <!-- Statistics Grid -->
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <!-- Total Days -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">Total Hari</p>
                                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $effectiveDay->total_days }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Study Days -->
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-green-600">Hari Belajar</p>
                                            <p class="text-2xl font-bold text-green-900 mt-1">{{ $effectiveDay->study_days }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Weekend Days -->
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-blue-600">Hari Libur Akhir Pekan</p>
                                            <p class="text-2xl font-bold text-blue-900 mt-1">{{ $effectiveDay->weekend_days }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Holiday Days -->
                                <div class="bg-yellow-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-yellow-600">Hari Libur</p>
                                            <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $effectiveDay->holiday_days }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-yellow-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Exam Days -->
                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-purple-600">Hari Ujian</p>
                                            <p class="text-2xl font-bold text-purple-900 mt-1">{{ $effectiveDay->exam_days }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Effective Weeks -->
                                <div class="bg-indigo-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-indigo-600">Minggu Efektif</p>
                                            <p class="text-2xl font-bold text-indigo-900 mt-1">{{ $effectiveDay->effective_weeks }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-indigo-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Percentage Bar -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Persentase Hari Efektif</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $effectiveDay->percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ $effectiveDay->percentage }}%"></div>
                                </div>
                            </div>

                            <!-- Last Calculated -->
                            <div class="flex items-center justify-between text-sm text-gray-600 border-t border-gray-200 pt-4">
                                <span>Terakhir dihitung:</span>
                                <span class="font-medium">{{ $effectiveDay->calculated_at?->format('d M Y H:i') ?? '-' }}</span>
                            </div>

                            <!-- Recalculate Button -->
                            @if(auth()->user()->canManageActivities())
                                <button 
                                    wire:click="recalculate({{ $semester->id }})"
                                    wire:confirm="Hitung ulang hari efektif {{ $semester->name }}?"
                                    class="w-full mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Hitung Ulang</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            <p>Belum ada data hari efektif</p>
                            @if(auth()->user()->canManageActivities())
                                <button 
                                    wire:click="recalculate({{ $semester->id }})"
                                    class="mt-4 text-blue-600 hover:text-blue-800"
                                >
                                    Hitung Sekarang
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Informasi Perhitungan</p>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1 list-disc list-inside">
                        <li><strong>Hari Efektif</strong> = Total Hari - Weekend - Libur - Ujian</li>
                        <li><strong>Minggu Efektif</strong> = Hari Efektif ÷ 5</li>
                        <li>Perhitungan otomatis berdasarkan kegiatan yang ditandai sebagai libur atau ujian</li>
                        <li>Klik "Hitung Ulang" setelah menambah/mengubah kegiatan</li>
                        <li>Klik <strong>"Lihat Validasi"</strong> untuk membandingkan dengan Excel referensi</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

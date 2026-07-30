<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Hari Efektif</h1>
        <p class="text-gray-800 mt-1">
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
                        <!-- Breakdown Per Jenjang Kelas (MAIN DISPLAY) -->
                        @if($effectiveDay->byGrades && $effectiveDay->byGrades->isNotEmpty())
                            <div class="p-6">
                                <!-- Comparison Table Per Jenjang -->
                                <div class="overflow-x-auto mb-4">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Kelas</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Periode</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Hari Belajar</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Minggu Efektif</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($effectiveDay->byGrades->sortBy('grade') as $gradeData)
                                                <tr class="hover:bg-gray-50 {{ $gradeData->isGradeXII() && $gradeData->hasEarlyEnd() ? 'bg-yellow-50' : '' }}">
                                                    <!-- Grade -->
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                                <span class="text-blue-800 font-bold">{{ $gradeData->grade }}</span>
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="text-sm font-medium text-gray-900">{{ $gradeData->grade_label }}</div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- Period -->
                                                    <td class="px-4 py-4 text-center">
                                                        <div class="text-sm text-gray-900">
                                                            {{ $gradeData->start_date->format('d M') }} - {{ $gradeData->end_date->format('d M Y') }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ number_format($gradeData->getDurationInMonths(), 1) }} bulan
                                                        </div>
                                                        @if($gradeData->hasEarlyEnd())
                                                            <div class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                                ⚡ Selesai Lebih Cepat
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <!-- Study Days -->
                                                    <td class="px-4 py-4">
                                                        <div class="text-center">
                                                            <div class="text-3xl font-bold text-green-600">{{ $gradeData->study_days }}</div>
                                                            <div class="text-xs text-gray-500 mt-1">hari</div>
                                                        </div>
                                                    </td>

                                                    <!-- Effective Weeks -->
                                                    <td class="px-4 py-4">
                                                        <div class="text-center">
                                                            <div class="text-2xl font-semibold text-indigo-600">{{ $gradeData->effective_weeks }}</div>
                                                            <div class="text-xs text-gray-500 mt-1">minggu</div>
                                                        </div>
                                                    </td>

                                                    <!-- Percentage with Progress Bar -->
                                                    <td class="px-4 py-4">
                                                        <div class="flex flex-col items-center">
                                                            <div class="text-xl font-bold mb-2 text-{{ $gradeData->status_color }}-600">
                                                                {{ $gradeData->percentage }}%
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-3" style="max-width: 120px;">
                                                                <div class="bg-{{ $gradeData->status_color }}-600 h-3 rounded-full transition-all duration-500" 
                                                                    style="width: {{ $gradeData->percentage }}%"></div>
                                                            </div>
                                                            <div class="text-xs font-medium text-{{ $gradeData->status_color }}-800 mt-2">
                                                                {{ $gradeData->status_label }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Summary Stats (Compact) -->
                                <div class="border-t border-gray-200 pt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                    <div class="text-center p-3 bg-gray-50 rounded">
                                        <div class="text-gray-600 text-xs">Total Hari</div>
                                        <div class="text-lg font-bold text-gray-900">{{ $effectiveDay->total_days }}</div>
                                    </div>
                                    <div class="text-center p-3 bg-blue-50 rounded">
                                        <div class="text-blue-600 text-xs">Weekend</div>
                                        <div class="text-lg font-bold text-blue-900">{{ $effectiveDay->weekend_days }}</div>
                                    </div>
                                    <div class="text-center p-3 bg-yellow-50 rounded">
                                        <div class="text-yellow-600 text-xs">Hari Libur</div>
                                        <div class="text-lg font-bold text-yellow-900">{{ $effectiveDay->holiday_days }}</div>
                                    </div>
                                    <div class="text-center p-3 bg-purple-50 rounded">
                                        <div class="text-purple-600 text-xs">Hari Ujian</div>
                                        <div class="text-lg font-bold text-purple-900">{{ $effectiveDay->exam_days }}</div>
                                    </div>
                                </div>

                                <!-- Info Note -->
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start text-xs text-blue-800">
                                        <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="font-semibold">Catatan:</p>
                                            <p class="mt-1">Kelas XII selesai lebih cepat karena ada Ujian Sekolah & UTBK. Kelas X & XI full semester.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Last Calculated & Recalculate Button -->
                                <div class="mt-4 flex items-center justify-between text-sm text-gray-800 border-t border-gray-200 pt-4">
                                    <span>Terakhir dihitung: <span class="font-medium">{{ $effectiveDay->calculated_at?->format('d M Y H:i') ?? '-' }}</span></span>
                                    
                                    @if(auth()->user()->canManageActivities())
                                        <button 
                                            wire:click="recalculate({{ $semester->id }})"
                                            wire:confirm="Hitung ulang hari efektif {{ $semester->name }}?"
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition flex items-center space-x-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <span>Hitung Ulang</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Fallback: Simple Statistics (if no grade breakdown data) -->
                            <div class="p-6">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-yellow-800">Data per jenjang kelas belum tersedia</p>
                                            <p class="text-sm text-yellow-700 mt-1">Klik tombol "Hitung Ulang" untuk menghasilkan perhitungan detail per kelas X, XI, dan XII.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Simple Summary Stats -->
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-green-50 rounded-lg p-4 text-center">
                                        <p class="text-sm text-green-600">Hari Belajar</p>
                                        <p class="text-3xl font-bold text-green-900 mt-1">{{ $effectiveDay->study_days }}</p>
                                    </div>
                                    <div class="bg-indigo-50 rounded-lg p-4 text-center">
                                        <p class="text-sm text-indigo-600">Minggu Efektif</p>
                                        <p class="text-3xl font-bold text-indigo-900 mt-1">{{ $effectiveDay->effective_weeks }}</p>
                                    </div>
                                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                                        <p class="text-sm text-blue-600">Persentase</p>
                                        <p class="text-3xl font-bold text-blue-900 mt-1">{{ $effectiveDay->percentage }}%</p>
                                    </div>
                                </div>

                                <!-- Last Calculated -->
                                <div class="flex items-center justify-between text-sm text-gray-800 border-t border-gray-200 pt-4">
                                    <span>Terakhir dihitung:</span>
                                    <span class="font-medium">{{ $effectiveDay->calculated_at?->format('d M Y H:i') ?? '-' }}</span>
                                </div>

                                <!-- Recalculate Button -->
                                @if(auth()->user()->canManageActivities())
                                    <button 
                                        wire:click="recalculate({{ $semester->id }})"
                                        wire:confirm="Hitung ulang hari efektif {{ $semester->name }}?"
                                        class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Hitung Ulang & Generate Data Per Jenjang</span>
                                    </button>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="p-6 text-center text-gray-700">
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



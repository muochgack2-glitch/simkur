<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Export Kalender</h1>
                <p class="mt-1 text-sm text-gray-600">Export kalender dan daftar kegiatan ke format PDF</p>
            </div>
            <a href="{{ route('activities.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </span>
            </a>
        </div>
    </div>

    <!-- Export Type Tabs -->
    <div class="mb-6">
        <div class="flex space-x-2 border-b border-gray-200">
            <button 
                wire:click="$set('exportType', 'yearly')"
                class="px-4 py-2 text-sm font-medium {{ $exportType === 'yearly' ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500 hover:text-gray-700' }} transition"
            >
                Kalender Tahunan
            </button>
            <button 
                wire:click="$set('exportType', 'monthly')"
                class="px-4 py-2 text-sm font-medium {{ $exportType === 'monthly' ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500 hover:text-gray-700' }} transition"
            >
                Kalender Bulanan
            </button>
            <button 
                wire:click="$set('exportType', 'list')"
                class="px-4 py-2 text-sm font-medium {{ $exportType === 'list' ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500 hover:text-gray-700' }} transition"
            >
                Daftar Kegiatan (PDF)
            </button>
            <button 
                wire:click="$set('exportType', 'excel')"
                class="px-4 py-2 text-sm font-medium {{ $exportType === 'excel' ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500 hover:text-gray-700' }} transition"
            >
                Export Excel
            </button>
        </div>
    </div>

    <!-- Export Forms -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        
        <!-- Yearly Export -->
        @if($exportType === 'yearly')
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Export Kalender Tahunan</h3>
                <p class="text-sm text-gray-600 mb-4">Export kalender dalam format grid 12 bulan dengan daftar kegiatan per bulan.</p>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran</label>
                    <select wire:model="filterAcademicYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Tahun Pelajaran</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="previewYearly" 
                        class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="previewYearly" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </span>
                        <span wire:loading wire:target="previewYearly">Memuat...</span>
                    </button>
                    @if($filterAcademicYear)
                        <a 
                            href="{{ route('activities.export.yearly', ['year' => $filterAcademicYear]) }}" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition inline-flex items-center"
                            target="_blank"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </a>
                    @else
                        <button 
                            disabled
                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed inline-flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </button>
                    @endif
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ Informasi</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Format: A4 Portrait</li>
                        <li>• Orientasi: Vertikal</li>
                        <li>• Konten: 12 bulan dalam grid 2 kolom</li>
                        <li>• Cocok untuk: Overview tahunan</li>
                    </ul>
                </div>
            </div>
        @endif

        <!-- Monthly Export -->
        @if($exportType === 'monthly')
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Export Kalender Bulanan</h3>
                <p class="text-sm text-gray-600 mb-4">Export kalender bulan tertentu dalam format grid kalender lengkap.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select wire:model="selectedYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @for($y = 2020; $y <= 2030; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select wire:model="selectedMonth" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $month)
                                <option value="{{ $index + 1 }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="previewMonthly" 
                        class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="previewMonthly" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </span>
                        <span wire:loading wire:target="previewMonthly">Memuat...</span>
                    </button>
                    <a 
                        href="{{ route('activities.export.monthly', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition inline-flex items-center"
                        target="_blank"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ Informasi</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Format: A4 Landscape</li>
                        <li>• Orientasi: Horizontal</li>
                        <li>• Konten: Kalender grid dengan kegiatan + daftar detail</li>
                        <li>• Cocok untuk: Distribusi bulanan, tampilan detail</li>
                    </ul>
                </div>
            </div>
        @endif

        <!-- Activity List Export -->
        @if($exportType === 'list')
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Export Daftar Kegiatan</h3>
                <p class="text-sm text-gray-600 mb-4">Export daftar kegiatan dalam format tabel dengan filter.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran</label>
                        <select wire:model="filterAcademicYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select wire:model="filterSemester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan</label>
                        <select wire:model="filterActivityType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="previewActivityList" 
                        class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="previewActivityList" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </span>
                        <span wire:loading wire:target="previewActivityList">Memuat...</span>
                    </button>
                    @php
                        $listFilters = [];
                        if($filterAcademicYear) $listFilters['academic_year_id'] = $filterAcademicYear;
                        if($filterSemester) $listFilters['semester_id'] = $filterSemester;
                        if($filterActivityType) $listFilters['activity_type_id'] = $filterActivityType;
                    @endphp
                    <a 
                        href="{{ route('activities.export.list', $listFilters) }}" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition inline-flex items-center"
                        target="_blank"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ Informasi</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Format: A4 Portrait</li>
                        <li>• Orientasi: Vertikal</li>
                        <li>• Konten: Tabel daftar kegiatan dengan detail lengkap</li>
                        <li>• Cocok untuk: Laporan, arsip, referensi</li>
                    </ul>
                </div>
            </div>
        @endif

        <!-- Excel Export -->
        @if($exportType === 'excel')
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Export ke Excel</h3>
                <p class="text-sm text-gray-600 mb-4">Export daftar kegiatan dan hari efektif ke format Excel (multi-sheet).</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran</label>
                        <select wire:model="filterAcademicYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select wire:model="filterSemester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan</label>
                        <select wire:model="filterActivityType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @php
                        $excelFilters = [];
                        if($filterAcademicYear) $excelFilters['academic_year_id'] = $filterAcademicYear;
                        if($filterSemester) $excelFilters['semester_id'] = $filterSemester;
                        if($filterActivityType) $excelFilters['activity_type_id'] = $filterActivityType;
                    @endphp
                    <a 
                        href="{{ route('activities.export.excel', $excelFilters) }}" 
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition inline-flex items-center"
                        target="_blank"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Excel
                    </a>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ Informasi</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Format: .xlsx (Microsoft Excel)</li>
                        <li>• Sheet 1: Daftar Kegiatan (tabel dengan filter)</li>
                        <li>• Sheet 2: Hari Efektif (per semester)</li>
                        <li>• Styled headers & alternating row colors</li>
                        <li>• Cocok untuk: Analisis data, editing lebih lanjut, import ke sistem lain</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Preview Export Modal Component -->
    @livewire('activity.preview-export')
</div>

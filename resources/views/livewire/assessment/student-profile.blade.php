<div>
    <!-- Print CSS -->
    <style>
        @media print {
            /* Hide elements that shouldn't be printed */
            .no-print {
                display: none !important;
            }
            
            /* Reset page styling */
            @page {
                size: A4;
                margin: 1.5cm;
            }
            
            body {
                background: white !important;
                color: black !important;
                font-size: 12pt;
                line-height: 1.5;
            }
            
            /* Container adjustments */
            .print-full-width {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            
            /* Card styling for print */
            .bg-white, .bg-gray-50, .bg-blue-50, .bg-green-50, .bg-yellow-50 {
                background: white !important;
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }
            
            /* Text color adjustments */
            .text-gray-900, .text-gray-800, .text-gray-700, 
            .text-blue-900, .text-blue-800, .text-green-900, 
            .text-green-800, .text-yellow-900, .text-yellow-800 {
                color: black !important;
            }
            
            .text-gray-600, .text-gray-500 {
                color: #4b5563 !important;
            }
            
            /* Badge/Label styling */
            .bg-blue-100, .bg-green-100, .bg-red-100, .bg-yellow-100 {
                border: 1px solid #000 !important;
                background: white !important;
                color: black !important;
                font-weight: 600 !important;
            }
            
            /* Score boxes */
            .bg-red-50, .bg-yellow-50, .bg-green-50, .bg-blue-50 {
                border: 2px solid #000 !important;
                background: white !important;
            }
            
            .text-red-600, .text-yellow-600, .text-green-600, .text-blue-600 {
                color: black !important;
                font-weight: bold !important;
            }
            
            /* Avatar circle */
            .bg-blue-600 {
                background: white !important;
                border: 3px solid #000 !important;
                color: black !important;
            }
            
            /* Rounded elements */
            .rounded-lg, .rounded-full {
                border-radius: 8px !important;
            }
            
            /* Lists and bullets */
            ul li {
                margin-bottom: 8px;
            }
            
            /* SVG icons - show as black */
            svg {
                color: black !important;
            }
            
            /* Spacing adjustments */
            .space-y-6 > * + * {
                margin-top: 1.5rem !important;
            }
            
            .mb-6 {
                margin-bottom: 1.5rem !important;
            }
            
            /* Headings */
            h1, h2, h3, h4 {
                color: black !important;
                font-weight: bold !important;
                page-break-after: avoid;
            }
            
            h1 { font-size: 20pt; }
            h2 { font-size: 16pt; }
            h3 { font-size: 14pt; }
            h4 { font-size: 12pt; }
            
            /* Avoid page breaks inside important elements */
            .bg-white, .bg-blue-50, .bg-green-50 {
                page-break-inside: avoid;
            }
            
            /* Grid adjustments */
            .grid {
                display: grid !important;
            }
            
            /* Table-like structures */
            .border-t {
                border-top: 1px solid #000 !important;
            }
            
            /* Footer/watermark */
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 9pt;
                color: #666;
                padding: 10px;
                border-top: 1px solid #ccc;
            }
            
            /* Show print-only elements */
            .print\:block {
                display: block !important;
            }
            
            .hidden.print\:block {
                display: block !important;
            }
        }
    </style>

    <!-- Action Buttons (No Print) -->
    <div class="mb-6 flex justify-between items-center no-print">
        <div>
            <a href="{{ route('assessment.class-report') }}" 
               class="text-gray-800 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Class Report
            </a>
        </div>
        <button onclick="window.print()" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak PDF
        </button>
    </div>

    <div class="print-full-width max-w-5xl mx-auto">
        <!-- Print Header (Only visible in print) -->
        <div class="hidden print:block mb-6 text-center border-b-2 border-gray-800 pb-4">
            <h1 class="text-xl font-bold">PROFIL GAYA BELAJAR SISWA</h1>
            <p class="text-sm mt-1">SMK PGRI Blora</p>
            <p class="text-xs mt-1">Tahun Ajaran {{ $assessment->academicYear->year ?? '-' }} - Semester {{ $assessment->semester->name ?? '-' }}</p>
        </div>

        <!-- Header Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Avatar -->
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    
                    <!-- Student Info -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                        <p class="text-gray-800 mt-1">{{ $student->getFullClassLabel() }}</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $student->username }}</p>
                        @if($student->nisn)
                            <p class="text-sm text-gray-700">NISN: {{ $student->nisn }}</p>
                        @endif
                    </div>
                </div>

                <!-- Assessment Info -->
                <div class="text-right">
                    <div class="inline-block px-3 py-1 rounded-full text-sm font-semibold border-2
                        {{ $assessment->isVark() ? 'bg-blue-100 text-blue-800 border-blue-800' : 'bg-green-100 text-green-800 border-green-800' }}">
                        {{ $assessment->getTypeLabel() }}
                    </div>
                    <p class="text-sm text-gray-800 mt-2 font-semibold">{{ $assessment->title }}</p>
                    <p class="text-xs text-gray-700 mt-1">
                        Selesai: {{ $profile->completed_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Content: VARK or Diagnostic -->
        @if($assessment->isVark())
            @include('livewire.assessment.partials.student-vark-profile')
        @else
            @include('livewire.assessment.partials.student-diagnostic-profile')
        @endif

        <!-- Print Footer (Only visible in print) -->
        <div class="hidden print:block mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-600">
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
            <p class="mt-1">SIM Kurikulum - SMK PGRI Blora</p>
        </div>
    </div>
</div>

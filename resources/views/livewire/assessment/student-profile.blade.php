<div>
    <!-- Print CSS -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-full-width {
                width: 100% !important;
                max-width: 100% !important;
            }
            body {
                background: white !important;
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
        <!-- Header Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
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
                    </div>
                </div>

                <!-- Assessment Info -->
                <div class="text-right">
                    <div class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                        {{ $assessment->isVark() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $assessment->getTypeLabel() }}
                    </div>
                    <p class="text-sm text-gray-800 mt-2">{{ $assessment->title }}</p>
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
    </div>
</div>

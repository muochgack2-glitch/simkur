<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
            @if($assessment->isVark())
                Hasil Asesmen Gaya Belajar
            @else
                Hasil Asesmen Diagnostik Kesiapan Belajar
            @endif
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            @if($assessment->isVark())
                Profil gaya belajar Anda dan rekomendasi
            @else
                Profil kesiapan belajar Anda dan rekomendasi
            @endif
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            <div class="overflow-hidden rounded-lg {{ $assessment->isVark() ? 'bg-blue-50 dark:bg-blue-900/30' : 'bg-green-50 dark:bg-green-900/30' }} shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 {{ $assessment->isVark() ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold {{ $assessment->isVark() ? 'text-blue-900 dark:text-blue-100' : 'text-green-900 dark:text-green-100' }}">
                                🎉 Selamat! Asesmen Berhasil Diselesaikan
                            </h3>
                            <p class="mt-1 text-sm {{ $assessment->isVark() ? 'text-blue-700 dark:text-blue-300' : 'text-green-700 dark:text-green-300' }}">
                                Diselesaikan pada: {{ $profile->completed_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($assessment->isVark())
                {{-- VARK RESULT --}}
                @include('livewire.student-assessment.partials.vark-result')
            @else
                {{-- DIAGNOSTIC RESULT --}}
                @include('livewire.student-assessment.partials.diagnostic-result')
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-center gap-4">
                <a href="{{ route('student.assessment.index') }}" 
                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Asesmen
                </a>
                
                <button onclick="window.print()" 
                        class="inline-flex items-center rounded-lg {{ $assessment->isVark() ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800' : 'bg-green-600 hover:bg-green-700 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800' }} px-5 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Hasil
                </button>
            </div>
        </div>
    </div>
</div>

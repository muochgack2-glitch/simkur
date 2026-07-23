<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
            Asesmen Gaya Belajar
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Isi asesmen untuk mengetahui gaya belajar Anda
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session()->has('success'))
                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-gray-800 dark:text-green-400" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-gray-800 dark:text-red-400" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Daftar Asesmen Tersedia</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Isi asesmen untuk mengetahui gaya belajar Anda
                        </p>
                    </div>

                    @if($assessments->isEmpty())
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-900">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada asesmen aktif</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Belum ada asesmen yang tersedia untuk Anda saat ini.
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($assessments as $assessment)
                                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $assessment->title }}
                                                </h4>
                                                @if($assessment->is_completed)
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        Sudah Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-sm font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                        Belum Dikerjakan
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $assessment->description }}
                                            </p>

                                            <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $assessment->start_date->format('d M Y') }} - {{ $assessment->end_date->format('d M Y') }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ $assessment->total_questions }} Pertanyaan
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    ± 10-15 menit
                                                </div>
                                            </div>

                                            @if($assessment->is_completed)
                                                <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                                    Diselesaikan pada: {{ $assessment->completion_date->format('d M Y H:i') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="ml-4">
                                            @if($assessment->is_completed)
                                                <a href="{{ route('student.assessment.result', $assessment->id) }}" 
                                                   class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    Lihat Hasil
                                                </a>
                                            @else
                                                <a href="{{ route('student.assessment.take', $assessment->id) }}" 
                                                   class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                    Mulai Asesmen
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

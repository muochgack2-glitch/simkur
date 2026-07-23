<div wire:poll.stop>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
            {{ $assessment->title }}
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Jawab semua pertanyaan dengan jujur sesuai kondisi Anda
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            
            <!-- Progress Bar -->
            <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                <div class="p-6">
                    <div class="mb-2 flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Progress Pengisian</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $answeredCount }}/{{ $totalQuestions }} soal</span>
                    </div>
                    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full rounded-full bg-blue-600 transition-all duration-300 dark:bg-blue-500" 
                             style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ round($progressPercentage) }}% selesai
                    </p>
                </div>
            </div>

            <!-- Questions -->
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    
                    @if (session()->has('error'))
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-gray-800 dark:text-red-400 flex items-center justify-between" role="alert">
                            <span>{{ session('error') }}</span>
                            <button wire:click="resetAssessment" type="button" class="ml-4 inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-gray-800 dark:text-green-400" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Halaman {{ $currentPage }} dari {{ $totalPages }}
                        </h3>
                    </div>

                    <form wire:submit.prevent="confirmSubmit">
                        <div class="space-y-8">
                            @foreach($currentQuestions as $question)
                                @php
                                    $globalIndex = $question->order_number;
                                @endphp
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
                                    <div class="mb-4 flex items-start">
                                        <span class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                            {{ $globalIndex }}
                                        </span>
                                        <p class="flex-1 text-base font-medium text-gray-900 dark:text-gray-100">
                                            {{ $question->question_text }}
                                        </p>
                                    </div>

                                    <div class="ml-11 space-y-3">
                                        @if($assessment->isVark())
                                            <!-- VARK: Radio buttons with labels A, B, C, D -->
                                            @foreach($question->options as $option)
                                                <label class="flex cursor-pointer items-start rounded-lg border-2 p-4 transition-all hover:bg-gray-100 dark:hover:bg-gray-800
                                                    {{ $answers[$question->id] == $option->id ? 'border-blue-600 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-700' }}">
                                                    <input type="radio" 
                                                           wire:model="answers.{{ $question->id }}" 
                                                           value="{{ $option->id }}"
                                                           class="mt-1 h-4 w-4 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                                    <span class="ml-3 flex-1 text-sm text-gray-700 dark:text-gray-300">
                                                        <strong>{{ chr(64 + $option->order_number) }}.</strong> {{ $option->option_text }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        @else
                                            <!-- Diagnostic: Horizontal Likert Scale 1-5 -->
                                            <div class="px-4">
                                                <div class="mb-3 flex justify-between text-xs text-gray-600 dark:text-gray-400">
                                                    <span>Sangat Tidak Sesuai</span>
                                                    <span>Sangat Sesuai</span>
                                                </div>
                                                <div class="flex items-center justify-between gap-2">
                                                    @foreach($question->options as $option)
                                                        <label class="flex flex-1 cursor-pointer flex-col items-center">
                                                            <input type="radio" 
                                                                   wire:model="answers.{{ $question->id }}" 
                                                                   value="{{ $option->id }}"
                                                                   class="peer sr-only">
                                                            <div class="flex h-12 w-full items-center justify-center rounded-lg border-2 transition-all peer-checked:border-green-600 peer-checked:bg-green-600 peer-checked:text-white hover:bg-gray-100 dark:hover:bg-gray-800
                                                                {{ $answers[$question->id] == $option->id ? 'border-green-600 bg-green-600 text-white' : 'border-gray-300 bg-white text-gray-700 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                                                <span class="text-lg font-bold">{{ $option->score_value }}</span>
                                                            </div>
                                                            <span class="mt-2 text-xs text-center text-gray-600 dark:text-gray-400">
                                                                {{ ['Sangat Tidak', 'Tidak', 'Cukup', 'Sesuai', 'Sangat'][$option->score_value - 1] }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation -->
                        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6 dark:border-gray-700">
                            <button type="button"
                                    wire:click="previousPage"
                                    @if($currentPage <= 1) disabled @endif
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </button>

                            <!-- Page Numbers -->
                            <div class="flex gap-2">
                                @for($i = 1; $i <= $totalPages; $i++)
                                    <button type="button"
                                            wire:click="goToPage({{ $i }})"
                                            class="h-10 w-10 rounded-lg text-sm font-medium transition-all
                                                {{ $currentPage == $i 
                                                    ? 'bg-blue-600 text-white dark:bg-blue-500' 
                                                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' 
                                                }}">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>

                            @if($currentPage < $totalPages)
                                <button type="button"
                                        wire:click="nextPage"
                                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    Selanjutnya
                                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <button type="submit"
                                        wire:loading.attr="disabled"
                                        wire:target="submit"
                                        @if($answeredCount < $totalQuestions) disabled @endif
                                        class="inline-flex items-center rounded-lg px-6 py-2 text-sm font-medium text-white focus:outline-none focus:ring-4 transition-all
                                        {{ $answeredCount == $totalQuestions 
                                            ? 'bg-green-600 hover:bg-green-700 focus:ring-green-300 cursor-pointer' 
                                            : 'bg-gray-400 cursor-not-allowed' }}">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span wire:loading.remove wire:target="submit">
                                        @if($answeredCount == $totalQuestions)
                                            Selesai & Submit
                                        @else
                                            Lengkapi Semua Soal ({{ $answeredCount }}/{{ $totalQuestions }})
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="submit" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memproses...
                                    </span>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mt-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/30">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3 text-sm text-blue-700 dark:text-blue-400">
                        <p class="font-medium">Tips Pengisian:</p>
                        <ul class="mt-1 list-inside list-disc space-y-1">
                            <li>Jawab sesuai dengan kondisi Anda yang sebenarnya</li>
                            <li>Tidak ada jawaban benar atau salah</li>
                            <li>Pastikan semua pertanyaan terjawab sebelum submit</li>
                            <li>Anda tidak bisa mengubah jawaban setelah submit</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true" 
             style="z-index: 9999;">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" style="z-index: 9999;"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle" style="z-index: 10000; position: relative;">
                    <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                    Konfirmasi Submit
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Apakah Anda yakin ingin submit jawaban? Anda tidak akan bisa mengubah jawaban setelah submit.
                                    </p>
                                    <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Total pertanyaan terjawab: {{ $answeredCount }}/{{ $totalQuestions }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 dark:bg-gray-900 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button"
                                wire:click="submit"
                                wire:loading.attr="disabled"
                                wire:target="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm">
                            <span wire:loading.remove wire:target="submit">Ya, Submit Sekarang</span>
                            <span wire:loading wire:target="submit" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                        <button type="button"
                                wire:click="cancelSubmit"
                                wire:loading.attr="disabled"
                                class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

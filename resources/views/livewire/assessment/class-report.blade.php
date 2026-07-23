<div>
    <!-- Page Header -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            Profil Belajar Siswa per Kelas
        </h1>
        <p class="mt-2 text-base text-gray-700 dark:text-gray-300">
            Lihat distribusi gaya belajar dan kesiapan belajar siswa
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Pilih Kelas</label>
                            <select wire:model.live="selectedGrade"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach($availableGrades as $grade)
                                    <option value="{{ $grade }}">Kelas {{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Pilih Jurusan</label>
                            <select wire:model.live="selectedMajor"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="all">Semua Jurusan</option>
                                @foreach($availableMajors as $major)
                                    <option value="{{ $major }}">{{ $major }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Pilih Semester</label>
                            <select wire:model.live="selectedSemester"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">
                                        {{ $semester->name }} ({{ $semester->academicYear->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px" aria-label="Tabs">
                        <button wire:click="switchTab('vark')" 
                                class="w-1/2 border-b-2 py-4 px-1 text-center text-sm font-medium transition-colors
                                {{ $activeTab === 'vark' 
                                    ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' 
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                            <span class="inline-flex items-center">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Gaya Belajar (VARK)
                            </span>
                        </button>
                        <button wire:click="switchTab('diagnostic')"
                                class="w-1/2 border-b-2 py-4 px-1 text-center text-sm font-medium transition-colors
                                {{ $activeTab === 'diagnostic' 
                                    ? 'border-green-500 text-green-600 dark:border-green-400 dark:text-green-400' 
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                            <span class="inline-flex items-center">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Kesiapan Belajar (Diagnostik)
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            @if($reportType === 'vark')
                @include('livewire.assessment.partials.vark-class-report')
            @else
                @include('livewire.assessment.partials.diagnostic-class-report')
            @endif

        </div>
    </div>
</div>

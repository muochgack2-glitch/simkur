<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                    Edit Asesmen
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Edit informasi asesmen gaya belajar
                </p>
            </div>
            <a href="{{ route('assessment.index') }}" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
        <div class="p-6">
            <form wire:submit.prevent="update">
                <!-- Assessment Type Badge (Read-only) -->
                <div class="mb-6 p-4 rounded-lg {{ $assessment->isVark() ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 {{ $assessment->isVark() ? 'text-blue-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium {{ $assessment->isVark() ? 'text-blue-800' : 'text-green-800' }}">
                            Tipe Asesmen: <strong>{{ $assessment->getTypeLabel() }}</strong>
                        </span>
                        <span class="text-xs {{ $assessment->isVark() ? 'text-blue-600' : 'text-green-600' }}">
                            (tidak dapat diubah)
                        </span>
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Judul Asesmen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="title"
                           class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                           placeholder="Contoh: Asesmen Gaya Belajar Semester Ganjil">
                    @error('title') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Deskripsi
                    </label>
                    <textarea wire:model="description" rows="3"
                              class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                              placeholder="Deskripsi asesmen..."></textarea>
                    @error('description') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6 grid gap-6 md:grid-cols-2">
                    <!-- Academic Year -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="academic_year_id"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <!-- Semester -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="semester_id"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                        @error('semester_id') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Target Grades -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Target Kelas (kosongkan untuk semua kelas)
                    </label>
                    <div class="flex gap-4">
                        @foreach($availableGrades as $grade)
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="target_grades" value="{{ $grade }}"
                                       class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600">
                                <span class="ml-2 text-sm text-gray-900 dark:text-gray-300">Kelas {{ $grade }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6 grid gap-6 md:grid-cols-2">
                    <!-- Start Date -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="start_date"
                               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('start_date') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="end_date"
                               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('end_date') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="is_active"
                               class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-900 dark:text-gray-300">Aktifkan asesmen</span>
                    </label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('assessment.index') }}"
                       class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Perbarui Asesmen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

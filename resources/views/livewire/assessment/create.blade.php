<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                    Buat Asesmen Baru
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Buat asesmen VARK atau Diagnostik untuk siswa
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
            <form wire:submit.prevent="save">
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

                <!-- Assessment Type -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Tipe Asesmen <span class="text-red-500">*</span>
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- VARK Option -->
                        <label class="flex cursor-pointer items-center rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 {{ $assessment_type === 'vark' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900 dark:border-blue-500' : '' }}">
                            <input type="radio" wire:model="assessment_type" value="vark" class="h-4 w-4 text-blue-600">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    VARK (Gaya Belajar)
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Mengidentifikasi gaya belajar Visual, Auditory, Reading, Kinesthetic
                                </div>
                            </div>
                        </label>

                        <!-- Diagnostic Option -->
                        <label class="flex cursor-pointer items-center rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 {{ $assessment_type === 'diagnostic' ? 'border-green-600 bg-green-50 dark:bg-green-900 dark:border-green-500' : '' }}">
                            <input type="radio" wire:model="assessment_type" value="diagnostic" class="h-4 w-4 text-green-600">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    Diagnostik (Kesiapan Belajar)
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Mengukur kesiapan, motivasi, kemandirian, kolaborasi & dunia kerja
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('assessment_type') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
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
                        Simpan Asesmen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

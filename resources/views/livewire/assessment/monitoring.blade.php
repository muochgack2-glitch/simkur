<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
            Monitoring: {{ $assessment->title }}
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Monitor progress pengisian asesmen per siswa
        </p>
    </div>

    <div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistics Cards -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Students -->
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Siswa</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>

                <!-- Completed -->
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-full bg-green-100 p-3 dark:bg-green-900">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Sudah Selesai</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completedCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-full bg-yellow-100 p-3 dark:bg-yellow-900">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Belum Selesai</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Completion Percentage -->
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-full bg-purple-100 p-3 dark:bg-purple-900">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Progress</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ round($completionPercentage) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    <!-- Filters -->
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Siswa</h3>
                        
                        <div class="flex gap-3">
                            <select wire:model.live="filterGrade"
                                    class="rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="all">Semua Kelas</option>
                                @foreach($availableGrades as $grade)
                                    <option value="{{ $grade }}">Kelas {{ $grade }}</option>
                                @endforeach
                            </select>

                            <select wire:model.live="filterStatus"
                                    class="rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="all">Semua Status</option>
                                <option value="completed">Sudah Selesai</option>
                                <option value="pending">Belum Selesai</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">No</th>
                                    <th scope="col" class="px-6 py-3">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-3">Kelas</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Waktu Selesai</th>
                                    <th scope="col" class="px-6 py-3">Gaya Belajar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $student->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                {{ $student->grade }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($student->learningProfiles->isNotEmpty())
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($student->learningProfiles->first())
                                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ $student->learningProfiles->first()->completed_at->format('d M Y H:i') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($student->learningProfiles->first())
                                                @php
                                                    $profile = $student->learningProfiles->first();
                                                @endphp
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                    {{ $profile->dominant_style === 'visual' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                                    {{ $profile->dominant_style === 'auditory' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                                    {{ $profile->dominant_style === 'kinesthetic' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : '' }}
                                                    {{ $profile->dominant_style === 'reading_writing' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}">
                                                    {{ $profile->getDominantStyleIcon() }} {{ $profile->getDominantStyleLabel() }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada siswa ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

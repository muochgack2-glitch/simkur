@if($statistics['total_students'] > 0 && isset($diagnosticAssessment))
    <!-- Statistics Overview -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-6 shadow-sm ">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-800 ">Total Siswa</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 ">{{ $statistics['total_students'] }}</p>
            </div>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-800 ">Rata-rata Kelas</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 ">
                    @if(!empty($statistics['average_aspects']))
                        {{ round(collect($statistics['average_aspects'])->avg(), 1) }}%
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-800 ">Perlu Pendampingan</p>
                <p class="mt-2 text-3xl font-bold text-red-600 ">
                    {{ $statistics['category_distribution']['perlu_pendampingan'] ?? 0 }}
                </p>
            </div>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-800 ">Sangat Baik</p>
                <p class="mt-2 text-3xl font-bold text-green-600 ">
                    {{ $statistics['category_distribution']['sangat_baik'] ?? 0 }}
                </p>
            </div>
        </div>
    </div>

    <!-- Average Aspects Chart -->
    @if(!empty($statistics['average_aspects']))
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900 ">📊 Rata-rata Kesiapan Belajar per Aspek - Kelas {{ $selectedGrade }}</h3>
                
                @php
                    $aspectLabels = [
                        'kesiapan' => ['label' => 'Kesiapan Belajar', 'icon' => '📚', 'color' => 'blue'],
                        'preferensi' => ['label' => 'Preferensi Belajar', 'icon' => '🎯', 'color' => 'purple'],
                        'motivasi' => ['label' => 'Motivasi Belajar', 'icon' => '🔥', 'color' => 'orange'],
                        'kemandirian' => ['label' => 'Kemandirian Belajar', 'icon' => '💪', 'color' => 'green'],
                        'kolaborasi' => ['label' => 'Kolaborasi & Komunikasi', 'icon' => '🤝', 'color' => 'pink'],
                        'dunia_kerja' => ['label' => 'Kesiapan Dunia Kerja', 'icon' => '💼', 'color' => 'indigo'],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach($statistics['average_aspects'] as $aspect => $avgScore)
                        @php
                            $aspectInfo = $aspectLabels[$aspect] ?? ['label' => ucfirst($aspect), 'icon' => '📌', 'color' => 'gray'];
                            $colorClass = match($aspectInfo['color']) {
                                'blue' => 'bg-blue-600',
                                'purple' => 'bg-purple-600',
                                'orange' => 'bg-orange-600',
                                'green' => 'bg-green-600',
                                'pink' => 'bg-pink-600',
                                'indigo' => 'bg-indigo-600',
                                default => 'bg-gray-600',
                            };
                            $statusColor = $avgScore >= 86 ? 'text-green-600' : ($avgScore >= 71 ? 'text-blue-600' : ($avgScore >= 56 ? 'text-yellow-600' : 'text-red-600'));
                        @endphp
                        
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <span class="font-medium text-gray-700 ">
                                    <span class="mr-2">{{ $aspectInfo['icon'] }}</span>
                                    {{ $aspectInfo['label'] }}
                                </span>
                                <span class="font-semibold {{ $statusColor }}">
                                    {{ round($avgScore, 1) }}%
                                </span>
                            </div>
                            <div class="h-6 w-full overflow-hidden rounded-full bg-gray-200 ">
                                <div class="h-full rounded-full transition-all duration-300 {{ $colorClass }}"
                                     style="width: {{ min($avgScore, 100) }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Category Distribution -->
    @if(!empty($statistics['category_distribution']))
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900 ">📈 Distribusi Kategori Kesiapan Belajar</h3>
                
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @php
                        $categories = [
                            'sangat_baik' => ['label' => 'Sangat Baik', 'icon' => '🌟', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
                            'baik' => ['label' => 'Baik', 'icon' => '👍', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                            'cukup' => ['label' => 'Cukup', 'icon' => '😊', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                            'perlu_pendampingan' => ['label' => 'Perlu Pendampingan', 'icon' => '💪', 'bg' => 'bg-red-100', 'text' => 'text-red-800'],
                        ];
                    @endphp

                    @foreach($categories as $key => $cat)
                        @php
                            $count = $statistics['category_distribution'][$key] ?? 0;
                            $percentage = $statistics['total_students'] > 0 ? round(($count / $statistics['total_students']) * 100) : 0;
                        @endphp
                        <div class="rounded-lg border-2 p-4 {{ $cat['bg'] }}">
                            <div class="text-center">
                                <div class="mb-2 text-3xl">{{ $cat['icon'] }}</div>
                                <p class="text-sm font-medium {{ $cat['text'] }}">{{ $cat['label'] }}</p>
                                <p class="mt-1 text-2xl font-bold {{ $cat['text'] }}">{{ $count }}</p>
                                <p class="text-xs {{ $cat['text'] }}">({{ $percentage }}%)</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Students Need Support -->
    @if(!empty($statistics['students_need_support']))
        <div class="overflow-hidden border-2 border-orange-300 bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-4 flex items-center">
                    <svg class="mr-2 h-6 w-6 text-orange-600 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 ">⚠️ Siswa yang Memerlukan Pendampingan</h3>
                </div>
                
                <p class="mb-4 text-sm font-medium text-gray-900 ">
                    {{ count($statistics['students_need_support']) }} siswa memerlukan perhatian khusus:
                </p>

                <div class="space-y-3">
                    @foreach($statistics['students_need_support'] as $student)
                        <div class="rounded-lg border-2 border-red-300 bg-red-50 p-4 shadow-sm ">
                            <div class="flex items-start justify-between">
                                <div class="w-full">
                                    <p class="text-lg font-semibold text-gray-900 ">{{ $student['name'] }}</p>
                                    <p class="mt-1 text-sm text-gray-900 ">
                                        Rata-rata: <strong class="text-red-700 ">{{ round($student['average'], 1) }}%</strong>
                                    </p>
                                    @if(!empty($student['needs_support_in']))
                                        <p class="mt-3 text-sm font-semibold text-gray-900 ">
                                            Aspek yang perlu ditingkatkan:
                                        </p>
                                        <ul class="mt-2 flex flex-wrap gap-2">
                                            @foreach($student['needs_support_in'] as $aspect)
                                                @php
                                                    $aspectInfo = $aspectLabels[$aspect] ?? ['label' => ucfirst($aspect), 'icon' => '📌'];
                                                @endphp
                                                <li class="inline-flex items-center rounded-full bg-white border border-red-300 px-3 py-1 text-sm font-medium text-gray-900 shadow-sm ">
                                                    <span class="mr-1">{{ $aspectInfo['icon'] }}</span> {{ $aspectInfo['label'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Class Recommendations -->
    @if(isset($statistics['recommendations']))
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-4 flex items-center">
                    <svg class="mr-2 h-6 w-6 text-green-600 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 ">💡 Rekomendasi untuk Kelas</h3>
                </div>
                
                <div class="space-y-4">
                    @if(isset($statistics['recommendations']['focus_areas']))
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 ">
                            <p class="text-sm font-medium text-gray-900 ">
                                <strong>🎯 Area Fokus:</strong> 
                            </p>
                            <p class="mt-1 text-sm text-gray-800 ">
                                {{ $statistics['recommendations']['focus_areas'] }}
                            </p>
                        </div>
                    @endif

                    @if(isset($statistics['recommendations']['support_needed']))
                        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 ">
                            <p class="text-sm font-medium text-gray-900 ">
                                <strong>⚠️ Perhatian:</strong>
                            </p>
                            <p class="mt-1 text-sm text-gray-800 ">
                                {{ $statistics['recommendations']['support_needed'] }}
                            </p>
                        </div>
                    @endif

                    @if(isset($statistics['recommendations']['status']))
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4 ">
                            <p class="text-sm font-medium text-gray-900 ">
                                <strong>✨ Status:</strong>
                            </p>
                            <p class="mt-1 text-sm text-gray-800 ">
                                {{ $statistics['recommendations']['status'] }}
                            </p>
                        </div>
                    @endif

                    @if(isset($statistics['recommendations']['actions']))
                        <div class="rounded-lg border-2 border-green-300 bg-green-50 p-5 shadow-sm ">
                            <h4 class="mb-4 text-lg font-bold text-gray-900 ">📋 Langkah yang Disarankan:</h4>
                            <ul class="space-y-3">
                                @foreach($statistics['recommendations']['actions'] as $action)
                                    <li class="flex items-start rounded-md bg-white p-3 shadow-sm ">
                                        <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-green-600 " fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-900 ">{{ $action }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Student List -->
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 ">Daftar Siswa Kelas {{ $selectedGrade }}</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700 ">
                    <thead class="bg-white text-xs uppercase text-gray-700 ">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Nama Siswa</th>
                            <th scope="col" class="px-6 py-3">Kategori</th>
                            <th scope="col" class="px-6 py-3">Rata-rata</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr class="border-b bg-white hover:bg-white ">
                                <td class="px-6 py-4">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 ">
                                    {{ $student->name }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_profile)
                                        @php 
                                            $profile = $student->profile;
                                            $catInfo = $categories[$profile->diagnostic_category] ?? $categories['cukup'];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $catInfo['bg'] }} {{ $catInfo['text'] }} {{ $catInfo['dark_bg'] }} {{ $catInfo['dark_text'] }}">
                                            {{ $catInfo['icon'] }} {{ $profile->getDiagnosticCategoryLabel() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Belum mengisi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_profile && $student->profile->aspect_scores)
                                        @php
                                            $avg = round(collect($student->profile->aspect_scores)->avg(), 1);
                                            $avgColor = $avg >= 86 ? 'text-green-600' : ($avg >= 71 ? 'text-blue-600' : ($avg >= 56 ? 'text-yellow-600' : 'text-red-600'));
                                        @endphp
                                        <span class="font-semibold {{ $avgColor }}">{{ $avg }}%</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_profile)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 ">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 ">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_profile)
                                        <a href="{{ route('assessment.student-profile', ['userId' => $student->id, 'assessmentId' => $student->profile->assessment_id]) }}" 
                                           class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 ">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat Detail
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-700 ">
                                    Tidak ada siswa di kelas ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@else
    <!-- No Data -->
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 ">Belum Ada Data Diagnostik</h3>
            <p class="mt-2 text-sm text-gray-700 ">
                @if(!isset($diagnosticAssessment))
                    Belum ada asesmen diagnostik untuk semester ini.
                @else
                    Belum ada siswa kelas {{ $selectedGrade }} yang mengisi asesmen diagnostik kesiapan belajar untuk semester ini.
                @endif
            </p>
        </div>
    </div>
@endif

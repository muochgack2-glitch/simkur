@if($statistics['completed_students'] > 0)
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
                <p class="text-sm font-medium text-gray-800 ">Sudah Mengisi</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 ">{{ $statistics['completed_students'] }}</p>
            </div>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-800 ">Progress</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 ">{{ round($statistics['completion_percentage']) }}%</p>
            </div>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-800 ">Gaya Dominan</p>
                <p class="mt-2 text-xl font-bold text-gray-900 ">
                    @if($statistics['dominant_style'])
                        {{ ucfirst(str_replace('_', '/', $statistics['dominant_style'])) }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Distribution Chart -->
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 ">📊 Distribusi Gaya Belajar Kelas {{ $selectedGrade }}</h3>
            
            <div class="space-y-4">
                @foreach($statistics['distribution'] as $style => $data)
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="font-medium text-gray-700 ">
                                @if($style === 'visual')
                                    👁️ Visual
                                @elseif($style === 'auditory')
                                    👂 Auditory
                                @elseif($style === 'kinesthetic')
                                    🤸 Kinesthetic
                                @elseif($style === 'reading_writing')
                                    📖 Reading/Writing
                                @endif
                            </span>
                            <span class="font-semibold text-gray-900 ">
                                {{ $data['count'] }} siswa ({{ $data['percentage'] }}%)
                            </span>
                        </div>
                        <div class="h-6 w-full overflow-hidden rounded-full bg-gray-200 ">
                            <div class="h-full rounded-full transition-all duration-300
                                {{ $style === 'visual' ? 'bg-blue-600' : '' }}
                                {{ $style === 'auditory' ? 'bg-green-600' : '' }}
                                {{ $style === 'kinesthetic' ? 'bg-orange-600' : '' }}
                                {{ $style === 'reading_writing' ? 'bg-purple-600' : '' }}"
                                 style="width: {{ $data['percentage'] }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Teaching Recommendations -->
    @if($statistics['recommendations'])
        <div class="overflow-hidden bg-gradient-to-r from-blue-50 to-blue-100 shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-4 flex items-center">
                    <svg class="mr-2 h-6 w-6 text-blue-600 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 ">💡 Rekomendasi Strategi Mengajar</h3>
                </div>
                
                <div class="rounded-lg bg-white p-6 ">
                    <p class="mb-4 text-sm text-gray-800 ">
                        Kelas ini didominasi oleh <strong class="text-gray-900 ">{{ ucfirst(str_replace('_', '/', $statistics['dominant_style'])) }} Learner</strong>. 
                        Berikut strategi yang direkomendasikan:
                    </p>

                    <div class="mb-6">
                        <h4 class="mb-3 font-medium text-gray-900 ">🎯 Prioritas Utama:</h4>
                        <ul class="space-y-2">
                            @foreach($statistics['recommendations']['priority'] as $tip)
                                <li class="flex items-start">
                                    <svg class="mr-2 mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-700 ">{{ $tip }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 ">
                        <p class="text-sm text-blue-800 ">
                            <strong>⚖️ Penting:</strong> {{ $statistics['recommendations']['balance'] }}
                        </p>
                    </div>
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
                            <th scope="col" class="px-6 py-3">Gaya Belajar Dominan</th>
                            <th scope="col" class="px-6 py-3">Skor Detail</th>
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
                                        @php $profile = $student->profile; @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                            {{ $profile->dominant_style === 'visual' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $profile->dominant_style === 'auditory' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $profile->dominant_style === 'kinesthetic' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $profile->dominant_style === 'reading_writing' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ $profile->getDominantStyleIcon() }} {{ $profile->getDominantStyleLabel() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Belum mengisi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_profile)
                                        <div class="text-xs text-gray-800 ">
                                            <div>V: {{ $student->profile->visual_score }}</div>
                                            <div>A: {{ $student->profile->auditory_score }}</div>
                                            <div>K: {{ $student->profile->kinesthetic_score }}</div>
                                            <div>R/W: {{ $student->profile->reading_writing_score }}</div>
                                        </div>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 ">Belum Ada Data VARK</h3>
            <p class="mt-2 text-sm text-gray-700 ">
                Belum ada siswa kelas {{ $selectedGrade }} yang mengisi asesmen gaya belajar VARK untuk semester ini.
            </p>
        </div>
    </div>
@endif

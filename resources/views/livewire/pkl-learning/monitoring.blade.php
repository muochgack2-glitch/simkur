<div>
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">📊 Monitoring Pembelajaran PKL</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Overview semua course pembelajaran selama PKL</p>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_courses'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Total Course</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-green-600">{{ $stats['published'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Published</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_submissions'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Tugas Masuk</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['total_graded'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Dinilai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-pink-600">{{ $stats['total_quiz_responses'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">Kuis Selesai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-red-500">{{ $stats['late_submissions'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">⚠ Terlambat</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['avg_assignment_score'] ?? '-' }}</p>
            <p class="text-xs text-gray-500">⭐ Rata-rata Tugas</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-cyan-600">{{ $stats['avg_quiz_score'] ?? '-' }}</p>
            <p class="text-xs text-gray-500">⭐ Rata-rata Kuis</p>
        </div>
    </div>

    <!-- Course Detail Modal -->
    @if($selectedCourseId && $courseDetail)
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-blue-300 dark:border-blue-700 p-5">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $courseDetail->title }}</h2>
                    <p class="text-sm text-gray-500">{{ $courseDetail->teacher->name ?? '-' }} - {{ $courseDetail->subject->name ?? '-' }}</p>
                </div>
                <button wire:click="closeDetail" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg">Tutup</button>
            </div>

            <!-- Per-Class Progress -->
            @if(count($classProgress) > 0)
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">👥 Progress Per Kelas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                @foreach($classProgress as $cp)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $cp['name'] }}</span>
                        <span class="text-sm font-bold {{ $cp['avg_progress'] >= 80 ? 'text-green-600' : ($cp['avg_progress'] >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ $cp['avg_progress'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-green-500 rounded-full" style="width: {{ $cp['avg_progress'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ $cp['student_count'] }} siswa</span>
                        <span>✅ {{ $cp['completed'] }} selesai 100%</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Per-Student Detail Table -->
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">📝 Detail Per Siswa</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <th class="text-left py-2.5 px-3 font-semibold">Siswa</th>
                        <th class="text-left py-2.5 px-3 font-semibold">Kelas</th>
                        <th class="text-center py-2.5 px-3 font-semibold">Progress</th>
                        <th class="text-center py-2.5 px-3 font-semibold">📝 Tugas</th>
                        <th class="text-center py-2.5 px-3 font-semibold">⚠ Telat</th>
                        <th class="text-center py-2.5 px-3 font-semibold">⭐ Nilai Tugas</th>
                        <th class="text-center py-2.5 px-3 font-semibold">❓ Kuis</th>
                        <th class="text-center py-2.5 px-3 font-semibold">⭐ Nilai Kuis</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($studentDetails as $sd)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="py-2.5 px-3 font-medium text-gray-800 dark:text-white">{{ $sd['name'] }}</td>
                            <td class="py-2.5 px-3 text-gray-500">{{ $sd['class'] }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="flex items-center gap-2 justify-center">
                                    <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $sd['progress'] >= 80 ? 'bg-green-500' : ($sd['progress'] >= 50 ? 'bg-amber-500' : 'bg-red-400') }}" style="width: {{ $sd['progress'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ $sd['progress'] >= 80 ? 'text-green-600' : ($sd['progress'] >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ $sd['progress'] }}%</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-center text-xs">{{ $sd['assignments_submitted'] }}/{{ $sd['assignments_total'] }}</td>
                            <td class="py-2.5 px-3 text-center text-xs {{ $sd['assignments_late'] > 0 ? 'text-red-500 font-bold' : 'text-gray-400' }}">{{ $sd['assignments_late'] }}</td>
                            <td class="py-2.5 px-3 text-center text-xs font-medium">{{ $sd['assignment_avg'] }}</td>
                            <td class="py-2.5 px-3 text-center text-xs">{{ $sd['quizzes_done'] }}/{{ $sd['quizzes_total'] }}</td>
                            <td class="py-2.5 px-3 text-center text-xs font-medium">{{ $sd['quiz_avg'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    
    <!-- Teacher Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-bold text-gray-800 dark:text-white">👨 Performa Guru PKL</h2>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50">
                <th class="text-left py-2.5 px-4 font-semibold">Guru</th>
                <th class="text-left py-2.5 px-4 font-semibold">Mapel</th>
                <th class="text-center py-2.5 px-4 font-semibold">Course</th>
                <th class="text-center py-2.5 px-4 font-semibold">✅ Published</th>
                <th class="text-center py-2.5 px-4 font-semibold">📚 Materi</th>
                <th class="text-center py-2.5 px-4 font-semibold">📝 Tugas</th>
                <th class="text-center py-2.5 px-4 font-semibold">❓ Kuis</th>
                <th class="text-center py-2.5 px-4 font-semibold">👥 Siswa</th>
                <th class="text-center py-2.5 px-4 font-semibold">⚠ Belum Dinilai</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($teacherStats as $ts)
                <tr class="hover:bg-gray-50/50 {{ !$ts['has_course'] ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                    <td class="py-2.5 px-4">
                        <span class="font-medium text-gray-800 dark:text-white">{{ $ts['name'] }}</span>
                        @if(!$ts['has_course'])<span class="ml-2 px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-bold">❌ Belum buat course</span>@endif
                    </td>
                    <td class="py-2.5 px-4 text-xs text-gray-500">{{ $ts['mapel'] }}</td>
                    <td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ $ts['courses'] }}</span></td>
                    <td class="py-2.5 px-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">{{ $ts['published'] }}</span></td>
                    <td class="py-2.5 px-4 text-center text-xs">{{ $ts['materials'] }}</td>
                    <td class="py-2.5 px-4 text-center text-xs">{{ $ts['assignments'] }}</td>
                    <td class="py-2.5 px-4 text-center text-xs">{{ $ts['quizzes'] }}</td>
                    <td class="py-2.5 px-4 text-center text-xs">{{ $ts['students'] }}</td>
                    <td class="py-2.5 px-4 text-center">
                        @if($ts['ungraded'] > 0)
                        <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-bold">{{ $ts['ungraded'] }}</span>
                        @else
                        <span class="text-green-500 text-xs">✅ 0</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="py-6 text-center text-gray-400">Belum ada data guru</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Course Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <th class="text-left py-3 px-4 font-semibold">Course</th>
                <th class="text-left py-3 px-4 font-semibold">Guru</th>
                <th class="text-left py-3 px-4 font-semibold">Mapel</th>
                <th class="text-center py-3 px-4 font-semibold">Materi</th>
                <th class="text-center py-3 px-4 font-semibold">Tugas</th>
                <th class="text-center py-3 px-4 font-semibold">Kuis</th>
                <th class="text-center py-3 px-4 font-semibold">Status</th>
                <th class="text-center py-3 px-4 font-semibold">Deadline</th>
                <th class="text-center py-3 px-4 font-semibold">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($courses as $course)
                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/30 {{ $selectedCourseId == $course->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <td class="py-3 px-4 font-medium text-gray-800 dark:text-white">{{ $course->title }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $course->teacher->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $course->subject->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">{{ $course->materials->count() }}</span></td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">{{ $course->assignments->count() }}</span></td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded-full text-xs font-medium">{{ $course->quizzes->count() }}</span></td>
                    <td class="py-3 px-4 text-center">
                        @if($course->is_published)<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Published</span>
                        @else<span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Draft</span>@endif
                    </td>
                    <td class="py-3 px-4 text-center text-xs text-gray-500">{{ $course->deadline ? $course->deadline->format('d M Y') : '-' }}</td>
                    <td class="py-3 px-4 text-center">
                        <button wire:click="showDetail({{ $course->id }})" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-medium">👁 Detail</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="py-8 text-center text-gray-400">Belum ada course</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
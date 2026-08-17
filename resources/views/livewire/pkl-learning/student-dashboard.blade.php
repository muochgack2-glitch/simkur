<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📚 Pembelajaran PKL Saya</h1>
        <p class="text-gray-500 mt-1 text-sm">Materi, tugas, dan kuis selama masa PKL</p>
    </div>

    @if($courses->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border shadow-sm">
        <div class="text-5xl mb-4">📚</div>
        <h3 class="text-lg font-bold text-gray-400">Belum ada pembelajaran</h3>
        <p class="text-sm text-gray-400 mt-2">Guru belum mempublikasikan materi untuk kelas Anda</p>
    </div>
    @else

    <!-- Overall Stats -->
    @php
        $totalCourses = $courses->count();
        $totalProgress = $totalCourses > 0 ? round(collect($progress)->avg('percentage')) : 0;
        $completedCourses = collect($progress)->where('percentage', '>=', 100)->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-blue-600 rounded-2xl p-4 text-white shadow-lg">
            <div class="text-2xl font-bold">{{ $totalCourses }}</div>
            <div class="text-blue-100 text-xs font-medium mt-1">Total Mapel</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $completedCourses }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">✅ Selesai</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-amber-600">{{ $totalCourses - $completedCourses }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">📖 Berlangsung</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold {{ $totalProgress >= 80 ? 'text-green-600' : ($totalProgress >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $totalProgress }}%</div>
            <div class="text-gray-500 text-xs font-medium mt-1">📊 Rata-rata</div>
        </div>
    </div>

    <!-- Period Sections -->
    @foreach($periods as $period)
    @php $periodCourses = $groupedCourses->get($period->id, collect()); @endphp
    @if($periodCourses->isNotEmpty())
    <div class="mb-8">
        <!-- Period Header -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold
                    {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700') }}">
                    {{ $period->period_number }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        {{ $period->title }}
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700') }}">
                            {{ $period->getStatusLabel() }}
                        </span>
                    </h2>
                    <p class="text-xs text-gray-400">📅 {{ $period->getDateRangeLabel() }}</p>
                </div>
            </div>
        </div>

        <!-- Course Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($periodCourses as $course)
            @php $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0]; @endphp
            @if($period->is_active)
            <a href="{{ route('pkl-learning.student.course', $course) }}" class="block bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:border-blue-300 transition-all overflow-hidden group">
                <!-- Top accent -->
                <div class="h-1.5 {{ $prog['percentage'] >= 100 ? 'bg-green-500' : ($period->isCurrentPeriod() ? 'bg-blue-500' : 'bg-gray-300') }}"></div>
                <div class="p-5">
                    <!-- Subject badge -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 font-semibold">{{ $course->subject->name ?? '' }}</span>
                        @if($prog['percentage'] >= 100)
                        <span class="text-green-500 text-lg">✅</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500">👨‍🏫 {{ $course->teacher->name ?? '' }}</p>

                    <!-- Content counts -->
                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">
                            <span>📄</span> {{ $course->materials->count() }}
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">
                            <span>📝</span> {{ $course->assignments->count() }}
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">
                            <span>❓</span> {{ $course->quizzes->count() }}
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-500 font-medium">Progress</span>
                            <span class="font-bold {{ $prog['percentage'] >= 80 ? 'text-green-600' : ($prog['percentage'] >= 50 ? 'text-amber-600' : 'text-gray-500') }}">{{ $prog['percentage'] }}%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $prog['percentage'] >= 80 ? 'bg-green-500' : ($prog['percentage'] >= 50 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ $prog['percentage'] }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $prog['completed'] }}/{{ $prog['total'] }} selesai</p>
                    </div>
                </div>
            </a>
            @else
            <div class="relative block bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden opacity-70 cursor-not-allowed">
                <div class="absolute inset-0 bg-gray-50/60 dark:bg-gray-900/60 z-10 flex items-center justify-center rounded-2xl">
                    <div class="text-center">
                        <div class="text-3xl mb-1">🔒</div>
                        <p class="text-xs font-semibold text-gray-500">Periode Tidak Aktif</p>
                    </div>
                </div>
                <!-- Top accent -->
                <div class="h-1.5 {{ $prog['percentage'] >= 100 ? 'bg-green-500' : ($period->isCurrentPeriod() ? 'bg-blue-500' : 'bg-gray-300') }}"></div>
                <div class="p-5">
                    <!-- Subject badge -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 font-semibold">{{ $course->subject->name ?? '' }}</span>
                        @if($prog['percentage'] >= 100)
                        <span class="text-green-500 text-lg">✅</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500">👨‍🏫 {{ $course->teacher->name ?? '' }}</p>

                    <!-- Content counts -->
                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">
                            <span>📄</span> {{ $course->materials->count() }}
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">
                            <span>📝</span> {{ $course->assignments->count() }}
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">
                            <span>❓</span> {{ $course->quizzes->count() }}
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-500 font-medium">Progress</span>
                            <span class="font-bold {{ $prog['percentage'] >= 80 ? 'text-green-600' : ($prog['percentage'] >= 50 ? 'text-amber-600' : 'text-gray-500') }}">{{ $prog['percentage'] }}%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $prog['percentage'] >= 80 ? 'bg-green-500' : ($prog['percentage'] >= 50 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ $prog['percentage'] }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $prog['completed'] }}/{{ $prog['total'] }} selesai</p>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    <!-- Courses without period -->
    @php $noPeriodCourses = $groupedCourses->get('', collect())->merge($groupedCourses->get(null, collect())); @endphp
    @if($noPeriodCourses->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-base font-bold text-gray-600 mb-4 flex items-center gap-2">
            📚 Lainnya
            <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">{{ $noPeriodCourses->count() }}</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($noPeriodCourses as $course)
            @php $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0]; @endphp
            <a href="{{ route('pkl-learning.student.course', $course) }}" class="block bg-white dark:bg-gray-800 rounded-2xl border shadow-sm hover:shadow-lg hover:border-blue-300 transition-all overflow-hidden group">
                <div class="h-1.5 bg-gray-300"></div>
                <div class="p-5">
                    <span class="px-2.5 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 font-semibold">{{ $course->subject->name ?? '' }}</span>
                    <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors mt-3 mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500">👨‍🏫 {{ $course->teacher->name ?? '' }}</p>
                    <div class="mt-4">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-bold text-gray-600">{{ $prog['percentage'] }}%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $prog['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
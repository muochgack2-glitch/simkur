<div>
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">📚 Pembelajaran PKL Saya</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">Materi, tugas, dan kuis selama masa PKL</p>
    </div>

    @if($courses->isEmpty())
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-500">Belum ada pembelajaran</h3>
        <p class="text-sm text-gray-400 mt-1">Guru belum mempublikasikan materi untuk kelas Anda</p>
    </div>
    @else

    <!-- Period Sections -->
    @foreach($periods as $period)
    @php $periodCourses = $groupedCourses->get($period->id, collect()); @endphp
    @if($periodCourses->isNotEmpty())
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold
                    {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700') }}">
                    {{ $period->period_number }}
                </div>
                <h2 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white">{{ $period->title }}</h2>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700') }}">
                    {{ $period->getStatusLabel() }}
                </span>
            </div>
            <p class="text-xs text-gray-400 sm:ml-auto">📅 {{ $period->getDateRangeLabel() }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($periodCourses as $course)
            @php $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0]; @endphp
            <a href="{{ route('pkl-learning.student.course', $course) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all overflow-hidden group">
                <div class="h-1.5 {{ $period->isCurrentPeriod() ? 'bg-green-500' : 'bg-blue-500' }}"></div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">{{ $course->subject->name ?? '' }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $course->teacher->name ?? '' }}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                        <span>{{ $course->materials->count() }} materi</span>
                        <span>{{ $course->assignments->count() }} tugas</span>
                        <span>{{ $course->quizzes->count() }} kuis</span>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-semibold {{ $prog['percentage'] >= 80 ? 'text-green-600' : ($prog['percentage'] >= 50 ? 'text-amber-600' : 'text-gray-600') }}">{{ $prog['percentage'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $prog['percentage'] >= 80 ? 'bg-green-500' : ($prog['percentage'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $prog['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    <!-- Courses without period -->
    @php $noPeriodCourses = $groupedCourses->get('', collect())->merge($groupedCourses->get(null, collect())); @endphp
    @if($noPeriodCourses->isNotEmpty())
    <div class="mb-6">
        <h2 class="text-base font-bold text-gray-600 mb-3">📚 Lainnya</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($noPeriodCourses as $course)
            @php $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0]; @endphp
            <a href="{{ route('pkl-learning.student.course', $course) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all overflow-hidden group">
                <div class="h-1.5 bg-gray-400"></div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">{{ $course->subject->name ?? '' }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $course->teacher->name ?? '' }}</p>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-semibold text-gray-600">{{ $prog['percentage'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
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
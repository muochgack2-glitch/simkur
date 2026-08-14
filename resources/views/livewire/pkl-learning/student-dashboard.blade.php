<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">?? Pembelajaran PKL Saya</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Materi, tugas, dan kuis selama masa PKL</p>
    </div>

    @if($courses->isEmpty())
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <i class="fas fa-book-reader text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-500">Belum ada pembelajaran</h3>
        <p class="text-sm text-gray-400 mt-1">Guru belum mempublikasikan materi untuk kelas Anda</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($courses as $course)
        @php $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0]; @endphp
        <a href="{{ route('pkl-learning.student.course', $course) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all overflow-hidden group">
            <div class="h-2 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">{{ $course->subject->name ?? '' }}</span>
                    @if($course->isOngoing())<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 font-medium">Aktif</span>@endif
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors">{{ $course->title }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $course->teacher->name ?? '' }}</p>
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $course->description }}</p>
                <div class="flex items-center gap-3 mt-3 text-xs text-gray-500">
                    <span><i class="fas fa-file-alt mr-1"></i>{{ $course->materials->count() }} materi</span>
                    <span><i class="fas fa-tasks mr-1"></i>{{ $course->assignments->count() }} tugas</span>
                    <span><i class="fas fa-question-circle mr-1"></i>{{ $course->quizzes->count() }} kuis</span>
                </div>
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Progress</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $prog['completed'] }}/{{ $prog['total'] }} ({{ $prog['percentage'] }}%)</span>
                    </div>
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all" style="width: {{ $prog['percentage'] }}%"></div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-clock mr-1"></i>Deadline: {{ $course->deadline->translatedFormat('d M Y') }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>

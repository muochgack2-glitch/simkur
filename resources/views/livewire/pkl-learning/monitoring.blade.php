<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><i class="fas fa-chart-bar mr-1"></i> Monitoring Pembelajaran PKL</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Overview semua course pembelajaran selama PKL</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_courses'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Course</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $stats['published'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Published</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_submissions'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Tugas Masuk</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['total_graded'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Dinilai</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-pink-600">{{ $stats['total_quiz_responses'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Kuis Selesai</div>
        </div>
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
                <th class="text-left py-3 px-4 font-semibold">Deadline</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($courses as $course)
                <tr class="hover:bg-gray-50/50">
                    <td class="py-3 px-4 font-medium text-gray-800 dark:text-white">{{ $course->title }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $course->teacher->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $course->subject->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">{{ $course->materials->count() }}</td>
                    <td class="py-3 px-4 text-center">{{ $course->assignments->count() }}</td>
                    <td class="py-3 px-4 text-center">{{ $course->quizzes->count() }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($course->is_published)<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 font-medium">Published</span>@else<span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 font-medium">Draft</span>@endif
                    </td>
                    <td class="py-3 px-4 text-xs text-gray-500">{{ $course->deadline->translatedFormat('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-8 text-center text-gray-400">Belum ada course</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

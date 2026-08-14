<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.student.dashboard') }}" class="text-gray-500 hover:text-gray-700">⬅</a>
        <div class="flex-1">
            <span class="text-sm text-gray-500">{{ $course->subject->name ?? '' }} - {{ $course->teacher->name ?? '' }}</span>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $course->title }}</h1>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-green-600">{{ $progress['percentage'] }}%</div>
            <div class="text-xs text-gray-500">{{ $progress['completed'] }}/{{ $progress['total'] }} selesai</div>
        </div>
    </div>
    <div class="space-y-6">
        @if($course->description)
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-blue-800 dark:text-blue-300">{{ $course->description }}</p>
        </div>
        @endif
        <!-- Materials -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">📖 Materi Pembelajaran</h2>
            @foreach($course->materials as $mat)
            <div class="flex items-center gap-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center"></div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $mat->title }}</p>
                    <p class="text-xs text-gray-500">{{ strtoupper($mat->type) }}</p>
                </div>
                @if($mat->file_path)<a href="{{ Storage::url($mat->file_path) }}" target="_blank" class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-sm font-medium">⬇ Unduh</a>@endif
                @if(!$mat->file_path && !$mat->external_url)<span class="text-xs text-gray-400 italic">⚠ File belum diupload guru</span>@endif
                @if($mat->external_url)<a href="{{ $mat->external_url }}" target="_blank" class="px-4 py-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-sm font-medium">🔗 Buka</a>@endif
            </div>
            @endforeach
            @if($course->materials->isEmpty())<p class="text-sm text-gray-400">Belum ada materi</p>@endif
        </div>
        <!-- Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">📝 Tugas</h2>
            @foreach($course->assignments as $asg)
            @php $status = $assignmentStatuses[$asg->id] ?? []; @endphp
            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $asg->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Deadline: {{ $asg->deadline->translatedFormat('d M Y H:i') }}
                        @if($asg->isOverdue() && !($status['submitted'] ?? false))<span class="text-red-500 font-medium ml-1">Lewat deadline</span>@endif
                    </p>
                    @if($status['graded'] ?? false)
                    <p class="text-xs text-green-600 font-medium mt-1">Dinilai: {{ $status['score'] }}/{{ $asg->max_score }}</p>
                    @endif
                </div>
                <div>
                    @if($status['submitted'] ?? false)
                    <span class="px-3 py-1.5 text-xs bg-green-100 text-green-700 rounded-lg font-medium">Dikumpulkan ✅</span>
                    @else
                    <a href="{{ route('pkl-learning.student.submission', $asg) }}" class="px-3 py-1.5 text-xs bg-purple-500 text-white hover:bg-purple-600 rounded-lg font-medium">▶ Kerjakan</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <!-- Quizzes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">❓ Kuis</h2>
            @foreach($course->quizzes as $quiz)
            @if(!$quiz->is_published) @continue @endif
            @php $qStatus = $quizStatuses[$quiz->id] ?? []; @endphp
            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $quiz->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $quiz->questions->count() }} soal - {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' menit' : 'Tanpa batas waktu' }} - Deadline: {{ $quiz->deadline->translatedFormat('d M Y H:i') }}</p>
                    @if($qStatus['graded'] ?? false)
                    <p class="text-xs text-green-600 font-medium mt-1">Nilai: {{ $qStatus['score'] }}</p>
                    @endif
                </div>
                <div>
                    @if($qStatus['submitted'] ?? false)
                    <span class="px-3 py-1.5 text-xs bg-green-100 text-green-700 rounded-lg font-medium">Selesai ✅</span>
                    @else
                    <a href="{{ route('pkl-learning.student.quiz', $quiz) }}" class="px-3 py-1.5 text-xs bg-pink-500 text-white hover:bg-pink-600 rounded-lg font-medium">{{ ($qStatus['started'] ?? false) ? 'Lanjutkan' : 'Mulai' }}</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
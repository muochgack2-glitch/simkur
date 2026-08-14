<div class="max-w-4xl mx-auto px-2 sm:px-0">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.student.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-xl">⬅</a>
        <div class="flex-1">
            <span class="text-xs sm:text-sm text-gray-500">{{ $course->subject->name ?? '' }} - {{ $course->teacher->name ?? '' }}</span>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $course->title }}</h1>
        </div>
        <div class="flex items-center gap-2 sm:text-right">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br {{ $progress['percentage'] >= 80 ? 'from-green-400 to-emerald-600' : ($progress['percentage'] >= 50 ? 'from-amber-400 to-orange-500' : 'from-red-400 to-rose-500') }} flex items-center justify-center text-white font-bold text-sm">{{ $progress['percentage'] }}%</div>
            <div class="text-xs text-gray-500">{{ $progress['completed'] }}/{{ $progress['total'] }}<br>selesai</div>
        </div>
    </div>

    @if($course->description)
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800 mb-6">
        <p class="text-sm text-blue-800 dark:text-blue-300">{{ $course->description }}</p>
    </div>
    @endif

    <div class="space-y-4">
        <!-- Materials -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white mb-4">📚 Materi Pembelajaran</h2>
            @foreach($course->materials as $mat)
            <div class="py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center text-lg
                        {{ $mat->type === 'pdf' ? 'bg-red-100 text-red-600' : '' }}
                        {{ $mat->type === 'document' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $mat->type === 'image' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $mat->type === 'video' ? 'bg-purple-100 text-purple-600' : '' }}
                        {{ $mat->type === 'link' ? 'bg-amber-100 text-amber-600' : '' }}">
                        {{ $mat->type === 'pdf' ? '📚' : ($mat->type === 'video' ? '▶' : ($mat->type === 'link' ? '🔗' : '📚')) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $mat->title }}</p>
                        <p class="text-xs text-gray-500">{{ strtoupper($mat->type) }}{{ $mat->file_size ? ' - ' . number_format($mat->file_size / 1024, 0) . ' KB' : '' }}</p>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 mt-2 ml-13 pl-13" style="padding-left: 52px;">
                    @if($mat->file_path)
                        @php
                            $fileUrl = Storage::url($mat->file_path);
                            $ext = strtolower(pathinfo($mat->file_path, PATHINFO_EXTENSION));
                            $isPdf = $ext === 'pdf';
                            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                        @endphp
                        @php $isDoc = in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx']); $fullUrl = url(Storage::url($mat->file_path)); @endphp
                        @if($isPdf)
                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs sm:text-sm font-medium transition">👁 Preview</a>
                        @elseif($isImage)
                        <button onclick="document.getElementById('preview-{{ $mat->id }}').classList.toggle('hidden')" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs sm:text-sm font-medium transition">👁 Lihat</button>
                        @elseif($isDoc)
                        <a href="https://docs.google.com/gview?url={{ urlencode($fullUrl) }}&embedded=true" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs sm:text-sm font-medium transition">👁 Preview</a>
                        @endif
                        <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-1 px-3 py-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs sm:text-sm font-medium transition">⬇ Unduh</a>
                    @endif
                    @if($mat->external_url)
                        <a href="{{ $mat->external_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 bg-purple-50 text-purple-600 hover:bg-purple-100 rounded-lg text-xs sm:text-sm font-medium transition">🔗 Buka Link</a>
                    @endif
                    @if(!$mat->file_path && !$mat->external_url)
                        <span class="text-xs text-gray-400 italic py-2">⚠ File belum diupload guru</span>
                    @endif
                </div>
                <!-- Image Preview -->
                @if($mat->file_path && in_array(strtolower(pathinfo($mat->file_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']))
                <div id="preview-{{ $mat->id }}" class="hidden mt-3" style="padding-left: 52px;">
                    <img src="{{ Storage::url($mat->file_path) }}" alt="{{ $mat->title }}" class="max-w-full rounded-lg border border-gray-200 shadow-sm">
                </div>
                @endif
            </div>
            @endforeach
            @if($course->materials->isEmpty())<p class="text-sm text-gray-400">Belum ada materi</p>@endif
        </div>

        <!-- Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white mb-4">📝 Tugas</h2>
            @foreach($course->assignments as $asg)
            @php $status = $assignmentStatuses[$asg->id] ?? []; @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between py-3 gap-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex-1">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $asg->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Deadline: {{ $asg->deadline->translatedFormat('d M Y H:i') }}
                        @if($asg->isOverdue() && !($status['submitted'] ?? false))<span class="text-red-500 font-medium ml-1">⚠ Lewat deadline</span>@endif
                    </p>
                    @if($status['graded'] ?? false)
                    <div class="mt-1.5 bg-green-50 dark:bg-green-900/20 rounded-lg px-3 py-2">
                        <p class="text-xs text-green-700 font-bold">✅ Nilai: {{ $status['score'] }}/{{ $asg->max_score }}</p>
                        @if($status['feedback'] ?? null)<p class="text-xs text-gray-600 mt-1">💬 {{ $status['feedback'] }}</p>@endif
                    </div>
                    @endif
                </div>
                <div class="flex-shrink-0">
                    @if($status['submitted'] ?? false)
                    <span class="inline-flex px-3 py-1.5 text-xs bg-green-100 text-green-700 rounded-lg font-medium">✅ Dikumpulkan</span>
                    @else
                    <a href="{{ route('pkl-learning.student.submission', $asg) }}" class="inline-flex px-4 py-2 text-xs bg-purple-500 text-white hover:bg-purple-600 rounded-lg font-medium transition">📝 Kerjakan</a>
                    @endif
                </div>
            </div>
            @endforeach
            @if($course->assignments->isEmpty())<p class="text-sm text-gray-400">Belum ada tugas</p>@endif
        </div>

        <!-- Quizzes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white mb-4">❓ Kuis</h2>
            @foreach($course->quizzes as $quiz)
            @if(!$quiz->is_published) @continue @endif
            @php $qStatus = $quizStatuses[$quiz->id] ?? []; @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between py-3 gap-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex-1">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $quiz->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $quiz->questions->count() }} soal - {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' menit' : 'Tanpa batas' }}</p>
                    <p class="text-xs text-gray-400">Deadline: {{ $quiz->deadline->translatedFormat('d M Y H:i') }}</p>
                    @if($qStatus['graded'] ?? false)
                    <div class="mt-1.5 bg-green-50 dark:bg-green-900/20 rounded-lg px-3 py-2">
                        <p class="text-xs text-green-700 font-bold">✅ Nilai: {{ $qStatus['score'] }}</p>
                    </div>
                    @endif
                </div>
                <div class="flex-shrink-0">
                    @if($qStatus['submitted'] ?? false)
                    <span class="inline-flex px-3 py-1.5 text-xs bg-green-100 text-green-700 rounded-lg font-medium">✅ Selesai</span>
                    @else
                    <a href="{{ route('pkl-learning.student.quiz', $quiz) }}" class="inline-flex px-4 py-2 text-xs bg-pink-500 text-white hover:bg-pink-600 rounded-lg font-medium transition">{{ ($qStatus['started'] ?? false) ? 'Lanjutkan' : '▶ Mulai' }}</a>
                    @endif
                </div>
            </div>
            @endforeach
            @if($course->quizzes->where('is_published', true)->isEmpty())<p class="text-sm text-gray-400">Belum ada kuis</p>@endif
        </div>
    </div>
</div>
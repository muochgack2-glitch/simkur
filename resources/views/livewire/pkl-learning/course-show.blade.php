<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.dashboard') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left text-lg"></i></a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                @if($course->is_published)<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 font-medium">Published</span>@else<span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 font-medium">Draft</span>@endif
                <span class="text-sm text-gray-500">{{ $course->subject->name ?? '' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $course->title }}</h1>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-2">Deskripsi</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $course->description ?: 'Tidak ada deskripsi' }}</p>
                @if($course->competency)
                <h3 class="font-semibold text-gray-800 dark:text-white mt-4 mb-2">Kompetensi</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $course->competency }}</p>
                @endif
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3"><i class="fas fa-book text-green-500 mr-2"></i>Materi ({{ $course->materials->count() }})</h3>
                @foreach($course->materials as $mat)
                <div class="flex items-center gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <i class="fas fa-file text-gray-400 text-lg"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $mat->title }}</p>
                        <p class="text-xs text-gray-500">{{ $mat->type }}</p>
                    </div>
                    @if($mat->file_path)<a href="{{ Storage::url($mat->file_path) }}" target="_blank" class="text-blue-500 text-sm"><i class="fas fa-download"></i></a>@endif
                    @if($mat->external_url)<a href="{{ $mat->external_url }}" target="_blank" class="text-blue-500 text-sm"><i class="fas fa-external-link-alt"></i></a>@endif
                </div>
                @endforeach
                @if($course->materials->isEmpty())<p class="text-sm text-gray-400">Belum ada materi</p>@endif
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3"><i class="fas fa-tasks text-purple-500 mr-2"></i>Tugas ({{ $course->assignments->count() }})</h3>
                @foreach($course->assignments as $asg)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $asg->title }}</p>
                        <p class="text-xs text-gray-500">Deadline: {{ $asg->deadline->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <a href="{{ route('pkl-learning.grading', $asg) }}" class="px-3 py-1.5 text-xs bg-purple-50 text-purple-600 hover:bg-purple-100 rounded-lg font-medium"><i class="fas fa-check-double mr-1"></i>Nilai</a>
                </div>
                @endforeach
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3"><i class="fas fa-question-circle text-pink-500 mr-2"></i>Kuis ({{ $course->quizzes->count() }})</h3>
                @foreach($course->quizzes as $quiz)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $quiz->title }}</p>
                        <p class="text-xs text-gray-500">{{ $quiz->questions->count() }} soal - {{ $quiz->responses->where('submitted_at', '!=', null)->count() }} selesai - Deadline: {{ $quiz->deadline->translatedFormat('d M Y H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sticky top-4">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3"><i class="fas fa-users text-blue-500 mr-2"></i>Progress Siswa ({{ $students->count() }})</h3>
                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    @foreach($students as $student)
                    @php $prog = $studentProgress[$student->id] ?? ['percentage' => 0]; @endphp
                    <div class="flex items-center gap-3 py-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $student->name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all" style="width: {{ $prog['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-500 w-8">{{ $prog['percentage'] }}%</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
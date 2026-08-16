<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.dashboard') }}" class="text-gray-500 hover:text-gray-700">⬅</a>
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
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">📖 Materi ({{ $course->materials->count() }})</h3>
                @foreach($course->materials as $mat)
                @php
                    $ext = $mat->file_path ? strtolower(pathinfo($mat->file_path, PATHINFO_EXTENSION)) : '';
                    $icon = match(true) {
                        $ext === 'pdf' => '📄',
                        in_array($ext, ['doc','docx']) => '📝',
                        in_array($ext, ['xls','xlsx']) => '📊',
                        in_array($ext, ['ppt','pptx']) => '📽️',
                        in_array($ext, ['jpg','jpeg','png','gif','webp']) => '🖼️',
                        default => '📎'
                    };
                    $fileUrl = $mat->file_path ? Storage::url($mat->file_path) : null;
                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    $isPdf = $ext === 'pdf';
                    $isDoc = in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx']);
                @endphp
                <div class="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-base flex-shrink-0">{{ $icon }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $mat->title }}</p>
                        <p class="text-[11px] text-gray-400">{{ strtoupper($ext ?: $mat->type) }}{{ $mat->file_size ? ' · ' . number_format($mat->file_size / 1024, 0) . ' KB' : '' }}</p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        @if($fileUrl)
                            @if($isPdf || $isDoc)
                            <a href="{{ $isPdf ? $fileUrl : 'https://docs.google.com/gview?url=' . urlencode(url($fileUrl)) . '&embedded=true' }}" target="_blank" 
                               class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center text-blue-600 transition" title="Lihat File">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @endif
                            @if($isImage)
                            <button onclick="document.getElementById('modal-mat-{{ $mat->id }}').classList.remove('hidden')" 
                                    class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center text-blue-600 transition" title="Lihat Gambar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            @endif
                            <a href="{{ $fileUrl }}" download class="w-8 h-8 rounded-lg bg-green-50 hover:bg-green-100 flex items-center justify-center text-green-600 transition" title="Download">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        @endif
                        @if($mat->external_url)
                            <a href="{{ $mat->external_url }}" target="_blank" class="w-8 h-8 rounded-lg bg-purple-50 hover:bg-purple-100 flex items-center justify-center text-purple-600 transition" title="Buka Link">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
                <!-- Image Modal -->
                @if($isImage && $fileUrl)
                <div id="modal-mat-{{ $mat->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-auto">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h3 class="font-bold text-gray-800">{{ $mat->title }}</h3>
                            <button onclick="this.closest('[id^=modal-mat]').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">✕</button>
                        </div>
                        <div class="p-4">
                            <img src="{{ $fileUrl }}" alt="{{ $mat->title }}" class="w-full rounded-xl">
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @if($course->materials->isEmpty())<p class="text-sm text-gray-400 py-2">Belum ada materi</p>@endif
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">📝 Tugas ({{ $course->assignments->count() }})</h3>
                @foreach($course->assignments as $asg)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $asg->title }}</p>
                        <p class="text-xs text-gray-500">Deadline: {{ $asg->deadline->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <a href="{{ route('pkl-learning.grading', $asg) }}" class="px-3 py-1.5 text-xs bg-purple-50 text-purple-600 hover:bg-purple-100 rounded-lg font-medium">✅ Nilai</a>
                </div>
                @endforeach
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">❓ Kuis ({{ $course->quizzes->count() }})</h3>
                @foreach($course->quizzes as $quiz)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $quiz->title }}</p>
                        <p class="text-xs text-gray-500">{{ $quiz->questions->count() }} soal - {{ $quiz->responses->where('submitted_at', '!=', null)->count() }} selesai - Deadline: {{ $quiz->deadline->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <a href="{{ route('pkl-learning.quiz-grading', $quiz) }}" class="px-3 py-1.5 text-xs bg-pink-50 text-pink-600 hover:bg-pink-100 rounded-lg font-medium">✅ Nilai Kuis</a>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sticky top-4">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">👥 Progress Siswa ({{ $students->count() }})</h3>
                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    @foreach($students as $student)
                    @php $prog = $studentProgress[$student->id] ?? ['percentage' => 0]; @endphp
                    <div class="flex items-center gap-3 py-2">
                        <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $student->name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full transition-all" style="width: {{ $prog['percentage'] }}%"></div>
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
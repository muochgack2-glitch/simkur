<div>
    <!-- Course Header -->
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-6">
        <div class="bg-blue-600 px-5 sm:px-6 py-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-xs font-medium">{{ $course->subject->name ?? '' }} · {{ $course->teacher->name ?? '' }}</p>
                    <h1 class="text-xl font-bold text-white mt-1">{{ $course->title }}</h1>
                </div>
                <div class="text-right">
                    <div class="w-12 h-12 rounded-full {{ $progress['percentage'] >= 80 ? 'bg-green-500' : ($progress['percentage'] >= 50 ? 'bg-amber-500' : 'bg-white/20') }} flex items-center justify-center">
                        <span class="text-white text-sm font-bold">{{ $progress['percentage'] }}%</span>
                    </div>
                    <p class="text-blue-100 text-[10px] mt-1">{{ $progress['completed'] }}/{{ $progress['total'] }} selesai</p>
                </div>
            </div>
        </div>
        @if($course->description)
        <div class="px-5 sm:px-6 py-3 bg-blue-50 text-blue-800 text-sm border-b">{{ $course->description }}</div>
        @endif
    </div>

    <div class="space-y-6">
        <!-- Materials -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">📚 Materi Pembelajaran <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $course->materials->count() }}</span></h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($course->materials as $mat)
                <div class="px-5 py-4">
                    <div class="flex items-start gap-3">
                        @php
                            $ext = $mat->file_path ? strtolower(pathinfo($mat->file_path, PATHINFO_EXTENSION)) : '';
                            $icon = match(true) {
                                $ext === 'pdf' => '📄',
                                in_array($ext, ['doc','docx']) => '📝',
                                in_array($ext, ['xls','xlsx']) => '📊',
                                in_array($ext, ['ppt','pptx']) => '📽️',
                                in_array($ext, ['jpg','jpeg','png','gif','webp']) => '🖼️',
                                in_array($ext, ['mp4','avi','mov']) => '🎬',
                                default => '📎'
                            };
                            $color = match(true) {
                                $ext === 'pdf' => 'bg-red-100',
                                in_array($ext, ['doc','docx']) => 'bg-blue-100',
                                in_array($ext, ['xls','xlsx']) => 'bg-green-100',
                                in_array($ext, ['ppt','pptx']) => 'bg-orange-100',
                                default => 'bg-gray-100'
                            };
                        @endphp
                        <div class="w-10 h-10 rounded-xl {{ $color }} flex items-center justify-center text-lg flex-shrink-0">{{ $icon }}</div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800">{{ $mat->title }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ strtoupper($ext ?: 'Link') }}{{ $mat->file_size ? ' · ' . number_format($mat->file_size / 1024, 0) . ' KB' : '' }}</p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($mat->file_path)
                                    @php $fileUrl = Storage::url($mat->file_path); $isPdf = $ext === 'pdf'; $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']); $isDoc = in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx']); $fullUrl = url(Storage::url($mat->file_path)); @endphp
                                    @if($isPdf)
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">👁️ Preview</a>
                                    @elseif($isImage)
                                    <button onclick="document.getElementById('preview-{{ $mat->id }}').classList.toggle('hidden')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">👁️ Lihat</button>
                                    @elseif($isDoc)
                                    <a href="https://docs.google.com/gview?url={{ urlencode($fullUrl) }}&embedded=true" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">👁️ Preview</a>
                                    @endif
                                    <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs font-medium transition">⬇️ Unduh</a>
                                @endif
                                @if($mat->external_url)
                                    <a href="{{ $mat->external_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-600 hover:bg-purple-100 rounded-lg text-xs font-medium transition">🔗 Buka Link</a>
                                @endif
                                @if(!$mat->file_path && !$mat->external_url)
                                    <span class="text-xs text-gray-400 italic py-1.5">📭 File belum diupload guru</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($mat->file_path && in_array(strtolower(pathinfo($mat->file_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']))
                    <div id="preview-{{ $mat->id }}" class="hidden mt-3 ml-13" style="padding-left: 52px;">
                        <img src="{{ Storage::url($mat->file_path) }}" alt="{{ $mat->title }}" class="max-w-full rounded-xl border shadow-sm">
                    </div>
                    @endif
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <div class="text-3xl mb-2">📚</div>
                    <p class="text-sm text-gray-400">Belum ada materi</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Assignments -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">📝 Tugas <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">{{ $course->assignments->count() }}</span></h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($course->assignments as $asg)
                @php $status = $assignmentStatuses[$asg->id] ?? []; @endphp
                <div class="px-5 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $asg->title }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-500">📅 Deadline: {{ $asg->deadline->translatedFormat('d M Y H:i') }}</span>
                                @if($asg->isOverdue() && !($status['submitted'] ?? false))
                                <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[10px] font-bold">⏰ Lewat</span>
                                @endif
                            </div>
                            @if($status['graded'] ?? false)
                            <div class="mt-2 bg-green-50 rounded-xl px-4 py-2.5 inline-block">
                                <span class="text-sm text-green-700 font-bold">🏆 Nilai: {{ $status['score'] }}/{{ $asg->max_score }}</span>
                                @if($status['feedback'] ?? null)<p class="text-xs text-gray-600 mt-1">💬 {{ $status['feedback'] }}</p>@endif
                            </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            @if($status['submitted'] ?? false)
                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-green-100 text-green-700 rounded-xl text-xs font-semibold">✅ Dikumpulkan</span>
                            @elseif($course->isPeriodLocked())
                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold">🔒 Terkunci</span>
                            @else
                            <a href="{{ route('pkl-learning.student.submission', $asg) }}" class="inline-flex items-center gap-1 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-lg transition-all">
                                📝 Kerjakan
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <div class="text-3xl mb-2">📝</div>
                    <p class="text-sm text-gray-400">Belum ada tugas</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quizzes -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">❓ Kuis <span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded-full text-xs font-bold">{{ $course->quizzes->where('is_published', true)->count() }}</span></h2>
            </div>
            <div class="divide-y divide-gray-100">
                @php $hasQuiz = false; @endphp
                @foreach($course->quizzes as $quiz)
                @if(!$quiz->is_published) @continue @endif
                @php $hasQuiz = true; $qStatus = $quizStatuses[$quiz->id] ?? []; @endphp
                <div class="px-5 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $quiz->title }}</h3>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">📋 {{ $quiz->questions->count() }} soal</span>
                                <span class="text-xs text-gray-500">⏱️ {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' menit' : 'Tanpa batas' }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">📅 Deadline: {{ $quiz->deadline->translatedFormat('d M Y H:i') }}</p>
                            @if($qStatus['graded'] ?? false)
                            <div class="mt-2 bg-green-50 rounded-xl px-4 py-2.5 inline-block">
                                <span class="text-sm text-green-700 font-bold">🏆 Nilai: {{ $qStatus['score'] }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            @if($qStatus['submitted'] ?? false)
                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-green-100 text-green-700 rounded-xl text-xs font-semibold">✅ Selesai</span>
                            @elseif($course->isPeriodLocked())
                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold">🔒 Terkunci</span>
                            @else
                            <a href="{{ route('pkl-learning.student.quiz', $quiz) }}" class="inline-flex items-center gap-1 px-5 py-2.5 bg-pink-600 hover:bg-pink-700 text-white rounded-xl text-xs font-bold shadow-lg transition-all">
                                {{ ($qStatus['started'] ?? false) ? '▶️ Lanjutkan' : '🎯 Mulai' }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                @if(!$hasQuiz)
                <div class="px-5 py-10 text-center">
                    <div class="text-3xl mb-2">❓</div>
                    <p class="text-sm text-gray-400">Belum ada kuis</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
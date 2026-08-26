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
                <div x-data="{ showDetail: false }">
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
                            @if($status['submitted'] ?? false)
                            <button @click="showDetail = !showDetail"
                                style="margin-top:8px;font-size:11px;color:#6366f1;background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;gap:4px;"
                                x-text="showDetail ? '▲ Sembunyikan jawaban' : '▼ Lihat jawaban saya'">
                            </button>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            @if($status['revision_requested'] ?? false)
                            <a href="{{ route('pkl-learning.student.submission', $asg) }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl text-xs font-semibold" style="background:#fef3c7;color:#b45309;border:1px solid #fcd34d;">
                                ✏️ Kerjakan Ulang
                            </a>
                            @elseif($status['submitted'] ?? false)
                            <div class="flex flex-col items-end gap-1">
                                <a href="{{ route('pkl-learning.student.submission', $asg) }}"
                                   class="inline-flex items-center gap-1 px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-xl text-xs font-semibold transition-colors">
                                    ✅ Dikumpulkan
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                                @if($status['graded'] ?? false)
                                <span class="text-[10px] font-bold text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">🏆 Nilai: {{ $status['score'] }}/{{ $asg->max_score }}</span>
                                @else
                                <span class="text-[10px] text-gray-400">Lihat jawaban →</span>
                                @endif
                            </div>
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
                {{-- Expandable: Lihat Jawaban --}}
                @if($status['submitted'] ?? false)
                <div x-show="showDetail" x-cloak style="display:none;border-top:1px solid #e2e8f0;background:#f8fafc;">
                    <div style="padding:16px 20px;">
                        <p style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 12px;">Jawaban Kamu</p>

                        {{-- Teks jawaban --}}
                        @if($status['content'] ?? null)
                        <div style="background:white;border:1px solid #e2e8f0;border-radius:12px;padding:14px;margin-bottom:10px;">
                            <p style="font-size:13px;color:#334155;white-space:pre-wrap;line-height:1.7;margin:0;word-break:break-word;">{{ $status['content'] }}</p>
                        </div>
                        @endif

                        {{-- File --}}
                        @if($status['file_path'] ?? null)
                        @php
                            $fUrl = \Storage::url($status['file_path']);
                            $fExt = strtolower(pathinfo($status['file_path'], PATHINFO_EXTENSION));
                            $fImg = in_array($fExt, ['jpg','jpeg','png','gif','webp']);
                        @endphp
                        <div style="margin-bottom:10px;">
                            @if($fImg)
                            <div style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                                <img src="{{ $fUrl }}" style="width:100%;max-height:300px;object-fit:contain;background:#f1f5f9;" alt="File jawaban">
                                <div style="padding:8px 12px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                                    <span style="font-size:11px;color:#64748b;">{{ $status['file_name'] ?? basename($status['file_path']) }}</span>
                                    <a href="{{ $fUrl }}" target="_blank" download style="font-size:11px;color:#6366f1;font-weight:600;text-decoration:none;">⬇ Unduh</a>
                                </div>
                            </div>
                            @else
                            <div style="background:white;border:1px solid #e2e8f0;border-radius:12px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span style="font-size:22px;">📎</span>
                                    <div>
                                        <p style="font-size:12px;font-weight:600;color:#334155;margin:0;">{{ $status['file_name'] ?? basename($status['file_path']) }}</p>
                                        <p style="font-size:10px;color:#94a3b8;margin:0;text-transform:uppercase;">{{ $fExt }}</p>
                                    </div>
                                </div>
                                <a href="{{ $fUrl }}" target="_blank"
                                   style="font-size:11px;color:#6366f1;font-weight:600;text-decoration:none;padding:5px 10px;border:1px solid #6366f1;border-radius:8px;">🔗 Buka</a>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if(!($status['content'] ?? null) && !($status['file_path'] ?? null))
                        <p style="font-size:12px;color:#94a3b8;text-align:center;padding:10px 0;">Tidak ada teks atau file yang dikumpulkan</p>
                        @endif

                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                            <p style="font-size:10px;color:#94a3b8;margin:0;">⏰ Dikumpulkan: {{ $status['submitted_at'] ?? '-' }}</p>
                            <a href="{{ route('pkl-learning.student.submission', $asg) }}"
                               style="font-size:11px;color:#6366f1;font-weight:600;text-decoration:none;">Edit jawaban →</a>
                        </div>
                    </div>
                </div>
                @endif
                </div>{{-- end x-data --}}
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




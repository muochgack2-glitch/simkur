<div class="mb-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
    <div class="p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    @if($course->is_published)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 font-medium">Dipublikasi</span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 font-medium">Draft</span>
                    @endif
                    <span class="px-2 py-0.5 text-xs rounded-full bg-teal-100 text-teal-700 border border-teal-200 font-medium">📚 {{ $course->subject->name ?? '-' }}</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $course->description }}</p>
                <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500">
                    <span>📅 {{ $course->start_date->translatedFormat('d M') }} - {{ $course->deadline->translatedFormat('d M Y') }}</span>
                    <span>📄 {{ $course->materials->count() }} materi</span>
                    <span>📝 {{ $course->assignments->count() }} tugas</span>
                    <span>❓ {{ $course->quizzes->count() }} kuis</span>
                </div>

                <!-- Target Kelas & Siswa -->
                @if(!empty($course->target_classes))
                <div class="mt-3 flex flex-wrap gap-2">
                    @php
                        $totalStudents = 0;
                    @endphp
                    @foreach($course->target_classes as $classId)
                        @php $cls = $classMap[$classId] ?? null; @endphp
                        @if($cls)
                        @php $totalStudents += $cls->students_count; @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-lg text-xs font-medium text-blue-700">
                            🏫 {{ $cls->name }}
                            <span class="bg-blue-200 text-blue-800 px-1.5 py-0.5 rounded-md text-[10px] font-bold">{{ $cls->students_count }}</span>
                        </span>
                        @endif
                    @endforeach
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 border border-green-200 rounded-lg text-xs font-semibold text-green-700">
                        👥 {{ $totalStudents }} siswa
                    </span>
                </div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pkl-learning.show', $course) }}" class="px-3 py-2 text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition font-medium">
                    Detail
                </a>
                <a href="{{ route('pkl-learning.edit', $course) }}" class="px-3 py-2 text-sm bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition font-medium">✏ Edit</a>
                <form action="{{ route('pkl-learning.toggle-publish', $course) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-sm {{ $course->is_published ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} rounded-lg transition font-medium">
                        <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-rocket' }} mr-1"></i>
                        {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <form action="{{ route('pkl-learning.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus course ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 text-sm bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition font-medium">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

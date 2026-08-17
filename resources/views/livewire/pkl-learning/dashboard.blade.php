<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📚 Pembelajaran PKL</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola materi pembelajaran untuk siswa yang sedang PKL</p>
        </div>
        <a href="{{ route('pkl-learning.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl transition flex items-center space-x-2 shadow-lg">
            ➕ <span>Buat Materi Baru</span>
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-5 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-800 dark:text-green-300">
        <span class="text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Period Filter -->
    <div class="mb-4 flex items-center gap-3">
        <label class="text-sm font-medium text-gray-600">Filter Periode:</label>
        <select wire:model.live="filterPeriod" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Periode</option>
            <option value="null">Tanpa Periode</option>
            @foreach($pklPeriods as $period)
                <option value="{{ $period->id }}">{{ $period->title }}</option>
            @endforeach
        </select>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_courses'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Materi</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $stats['published'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dipublikasi</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['draft'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Draft</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_assignments'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tugas</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="text-2xl font-bold text-pink-600">{{ $stats['total_quizzes'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kuis</div>
        </div>
    </div>

    <!-- PKL Activity Info -->
    @if($pklActivity)
    <div class="mb-6 bg-indigo-50 dark:from-indigo-900/20 dark:to-purple-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center text-white text-xl">🏭</div>
            <div>
                <p class="font-semibold text-indigo-800 dark:text-indigo-300">{{ $pklActivity->name }}</p>
                <p class="text-xs text-indigo-600 dark:text-indigo-400">
                    {{ $pklActivity->start_date->translatedFormat('d M Y') }}  {{ $pklActivity->end_date->translatedFormat('d M Y') }}
                     {{ $pklActivity->getTargetGradesLabel() }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Course List -->
    @forelse($courses as $course)
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
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $course->pklPeriod ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-gray-100 text-gray-500 border-gray-200' }} font-medium border">📋 {{ $course->pklPeriod?->title ?? 'Tanpa Periode' }}</span>
                        <span class="text-xs text-gray-500">{{ $course->subject->name ?? '-' }}</span>
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
    @empty
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Belum ada materi</h3>
        <p class="text-sm text-gray-400 mt-1">Klik "Buat Materi Baru" untuk memulai</p>
    </div>
    @endforelse
</div>
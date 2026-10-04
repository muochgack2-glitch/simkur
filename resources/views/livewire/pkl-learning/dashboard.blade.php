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

    <!-- Course List grouped by Period -->
    @if($courses->isEmpty())
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Belum ada materi</h3>
        <p class="text-sm text-gray-400 mt-1">Klik "Buat Materi Baru" untuk memulai</p>
    </div>
    @else

    @foreach($periods as $period)
    @php $periodCourses = $groupedCourses->get($period->id, collect()); @endphp
    @if($periodCourses->isNotEmpty())
    <div class="mb-6" x-data="{ open: {{ $period->isCurrentPeriod() ? 'true' : 'false' }} }">
        {{-- Period Header --}}
        <div @click="open = !open" class="flex items-center gap-3 mb-3 cursor-pointer select-none group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 hover:border-blue-300 transition-all">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black shadow-sm
                {{ $period->isCurrentPeriod() ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white' : ($period->isPast() ? 'bg-gray-200 text-gray-500' : 'bg-blue-100 text-blue-600') }}">
                {{ $period->period_number }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors">{{ $period->title }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $period->isCurrentPeriod() ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-600') }}">
                        {{ $period->isCurrentPeriod() ? '🟢 Aktif' : ($period->isPast() ? 'Selesai' : '🔵 Mendatang') }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">{{ $periodCourses->count() }} materi</span>
                </div>
                <p class="text-xs text-gray-400">📅 {{ $period->getDateRangeLabel() }}</p>
            </div>
            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div x-show="open" x-collapse>
        @foreach($periodCourses as $course)
        @include('livewire.pkl-learning._course-card', ['course' => $course, 'classMap' => $classMap])
        @endforeach
        </div>
    </div>
    @endif
    @endforeach

    {{-- Courses without period --}}
    @php $noPeriodCourses = $groupedCourses->get('', collect())->merge($groupedCourses->get(null, collect())); @endphp
    @if($noPeriodCourses->isNotEmpty())
    <div class="mb-6" x-data="{ open: true }">
        <div @click="open = !open" class="flex items-center gap-3 mb-3 cursor-pointer select-none group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 hover:border-blue-300 transition-all">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black shadow-sm bg-gray-200 text-gray-500">—</div>
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors">Tanpa Periode</h2>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ $noPeriodCourses->count() }} materi</span>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-collapse>
        @foreach($noPeriodCourses as $course)
        @include('livewire.pkl-learning._course-card', ['course' => $course, 'classMap' => $classMap])
        @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
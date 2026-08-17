<div class="">

    @if($courses->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-6xl mb-4">📚</div>
        <h2 class="text-xl font-bold text-gray-700 dark:text-gray-300">Belum Ada Materi PKL</h2>
        <p class="text-gray-500 mt-2 text-sm">Materi pembelajaran akan muncul di sini setelah guru mempublikasikannya.</p>
    </div>
    @else

    {{-- GREETING CARD --}}
    <div class="mb-6 rounded-2xl bg-blue-600 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-200 text-sm font-medium">Halo, Selamat Datang! 👋</p>
                <h1 class="text-2xl font-bold mt-0.5">{{ $user->name }}</h1>
                <p class="text-blue-200 text-xs mt-1">{{ $user->schoolClass?->name ?? 'Kelas PKL' }} &bull; Tahun Ajaran Aktif</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-black">{{ $stats['total_progress'] }}%</div>
                <div class="text-blue-200 text-xs">Progress Keseluruhan</div>
                <div class="mt-2 h-1.5 w-24 bg-blue-500 rounded-full ml-auto">
                    <div class="h-full bg-white rounded-full transition-all" style="width: {{ $stats['total_progress'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

{{-- HOW TO USE GUIDE --}}
    <div class="mb-5 bg-white border border-blue-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 bg-blue-600 flex items-center justify-between cursor-pointer" onclick="document.getElementById('how-to-use').classList.toggle('hidden')">
            <div class="flex items-center gap-2">
                <span class="text-xl">🗺️</span>
                <span class="font-bold text-white text-sm">Cara Menggunakan Pembelajaran PKL</span>
                <span class="text-xs bg-blue-500 text-blue-100 px-2 py-0.5 rounded-full">Panduan</span>
            </div>
            <span class="text-white text-xs">Tap untuk lihat/tutup ▼</span>
        </div>
        <div id="how-to-use" class="hidden">
            <div class="p-5">
                <p class="text-sm text-gray-500 mb-4">Ikuti langkah berikut untuk menggunakan sistem pembelajaran PKL:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="flex items-start gap-3 bg-blue-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">1</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Pilih Mata Pelajaran</p>
                            <p class="text-xs text-gray-500 mt-0.5">Klik salah satu kartu mata pelajaran di bawah ini</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-green-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">2</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">📖 Baca Materi</p>
                            <p class="text-xs text-gray-500 mt-0.5">Download & pelajari materi yang diberikan guru</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-amber-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-sm flex-shrink-0">3</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">📝 Kerjakan Tugas</p>
                            <p class="text-xs text-gray-500 mt-0.5">Klik tombol "Kerjakan" lalu tulis jawaban & kumpulkan</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-purple-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">4</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">🧠 Ikuti Kuis</p>
                            <p class="text-xs text-gray-500 mt-0.5">Jawab kuis online dengan batas waktu yang ditentukan</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2">
                    <span class="text-lg flex-shrink-0">⚠️</span>
                    <div>
                        <p class="text-xs font-bold text-amber-700">Perhatikan Deadline!</p>
                        <p class="text-xs text-amber-600 mt-0.5">Tugas yang melewati batas waktu mungkin tidak bisa dikumpulkan. Pantau bagian "Tugas Segera Deadline" di atas.</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-gray-400 text-xs">Kartu dengan ikon 🔒 = periode belum aktif, tidak bisa dikerjakan dulu</span>
                </div>
            </div>
        </div>
    </div>



{{-- URGENT DEADLINES --}}
    @if($urgentAssignments->isNotEmpty())
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
        <h3 class="text-sm font-bold text-red-700 dark:text-red-400 flex items-center gap-2 mb-3">
            ⚠️ Tugas Segera Deadline (7 Hari ke Depan)
        </h3>
        <div class="space-y-2">
            @foreach($urgentAssignments as $urgent)
            <a href="{{ route('pkl-learning.student.course', $urgent['course_id']) }}" class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl px-4 py-2.5 hover:bg-red-50 transition border border-red-100">
                <div>
                    <span class="font-semibold text-gray-800 dark:text-white text-sm">{{ $urgent['title'] }}</span>
                    <span class="text-gray-400 text-xs ml-2">— {{ $urgent['course'] }}</span>
                </div>
                <span class="text-xs font-bold {{ ($urgent['days_left'] ?? 0) <= 2 ? 'text-red-600' : 'text-amber-600' }} whitespace-nowrap ml-3">
                    @php $dl = $urgent['days_left'] ?? 0; @endphp @if($dl == 0)Hari ini!@elseif($dl == 1)Besok!@else Sisa {{ $dl }} hari @endif
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

{{-- HOW TO USE GUIDE --}}
    <div class="mb-5 bg-white border border-blue-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 bg-blue-600 flex items-center justify-between cursor-pointer" onclick="document.getElementById('how-to-use').classList.toggle('hidden')">
            <div class="flex items-center gap-2">
                <span class="text-xl">🗺️</span>
                <span class="font-bold text-white text-sm">Cara Menggunakan Pembelajaran PKL</span>
                <span class="text-xs bg-blue-500 text-blue-100 px-2 py-0.5 rounded-full">Panduan</span>
            </div>
            <span class="text-white text-xs">Tap untuk lihat/tutup ▼</span>
        </div>
        <div id="how-to-use" class="hidden">
            <div class="p-5">
                <p class="text-sm text-gray-500 mb-4">Ikuti langkah berikut untuk menggunakan sistem pembelajaran PKL:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="flex items-start gap-3 bg-blue-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">1</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Pilih Mata Pelajaran</p>
                            <p class="text-xs text-gray-500 mt-0.5">Klik salah satu kartu mata pelajaran di bawah ini</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-green-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">2</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">📖 Baca Materi</p>
                            <p class="text-xs text-gray-500 mt-0.5">Download & pelajari materi yang diberikan guru</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-amber-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-sm flex-shrink-0">3</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">📝 Kerjakan Tugas</p>
                            <p class="text-xs text-gray-500 mt-0.5">Klik tombol "Kerjakan" lalu tulis jawaban & kumpulkan</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-purple-50 rounded-xl p-3">
                        <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">4</div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">🧠 Ikuti Kuis</p>
                            <p class="text-xs text-gray-500 mt-0.5">Jawab kuis online dengan batas waktu yang ditentukan</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2">
                    <span class="text-lg flex-shrink-0">⚠️</span>
                    <div>
                        <p class="text-xs font-bold text-amber-700">Perhatikan Deadline!</p>
                        <p class="text-xs text-amber-600 mt-0.5">Tugas yang melewati batas waktu mungkin tidak bisa dikumpulkan. Pantau bagian "Tugas Segera Deadline" di atas.</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-gray-400 text-xs">Kartu dengan ikon 🔒 = periode belum aktif, tidak bisa dikerjakan dulu</span>
                </div>
            </div>
        </div>
    </div>

{{-- STAT PILLS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center text-xl">📖</div>
            <div>
                <div class="text-xl font-black text-blue-600">{{ $stats['courses'] }}</div>
                <div class="text-xs text-gray-500">Mata Pelajaran</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/40 rounded-xl flex items-center justify-center text-xl">📝</div>
            <div>
                <div class="text-xl font-black text-amber-600">{{ $stats['asg_done'] }}<span class="text-sm font-normal text-gray-400">/{{ $stats['asg_total'] }}</span></div>
                <div class="text-xs text-gray-500">Tugas Selesai</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-xl flex items-center justify-center text-xl">🧠</div>
            <div>
                <div class="text-xl font-black text-purple-600">{{ $stats['quiz_done'] }}<span class="text-sm font-normal text-gray-400">/{{ $stats['quiz_total'] }}</span></div>
                <div class="text-xs text-gray-500">Kuis Selesai</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center text-xl">⭐</div>
            <div>
                <div class="text-xl font-black text-green-600">{{ $stats['avg_score'] ?? '-' }}</div>
                <div class="text-xs text-gray-500">Rata-rata Nilai</div>
            </div>
        </div>
    </div>

{{-- PERIOD SECTIONS --}}
    @foreach($periods as $period)
    @php $periodCourses = $groupedCourses->get($period->id, collect()); @endphp
    @if($periodCourses->isNotEmpty())
    <div class="mb-8">
        {{-- Period Header --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black shadow-sm
                {{ $period->is_active ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white' : ($period->isPast() ? 'bg-gray-200 text-gray-500' : 'bg-blue-100 text-blue-600') }}">
                {{ $period->period_number }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-bold text-gray-800 dark:text-white">{{ $period->title }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $period->is_active ? 'bg-green-100 text-green-700' : ($period->isPast() ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-600') }}">
                        {{ $period->is_active ? '🟢 Aktif' : ($period->isPast() ? 'Selesai' : '🔵 Mendatang') }}
                    </span>
                </div>
                <p class="text-xs text-gray-400">📅 {{ $period->getDateRangeLabel() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($periodCourses as $course)
            @php
                $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0];
                $pending = $pendingPerCourse[$course->id] ?? 0;
                $pct = $prog['percentage'];
            @endphp
            @if($period->is_active)
            <a href="{{ route('pkl-learning.student.course', $course) }}" class="group block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 hover:border-blue-300 transition-all duration-200 overflow-hidden">
                {{-- Color bar based on progress --}}
                <div class="h-1.5 {{ $pct >= 100 ? 'bg-green-400' : ($pct >= 50 ? 'bg-amber-400' : 'bg-blue-500') }}"></div>
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <span class="px-2 py-0.5 text-xs rounded-lg bg-blue-50 text-blue-700 font-semibold border border-blue-100">{{ $course->subject->name ?? '' }}</span>
                        <div class="flex items-center gap-1.5">
                            @if($pending > 0)
                            <span class="w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-black flex items-center justify-center">{{ $pending }}</span>
                            @endif
                            @if($pct >= 100)
                            <span class="text-lg">✅</span>
                            @endif
                        </div>
                    </div>
                    <h3 class="font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors text-sm leading-snug mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-400 mb-3">👨‍🏫 {{ $course->teacher->name ?? '' }}</p>
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                        <span class="bg-gray-50 px-2 py-0.5 rounded-lg">📄 {{ $course->materials->count() }} materi</span>
                        <span class="bg-gray-50 px-2 py-0.5 rounded-lg">📝 {{ $course->assignments->count() }} tugas</span>
                        <span class="bg-gray-50 px-2 py-0.5 rounded-lg">🧠 {{ $course->quizzes->count() }} kuis</span>
                    </div>
                    {{-- Progress --}}
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-400">Progress</span>
                            <span class="font-bold {{ $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-amber-600' : 'text-blue-600') }}">{{ $pct }}%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pct >= 80 ? 'bg-green-400' : ($pct >= 50 ? 'bg-amber-400' : 'bg-blue-500') }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $prog['completed'] }}/{{ $prog['total'] }} selesai</p>
                    </div>
                </div>
            </a>
            @else
            {{-- LOCKED CARD --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-600 overflow-hidden opacity-60 cursor-not-allowed">
                <div class="h-1.5 bg-gray-200"></div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 text-xs rounded-lg bg-gray-100 text-gray-400 font-semibold">{{ $course->subject->name ?? '-' }}</span>
                        <span class="text-lg">🔒</span>
                    </div>
                    <h3 class="font-bold text-gray-400 text-sm mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-300">👨‍🏫 {{ $course->teacher->name ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-3 italic">Periode belum aktif</p>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    {{-- COURSES WITHOUT PERIOD --}}
    @php $noPeriodCourses = $groupedCourses->get('', collect())->merge($groupedCourses->get(null, collect())); @endphp
    @if($noPeriodCourses->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-base font-bold text-gray-600 dark:text-gray-400 mb-4 flex items-center gap-2">
            📋 Materi Lainnya
            <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">{{ $noPeriodCourses->count() }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($noPeriodCourses as $course)
            @php
                $prog = $progress[$course->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0];
                $pending = $pendingPerCourse[$course->id] ?? 0;
                $pct = $prog['percentage'];
            @endphp
            <a href="{{ route('pkl-learning.student.course', $course) }}" class="group block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-blue-300 transition-all duration-200 overflow-hidden">
                <div class="h-1.5 {{ $pct >= 100 ? 'bg-green-400' : 'bg-blue-500' }}"></div>
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <span class="px-2 py-0.5 text-xs rounded-lg bg-blue-50 text-blue-700 font-semibold border border-blue-100">{{ $course->subject->name ?? '' }}</span>
                        @if($pending > 0)
                        <span class="w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-black flex items-center justify-center">{{ $pending }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors text-sm mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-400 mb-3">👨‍🏫 {{ $course->teacher->name ?? '' }}</p>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-400">Progress</span>
                            <span class="font-bold text-blue-600">{{ $pct }}%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
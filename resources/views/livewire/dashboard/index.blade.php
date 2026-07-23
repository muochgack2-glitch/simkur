<div>
    <!-- Welcome Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin 👋</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Statistics Cards - Row 1: Kalender Akademik -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">📅 Kalender Akademik</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Tahun Pelajaran</p>
                        <p class="text-2xl font-bold mt-1">
                            @if($activeYear) {{ $activeYear->year }} @else - @endif
                        </p>
                    </div>
                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Kegiatan</p>
                        <p class="text-2xl font-bold mt-1">{{ $totalActivities }}</p>
                    </div>
                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Hari Efektif</p>
                        <p class="text-2xl font-bold mt-1">{{ $effectiveDays['study_days'] ?? 0 }}</p>
                        <p class="text-xs opacity-75">{{ $effectiveDays['effective_weeks'] ?? 0 }} minggu</p>
                    </div>
                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Pengguna</p>
                        <p class="text-2xl font-bold mt-1">{{ $totalUsers }}</p>
                    </div>
                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - Row 2: Jurnal Mengajar -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">📓 Jurnal Mengajar</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Total Jurnal</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalJournals }}</p>
                <p class="text-xs text-gray-500 mt-1">Bulan ini: {{ $journalsThisMonth }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
                <p class="text-sm text-gray-600">Guru Belum Isi</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $teachersNotFillingJournal }}</p>
                <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Rata-rata Kehadiran</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $averageAttendance }}%</p>
                <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-orange-500">
                <p class="text-sm text-gray-600">Mata Pelajaran Aktif</p>
                <p class="text-3xl font-bold text-orange-600 mt-1">{{ $totalSubjectsTaught }}</p>
                <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
            </div>
        </div>
    </div>

    <!-- Top 3 Teachers -->
    @if($topTeachers->count() > 0)
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏆 Top 3 Guru Ter-rajin (Bulan Ini)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($topTeachers as $index => $teacherData)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold">
                                #{{ $index + 1 }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $teacherData->teacher->name }}</p>
                            <p class="text-sm text-gray-600">{{ $teacherData->journal_count }} jurnal</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Activity Chart -->
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Kegiatan per Bulan</h3>
            <canvas id="activityChart" height="200"></canvas>
        </div>

        <!-- Journal Chart -->
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📓 Jurnal per Bulan (6 Bulan Terakhir)</h3>
            <canvas id="journalChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Activities -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">📅 Kegiatan Mendatang</h3>
            </div>
            <div class="p-5">
                @forelse($upcomingActivities as $activity)
                    <div class="flex items-start gap-3 mb-4 pb-4 border-b last:border-0">
                        <div class="flex-shrink-0 w-12 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $activity->start_date->format('d') }}</div>
                            <div class="text-xs text-gray-500">{{ $activity->start_date->format('M') }}</div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $activity->name }}</p>
                            <p class="text-sm text-gray-600">{{ $activity->activityType->name }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Tidak ada kegiatan mendatang</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Journals -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">📓 Jurnal Terbaru</h3>
            </div>
            <div class="p-5">
                @forelse($recentJournals as $journal)
                    <div class="flex items-start gap-3 mb-4 pb-4 border-b last:border-0">
                        <div class="flex-shrink-0 w-12 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $journal->date->format('d') }}</div>
                            <div class="text-xs text-gray-500">{{ $journal->date->format('M') }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $journal->teacher->name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ $journal->schoolClass->name }} - {{ $journal->subject->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($journal->topic, 50) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada jurnal</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels'] ?? []),
            datasets: [{
                label: 'Kegiatan',
                data: @json($chartData['data'] ?? []),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Journal Chart
    const journalCtx = document.getElementById('journalChart').getContext('2d');
    new Chart(journalCtx, {
        type: 'line',
        data: {
            labels: @json($journalChartData['labels'] ?? []),
            datasets: [{
                label: 'Jurnal',
                data: @json($journalChartData['data'] ?? []),
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush

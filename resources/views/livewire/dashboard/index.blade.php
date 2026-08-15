<div>
    <!-- Welcome Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin 👋</h1>
        <p class="text-gray-800 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Statistics Cards - Row 1: Kalender Akademik -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">📅 Kalender Akademik</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-600 rounded-lg shadow p-5 text-white">
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

            <div class="bg-green-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Kegiatan</p>
                        <p class="text-2xl font-bold mt-1">{{ $totalActivities }}</p>
                    </div>
                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>

            <div class="bg-purple-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Hari Efektif</p>
                        <p class="text-2xl font-bold mt-1">{{ $effectiveDays['study_days'] ?? 0 }}</p>
                        <p class="text-xs opacity-75">{{ $effectiveDays['effective_weeks'] ?? 0 }} minggu</p>
                    </div>
                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-indigo-600 rounded-lg shadow p-5 text-white">
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
                <p class="text-sm text-gray-800">Total Jurnal</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalJournals }}</p>
                <p class="text-xs text-gray-700 mt-1">Bulan ini: {{ $journalsThisMonth }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
                <p class="text-sm text-gray-800">Guru Belum Isi</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $teachersNotFillingJournal }}</p>
                <p class="text-xs text-gray-700 mt-1">Bulan ini</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <p class="text-sm text-gray-800">Rata-rata Kehadiran</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $averageAttendance }}%</p>
                <p class="text-xs text-gray-700 mt-1">Bulan ini</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-orange-500">
                <p class="text-sm text-gray-800">Mata Pelajaran Aktif</p>
                <p class="text-3xl font-bold text-orange-600 mt-1">{{ $totalSubjectsTaught }}</p>
                <p class="text-xs text-gray-700 mt-1">Bulan ini</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - Row 3: Perangkat Ajar -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">📚 Perangkat Ajar</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-indigo-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Materials</p>
                        <p class="text-3xl font-bold mt-1">{{ $totalMaterials }}</p>
                    </div>
                    <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <p class="text-sm text-gray-800">✅ Approved</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $materialsApproved }}</p>
                <p class="text-xs text-gray-700 mt-1">Siap digunakan</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-800">⏳ Pending</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $materialsPending }}</p>
                <p class="text-xs text-gray-700 mt-1">Menunggu approval</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-gray-500">
                <p class="text-sm text-gray-800">📝 Draft</p>
                <p class="text-3xl font-bold text-gray-600 mt-1">{{ $materialsDraft }}</p>
                <p class="text-xs text-gray-700 mt-1">Belum disubmit</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
                <p class="text-sm text-gray-800">❌ Rejected</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $materialsRejected }}</p>
                <p class="text-xs text-gray-700 mt-1">Perlu revisi</p>
            </div>
        </div>

        <!-- Material Usage Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800">📥 Total Downloads</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($totalDownloads) }}</p>
                    </div>
                    <svg class="w-12 h-12 opacity-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800">👁️ Total Views</p>
                        <p class="text-3xl font-bold text-purple-600 mt-1">{{ number_format($totalViews) }}</p>
                    </div>
                    <svg class="w-12 h-12 opacity-20 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Contributors & Category Coverage -->
    @if($topContributors->count() > 0 || count($categoryCoverage) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Contributors -->
        @if($topContributors->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">🏆 Top 5 Kontributor Perangkat Ajar</h3>
                <p class="text-sm text-gray-800">Guru paling produktif (approved materials)</p>
            </div>
            <div class="p-5">
                @foreach($topContributors as $index => $contributor)
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b last:border-0">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center text-white font-bold">
                                #{{ $index + 1 }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $contributor->creator->name }}</p>
                            <p class="text-sm text-gray-800">{{ $contributor->material_count }} perangkat ajar approved</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Category Coverage -->
        @if(count($categoryCoverage) > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">📂 Top 10 Kategori (Approved)</h3>
                <p class="text-sm text-gray-800">Distribusi perangkat ajar per kategori</p>
            </div>
            <div class="p-5">
                @foreach($categoryCoverage as $cat)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $cat['label'] }}</p>
                            <span class="text-sm font-bold text-blue-600">{{ $cat['count'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(($cat['count'] / max(array_column($categoryCoverage, 'count'))) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
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

        <!-- Material Chart -->
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📚 Perangkat Ajar Approved (6 Bulan)</h3>
            <canvas id="materialChart" height="200"></canvas>
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
                            <div class="text-xs text-gray-700">{{ $activity->start_date->format('M') }}</div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $activity->name }}</p>
                            <p class="text-sm text-gray-800">{{ $activity->activityType->name }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-700 text-center py-4">Tidak ada kegiatan mendatang</p>
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
                            <div class="text-xs text-gray-700">{{ $journal->date->format('M') }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $journal->teacher->name }}</p>
                            <p class="text-sm text-gray-800 truncate">{{ $journal->schoolClass->name }} - {{ $journal->subject->name }}</p>
                            <p class="text-xs text-gray-700 mt-1">{{ Str::limit($journal->topic, 50) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-700 text-center py-4">Belum ada jurnal</p>
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

    // Material Chart
    const materialCtx = document.getElementById('materialChart').getContext('2d');
    new Chart(materialCtx, {
        type: 'line',
        data: {
            labels: @json($materialChartData['labels'] ?? []),
            datasets: [{
                label: 'Materials Approved',
                data: @json($materialChartData['data'] ?? []),
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderColor: 'rgb(99, 102, 241)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(99, 102, 241)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush



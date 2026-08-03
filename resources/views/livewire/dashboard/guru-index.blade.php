<div>
    <!-- Welcome Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Guru 👨‍🏫</h1>
        <p class="text-gray-800 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Alert - Need Journal Today -->
    @if($todayScheduleCount > 0)
        @if($todayJournalCount == 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-medium text-yellow-800">⏰ Reminder: Anda ada {{ $todayScheduleCount }} jam mengajar hari ini tapi belum isi jurnal!</p>
                    @if(count($todayScheduleDetails) > 0)
                    <p class="text-sm text-yellow-700 mt-2">Jam mengajar hari ini:</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($todayScheduleDetails as $detail)
                        <span 
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 cursor-help"
                            title="{{ $detail['detailed'] ?? '' }}"
                        >
                            {{ $detail['compact'] ?? '-' }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                    <p class="text-sm text-yellow-700 mt-2">Segera isi jurnal mengajar untuk dokumentasi pembelajaran hari ini.</p>
                    <a href="{{ route('teaching-journal.create') }}" class="text-sm text-yellow-800 font-semibold underline mt-2 inline-block">Isi Jurnal Sekarang →</a>
                </div>
            </div>
        </div>
        @elseif($todayJournalCount < $todayScheduleCount)
        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-orange-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium text-orange-800">📝 Reminder: Anda ada {{ $todayScheduleCount }} jam mengajar hari ini, sudah isi {{ $todayJournalCount }} jurnal</p>
                    <p class="text-sm text-orange-700 mt-1">Masih ada jam mengajar yang belum diisi jurnalnya. Pastikan semua jurnal terisi lengkap.</p>
                    <a href="{{ route('teaching-journal.create') }}" class="text-sm text-orange-800 font-semibold underline mt-2 inline-block">Isi Jurnal Lagi →</a>
                </div>
            </div>
        </div>
        @else
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium text-green-800">✅ Jurnal hari ini sudah lengkap!</p>
                    <p class="text-sm text-green-700 mt-1">Anda telah mengisi {{ $todayJournalCount }} jurnal untuk {{ $todayScheduleCount }} jam mengajar hari ini. Terima kasih atas kedisiplinan Anda! 🎉</p>
                </div>
            </div>
        </div>
        @endif
    @elseif($todayJournalCount > 0)
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-medium text-green-800">✅ Jurnal hari ini sudah terisi!</p>
                <p class="text-sm text-green-700 mt-1">Anda telah mengisi {{ $todayJournalCount }} jurnal hari ini. Terima kasih atas kedisiplinan Anda.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Missing Journal Days Alert -->
    @if($missingJournalDaysWeek > 0 || $missingJournalDaysMonth > 0)
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium text-red-800">⚠️ Ada hari-hari yang belum terisi jurnalnya</p>
                <div class="mt-2 space-y-1">
                    @if($missingJournalDaysWeek > 0)
                    <p class="text-sm text-red-700">
                        📅 <strong>1 Minggu terakhir:</strong> {{ $missingJournalDaysWeek }} hari belum isi jurnal 
                        <span class="text-xs">(dari {{ count($weekScheduleDays) }} hari ada jadwal)</span>
                    </p>
                    @endif
                    @if($missingJournalDaysMonth > 0)
                    <p class="text-sm text-red-700">
                        📅 <strong>1 Bulan terakhir:</strong> {{ $missingJournalDaysMonth }} hari belum isi jurnal 
                        <span class="text-xs">(dari {{ count($monthScheduleDays) }} hari ada jadwal)</span>
                    </p>
                    @endif
                </div>
                <p class="text-sm text-red-700 mt-2">Lengkapi jurnal Anda untuk dokumentasi pembelajaran yang lebih baik.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Jurnal Bulan Ini</p>
                    <p class="text-3xl font-bold mt-1">{{ $myJournalsThisMonth }}</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Jurnal</p>
                    <p class="text-3xl font-bold mt-1">{{ $myTotalJournals }}</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Kelas Diampu</p>
                    <p class="text-3xl font-bold mt-1">{{ $myClassesCount }}</p>
                    <p class="text-xs opacity-75 mt-1">{{ $mySubjectsCount }} mapel</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Rata-rata Kehadiran</p>
                    <p class="text-3xl font-bold mt-1">{{ $averageAttendanceMyClasses }}%</p>
                    <p class="text-xs opacity-75 mt-1">Bulan ini</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Belum Isi (1 Bulan)</p>
                    <p class="text-3xl font-bold mt-1">{{ $missingJournalDaysMonth }}</p>
                    <p class="text-xs opacity-75 mt-1">{{ $missingJournalDaysWeek }} hari (1 minggu)</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- My Perangkat Ajar Stats -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">📚 Perangkat Ajar Saya</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Materials</p>
                        <p class="text-3xl font-bold mt-1">{{ $myMaterialsTotal }}</p>
                    </div>
                    <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <p class="text-sm text-gray-800">✅ Approved</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $myMaterialsApproved }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-800">⏳ Pending</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $myMaterialsPending }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-gray-500">
                <p class="text-sm text-gray-800">📝 Draft</p>
                <p class="text-3xl font-bold text-gray-600 mt-1">{{ $myMaterialsDraft }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
                <p class="text-sm text-gray-800">❌ Rejected</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $myMaterialsRejected }}</p>
            </div>
        </div>

        <!-- My Material Usage & Most Downloaded -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <p class="text-sm text-gray-800">📥 Total Downloads</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($myTotalDownloads) }}</p>
                <p class="text-xs text-gray-700 mt-1">Dari semua materials saya</p>
            </div>

            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
                <p class="text-sm text-gray-800">👁️ Total Views</p>
                <p class="text-3xl font-bold text-purple-600 mt-1">{{ number_format($myTotalViews) }}</p>
                <p class="text-xs text-gray-700 mt-1">Dari semua materials saya</p>
            </div>

            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg shadow p-5 border border-yellow-200">
                <p class="text-sm text-yellow-700 font-semibold mb-1">⭐ Most Downloaded</p>
                @if($myMostDownloaded)
                    <p class="font-bold text-gray-800 text-sm truncate">{{ $myMostDownloaded->title }}</p>
                    <p class="text-xs text-gray-700 mt-1">{{ $myMostDownloaded->download_count }} downloads</p>
                @else
                    <p class="text-sm text-gray-700">Belum ada</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Breakdown -->
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Breakdown Kehadiran Siswa (Bulan Ini)</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-3xl font-bold text-green-600">{{ $attendanceBreakdown['hadir'] }}</p>
                <p class="text-sm text-gray-800 mt-1">✓ Hadir</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-3xl font-bold text-yellow-600">{{ $attendanceBreakdown['sakit'] }}</p>
                <p class="text-sm text-gray-800 mt-1">⚠ Sakit</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-3xl font-bold text-blue-600">{{ $attendanceBreakdown['izin'] }}</p>
                <p class="text-sm text-gray-800 mt-1">ⓘ Izin</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-3xl font-bold text-red-600">{{ $attendanceBreakdown['alpha'] }}</p>
                <p class="text-sm text-gray-800 mt-1">✗ Alpha</p>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Journals -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Journal Chart -->
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Jurnal Saya (6 Bulan Terakhir)</h3>
            <canvas id="journalChart" height="250"></canvas>
        </div>

        <!-- My Material Chart -->
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📚 Upload Perangkat Ajar (6 Bulan)</h3>
            <canvas id="myMaterialChart" height="250"></canvas>
        </div>

        <!-- My Category Coverage -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">📂 Kategori Saya (Top 5)</h3>
                <p class="text-sm text-gray-800">Approved materials</p>
            </div>
            <div class="p-5">
                @if(count($myCategoryCoverage) > 0)
                    @foreach($myCategoryCoverage as $cat)
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-medium text-gray-800 truncate" title="{{ $cat['label'] }}">{{ Str::limit($cat['label'], 25) }}</p>
                                <span class="text-sm font-bold text-indigo-600">{{ $cat['count'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ min(($cat['count'] / max(array_column($myCategoryCoverage, 'count'))) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-gray-700 py-4 text-sm">Belum ada material approved</p>
                @endif
            </div>
        </div>
    </div>

    <!-- My Classes & Recent Journals -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- My Classes -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">🏫 Kelas yang Saya Ampu</h3>
            </div>
            <div class="p-5">
                @forelse($myClasses as $class)
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg mb-3">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $class->name }}</p>
                            <p class="text-sm text-gray-800">{{ $class->grade }} {{ $class->getMajorLabel() }}</p>
                        </div>
                        <span class="text-sm text-gray-700">{{ $class->students->count() }} siswa</span>
                    </div>
                @empty
                    <p class="text-gray-700 text-center py-4">Belum ada kelas</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Journals moved here -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">📓 Jurnal Terbaru Saya</h3>
                <a href="{{ route('teaching-journal.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Lihat Semua →</a>
            </div>
            <div class="p-5">
                @forelse($recentJournals as $journal)
                    <div class="flex items-start gap-3 mb-4 pb-4 border-b last:border-0">
                        <div class="flex-shrink-0 w-12 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $journal->date->format('d') }}</div>
                            <div class="text-xs text-gray-700">{{ $journal->date->format('M') }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800">{{ $journal->schoolClass->name }} - {{ $journal->subject->name }}</p>
                            <p class="text-sm text-gray-800 mt-1 truncate">{{ $journal->topic }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs">
                                <span class="text-green-600">✓ {{ $journal->present_count }}</span>
                                <span class="text-yellow-600">⚠ {{ $journal->sick_count }}</span>
                                <span class="text-blue-600">ⓘ {{ $journal->permission_count }}</span>
                                <span class="text-red-600">✗ {{ $journal->absent_count }}</span>
                            </div>
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
    // Journal Chart
    const ctx = document.getElementById('journalChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($journalChartData['labels'] ?? []),
            datasets: [{
                label: 'Jurnal',
                data: @json($journalChartData['data'] ?? []),
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

    // My Material Chart
    const myMaterialCtx = document.getElementById('myMaterialChart').getContext('2d');
    new Chart(myMaterialCtx, {
        type: 'line',
        data: {
            labels: @json($myMaterialChartData['labels'] ?? []),
            datasets: [{
                label: 'Upload Saya',
                data: @json($myMaterialChartData['data'] ?? []),
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderColor: 'rgb(99, 102, 241)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(99, 102, 241)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
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



<div>
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Kepala Sekolah 👨‍💼</h1>
        <p class="text-gray-800 mt-2">Selamat datang, {{ auth()->user()->name }}! Monitor dan kelola aktivitas sekolah dengan mudah.</p>
    </div>

    <!-- Statistics Cards (6 cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Kegiatan Tahun Ini -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Total Kegiatan</p>
            <p class="text-3xl font-bold">{{ $totalActivitiesThisYear }}</p>
            <p class="text-xs opacity-75 mt-2">Tahun pelajaran {{ $activeYear ? $activeYear->year : '-' }}</p>
        </div>

        <!-- Kegiatan Bulan Ini -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Kegiatan Bulan Ini</p>
            <p class="text-3xl font-bold">{{ $activitiesThisMonth }}</p>
            <p class="text-xs opacity-75 mt-2">{{ now()->format('F Y') }}</p>
        </div>

        <!-- Kegiatan Ujian -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Kegiatan Ujian</p>
            <p class="text-3xl font-bold">{{ $totalExams }}</p>
            <p class="text-xs opacity-75 mt-2">PTS, PAS, PAT, ANBK</p>
        </div>

        <!-- Kegiatan Libur -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Hari Libur</p>
            <p class="text-3xl font-bold">{{ $totalHolidays }}</p>
            <p class="text-xs opacity-75 mt-2">Libur nasional & semester</p>
        </div>

        <!-- Hari Efektif Progress -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Hari Efektif</p>
            @if($effectiveDays)
                <p class="text-3xl font-bold">{{ $effectiveDays->study_days }} Hari</p>
                <p class="text-xs opacity-75 mt-2">{{ number_format($effectiveDays->percentage, 1) }}% dari target</p>
            @else
                <p class="text-3xl font-bold">-</p>
                <p class="text-xs opacity-75 mt-2">Belum dihitung</p>
            @endif
        </div>

        <!-- Total Pengguna Aktif -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Pengguna Aktif</p>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
            <p class="text-xs opacity-75 mt-2">{{ $totalActivityTypes }} jenis kegiatan</p>
        </div>
    </div>

    <!-- Charts & Timeline Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Bar Chart: Activities per Month -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">📊 Kegiatan per Bulan</h2>
                <p class="text-sm text-gray-800">Grafik kegiatan 12 bulan terakhir</p>
            </div>
            <div class="p-6">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>

        <!-- Pie Chart: Activities by Category -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">📈 Kategori Kegiatan</h2>
                <p class="text-sm text-gray-800">Distribusi berdasarkan kategori</p>
            </div>
            <div class="p-6">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-semibold mb-1">Bulan Ini</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $insights['current_month_activities'] }} Kegiatan</p>
                </div>
                <div class="text-blue-600">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-semibold mb-1">Progress Hari Efektif</p>
                    <p class="text-2xl font-bold text-green-900">{{ $insights['effective_days_progress'] }}%</p>
                </div>
                <div class="text-green-600">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-semibold mb-1">User Paling Aktif</p>
                    @if($insights['most_active_user'])
                        <p class="text-lg font-bold text-purple-900">{{ $insights['most_active_user']->user->name ?? 'N/A' }}</p>
                        <p class="text-xs text-purple-600">{{ $insights['most_active_user']->total }} aktivitas</p>
                    @else
                        <p class="text-lg font-bold text-purple-900">-</p>
                    @endif
                </div>
                <div class="text-purple-600">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">⏱️ Aktivitas Terbaru</h2>
            <p class="text-sm text-gray-800">10 aktivitas terakhir dalam sistem</p>
        </div>
        <div class="p-6">
            @if($activityLogs->count() > 0)
                <div class="space-y-4">
                    @foreach($activityLogs as $log)
                        <div class="flex items-start space-x-4 p-4 bg-white rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-shrink-0">
                                @if($log->action === 'create')
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @elseif($log->action === 'update')
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                    </div>
                                @elseif($log->action === 'delete')
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $log->user->name ?? 'System' }}
                                    <span class="text-gray-800">{{ $log->description }}</span>
                                </p>
                                <p class="text-xs text-gray-700 mt-1">
                                    {{ $log->created_at->diffForHumans() }} • {{ $log->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($log->action === 'create')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Buat</span>
                                @elseif($log->action === 'update')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Edit</span>
                                @elseif($log->action === 'delete')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Hapus</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-700">Belum ada aktivitas</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Bar Chart
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: @js($monthLabels),
                        datasets: [{
                            label: 'Jumlah Kegiatan',
                            data: @js($monthlyData),
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }

            // Category Pie Chart
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Ujian', 'Libur', 'Reguler'],
                        datasets: [{
                            data: [@js($categoryData['ujian']), @js($categoryData['libur']), @js($categoryData['reguler'])],
                            backgroundColor: [
                                'rgba(147, 51, 234, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(59, 130, 246, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }
        });
    </script>
</div>

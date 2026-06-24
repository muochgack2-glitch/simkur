<div>
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-gray-600 mt-2">Kelola kalender pendidikan sekolah dengan mudah dan efisien</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Active Academic Year -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3" style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 0.5rem;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Tahun Pelajaran Aktif</p>
            <p class="text-2xl font-bold">
                @if($activeYear)
                    {{ $activeYear->year }}
                @else
                    <span class="text-base">Belum Ada</span>
                @endif
            </p>
            <p class="text-xs opacity-75 mt-2">2 Semester</p>
        </div>

        <!-- Total Activities -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3" style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 0.5rem;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Total Kegiatan</p>
            <p class="text-2xl font-bold">{{ $totalActivities }}</p>
            <p class="text-xs opacity-75 mt-2">Tahun ini</p>
        </div>

        <!-- Effective Days -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3" style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 0.5rem;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Hari Efektif</p>
            @if($effectiveDays)
                <p class="text-2xl font-bold">{{ $effectiveDays->study_days }} Hari</p>
                <p class="text-xs opacity-75 mt-2">{{ number_format($effectiveDays->effective_weeks, 1) }} Minggu</p>
            @else
                <p class="text-2xl font-bold">-</p>
                <p class="text-xs opacity-75 mt-2">Belum dihitung</p>
            @endif
        </div>

        <!-- Total Users -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-3" style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 0.5rem;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm opacity-90 mb-2">Pengguna Aktif</p>
            <p class="text-2xl font-bold">{{ $totalUsers }}</p>
            <p class="text-xs opacity-75 mt-2">{{ $totalActivityTypes }} Jenis Kegiatan</p>
        </div>
    </div>

    <!-- Chart & Upcoming Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart: Activities per Month -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Grafik Kegiatan per Bulan</h2>
                <p class="text-sm text-gray-600">Distribusi kegiatan sepanjang tahun pelajaran</p>
            </div>
            <div class="p-6">
                @if(!empty($chartData['labels']))
                    <canvas id="activityChart" height="100"></canvas>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm">Data grafik tidak tersedia</p>
                        <p class="text-gray-400 text-xs mt-1">Pastikan tahun pelajaran aktif sudah diatur</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Agenda Terdekat</h2>
                <p class="text-sm text-gray-600">7 hari ke depan</p>
            </div>
            <div class="p-4">
                @if($upcomingActivities->count() > 0)
                    <div class="space-y-3 max-h-[350px] overflow-y-auto">
                        @foreach($upcomingActivities as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $activity->color }}20;">
                                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $activity->color }};"></div>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $activity->name }}</h3>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $activity->color }}20; color: {{ $activity->color }};">
                                            {{ $activity->activityType->name }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $activity->start_date->format('d M') }}
                                        @if(!$activity->start_date->isSameDay($activity->end_date))
                                            - {{ $activity->end_date->format('d M') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm">Tidak ada agenda</p>
                        <p class="text-gray-400 text-xs mt-1">dalam 7 hari ke depan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions (Only for Admin & Waka) -->
    @if(auth()->user()->canManageActivities())
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Aksi Cepat
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('activities.index') }}" class="bg-white/30 hover:bg-white/40 rounded-lg p-4 transition group">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <h3 class="font-semibold">Tambah Kegiatan</h3>
                    <p class="text-sm opacity-90 mt-1">Buat kegiatan baru</p>
                </a>
                <a href="{{ route('activities.index') }}" class="bg-white/30 hover:bg-white/40 rounded-lg p-4 transition group">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="font-semibold">Lihat Kalender</h3>
                    <p class="text-sm opacity-90 mt-1">Buka kalender lengkap</p>
                </a>
                <a href="{{ route('calendar.official') }}" target="_blank" class="bg-white/30 hover:bg-white/40 rounded-lg p-4 transition group">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="font-semibold">Kalender Resmi</h3>
                    <p class="text-sm opacity-90 mt-1">Lihat kalender publik</p>
                </a>
                <a href="{{ route('activities.export') }}" class="bg-white/30 hover:bg-white/40 rounded-lg p-4 transition group">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="font-semibold">Export PDF</h3>
                    <p class="text-sm opacity-90 mt-1">Download kalender</p>
                </a>
            </div>
        </div>
    @endif

    <!-- Chart.js Script -->
    @if(!empty($chartData['labels']))
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('activityChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @js($chartData['labels']),
                            datasets: [{
                                label: 'Jumlah Kegiatan',
                                data: @js($chartData['data']),
                                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                hoverBackgroundColor: 'rgba(59, 130, 246, 0.8)',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: {
                                        size: 14
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</div>

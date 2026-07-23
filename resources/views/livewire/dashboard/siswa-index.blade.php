<div>
    <!-- Welcome Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Siswa 👨‍🎓</h1>
        <p class="text-gray-800 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
        @if(auth()->user()->schoolClass)
            <p class="text-sm text-gray-700">Kelas: <span class="font-semibold">{{ auth()->user()->schoolClass->name }}</span></p>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Kehadiran Bulan Ini</p>
                    <p class="text-3xl font-bold mt-1">{{ $attendancePercentage }}%</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Mapel Dipelajari</p>
                    <p class="text-3xl font-bold mt-1">{{ $subjectsLearnedThisMonth }}</p>
                    <p class="text-xs opacity-75 mt-1">Bulan ini</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Asesmen Tersedia</p>
                    <p class="text-3xl font-bold mt-1">{{ $availableAssessments }}</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Asesmen Selesai</p>
                    <p class="text-3xl font-bold mt-1">{{ $completedAssessments }}</p>
                </div>
                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Attendance Breakdown -->
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Detail Kehadiran (Bulan Ini)</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-3xl font-bold text-green-600">{{ $attendanceThisMonth['hadir'] }}</p>
                <p class="text-sm text-gray-800 mt-1">✓ Hadir</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-3xl font-bold text-yellow-600">{{ $attendanceThisMonth['sakit'] }}</p>
                <p class="text-sm text-gray-800 mt-1">⚠ Sakit</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-3xl font-bold text-blue-600">{{ $attendanceThisMonth['izin'] }}</p>
                <p class="text-sm text-gray-800 mt-1">ⓘ Izin</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-3xl font-bold text-red-600">{{ $attendanceThisMonth['alpha'] }}</p>
                <p class="text-sm text-gray-800 mt-1">✗ Alpha</p>
            </div>
        </div>
    </div>

    <!-- My Learning Profile -->
    @if($myLearningProfile)
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow p-6 text-white mb-6">
        <div class="flex items-center gap-4">
            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold">Profil Belajar Saya</h3>
                <p class="text-sm opacity-90 mt-1">{{ $myLearningProfile->assessment->name }}</p>
                <p class="text-xs opacity-75 mt-2">Diselesaikan: {{ $myLearningProfile->completed_at->format('d F Y') }}</p>
                <a href="{{ route('student.assessment.result', $myLearningProfile->assessment_id) }}" class="inline-block mt-3 px-4 py-2 bg-white text-indigo-600 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                    Lihat Detail Profil →
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Chart & Subjects -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Attendance Chart -->
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Trend Kehadiran (6 Bulan Terakhir)</h3>
            <canvas id="attendanceChart" height="250"></canvas>
        </div>

        <!-- Recent Subjects -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-800">📚 Mata Pelajaran Terbaru</h3>
            </div>
            <div class="p-5">
                @forelse($recentSubjects as $attendance)
                    <div class="flex items-start gap-3 mb-4 pb-4 border-b last:border-0">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800">{{ $attendance->teachingJournal->subject->name }}</p>
                            <p class="text-sm text-gray-800 mt-1">Guru: {{ $attendance->teachingJournal->teacher->name }}</p>
                            <p class="text-xs text-gray-700 mt-1">{{ $attendance->teachingJournal->date->format('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-700 text-center py-4">Belum ada pembelajaran</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Available Assessments -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-5 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">📝 Asesmen Tersedia</h3>
            <a href="{{ route('student.assessment.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Lihat Semua →</a>
        </div>
        <div class="p-5">
            @forelse($assessments as $assessment)
                <div class="flex items-center justify-between p-4 bg-white rounded-lg mb-3">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $assessment->title }}</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $assessment->description ?? 'Asesmen untuk mengukur profil belajar' }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                {{ $assessment->assessment_type === 'vark' ? 'VARK' : 'Diagnostik' }}
                            </span>
                            @if($assessment->isOngoing())
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Aktif</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('student.assessment.take', $assessment->id) }}" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        Kerjakan
                    </a>
                </div>
            @empty
                <p class="text-gray-700 text-center py-4">Tidak ada asesmen tersedia saat ini</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($attendanceChartData['labels'] ?? []),
            datasets: [
                {
                    label: 'Hadir',
                    data: @json($attendanceChartData['hadir'] ?? []),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Sakit',
                    data: @json($attendanceChartData['sakit'] ?? []),
                    borderColor: 'rgb(234, 179, 8)',
                    backgroundColor: 'rgba(234, 179, 8, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Izin',
                    data: @json($attendanceChartData['izin'] ?? []),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Alpha',
                    data: @json($attendanceChartData['alpha'] ?? []),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush

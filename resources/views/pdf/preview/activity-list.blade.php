<!-- HTML Preview for Activity List -->
<div class="activity-list-preview">
    <!-- Header -->
    <div class="text-center mb-6 pb-4 border-b-4 border-blue-600">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $schoolName }}</h1>
        <p class="text-lg text-gray-600">Daftar Kegiatan</p>
    </div>

    <!-- Activity Table -->
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="bg-blue-600 text-white">
                <th class="border border-blue-800 p-2 text-left font-bold w-8">No</th>
                <th class="border border-blue-800 p-2 text-left font-bold">Tanggal Mulai</th>
                <th class="border border-blue-800 p-2 text-left font-bold">Tanggal Selesai</th>
                <th class="border border-blue-800 p-2 text-left font-bold">Nama Kegiatan</th>
                <th class="border border-blue-800 p-2 text-left font-bold">Jenis</th>
                <th class="border border-blue-800 p-2 text-left font-bold">Semester</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $index => $activity)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 p-2">
                        {{ \Carbon\Carbon::parse($activity->start_date)->format('d M Y') }}
                    </td>
                    <td class="border border-gray-300 p-2">
                        {{ \Carbon\Carbon::parse($activity->end_date)->format('d M Y') }}
                    </td>
                    <td class="border border-gray-300 p-2">
                        <div class="font-semibold">{{ $activity->name }}</div>
                        @if($activity->description)
                            <div class="text-xs text-gray-600 mt-1">{{ $activity->description }}</div>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-2">
                        <span class="inline-block px-2 py-1 rounded text-xs text-white font-bold" 
                              style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};">
                            {{ $activity->activityType->name }}
                        </span>
                    </td>
                    <td class="border border-gray-300 p-2">{{ $activity->semester->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border border-gray-300 p-4 text-center text-gray-500">
                        Tidak ada kegiatan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-600 text-sm">
        <strong>Total Kegiatan:</strong> {{ $activities->count() }} kegiatan
    </div>

    <!-- Footer -->
    <div class="mt-6 pt-3 border-t border-gray-300 text-center text-xs text-gray-600">
        Dicetak pada: {{ $generatedAt }} | e-KALDIK - Kalender Pendidikan Digital
    </div>
</div>

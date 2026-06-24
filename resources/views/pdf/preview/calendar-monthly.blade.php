<!-- HTML Preview for Monthly Calendar -->
<div class="monthly-preview">
    <!-- Header -->
    <div class="text-center mb-6 pb-4 border-b-4 border-blue-600">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $schoolName }}</h1>
        <p class="text-lg text-gray-600">Kalender Kegiatan Bulan {{ $monthName }}</p>
    </div>

    <!-- Calendar Grid -->
    <table class="w-full border-collapse mb-6">
        <thead>
            <tr class="bg-blue-600 text-white">
                <th class="border border-blue-800 p-2 text-center font-bold">Senin</th>
                <th class="border border-blue-800 p-2 text-center font-bold">Selasa</th>
                <th class="border border-blue-800 p-2 text-center font-bold">Rabu</th>
                <th class="border border-blue-800 p-2 text-center font-bold">Kamis</th>
                <th class="border border-blue-800 p-2 text-center font-bold">Jumat</th>
                <th class="border border-blue-800 p-2 text-center font-bold">Sabtu</th>
                <th class="border border-blue-800 p-2 text-center font-bold">Minggu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calendar as $week)
                <tr>
                    @foreach($week as $day)
                        <td class="border border-gray-300 p-2 align-top h-24 w-[14.28%]
                            {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-100' }}
                            {{ $day['isWeekend'] ? 'bg-red-50' : '' }}">
                            <div class="font-bold text-base mb-1 {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $day['day'] }}
                            </div>
                            @foreach($day['activities'] as $activity)
                                <div class="text-xs px-2 py-1 mb-1 rounded text-white font-bold" 
                                     style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }}; cursor: help; white-space: normal; overflow: visible; word-wrap: break-word;"
                                     title="{{ $activity->name }}">
                                    {{ $activity->name }}
                                </div>
                            @endforeach
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Activity List -->
    @if($activities->isNotEmpty())
        <div class="mt-6">
            <div class="font-bold text-base mb-3 text-gray-900 p-2 bg-blue-50 border-l-4 border-blue-600">
                Daftar Kegiatan Bulan Ini
            </div>
            <div class="space-y-2">
                @foreach($activities as $activity)
                    <div class="p-3 bg-gray-50 border-l-4 text-sm" 
                         style="border-left-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};">
                        <div>
                            <strong>{{ \Carbon\Carbon::parse($activity->start_date)->format('d M Y') }}</strong>
                            @if($activity->start_date !== $activity->end_date)
                                - <strong>{{ \Carbon\Carbon::parse($activity->end_date)->format('d M Y') }}</strong>
                            @endif
                            : {{ $activity->name }}
                            <span class="inline-block px-2 py-0.5 rounded text-xs text-white font-bold ml-2" 
                                  style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};">
                                {{ $activity->activityType->name }}
                            </span>
                        </div>
                        @if($activity->description)
                            <div class="text-xs text-gray-600 mt-1 ml-2">{{ $activity->description }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="mt-6 pt-3 border-t border-gray-300 text-center text-xs text-gray-600">
        Dicetak pada: {{ $generatedAt }} | e-KALDIK - Kalender Pendidikan Digital
    </div>
</div>

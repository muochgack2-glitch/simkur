<!-- HTML Preview for Yearly Calendar -->
<div class="yearly-preview">
    <!-- Header -->
    <div class="text-center mb-6 pb-4 border-b-4 border-blue-600">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $schoolName }}</h1>
        <p class="text-base text-gray-600 mb-1">Kalender Pendidikan Tahun Pelajaran {{ $academicYear->year }}</p>
        <p class="text-sm text-gray-500">
            {{ \Carbon\Carbon::parse($academicYear->start_date)->format('d F Y') }} s/d 
            {{ \Carbon\Carbon::parse($academicYear->end_date)->format('d F Y') }}
        </p>
    </div>

    <!-- Months Grid -->
    <div class="space-y-6">
        @foreach($months->chunk(2) as $rowIndex => $chunk)
            <div class="grid grid-cols-2 gap-4">
                @foreach($chunk as $monthData)
                    <div class="border border-gray-300 rounded-lg overflow-hidden bg-white">
                        <div class="bg-blue-600 text-white font-bold text-center py-2 px-3">
                            {{ $monthData['name'] }}
                        </div>
                        
                        <!-- Calendar Grid -->
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-1 text-center font-bold">Sen</th>
                                    <th class="border border-gray-300 p-1 text-center font-bold">Sel</th>
                                    <th class="border border-gray-300 p-1 text-center font-bold">Rab</th>
                                    <th class="border border-gray-300 p-1 text-center font-bold">Kam</th>
                                    <th class="border border-gray-300 p-1 text-center font-bold">Jum</th>
                                    <th class="border border-gray-300 p-1 text-center font-bold">Sab</th>
                                    <th class="border border-gray-300 p-1 text-center font-bold">Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthData['calendar'] as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            <td class="border border-gray-200 p-1 align-top h-12
                                                {{ !$day['isCurrentMonth'] ? 'bg-gray-50 text-gray-400' : '' }}
                                                {{ $day['isWeekend'] ? 'bg-red-50' : 'bg-white' }}">
                                                <div class="font-bold text-xs mb-0.5">{{ $day['day'] }}</div>
                                                @foreach($day['activities'] as $activity)
                                                    <div class="w-full h-1 mb-0.5 rounded-sm" 
                                                         style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};"
                                                         title="{{ $activity->name }}"></div>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Activity Legend -->
                        @if($monthData['activities']->count() > 0)
                            <div class="p-2 bg-gray-50 border-t border-gray-200 text-xs space-y-1">
                                @foreach($monthData['activities']->take(5) as $activity)
                                    <div class="flex items-start">
                                        <span class="inline-block w-3 h-3 mr-2 border border-gray-400 flex-shrink-0 mt-0.5" 
                                              style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};"></span>
                                        <span class="text-xs" title="{{ $activity->name }}" style="cursor: help;">
                                            {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m') }}: 
                                            {{ $activity->name }}
                                        </span>
                                    </div>
                                @endforeach
                                @if($monthData['activities']->count() > 5)
                                    <div class="text-xs text-gray-600 italic">
                                        ... dan {{ $monthData['activities']->count() - 5 }} kegiatan lainnya
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Footer -->
    <div class="mt-6 pt-3 border-t border-gray-300 text-center text-xs text-gray-600">
        Dicetak pada: {{ $generatedAt }} | e-KALDIK - Kalender Pendidikan Digital
    </div>
</div>

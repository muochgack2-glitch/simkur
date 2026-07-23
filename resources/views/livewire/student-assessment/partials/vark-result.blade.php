<!-- Main Result Card -->
<div class="overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50 shadow-lg dark:from-blue-900/30 dark:to-purple-900/30 sm:rounded-lg">
    <div class="p-8 text-center">
        <div class="mb-4 text-6xl">
            {{ $profile->getDominantStyleIcon() }}
        </div>
        <h2 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">
            {{ $profile->getDominantStyleLabel() }}
        </h2>
        <p class="mx-auto max-w-2xl text-lg text-gray-700 dark:text-gray-300">
            {{ $profile->recommendations['description'] }}
        </p>
    </div>
</div>

<!-- Score Chart -->
<div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
    <div class="p-6">
        <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">📊 Detail Skor Gaya Belajar Anda</h3>
        
        <div class="space-y-4">
            @php
                $scores = $chartData['data'];
                $percentages = $chartData['percentages'];
                $styles = [
                    ['name' => 'Visual', 'icon' => '👁️', 'color' => 'blue', 'score' => $scores[0], 'percentage' => $percentages['visual']],
                    ['name' => 'Auditory', 'icon' => '👂', 'color' => 'green', 'score' => $scores[1], 'percentage' => $percentages['auditory']],
                    ['name' => 'Kinesthetic', 'icon' => '🤸', 'color' => 'orange', 'score' => $scores[2], 'percentage' => $percentages['kinesthetic']],
                    ['name' => 'Reading/Writing', 'icon' => '📖', 'color' => 'purple', 'score' => $scores[3], 'percentage' => $percentages['reading_writing']],
                ];
            @endphp

            @foreach($styles as $style)
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="mr-2 text-2xl">{{ $style['icon'] }}</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $style['name'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $style['score'] }}</span>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ round($style['percentage']) }}%)</span>
                        </div>
                    </div>
                    <div class="h-8 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="flex h-full items-center rounded-full px-3 text-xs font-semibold text-white transition-all duration-500
                            {{ $style['color'] === 'blue' ? 'bg-blue-600' : '' }}
                            {{ $style['color'] === 'green' ? 'bg-green-600' : '' }}
                            {{ $style['color'] === 'orange' ? 'bg-orange-600' : '' }}
                            {{ $style['color'] === 'purple' ? 'bg-purple-600' : '' }}"
                             style="width: {{ $style['percentage'] }}%">
                            @if($style['percentage'] > 15)
                                {{ round($style['percentage']) }}%
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <strong>Total Skor:</strong> {{ $profile->total_score }} poin
            </p>
        </div>
    </div>
</div>

<!-- Learning Tips -->
<div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
    <div class="p-6">
        <div class="mb-4 flex items-center">
            <svg class="mr-2 h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">💡 Tips Belajar untuk Anda</h3>
        </div>
        
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Berikut adalah tips belajar yang cocok dengan gaya belajar Anda:
        </p>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($profile->recommendations['tips'] as $tip)
                <div class="flex items-start rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tip }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Study Methods -->
<div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
    <div class="p-6">
        <div class="mb-4 flex items-center">
            <svg class="mr-2 h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📚 Metode Belajar yang Disarankan</h3>
        </div>

        <div class="space-y-3">
            @foreach($profile->recommendations['study_methods'] as $method)
                <div class="flex items-start rounded-lg bg-blue-50 p-4 dark:bg-blue-900/30">
                    <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $method }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Secondary Style (if exists) -->
@if(isset($profile->recommendations['secondary_style']))
    <div class="overflow-hidden border-2 border-dashed border-purple-300 bg-purple-50 shadow-sm dark:border-purple-700 dark:bg-purple-900/30 sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-start">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <div>
                    <h4 class="font-semibold text-purple-900 dark:text-purple-100">
                        ⚡ Gaya Belajar Sekunder: {{ $profile->recommendations['secondary_style']['label'] }}
                    </h4>
                    <p class="mt-2 text-sm text-purple-700 dark:text-purple-300">
                        {{ $profile->recommendations['secondary_style']['tip'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

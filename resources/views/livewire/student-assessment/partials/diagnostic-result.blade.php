@php
    $categoryColors = [
        'sangat_baik' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'dark_bg' => ''dark_text' => '
        'baik' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'dark_bg' => ''dark_text' => '
        'cukup' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'dark_bg' => ''dark_text' => '
        'perlu_pendampingan' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'dark_bg' => ''dark_text' => '
    ];
    $colors = $categoryColors[$profile->diagnostic_category] ?? $categoryColors['cukup'];
    
    $categoryIcons = [
        'sangat_baik' => '🌟',
        'baik' => '👍',
        'cukup' => '😊',
        'perlu_pendampingan' => '💪',
    ];
    $icon = $categoryIcons[$profile->diagnostic_category] ?? '😊';
@endphp

<!-- Main Result Card -->
<div class="overflow-hidden bg-gradient-to-br from-green-50 to-teal-50 shadow-lg sm:rounded-lg">
    <div class="p-8 text-center">
        <div class="mb-4 text-6xl">
            {{ $icon }}
        </div>
        <h2 class="mb-2 text-3xl font-bold text-gray-900 ">
            {{ $profile->getDiagnosticCategoryLabel() }}
        </h2>
        @if($profile->aspect_scores)
            @php
                $averageScore = round(collect($profile->aspect_scores)->avg(), 2);
            @endphp
            <p class="mx-auto max-w-2xl text-lg text-gray-900 ">
                Skor rata-rata kesiapan belajar Anda: <strong class="text-2xl">{{ $averageScore }}%</strong>
            </p>
        @endif
    </div>
</div>

<!-- Aspect Scores Chart -->
<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="mb-6 text-lg font-semibold text-gray-900 ">📊 Detail Kesiapan Belajar per Aspek</h3>
        
        @if($profile->aspect_scores)
            <div class="space-y-5">
                @php
                    $aspectLabels = [
                        'kesiapan' => ['label' => 'Kesiapan Belajar', 'icon' => '📚', 'color' => 'blue', 'weight' => 30],
                        'preferensi' => ['label' => 'Preferensi Belajar', 'icon' => '🎯', 'color' => 'purple', 'weight' => 20],
                        'motivasi' => ['label' => 'Motivasi Belajar', 'icon' => '🔥', 'color' => 'orange', 'weight' => 20],
                        'kemandirian' => ['label' => 'Kemandirian Belajar', 'icon' => '💪', 'color' => 'green', 'weight' => 15],
                        'kolaborasi' => ['label' => 'Kolaborasi & Komunikasi', 'icon' => '🤝', 'color' => 'pink', 'weight' => 15],
                        'dunia_kerja' => ['label' => 'Kesiapan Dunia Kerja', 'icon' => '💼', 'color' => 'indigo', 'weight' => 0],
                    ];
                @endphp

                @foreach($profile->aspect_scores as $aspect => $score)
                    @php
                        $aspectInfo = $aspectLabels[$aspect] ?? ['label' => ucfirst($aspect), 'icon' => '📌', 'color' => 'gray', 'weight' => 0];
                        $colorClass = match($aspectInfo['color']) {
                            'blue' => 'bg-blue-600',
                            'purple' => 'bg-purple-600',
                            'orange' => 'bg-orange-600',
                            'green' => 'bg-green-600',
                            'pink' => 'bg-pink-600',
                            'indigo' => 'bg-indigo-600',
                            default => 'bg-gray-600',
                        };
                        
                        $statusColor = $score >= 86 ? 'text-green-600' : ($score >= 71 ? 'text-blue-600' : ($score >= 56 ? 'text-yellow-600' : 'text-red-600'));
                    @endphp
                    
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="mr-2 text-2xl">{{ $aspectInfo['icon'] }}</span>
                                <div>
                                    <span class="font-medium text-gray-700 ">{{ $aspectInfo['label'] }}</span>
                                    @if($aspectInfo['weight'] > 0)
                                        <span class="ml-2 text-xs text-gray-700 ">(Bobot: {{ $aspectInfo['weight'] }}%)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold {{ $statusColor }}">{{ round($score, 1) }}%</span>
                            </div>
                        </div>
                        <div class="h-8 w-full overflow-hidden rounded-full bg-gray-200 ">
                            <div class="flex h-full items-center rounded-full px-3 text-xs font-semibold text-white transition-all duration-500 {{ $colorClass }}"
                                 style="width: {{ min($score, 100) }}%">
                                @if($score > 15)
                                    {{ round($score) }}%
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Category Info -->
        <div class="mt-6 rounded-lg {{ $colors['bg'] }} {{ $colors['dark_bg'] }} border-2 {{ $colors['border'] }} p-4">
            <div class="flex items-center">
                <span class="text-3xl mr-3">{{ $icon }}</span>
                <div>
                    <p class="font-semibold {{ $colors['text'] }} {{ $colors['dark_text'] }}">
                        Kategori: {{ $profile->getDiagnosticCategoryLabel() }}
                    </p>
                    <p class="text-sm {{ $colors['text'] }} {{ $colors['dark_text'] }} mt-1">
                        @if($profile->diagnostic_category === 'sangat_baik')
                            Anda menunjukkan kesiapan belajar yang sangat baik! Pertahankan konsistensi Anda.
                        @elseif($profile->diagnostic_category === 'baik')
                            Anda memiliki kesiapan belajar yang baik. Terus tingkatkan kualitas belajar Anda.
                        @elseif($profile->diagnostic_category === 'cukup')
                            Kesiapan belajar Anda cukup baik. Ada beberapa area yang perlu ditingkatkan.
                        @else
                            Anda memerlukan pendampingan untuk meningkatkan kesiapan belajar. Jangan khawatir, kami akan membantu!
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Areas Needing Support (if any) -->
@if(!empty($profile->needs_support_in))
    <div class="overflow-hidden border-2 border-orange-300 bg-orange-50 shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-start">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 text-orange-600 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-semibold text-orange-900 ">
                        🎯 Area yang Perlu Ditingkatkan
                    </h4>
                    <p class="mt-2 text-sm text-orange-700 ">
                        Aspek berikut memiliki skor di bawah 60% dan perlu perhatian lebih:
                    </p>
                    <ul class="mt-3 space-y-2">
                        @foreach($profile->needs_support_in as $aspect)
                            @php
                                $aspectInfo = $aspectLabels[$aspect] ?? ['label' => ucfirst($aspect), 'icon' => '📌'];
                            @endphp
                            <li class="flex items-center text-sm text-orange-800 ">
                                <span class="mr-2">{{ $aspectInfo['icon'] }}</span>
                                <strong>{{ $aspectInfo['label'] }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Recommendations -->
@if($profile->diagnostic_recommendations)
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="mb-4 flex items-center">
                <svg class="mr-2 h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 ">💡 Rekomendasi untuk Anda</h3>
            </div>
            
            @if(isset($profile->diagnostic_recommendations['suggestions']))
                <p class="mb-4 text-sm text-gray-800 ">
                    Berikut adalah saran untuk meningkatkan kesiapan belajar Anda:
                </p>

                <div class="space-y-3">
                    @foreach($profile->diagnostic_recommendations['suggestions'] as $suggestion)
                        <div class="flex items-start rounded-lg border border-green-200 bg-green-50 p-4 ">
                            <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-green-600 " fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-700 ">{{ $suggestion }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(isset($profile->diagnostic_recommendations['status']))
                <div class="mt-4 rounded-lg bg-blue-50 p-4 ">
                    <p class="text-sm text-blue-700 ">
                        ✨ <strong>{{ $profile->diagnostic_recommendations['status'] }}</strong>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Aspect-Specific Tips -->
    @if(isset($profile->diagnostic_recommendations['aspect_tips']) && !empty($profile->diagnostic_recommendations['aspect_tips']))
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-4 flex items-center">
                    <svg class="mr-2 h-6 w-6 text-blue-600 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 ">🎓 Tips Khusus per Aspek</h3>
                </div>

                <div class="space-y-4">
                    @foreach($profile->diagnostic_recommendations['aspect_tips'] as $aspect => $tips)
                        @php
                            $aspectInfo = $aspectLabels[$aspect] ?? ['label' => ucfirst($aspect), 'icon' => '📌'];
                        @endphp
                        <div class="rounded-lg border border-gray-200 p-4 ">
                            <h4 class="mb-2 flex items-center font-medium text-gray-900 ">
                                <span class="mr-2 text-xl">{{ $aspectInfo['icon'] }}</span>
                                {{ $aspectInfo['label'] }}
                            </h4>
                            <ul class="space-y-2">
                                @foreach($tips as $tip)
                                    <li class="flex items-start text-sm text-gray-800 ">
                                        <svg class="mr-2 mt-0.5 h-4 w-4 flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $tip }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif

<!-- Motivational Message -->
<div class="overflow-hidden border-2 border-dashed border-purple-300 bg-purple-50 shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-start">
            <span class="mr-3 text-3xl">🌱</span>
            <div>
                <h4 class="font-semibold text-purple-900 ">
                    Pesan untuk Anda
                </h4>
                <p class="mt-2 text-sm text-purple-700 ">
                    @if($profile->diagnostic_category === 'sangat_baik')
                        Pencapaian Anda luar biasa! Terus pertahankan semangat dan konsistensi belajar Anda. Anda adalah inspirasi bagi teman-teman yang lain.
                    @elseif($profile->diagnostic_category === 'baik')
                        Anda sudah menunjukkan kesiapan belajar yang baik! Dengan sedikit usaha lagi di beberapa aspek, Anda bisa mencapai level yang lebih tinggi.
                    @elseif($profile->diagnostic_category === 'cukup')
                        Setiap siswa memiliki cara belajar yang berbeda. Fokus pada area yang perlu ditingkatkan, dan jangan ragu untuk meminta bantuan guru atau teman.
                    @else
                        Jangan berkecil hati! Setiap orang memiliki titik awal yang berbeda. Yang penting adalah komitmen untuk terus belajar dan berkembang. Guru dan teman-teman siap membantu Anda.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

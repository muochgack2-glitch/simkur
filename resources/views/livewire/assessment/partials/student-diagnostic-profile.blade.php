<!-- Diagnostic Profile Detail -->
<div class="space-y-6">
    
    <!-- Category & Overall Score -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Kategori Kesiapan Belajar</h2>
                <div class="inline-block px-4 py-2 rounded-lg text-lg font-bold
                    {{ $profile->diagnostic_category === 'sangat_baik' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $profile->diagnostic_category === 'baik' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $profile->diagnostic_category === 'cukup' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $profile->diagnostic_category === 'perlu_pendampingan' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucwords(str_replace('_', ' ', $profile->diagnostic_category)) }}
                </div>
            </div>
            @if(!empty($profile->needs_support_in))
                <div class="text-right">
                    <span class="text-sm text-gray-800">Perlu Dukungan di:</span>
                    <div class="mt-1">
                        @foreach($profile->needs_support_in as $aspect)
                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded mr-1 mt-1">
                                {{ ucfirst($aspect) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Aspect Scores -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Skor Per Aspek</h3>
        <div class="space-y-3">
            @foreach($profile->aspect_scores as $aspect => $score)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="font-medium text-gray-700">{{ ucfirst($aspect) }}</span>
                        <span class="font-bold {{ $score >= 70 ? 'text-green-600' : ($score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($score, 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $score >= 70 ? 'bg-green-500' : ($score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $score }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recommendations -->
    <div class="bg-blue-50 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-3">Rekomendasi untuk Siswa</h3>
        
        @if(isset($profile->diagnostic_recommendations['status']))
            <div class="bg-white rounded p-4 mb-4">
                <p class="text-blue-800 font-medium">{{ $profile->diagnostic_recommendations['status'] }}</p>
            </div>
        @endif

        @if(isset($profile->diagnostic_recommendations['areas_to_improve']))
            <div class="mb-4">
                <h4 class="font-semibold text-blue-900 mb-2">Area yang Perlu Ditingkatkan:</h4>
                <ul class="space-y-1">
                    @foreach($profile->diagnostic_recommendations['areas_to_improve'] as $area)
                        <li class="flex items-center text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ $area }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($profile->diagnostic_recommendations['suggestions']))
            <div class="mb-4">
                <h4 class="font-semibold text-blue-900 mb-2">Saran Perbaikan:</h4>
                <ul class="space-y-2">
                    @foreach($profile->diagnostic_recommendations['suggestions'] as $suggestion)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-blue-800">{{ $suggestion }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Aspect Tips -->
        @if(isset($profile->diagnostic_recommendations['aspect_tips']))
            <div class="mt-4">
                <h4 class="font-semibold text-blue-900 mb-3">Tips Per Aspek:</h4>
                <div class="space-y-3">
                    @foreach($profile->diagnostic_recommendations['aspect_tips'] as $aspect => $tips)
                        <div class="bg-white rounded p-3">
                            <h5 class="font-medium text-gray-900 mb-2">{{ ucfirst($aspect) }}</h5>
                            <ul class="space-y-1">
                                @foreach($tips as $tip)
                                    <li class="text-sm text-gray-700 flex items-start">
                                        <span class="inline-block w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 mr-2"></span>
                                        {{ $tip }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Action Plan for Teacher -->
    <div class="bg-green-50 rounded-lg shadow-sm p-6 border-2 border-green-200">
        <h3 class="text-lg font-bold text-green-900 mb-3 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
            </svg>
            Panduan untuk Guru
        </h3>
        
        @if($profile->diagnostic_category === 'perlu_pendampingan')
            <div class="bg-red-100 border border-red-300 rounded p-3 mb-4">
                <p class="text-red-900 font-medium">⚠️ Siswa ini memerlukan pendampingan khusus</p>
            </div>
        @endif

        <div class="bg-white rounded p-4 space-y-3">
            @if(!empty($profile->needs_support_in))
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Fokus Pendampingan:</h4>
                    <ul class="space-y-2">
                        @foreach($profile->needs_support_in as $aspect)
                            <li class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-red-600 rounded-full mt-2 mr-3"></span>
                                <span class="text-gray-700">
                                    Berikan perhatian ekstra pada aspek <strong>{{ ucfirst($aspect) }}</strong>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Strategi Mengajar:</h4>
                <ul class="space-y-2">
                    @if($profile->diagnostic_category === 'perlu_pendampingan')
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Lakukan konseling individual</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Berikan target belajar yang jelas dan terukur</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Pantau perkembangan secara berkala (mingguan)</span>
                        </li>
                    @elseif($profile->diagnostic_category === 'cukup')
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Dorong siswa untuk konsisten dan tingkatkan kemandirian</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Berikan feedback positif untuk membangun motivasi</span>
                        </li>
                    @else
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Pertahankan kualitas pembelajaran</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                            <span class="text-gray-700">Berikan tantangan lebih untuk pengembangan</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

</div>

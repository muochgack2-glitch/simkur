<!-- VARK Profile Detail -->
<div class="space-y-6">
    
    <!-- Scores Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Distribusi Gaya Belajar</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-3xl font-bold text-red-600">{{ $profile->visual_score }}</div>
                <div class="text-sm text-gray-600 mt-1">Visual</div>
                <div class="text-xs text-gray-500">{{ $chartData['percentages']['visual'] }}%</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-3xl font-bold text-yellow-600">{{ $profile->auditory_score }}</div>
                <div class="text-sm text-gray-600 mt-1">Auditory</div>
                <div class="text-xs text-gray-500">{{ $chartData['percentages']['auditory'] }}%</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600">{{ $profile->kinesthetic_score }}</div>
                <div class="text-sm text-gray-600 mt-1">Kinesthetic</div>
                <div class="text-xs text-gray-500">{{ $chartData['percentages']['kinesthetic'] }}%</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600">{{ $profile->reading_writing_score }}</div>
                <div class="text-sm text-gray-600 mt-1">Reading/Writing</div>
                <div class="text-xs text-gray-500">{{ $chartData['percentages']['reading_writing'] }}%</div>
            </div>
        </div>

        <!-- Dominant Style -->
        <div class="border-t pt-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-600">Gaya Belajar Dominan:</span>
                    <span class="ml-2 text-lg font-bold text-blue-600">
                        {{ ucfirst(str_replace('_', ' ', $profile->dominant_style)) }}
                    </span>
                </div>
                @if(isset($profile->recommendations['secondary_style']))
                    <div class="text-right">
                        <span class="text-sm text-gray-600">Secondary:</span>
                        <span class="ml-2 text-md font-semibold text-gray-700">
                            {{ $profile->recommendations['secondary_style']['label'] ?? '' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recommendations for Student -->
    <div class="bg-blue-50 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
            </svg>
            {{ $profile->recommendations['title'] }}
        </h3>
        <p class="text-blue-800 mb-4">{{ $profile->recommendations['description'] }}</p>

        <!-- Tips for Student -->
        <div class="mb-4">
            <h4 class="font-semibold text-blue-900 mb-2">Tips Belajar:</h4>
            <ul class="space-y-2">
                @foreach($profile->recommendations['tips'] as $tip)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-blue-800">{{ $tip }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Study Methods -->
        <div>
            <h4 class="font-semibold text-blue-900 mb-2">Metode Belajar yang Efektif:</h4>
            <ul class="space-y-2">
                @foreach($profile->recommendations['study_methods'] as $method)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-blue-800">{{ $method }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Secondary Style Tip -->
        @if(isset($profile->recommendations['secondary_style']))
            <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                <p class="text-sm text-blue-900">
                    <strong>💡 Bonus:</strong> {{ $profile->recommendations['secondary_style']['tip'] }}
                </p>
            </div>
        @endif
    </div>

    <!-- Teaching Strategies for Teacher -->
    <div class="bg-green-50 rounded-lg shadow-sm p-6 border-2 border-green-200">
        <h3 class="text-lg font-bold text-green-900 mb-3 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
            </svg>
            Strategi Mengajar untuk Guru
        </h3>
        <p class="text-green-800 text-sm mb-3">
            Panduan untuk mengajar siswa dengan gaya belajar {{ ucfirst(str_replace('_', ' ', $profile->dominant_style)) }}
        </p>
        
        <div class="bg-white rounded p-4">
            <ul class="space-y-2">
                <li class="flex items-start">
                    <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                    <span class="text-gray-700">
                        @if($profile->dominant_style === 'visual')
                            Gunakan presentasi dengan banyak diagram, gambar, dan infografis
                        @elseif($profile->dominant_style === 'auditory')
                            Perbanyak diskusi kelas, penjelasan verbal, dan metode storytelling
                        @elseif($profile->dominant_style === 'kinesthetic')
                            Perbanyak praktik hands-on, simulasi, dan project-based learning
                        @else
                            Sediakan modul lengkap, handout, dan referensi bacaan yang banyak
                        @endif
                    </span>
                </li>
                <li class="flex items-start">
                    <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                    <span class="text-gray-700">
                        @if($profile->dominant_style === 'visual')
                            Beri demo visual sebelum praktik, gunakan color coding untuk konsep berbeda
                        @elseif($profile->dominant_style === 'auditory')
                            Izinkan diskusi kelompok, sediakan podcast atau audio materi
                        @elseif($profile->dominant_style === 'kinesthetic')
                            Buat aktivitas yang melibatkan gerakan dan eksplorasi langsung
                        @else
                            Berikan tugas menulis laporan dan jurnal refleksi
                        @endif
                    </span>
                </li>
                <li class="flex items-start">
                    <span class="inline-block w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></span>
                    <span class="text-gray-700">Seimbangkan dengan metode lain untuk learning diversity di kelas</span>
                </li>
            </ul>
        </div>
    </div>

</div>

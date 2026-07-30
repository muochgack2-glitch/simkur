            <!-- Detailed Cards per Semester -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($academicYear->semesters as $semester)
                @php
                    $effectiveDay = $semester->effectiveDay;
                @endphp
                
                <!-- Semester Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-blue-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">Semester {{ ucfirst($semester->type) }} {{ $academicYear->year }}</h3>
                        <p class="text-blue-100 text-sm mt-1">{{ $semester->start_date->format('d M Y') }} - {{ $semester->end_date->format('d M Y') }}</p>
                    </div>
                    
                    <div class="p-6">
                        @if(!$selectedGrade && $effectiveDay && $effectiveDay->byGrades && $effectiveDay->byGrades->isNotEmpty())
                            <!-- TABLE VIEW for All Grades -->
                            <div class="overflow-x-auto mb-4">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b-2 border-gray-300">
                                            <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                                            <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Periode</th>
                                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hari Belajar</th>
                                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Minggu Efektif</th>
                                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($effectiveDay->byGrades->sortBy('grade') as $gradeData)
                                            <tr class="border-b border-gray-200 hover:bg-gray-50 {{ $gradeData->hasEarlyEnd() ? 'bg-yellow-50' : 'bg-white' }}">
                                                <!-- Grade Badge -->
                                                <td class="px-3 py-4">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-base font-bold
                                                            {{ $gradeData->grade === 'X' ? 'bg-green-100 text-green-700' : '' }}
                                                            {{ $gradeData->grade === 'XI' ? 'bg-blue-100 text-blue-700' : '' }}
                                                            {{ $gradeData->grade === 'XII' ? 'bg-purple-100 text-purple-700' : '' }}">
                                                            {{ $gradeData->grade }}
                                                        </div>
                                                        <span class="text-sm font-semibold text-gray-800">Kelas {{ $gradeData->grade }}</span>
                                                    </div>
                                                </td>
                                                
                                                <!-- Periode -->
                                                <td class="px-3 py-4">
                                                    <div class="text-xs text-gray-700 font-medium">
                                                        {{ $gradeData->start_date->format('d M') }} - {{ $gradeData->end_date->format('d M Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                        {{ number_format($gradeData->getDurationInMonths(), 1) }} bulan
                                                    </div>
                                                    @if($gradeData->hasEarlyEnd())
                                                        <div class="inline-block mt-1 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-800 rounded font-medium">
                                                            ⚡ Selesai Lebih Cepat
                                                        </div>
                                                    @endif
                                                </td>
                                                
                                                <!-- Hari Belajar -->
                                                <td class="px-3 py-4 text-center">
                                                    <div class="text-3xl font-bold text-green-600">{{ $gradeData->study_days }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">hari</div>
                                                </td>
                                                
                                                <!-- Minggu Efektif -->
                                                <td class="px-3 py-4 text-center">
                                                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($gradeData->effective_weeks, 2) }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">minggu</div>
                                                </td>
                                                
                                                <!-- Persentase with Progress Bar -->
                                                <td class="px-3 py-4">
                                                    <div class="flex flex-col items-center">
                                                        <div class="text-xl font-bold mb-2 text-{{ $gradeData->status_color }}-600">
                                                            {{ number_format($gradeData->percentage, 2) }}%
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5" style="max-width: 120px;">
                                                            <div class="bg-{{ $gradeData->status_color }}-500 h-2.5 rounded-full transition-all" style="width: {{ $gradeData->percentage }}%"></div>
                                                        </div>
                                                        <div class="text-xs font-semibold text-{{ $gradeData->status_color }}-700 mt-2">
                                                            {{ $gradeData->status_label }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Summary Stats (Compact 4 boxes) -->
                            <div class="grid grid-cols-4 gap-3 text-center mb-4">
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="text-xs text-gray-600 font-medium">Total Hari</div>
                                    <div class="text-xl font-bold text-gray-900 mt-1">{{ $effectiveDay->total_days }}</div>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="text-xs text-blue-700 font-medium">Weekend</div>
                                    <div class="text-xl font-bold text-blue-900 mt-1">{{ $effectiveDay->weekend_days }}</div>
                                </div>
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <div class="text-xs text-yellow-700 font-medium">Hari Libur</div>
                                    <div class="text-xl font-bold text-yellow-900 mt-1">{{ $effectiveDay->holiday_days }}</div>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                    <div class="text-xs text-purple-700 font-medium">Hari Ujian</div>
                                    <div class="text-xl font-bold text-purple-900 mt-1">{{ $effectiveDay->exam_days }}</div>
                                </div>
                            </div>
                            
                            <!-- Catatan -->
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-800 flex items-start">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span><strong>Catatan:</strong> Kelas XII biasanya selesai lebih cepat karena ada Ujian Sekolah & UTBK. Kelas X & XI full semester.</span>
                                </p>
                            </div>
                        @else
                            <!-- CARD VIEW for Single Grade (when grade selected) -->
                            @php
                                if ($selectedGrade && $effectiveDay && $effectiveDay->byGrades) {
                                    $gradeData = $effectiveDay->byGrades->where('grade', $selectedGrade)->first();
                                    if ($gradeData) {
                                        $displayData = [
                                            'total_days' => $gradeData->total_days,
                                            'study_days' => $gradeData->study_days,
                                            'weekend_days' => $gradeData->weekend_days,
                                            'holiday_days' => $gradeData->holiday_days,
                                            'exam_days' => $gradeData->exam_days,
                                            'effective_weeks' => $gradeData->effective_weeks,
                                            'percentage' => $gradeData->percentage,
                                            'calculated_at' => $gradeData->calculated_at,
                                            'period' => $gradeData->start_date->format('d M') . ' - ' . $gradeData->end_date->format('d M Y'),
                                            'isEarlyEnd' => $gradeData->hasEarlyEnd(),
                                        ];
                                    } else {
                                        $displayData = null;
                                    }
                                } else {
                                    $displayData = null;
                                }
                            @endphp
                            
                            @if($displayData)
                            <div class="space-y-4">
                                <!-- Header for Selected Grade -->
                                <div class="mb-4 pb-4 border-b border-gray-200">
                                    <h4 class="text-lg font-bold text-gray-800">
                                        Kelas {{ $selectedGrade }}
                                        @if($displayData['isEarlyEnd'])
                                            <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded font-medium">⚡ Selesai Lebih Cepat</span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $displayData['period'] }}</p>
                                </div>
                                
                                <!-- Stats Grid (6 boxes) -->
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Total Hari -->
                                    <div class="stat-card bg-gray-100">
                                        <div>
                                            <p class="text-gray-600 text-sm font-medium">Total Hari</p>
                                            <p class="text-2xl font-bold text-gray-800">{{ $displayData['total_days'] }}</p>
                                        </div>
                                        <div class="stat-icon bg-gray-200 text-gray-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Hari Belajar -->
                                    <div class="stat-card bg-green-50">
                                        <div>
                                            <p class="text-green-700 text-sm font-medium">Hari Belajar</p>
                                            <p class="text-2xl font-bold text-green-800">{{ $displayData['study_days'] }}</p>
                                        </div>
                                        <div class="stat-icon bg-green-200 text-green-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Weekend -->
                                    <div class="stat-card bg-blue-50">
                                        <div>
                                            <p class="text-blue-700 text-sm font-medium">Hari Libur Akhir Pekan</p>
                                            <p class="text-2xl font-bold text-blue-800">{{ $displayData['weekend_days'] }}</p>
                                        </div>
                                        <div class="stat-icon bg-blue-200 text-blue-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Hari Libur -->
                                    <div class="stat-card bg-yellow-50">
                                        <div>
                                            <p class="text-yellow-700 text-sm font-medium">Hari Libur</p>
                                            <p class="text-2xl font-bold text-yellow-800">{{ $displayData['holiday_days'] }}</p>
                                        </div>
                                        <div class="stat-icon bg-yellow-200 text-yellow-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Hari Ujian -->
                                    <div class="stat-card bg-purple-50">
                                        <div>
                                            <p class="text-purple-700 text-sm font-medium">Hari Ujian</p>
                                            <p class="text-2xl font-bold text-purple-800">{{ $displayData['exam_days'] }}</p>
                                        </div>
                                        <div class="stat-icon bg-purple-200 text-purple-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Minggu Efektif -->
                                    <div class="stat-card bg-indigo-50">
                                        <div>
                                            <p class="text-indigo-700 text-sm font-medium">Minggu Efektif</p>
                                            <p class="text-2xl font-bold text-indigo-800">{{ number_format($displayData['effective_weeks'], 1) }}</p>
                                        </div>
                                        <div class="stat-icon bg-indigo-200 text-indigo-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Persentase Hari Efektif</span>
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($displayData['percentage'], 2) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $displayData['percentage'] }}%"></div>
                                    </div>
                                </div>

                                <!-- Last Updated -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-xs text-gray-500 text-right">
                                        Terakhir dihitung: {{ $displayData['calculated_at'] ? $displayData['calculated_at']->format('d M Y H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

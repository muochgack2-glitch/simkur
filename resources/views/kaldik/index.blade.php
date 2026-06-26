<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Pendidikan {{ $academicYear->year }} - {{ $schoolName }}</title>
    
    <!-- Tailwind CSS CDN (Fallback if Vite not built) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
        
        /* Card styles for effective days */
        .stat-card {
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        /* Responsive Design for Mobile */
        @media screen and (max-width: 768px) {
            .bg-white.max-w-\\[1400px\\], 
            .bg-white.max-w-\\[1000px\\] {
                max-width: 100% !important;
                padding: 1rem !important;
                margin: 0 !important;
            }
            
            /* Scale down header text */
            h1 {
                font-size: 1.25rem !important;
            }
            
            h2 {
                font-size: 1.125rem !important;
            }
            
            h3 {
                font-size: 1rem !important;
            }
            
            /* Calendar grid - stack vertically */
            .grid.grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
            
            /* Table responsive - allow horizontal scroll */
            .overflow-x-auto {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            table {
                min-width: 600px;
            }
            
            /* Adjust padding and font sizes */
            .border-2 {
                border-width: 1px !important;
            }
            
            .px-6 {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            
            .py-3 {
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }
            
            /* Calendar day cells */
            .calendar-day {
                min-height: 50px;
                font-size: 0.75rem;
            }
            
            /* Legend items */
            .legend .flex.flex-wrap {
                gap: 0.5rem !important;
            }
            
            .legend .flex.items-center {
                font-size: 0.625rem !important;
                padding: 0.375rem 0.5rem !important;
            }
        }
        
        @media screen and (max-width: 480px) {
            .calendar-day {
                min-height: 40px;
                padding: 2px;
            }
            
            .calendar-day-number {
                font-size: 0.625rem;
            }
            
            .activity-bar {
                height: 2px;
            }
            
            /* Even smaller text for activity list */
            .activity-list ul {
                font-size: 0.625rem !important;
            }
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
        }
        
        .calendar-day-header {
            background: #f3f4f6;
            font-weight: 600;
            text-align: center;
            padding: 6px 2px;
            font-size: 0.75rem;
            border: 1px solid #e5e7eb;
        }
        
        .calendar-day {
            min-height: 60px;
            border: 1px solid #e5e7eb;
            padding: 4px;
            position: relative;
            background: white;
        }
        
        /* Weekend tanpa kegiatan - merah muda opacity 50% */
        .calendar-day.weekend-empty {
            background: rgba(254, 226, 226, 0.5) !important;
        }
        
        /* Weekend dengan kegiatan libur - PUTIH, bukan merah */
        /* Ini harus override weekend-empty dengan specificity lebih tinggi */
        div.calendar-day.weekend-with-activity {
            background: white !important;
        }
        
        /* Semua activity cells harus putih sebagai base */
        div.calendar-day.has-activity {
            position: relative;
            background: white !important; /* Force white background */
        }
        
        /* Activity gradient overlay */
        div.calendar-day.has-activity::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.50;
            z-index: 0;
            background: var(--activity-gradient);
            pointer-events: none;
        }
        
        /* Double specificity: weekend with activity = NEVER red */
        div.calendar-day.weekend-with-activity.has-activity {
            background: white !important;
        }
        
        div.calendar-day.weekend-with-activity.has-activity::before {
            background: var(--activity-gradient) !important;
            opacity: 0.50;
        }
        
        .calendar-day-number {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 2px;
            position: relative;
            z-index: 1;
        }
        
        .activity-bars {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 4px;
        }
        
        .activity-bar {
            height: 3px;
            border-radius: 1px;
        }
        
        .activity-icon {
            font-size: 0.75rem;
            display: inline-block;
            margin-right: 2px;
            line-height: 1;
        }
        
        .activity-icons-row {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            margin-top: 2px;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- PAGE 1: Calendar Visual -->
    <div class="bg-white max-w-[1400px] mx-auto p-8 shadow-lg">
        <!-- Header / KOP Sekolah -->
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            @if($schoolLogo)
                <img src="{{ asset($schoolLogo) }}" alt="Logo" class="w-20 h-20 mx-auto mb-3 object-contain">
            @endif
            <h1 class="text-2xl font-bold uppercase">{{ $schoolName }}</h1>
            <p class="text-sm mt-1">{{ $schoolAddress }}</p>
            <h2 class="text-xl font-bold mt-4 uppercase">KALENDER PENDIDIKAN TAHUN {{ $academicYear->year }}</h2>
        </div>

        <!-- Filter by Grade (No Print) -->
        <div class="no-print mb-6">
            <div class="flex items-center justify-center gap-4">
                <label for="gradeFilter" class="font-semibold text-gray-700">Filter Kelas:</label>
                <select id="gradeFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="filterByGrade(this.value)">
                    <option value="" {{ !$selectedGrade ? 'selected' : '' }}>Semua Kelas</option>
                    <option value="X" {{ $selectedGrade === 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ $selectedGrade === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ $selectedGrade === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>
        </div>

        <script>
            function filterByGrade(grade) {
                const url = new URL(window.location.href);
                if (grade) {
                    url.searchParams.set('grade', grade);
                } else {
                    url.searchParams.delete('grade');
                }
                window.location.href = url.toString();
            }
        </script>

        <!-- Floating Action Button (FAB) for Print/Download -->
        <div class="fixed bottom-6 right-6 no-print" style="z-index: 9999;">
            <div class="relative group">
                <!-- Dropdown Menu (hidden by default, show on hover) -->
                <div class="absolute bottom-16 right-0 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 ease-in-out" style="z-index: 10000;">
                    <div class="bg-white rounded-lg shadow-2xl py-2 min-w-[180px]" style="box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);">
                        <!-- Preview PDF -->
                        <a href="{{ route('kaldik.download') }}?preview=1" target="_blank" 
                           class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors group/item" style="text-decoration: none; color: #374151;">
                            <span style="font-size: 1.5rem;">👁️</span>
                            <span class="font-medium text-gray-700 group-hover/item:text-blue-600">Preview PDF</span>
                        </a>
                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-1" style="border-color: #e5e7eb;"></div>
                        <!-- Download PDF -->
                        <a href="{{ route('kaldik.download') }}" 
                           class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors group/item" style="text-decoration: none; color: #374151;">
                            <span style="font-size: 1.5rem;">📥</span>
                            <span class="font-medium text-gray-700 group-hover/item:text-blue-600">Download PDF</span>
                        </a>
                    </div>
                </div>
                
                <!-- Main FAB Button -->
                <button type="button" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white w-16 h-16 rounded-full shadow-2xl hover:shadow-blue-500/50 flex items-center justify-center hover:scale-110 transition-all duration-300 group-hover:rotate-90" 
                        style="background: linear-gradient(to right, #2563eb, #1e40af); width: 64px; height: 64px; border-radius: 9999px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center;">
                    <svg class="w-7 h-7" style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Calendar Grid 12 Months (2 columns × 6 rows) -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            @foreach($months as $month)
                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <!-- Month Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center py-3" style="background: linear-gradient(to right, #2563eb, #1e40af); color: white; text-align: center; padding: 12px 0; font-weight: bold; font-size: 1.125rem;">
                        <h3 class="text-lg font-bold uppercase" style="color: white; font-weight: bold; text-transform: uppercase; margin: 0;">{{ $month['name'] }} {{ $month['year'] }}</h3>
                    </div>
                    
                    <div class="p-3">
                        <!-- Day Headers -->
                        <div class="calendar-grid mb-1">
                            <div class="calendar-day-header">Sen</div>
                            <div class="calendar-day-header">Sel</div>
                            <div class="calendar-day-header">Rab</div>
                            <div class="calendar-day-header">Kam</div>
                            <div class="calendar-day-header">Jum</div>
                            <div class="calendar-day-header">Sab</div>
                            <div class="calendar-day-header">Min</div>
                        </div>
                        
                        <!-- Days Grid -->
                        <div class="calendar-grid">
                            @php
                                $firstDay = $month['days'][0]['dayOfWeek'];
                                $firstDay = $firstDay == 0 ? 7 : $firstDay;
                            @endphp
                            
                            @for($i = 1; $i < $firstDay; $i++)
                                <div class="calendar-day bg-gray-50"></div>
                            @endfor
                            
                            @foreach($month['days'] as $day)
                                @php
                                    // Function to get icon based on activity type code
                                    $getActivityIcon = function($code) {
                                        $icons = [
                                            'LAP' => '🌙',      // Libur Awal Puasa
                                            'PKL' => '💼',      // PKL
                                            'MPLS' => '🎓',     // MPLS
                                            'PTS' => '📝',      // PTS (Ujian)
                                            'PAS' => '📋',      // PAS (Ujian)
                                            'PAT' => '📄',      // PAT (Ujian)
                                            'ANBK' => '💻',     // ANBK (Ujian)
                                            'LIBNAS' => '🏖️',   // Libur Nasional
                                            'LIBSEM' => '🏝️',   // Libur Semester
                                            'RAPAT' => '👥',    // Rapat Guru
                                            'KEGIATAN' => '🎯', // Kegiatan Sekolah
                                            'UPACARA' => '🚩',  // Upacara
                                            'TKA' => '✏️',      // TKA
                                            'RAPOR' => '📜',    // Pembagian Rapor
                                        ];
                                        return $icons[$code] ?? '📅';
                                    };
                                    
                                    // Determine CSS class for weekend styling
                                    $weekendClass = '';
                                    if ($day['isWeekend']) {
                                        // PENTING: Jika ada activity, JANGAN tambahkan weekend-empty
                                        if ($day['hasActivity']) {
                                            $weekendClass = 'weekend-with-activity';
                                        } else {
                                            $weekendClass = 'weekend-empty';
                                        }
                                    }
                                    
                                    // Create horizontal gradient based on number of activities
                                    $activityGradient = '';
                                    if ($day['hasActivity'] && $day['activities']->isNotEmpty()) {
                                        $colors = $day['activities']->pluck('color')->values(); // Keep all colors including duplicates
                                        $count = $colors->count();
                                        
                                        if ($count == 1) {
                                            // Single color - solid background
                                            $activityGradient = $colors->first();
                                        } else {
                                            // Multiple activities - horizontal stripes (top to bottom)
                                            $percentage = 100 / $count;
                                            $gradientStops = [];
                                            
                                            foreach ($colors as $index => $color) {
                                                $start = $index * $percentage;
                                                $end = ($index + 1) * $percentage;
                                                $gradientStops[] = "{$color} {$start}%, {$color} {$end}%";
                                            }
                                            
                                            // Horizontal gradient (0deg = top to bottom)
                                            $activityGradient = 'linear-gradient(180deg, ' . implode(', ', $gradientStops) . ')';
                                        }
                                    }
                                @endphp
                                
                                <div class="calendar-day {{ $weekendClass }} {{ $day['hasActivity'] ? 'has-activity' : '' }}"
                                     @if($activityGradient)
                                        style="--activity-gradient: {{ $activityGradient }};"
                                     @endif
                                >
                                    <div class="calendar-day-number">{{ $day['date'] }}</div>
                                    @if($day['hasActivity'])
                                        <!-- Icons -->
                                        <div class="activity-icons-row">
                                            @foreach($day['activities']->take(4) as $activity)
                                                <span 
                                                    class="activity-icon" 
                                                    style="color: {{ $activity->color }};"
                                                    title="{{ $activity->name }}"
                                                >{{ $getActivityIcon($activity->activityType->code) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Activity List for this month -->
                        @if($month['activities']->count() > 0)
                            <div class="mt-3 border-t border-gray-200 pt-2">
                                <ul class="text-xs space-y-1">
                                    @foreach($month['activities'] as $activity)
                                        <li class="flex items-start gap-2">
                                            <span class="w-3 h-3 rounded-full flex-shrink-0 mt-0.5" style="background-color: {{ $activity->color }};"></span>
                                            <span class="flex-1">
                                                <strong>{{ $activity->start_date->format('d/m') }}</strong>: {{ $activity->name }}
                                                @if($activity->isForAllGrades())
                                                    <span class="inline-block ml-1 px-1.5 py-0.5 text-[0.625rem] font-medium rounded bg-gray-200 text-gray-700">Semua</span>
                                                @else
                                                    @foreach($activity->target_grades ?? [] as $grade)
                                                        <span class="inline-block ml-1 px-1.5 py-0.5 text-[0.625rem] font-medium rounded 
                                                            {{ $grade === 'X' ? 'bg-green-100 text-green-700' : '' }}
                                                            {{ $grade === 'XI' ? 'bg-blue-100 text-blue-700' : '' }}
                                                            {{ $grade === 'XII' ? 'bg-purple-100 text-purple-700' : '' }}">
                                                            {{ $grade }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <!-- PAGE 2: Effective Days & Signature -->
    <div class="page-break"></div>
    <div class="bg-white max-w-[1000px] mx-auto p-8 shadow-lg mt-8">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold uppercase">KALENDER PENDIDIKAN TAHUN {{ $academicYear->year }}</h2>
            <p class="text-sm text-gray-600">(Halaman 2 - Perhitungan Hari Efektif)</p>
        </div>

        <!-- Tabel Hari Efektif -->
        <div class="mb-12">
            <h3 class="text-center font-bold text-lg mb-4 uppercase">Perhitungan Hari Efektif</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-2 border-black">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <th class="border-2 border-black px-4 py-3">Semester</th>
                            <th class="border-2 border-black px-4 py-3">Total Hari</th>
                            <th class="border-2 border-black px-4 py-3">Hari Libur</th>
                            <th class="border-2 border-black px-4 py-3">Hari Ujian</th>
                            <th class="border-2 border-black px-4 py-3">Hari Efektif</th>
                            <th class="border-2 border-black px-4 py-3">Minggu Efektif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($effectiveDays as $ed)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="border-2 border-black px-4 py-3 font-semibold">
                                    Semester {{ ucfirst($ed->semester->type) }}
                                </td>
                                <td class="border-2 border-black px-4 py-3 text-center">{{ $ed->total_days }}</td>
                                <td class="border-2 border-black px-4 py-3 text-center">{{ $ed->weekend_days + $ed->holiday_days }}</td>
                                <td class="border-2 border-black px-4 py-3 text-center">{{ $ed->exam_days }}</td>
                                <td class="border-2 border-black px-4 py-3 text-center font-bold text-blue-700">{{ $ed->study_days }} hari</td>
                                <td class="border-2 border-black px-4 py-3 text-center font-bold text-blue-700">{{ number_format($ed->effective_weeks, 1) }} minggu</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 font-bold">
                            <td class="border-2 border-black px-4 py-3">TOTAL</td>
                            <td class="border-2 border-black px-4 py-3 text-center">{{ $totalDays }}</td>
                            <td class="border-2 border-black px-4 py-3 text-center">{{ $totalWeekends + $totalHolidays }}</td>
                            <td class="border-2 border-black px-4 py-3 text-center">{{ $totalExams }}</td>
                            <td class="border-2 border-black px-4 py-3 text-center text-blue-700">{{ $totalStudyDays }} hari</td>
                            <td class="border-2 border-black px-4 py-3 text-center text-blue-700">{{ $totalEffectiveWeeks }} minggu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- PAGE 3: PERHITUNGAN HARI EFEKTIF -->
    <div class="page-break"></div>
    <div class="bg-gray-50 min-h-screen py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">KALENDER PENDIDIKAN TAHUN {{ $academicYear->year }}</h1>
                <p class="text-gray-600 mt-2">(Halaman 2 - Perhitungan Hari Efektif)</p>
            </div>

            <!-- Detailed Cards per Semester -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($academicYear->semesters as $semester)
                @php
                    $effectiveDay = $semester->effectiveDay;
                    $percentage = $effectiveDay->percentage ?? 0;
                @endphp
                <!-- Semester Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-blue-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">Semester {{ ucfirst($semester->type) }} {{ $academicYear->year }}</h3>
                        <p class="text-blue-100 text-sm mt-1">{{ $semester->start_date->format('d M Y') }} - {{ $semester->end_date->format('d M Y') }}</p>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Total Hari -->
                            <div class="stat-card bg-gray-100">
                                <div>
                                    <p class="text-gray-600 text-sm font-medium">Total Hari</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $effectiveDay->total_days ?? 0 }}</p>
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
                                    <p class="text-2xl font-bold text-green-800">{{ $effectiveDay->study_days ?? 0 }}</p>
                                </div>
                                <div class="stat-icon bg-green-200 text-green-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Hari Libur Akhir Pekan -->
                            <div class="stat-card bg-blue-50">
                                <div>
                                    <p class="text-blue-700 text-sm font-medium">Hari Libur Akhir Pekan</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ $effectiveDay->weekend_days ?? 0 }}</p>
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
                                    <p class="text-2xl font-bold text-yellow-800">{{ $effectiveDay->holiday_days ?? 0 }}</p>
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
                                    <p class="text-2xl font-bold text-purple-800">{{ $effectiveDay->exam_days ?? 0 }}</p>
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
                                    <p class="text-2xl font-bold text-indigo-800">{{ $effectiveDay->effective_weeks ?? 0 }}</p>
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
                                <span class="text-sm font-bold text-gray-900">{{ number_format($percentage, 2) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Last Updated -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-right">
                                Terakhir dihitung: {{ $effectiveDay->calculated_at ? $effectiveDay->calculated_at->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PAGE 4: Daftar Kegiatan -->
    <div class="page-break"></div>
    <div class="bg-white max-w-[1000px] mx-auto p-8 shadow-lg mt-8">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold uppercase">{{ $schoolName }}</h2>
            <h3 class="text-lg font-bold mt-2 uppercase">Daftar Kegiatan</h3>
            <p class="text-gray-600 text-sm mt-1">(Halaman 3)</p>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-3"></div>
        </div>

        <!-- Tabel Daftar Kegiatan -->
        <div class="overflow-x-auto">
            <table class="w-full border-2 border-blue-600">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <th class="border-2 border-blue-600 px-3 py-3 text-center w-16">No</th>
                        <th class="border-2 border-blue-600 px-4 py-3 text-center w-32">Tanggal<br>Mulai</th>
                        <th class="border-2 border-blue-600 px-4 py-3 text-center w-32">Tanggal<br>Selesai</th>
                        <th class="border-2 border-blue-600 px-6 py-3 text-left">Nama Kegiatan</th>
                        <th class="border-2 border-blue-600 px-4 py-3 text-center w-40">Jenis</th>
                        <th class="border-2 border-blue-600 px-4 py-3 text-center w-32">Kelas</th>
                        <th class="border-2 border-blue-600 px-4 py-3 text-center w-48">Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $allActivities = \App\Models\Activity::with(['activityType', 'semester'])
                            ->where('academic_year_id', $academicYear->id)
                            ->orderBy('start_date')
                            ->orderBy('name')
                            ->get();
                    @endphp
                    
                    @foreach($allActivities as $index => $activity)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="border-2 border-gray-300 px-3 py-3 text-center font-semibold">{{ $index + 1 }}</td>
                            <td class="border-2 border-gray-300 px-4 py-3 text-center">
                                {{ $activity->start_date->format('d M Y') }}
                            </td>
                            <td class="border-2 border-gray-300 px-4 py-3 text-center">
                                {{ $activity->end_date->format('d M Y') }}
                            </td>
                            <td class="border-2 border-gray-300 px-6 py-3">
                                <div class="font-semibold text-gray-900">{{ $activity->name }}</div>
                                @if($activity->description)
                                    <div class="text-sm text-gray-600 mt-1">{{ $activity->description }}</div>
                                @endif
                            </td>
                            <td class="border-2 border-gray-300 px-4 py-3 text-center">
                                <span class="inline-block px-4 py-2 rounded-lg text-white font-semibold text-sm" 
                                      style="background-color: {{ $activity->activityType->default_color }};">
                                    {{ $activity->activityType->name }}
                                </span>
                            </td>
                            <td class="border-2 border-gray-300 px-4 py-3 text-center">
                                @if($activity->isForAllGrades())
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">Semua</span>
                                @else
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        @foreach($activity->target_grades ?? [] as $grade)
                                            <span class="inline-block px-2 py-1 text-xs font-medium rounded 
                                                {{ $grade === 'X' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $grade === 'XI' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $grade === 'XII' ? 'bg-purple-100 text-purple-700' : '' }}">
                                                {{ $grade }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="border-2 border-gray-300 px-4 py-3 text-center">
                                <span class="font-medium">{{ $activity->semester->name }}</span>
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($allActivities->count() === 0)
                        <tr>
                            <td colspan="7" class="border-2 border-gray-300 px-6 py-8 text-center text-gray-500">
                                Belum ada kegiatan yang terdaftar
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

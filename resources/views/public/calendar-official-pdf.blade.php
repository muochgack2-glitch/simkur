<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Pendidikan {{ $academicYear->year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
        }
        
        /* PAGE 1 */
        .page-1 {
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid black;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 16px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 12px;
            margin-top: 6px;
            text-transform: uppercase;
        }
        
        .header p {
            font-size: 9px;
        }
        
        /* Calendar Grid */
        .calendar-container {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .calendar-row {
            display: table-row;
        }
        
        .calendar-month {
            display: table-cell;
            width: 50%;
            padding: 3px;
            vertical-align: top;
        }
        
        .month-box {
            border: 1px solid #333;
            height: 100%;
        }
        
        .month-header {
            background: linear-gradient(to right, #2563eb, #1e40af);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding: 5px 0;
        }
        
        .month-body {
            padding: 4px;
        }
        
        .calendar-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        
        .day-header {
            background: #f3f4f6;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
            padding: 3px 1px;
            border: 0.5px solid #ddd;
        }
        
        .calendar-day {
            text-align: left;
            font-size: 8px;
            padding: 2px 3px;
            border: 0.5px solid #ddd;
            height: 28px;
            vertical-align: top;
            position: relative;
        }
        
        .calendar-day.weekend {
            background: #f9fafb;
        }
        
        .activity-bars {
            margin-top: 2px;
        }
        
        .activity-bar {
            height: 2px;
            margin: 1px 0;
        }
        
        /* Activity List */
        .activity-list {
            border-top: 1px solid #e5e7eb;
            padding-top: 3px;
            font-size: 7px;
        }
        
        .activity-item {
            margin: 1px 0;
            display: table;
            width: 100%;
        }
        
        .activity-dot {
            display: table-cell;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            vertical-align: top;
            padding-top: 2px;
        }
        
        .activity-text {
            display: table-cell;
            padding-left: 3px;
            vertical-align: top;
        }
        
        /* Legend */
        .legend {
            text-align: center;
            margin: 8px 0;
            font-size: 7px;
            padding: 4px;
            border-top: 1px solid #ccc;
        }
        
        .legend-title {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 3px;
        }
        
        .legend-item {
            display: inline-block;
            margin: 2px 4px;
            padding: 2px 5px;
            border: 0.5px solid #999;
            border-radius: 6px;
        }
        
        .legend-box {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 2px;
        }
        
        /* PAGE 2 */
        .page-2 {
            page-break-before: always;
            padding: 20px;
        }
        
        .page-2 .header {
            margin-bottom: 15px;
        }
        
        /* PAGE 3 */
        .page-3 {
            page-break-before: always;
            padding: 20px;
        }
        
        .page-3 .header {
            margin-bottom: 15px;
        }
        
        .table-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin: 15px 0 10px;
            text-transform: uppercase;
        }
        
        table.effective-days {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 9px;
        }
        
        table.effective-days th,
        table.effective-days td {
            border: 1.5px solid black;
            padding: 6px 8px;
            text-align: center;
        }
        
        table.effective-days th {
            background: linear-gradient(to right, #2563eb, #1e40af);
            color: white;
            font-weight: bold;
        }
        
        table.effective-days tr.total {
            background: #f3f4f6;
            font-weight: bold;
        }
        
        /* Signature */
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .signature-space {
            height: 60px;
        }
        
        .signature-line {
            padding-top: 4px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- PAGE 1: Calendar Visual -->
    <div class="page-1">
        <!-- Header -->
        <div class="header">
            @if($schoolLogo)
                <img src="{{ public_path($schoolLogo) }}" alt="Logo" style="width: 50px; height: 50px; margin: 0 auto 5px; object-fit: contain;">
            @endif
            <h1>{{ $schoolName }}</h1>
            <p>{{ $schoolAddress }}</p>
            <h2>Kalender Pendidikan Tahun {{ $academicYear->year }}</h2>
        </div>

        <!-- Calendar 12 Months (2 columns × 6 rows) -->
        @for($row = 0; $row < 6; $row++)
            <div class="calendar-container">
                <div class="calendar-row">
                    @for($col = 0; $col < 2; $col++)
                        @php
                            $index = $row * 2 + $col;
                            $month = $months[$index] ?? null;
                        @endphp
                        
                        @if($month)
                            <div class="calendar-month">
                                <div class="month-box">
                                    <div class="month-header">
                                        {{ strtoupper($month['name']) }} {{ $month['year'] }}
                                    </div>
                                    
                                    <div class="month-body">
                                        <!-- Calendar Grid -->
                                        <table class="calendar-grid">
                                            <tr>
                                                <td class="day-header">Sen</td>
                                                <td class="day-header">Sel</td>
                                                <td class="day-header">Rab</td>
                                                <td class="day-header">Kam</td>
                                                <td class="day-header">Jum</td>
                                                <td class="day-header">Sab</td>
                                                <td class="day-header">Min</td>
                                            </tr>
                                            
                                            @php
                                                $firstDay = $month['days'][0]['dayOfWeek'];
                                                $firstDay = $firstDay == 0 ? 7 : $firstDay;
                                                $weeks = [];
                                                $currentWeek = array_fill(0, $firstDay - 1, null);
                                                
                                                foreach($month['days'] as $day) {
                                                    $currentWeek[] = $day;
                                                    if(count($currentWeek) == 7) {
                                                        $weeks[] = $currentWeek;
                                                        $currentWeek = [];
                                                    }
                                                }
                                                
                                                if(!empty($currentWeek)) {
                                                    while(count($currentWeek) < 7) {
                                                        $currentWeek[] = null;
                                                    }
                                                    $weeks[] = $currentWeek;
                                                }
                                            @endphp
                                            
                                            @foreach($weeks as $week)
                                                <tr>
                                                    @foreach($week as $day)
                                                        <td class="calendar-day {{ $day && $day['isWeekend'] ? 'weekend' : '' }}">
                                                            @if($day)
                                                                {{ $day['date'] }}
                                                                @if($day['hasActivity'])
                                                                    <div class="activity-bars">
                                                                        @foreach($day['activities']->take(2) as $activity)
                                                                            <div class="activity-bar" style="background-color: {{ $activity->color }};"></div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </table>
                                        
                                        <!-- Activity List -->
                                        @if($month['activities']->count() > 0)
                                            <div class="activity-list">
                                                @foreach($month['activities'] as $activity)
                                                    <div class="activity-item">
                                                        <span class="activity-dot" style="background-color: {{ $activity->color }};"></span>
                                                        <span class="activity-text">
                                                            <strong>{{ $activity->start_date->format('d/m') }}</strong>: {{ $activity->name }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        @endfor

        <!-- Legend -->
        <div class="legend">
            <div class="legend-title">KETERANGAN:</div>
            @php
                $activityTypes = App\Models\ActivityType::orderBy('sort_order')->get();
            @endphp
            
            @foreach($activityTypes as $type)
                <div class="legend-item">
                    <span class="legend-box" style="background-color: {{ $type->default_color }};"></span>
                    <span>{{ $type->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- PAGE 2: Effective Days & Signature -->
    <div class="page-2">
        <div class="header">
            <h2>Kalender Pendidikan Tahun {{ $academicYear->year }}</h2>
            <p style="margin-top: 3px;">(Halaman 2 - Perhitungan Hari Efektif)</p>
        </div>

        <div class="table-title">Perhitungan Hari Efektif</div>
        <table class="effective-days">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Total Hari</th>
                    <th>Hari Libur</th>
                    <th>Hari Ujian</th>
                    <th>Hari Efektif</th>
                    <th>Minggu Efektif</th>
                </tr>
            </thead>
            <tbody>
                @foreach($effectiveDays as $ed)
                    <tr>
                        <td style="font-weight: bold;">Semester {{ ucfirst($ed->semester->type) }}</td>
                        <td>{{ $ed->total_days }}</td>
                        <td>{{ $ed->weekend_days + $ed->holiday_days }}</td>
                        <td>{{ $ed->exam_days }}</td>
                        <td style="font-weight: bold; color: #2563eb;">{{ $ed->study_days }} hari</td>
                        <td style="font-weight: bold; color: #2563eb;">{{ number_format($ed->effective_weeks, 1) }} minggu</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td>TOTAL</td>
                    <td>{{ $totalDays }}</td>
                    <td>{{ $totalWeekends + $totalHolidays }}</td>
                    <td>{{ $totalExams }}</td>
                    <td style="color: #2563eb;">{{ $totalStudyDays }} hari</td>
                    <td style="color: #2563eb;">{{ $totalEffectiveWeeks }} minggu</td>
                </tr>
            </tbody>
        </table>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-box">
                <p>Blora, _Juli {{ $academicYear->start_date->format('Y') }}</p>
                <p style="font-weight: bold; margin-top: 3px;">Kepala Sekolah</p>
                <div class="signature-space"></div>
                <div class="signature-line">
                    <p style="font-weight: bold; font-size: 10px;">{{ $principalName }}</p>
                    <p style="font-weight: bold; font-size: 9px; margin-top: 2px;">NIY. {{ $principalNiy }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE 3: Daftar Kegiatan -->
    <div class="page-3">
        <div class="header">
            <h1>{{ $schoolName }}</h1>
            <h2 style="font-size: 11px; margin-top: 8px;">Daftar Kegiatan</h2>
            <div style="width: 60px; height: 3px; background: #2563eb; margin: 6px auto 0;"></div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 8px; margin-top: 10px;">
            <thead>
                <tr style="background: linear-gradient(to right, #2563eb, #1e40af); color: white;">
                    <th style="border: 1.5px solid #2563eb; padding: 6px; text-align: center; width: 8%;">No</th>
                    <th style="border: 1.5px solid #2563eb; padding: 6px; text-align: center; width: 12%;">Tanggal<br>Mulai</th>
                    <th style="border: 1.5px solid #2563eb; padding: 6px; text-align: center; width: 12%;">Tanggal<br>Selesai</th>
                    <th style="border: 1.5px solid #2563eb; padding: 6px; text-align: left; width: 35%;">Nama Kegiatan</th>
                    <th style="border: 1.5px solid #2563eb; padding: 6px; text-align: center; width: 15%;">Jenis</th>
                    <th style="border: 1.5px solid #2563eb; padding: 6px; text-align: center; width: 18%;">Semester</th>
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
                    <tr style="background: white;">
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">
                            {{ $activity->start_date->format('d M Y') }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">
                            {{ $activity->end_date->format('d M Y') }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 5px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">{{ $activity->name }}</div>
                            @if($activity->description)
                                <div style="font-size: 7px; color: #666;">{{ $activity->description }}</div>
                            @endif
                        </td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">
                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; color: white; font-weight: bold; font-size: 7px; background-color: {{ $activity->activityType->default_color }};">
                                {{ $activity->activityType->name }}
                            </span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-weight: bold;">
                            {{ $activity->semester->name }}
                        </td>
                    </tr>
                @endforeach
                
                @if($allActivities->count() === 0)
                    <tr>
                        <td colspan="6" style="border: 1px solid #ddd; padding: 20px; text-align: center; color: #999;">
                            Belum ada kegiatan yang terdaftar
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>

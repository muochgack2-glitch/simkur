<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Pendidikan {{ $academicYear->year }}</title>
    <style>
        @page {
            size: 215mm 330mm;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.4;
            color: #111827;
            margin: 15px;
        }
        
        /* Page breaks */
        .page-break {
            page-break-before: always;
        }
        
        /* HALAMAN 1 - KALENDER */
        .page-1 {
            padding: 10mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        
        .header-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 8px;
            object-fit: contain;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #1f2937;
        }
        
        .header p {
            font-size: 10pt;
            margin-bottom: 4px;
            color: #6b7280;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 6px;
            text-transform: uppercase;
            color: #1f2937;
        }
        
        /* Calendar Grid - 2 columns per row */
        .months-container {
            width: 100%;
        }
        
        .month-row {
            width: 100%;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .month-row::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .month-box {
            width: 48%;
            float: left;
            margin-right: 2%;
            border: 1px solid #d1d5db;
            padding: 5px;
            background: #ffffff;
            page-break-inside: avoid;
        }
        
        .month-box:nth-child(2) {
            margin-right: 0;
        }
        
        .month-header {
            background: #2563eb;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            padding: 4px;
            margin-bottom: 5px;
        }
        
        .month-body {
            padding: 0;
        }
        
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        .day-header-cell {
            background: #f3f4f6;
            font-weight: bold;
            text-align: center;
            font-size: 7pt;
            padding: 2px;
            border: 1px solid #d1d5db;
        }
        
        .day-cell {
            border: 1px solid #e5e7eb;
            padding: 2px;
            height: 20px;
            vertical-align: top;
            font-size: 7pt;
            position: relative;
        }
        
        .day-cell.weekend {
            background: #fef2f2;
        }
        
        .day-cell.empty {
            background: #f9fafb;
        }
        
        .day-cell.other-month {
            background: #f9fafb;
            color: #9ca3af;
        }
        
        .day-number {
            font-weight: bold;
            margin-bottom: 1px;
            font-size: 7pt;
        }
        
        .activity-indicators {
            margin-top: 1px;
        }
        
        .activity-bar {
            width: 100%;
            height: 3px;
            margin-bottom: 1px;
            border-radius: 1px;
        }
        
        .activity-list {
            margin-top: 3px;
            padding: 4px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            font-size: 7pt;
        }
        
        .activity-list-item {
            margin-bottom: 2px;
            padding-left: 2px;
        }
        
        .activity-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-right: 3px;
            vertical-align: middle;
            border: 1px solid #9ca3af;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
        
        /* HALAMAN 2 - DAFTAR KEGIATAN */
        .page-2 {
            padding: 10mm;
            padding-top: 3mm;
            padding-bottom: 3mm;
        }
        
        .page-2 .header {
            margin-bottom: 10px;
        }
        
        .page-2 .header h1 {
            font-size: 14pt;
        }
        
        /* Two column layout */
        .activities-row {
            width: 100%;
            margin-bottom: 0;
        }
        
        .activities-row::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .activities-column {
            width: 48%;
            float: left;
        }
        
        .activities-column:first-child {
            margin-right: 4%;
        }
        
        .kegiatan-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 0;
            page-break-inside: avoid;
        }
        
        .kegiatan-table th {
            background: #2563eb;
            color: white;
            border: 0.5pt solid #1e40af;
            padding: 5px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 7.5pt;
        }
        
        .kegiatan-table td {
            border: 0.5pt solid #d1d5db;
            padding: 4px 5px;
            font-size: 7pt;
            line-height: 1.4;
        }
        
        .kegiatan-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .kegiatan-table tbody tr:nth-child(odd) {
            background: white;
        }
        
        .kegiatan-name {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 7.5pt;
        }
        
        .kegiatan-desc {
            font-size: 6.5pt;
            color: #6b7280;
            font-style: italic;
        }
        
        /* Signature */
        .signature-block {
            margin-top: 2px;
            text-align: right;
            padding-right: 50px;
        }
        
        .signature-content {
            display: inline-block;
            text-align: center;
            min-width: 180px;
        }
        
        .signature-place-date {
            margin-bottom: 4px;
            font-size: 9pt;
        }
        
        .signature-title {
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 9pt;
        }
        
        .signature-space {
            height: 90px;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 10pt;
            text-decoration: underline;
        }
        
        .signature-niy {
            font-size: 9pt;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: KALENDER 12 BULAN -->
    <div class="page-1">
        <!-- Header -->
        <div class="header">
            @if($schoolLogo)
                <img src="{{ public_path($schoolLogo) }}" alt="Logo" class="header-logo">
            @endif
            <h1>{{ strtoupper($schoolName) }}</h1>
            <p>{{ $schoolAddress }}</p>
            <h2>KALENDER PENDIDIKAN TAHUN AJARAN {{ $academicYear->year }}</h2>
            <p style="font-size: 9pt; margin-top: 3px;">{{ \Carbon\Carbon::parse($academicYear->start_date)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($academicYear->end_date)->format('d F Y') }}</p>
        </div>

        <!-- Months Grid (2 columns × 6 rows) -->
        <div class="months-container">
            @foreach(array_chunk($months, 2) as $rowIndex => $chunk)
                <div class="month-row">
                    @foreach($chunk as $month)
                        <div class="month-box">
                            <div class="month-header">
                                {{ strtoupper($month['name']) }} {{ $month['year'] }}
                            </div>
                            
                            <div class="month-body">
                                <!-- Calendar Table -->
                                <table class="calendar-table">
                                    <tr>
                                        <td class="day-header-cell">Sen</td>
                                        <td class="day-header-cell">Sel</td>
                                        <td class="day-header-cell">Rab</td>
                                        <td class="day-header-cell">Kam</td>
                                        <td class="day-header-cell">Jum</td>
                                        <td class="day-header-cell">Sab</td>
                                        <td class="day-header-cell">Min</td>
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
                                                <td class="day-cell {{ $day ? ($day['isWeekend'] ? 'weekend' : '') : 'other-month' }}">
                                                    @if($day)
                                                        <div class="day-number">{{ $day['date'] }}</div>
                                                        @if($day['hasActivity'])
                                                            <div class="activity-indicators">
                                                                @foreach($day['activities']->take(3) as $activity)
                                                                    <div class="activity-bar" style="background-color: {{ $activity->color }};" title="{{ $activity->name }}"></div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                                
                                <!-- Activity List for this month -->
                                @if($month['activities']->count() > 0)
                                    <div class="activity-list">
                                        @foreach($month['activities']->take(5) as $activity)
                                            <div class="activity-list-item">
                                                <span class="activity-dot" style="background-color: {{ $activity->color }};"></span>
                                                <span>{{ $activity->start_date->format('d/m') }}: {{ $activity->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($rowIndex == 2)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB | e-KALDIK</p>
        </div>
        
        <!-- Signature for Page 1 -->
        @php
            // Use new signature settings
            $fullName = $signatureName;
            if (!empty($signatureDegree)) {
                $fullName .= ', ' . $signatureDegree;
            }
        @endphp
        
        <div class="signature-block">
            <div class="signature-content">
                <div class="signature-place-date">{{ $signatureCity }}, {{ $signatureDate }}</div>
                <div class="signature-title">{{ $signaturePosition }}</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $fullName }}</div>
                <div class="signature-niy">NIY. {{ $signatureNiy }}</div>
            </div>
        </div>
    </div>

    <!-- HALAMAN 2: DAFTAR KEGIATAN -->
    <div class="page-2 page-break">
        <!-- Header -->
        <div class="header">
            <h1>DAFTAR KEGIATAN TAHUN AJARAN {{ $academicYear->year }}</h1>
        </div>

        <!-- Activities in 2 columns: Semester Ganjil (Odd) and Semester Genap (Even) -->
        @php
            // Get semester Ganjil (odd)
            $semesterGanjil = $academicYear->semesters->where('type', 'ganjil')->first();
            $activitiesSemester1 = \App\Models\Activity::with(['activityType'])
                ->where('academic_year_id', $academicYear->id)
                ->where('semester_id', $semesterGanjil?->id)
                ->orderBy('start_date')
                ->orderBy('name')
                ->get();
            
            // Get semester Genap (even)
            $semesterGenap = $academicYear->semesters->where('type', 'genap')->first();
            $activitiesSemester2 = \App\Models\Activity::with(['activityType'])
                ->where('academic_year_id', $academicYear->id)
                ->where('semester_id', $semesterGenap?->id)
                ->orderBy('start_date')
                ->orderBy('name')
                ->get();
        @endphp
        
        <div class="activities-row">
            <!-- Column 1: Semester Ganjil -->
            <div class="activities-column">
                <table class="kegiatan-table">
                    <thead>
                        <tr>
                            <th colspan="3" style="background: #059669; color: white; font-size: 8.5pt; padding: 6px;">
                                SEMESTER GANJIL
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th style="width: 28%;">Tanggal</th>
                            <th style="width: 64%;">Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activitiesSemester1 as $index => $activity)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                                <td style="text-align: center;">
                                    @if($activity->start_date->equalTo($activity->end_date))
                                        {{ $activity->start_date->format('d/m/y') }}
                                    @else
                                        {{ $activity->start_date->format('d/m/y') }}<br>{{ $activity->end_date->format('d/m/y') }}
                                    @endif
                                </td>
                                <td>
                                    <div class="kegiatan-name">{{ $activity->name }}</div>
                                    @if($activity->description)
                                        <div class="kegiatan-desc">{{ Str::limit($activity->description, 45) }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 10px; color: #9ca3af;">
                                    Belum ada kegiatan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Signature for Page 2 - Di bawah kolom kiri -->
                @php
                    // Use new signature settings
                    $fullName = $signatureName;
                    if (!empty($signatureDegree)) {
                        $fullName .= ', ' . $signatureDegree;
                    }
                @endphp
                
                <div style="margin-top: 60px; text-align: left;">
                    <div style="display: inline-block; text-align: center; min-width: 180px;">
                        <div style="margin-bottom: 4px; font-size: 9pt;">{{ $signatureCity }}, {{ $signatureDate }}</div>
                        <div style="margin-bottom: 4px; font-weight: bold; font-size: 9pt;">{{ $signaturePosition }}</div>
                        <div style="height: 90px;"></div>
                        <div style="font-weight: bold; font-size: 10pt; text-decoration: underline;">{{ $fullName }}</div>
                        <div style="font-size: 9pt; margin-top: 2px;">NIY. {{ $signatureNiy }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Column 2: Semester Genap -->
            <div class="activities-column">
                <table class="kegiatan-table">
                    <thead>
                        <tr>
                            <th colspan="3" style="background: #dc2626; color: white; font-size: 8.5pt; padding: 6px;">
                                SEMESTER GENAP
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th style="width: 28%;">Tanggal</th>
                            <th style="width: 64%;">Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activitiesSemester2 as $index => $activity)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                                <td style="text-align: center;">
                                    @if($activity->start_date->equalTo($activity->end_date))
                                        {{ $activity->start_date->format('d/m/y') }}
                                    @else
                                        {{ $activity->start_date->format('d/m/y') }}<br>{{ $activity->end_date->format('d/m/y') }}
                                    @endif
                                </td>
                                <td>
                                    <div class="kegiatan-name">{{ $activity->name }}</div>
                                    @if($activity->description)
                                        <div class="kegiatan-desc">{{ Str::limit($activity->description, 45) }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 10px; color: #9ca3af;">
                                    Belum ada kegiatan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($activitiesSemester1->count() === 0 && $activitiesSemester2->count() === 0)
            <p style="text-align: center; padding: 30px; color: #9ca3af; font-size: 9pt;">
                Belum ada kegiatan yang terdaftar
            </p>
        @endif
    </div>
</body>
</html>

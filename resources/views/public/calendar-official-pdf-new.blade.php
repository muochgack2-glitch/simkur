<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Pendidikan {{ $academicYear->year }}</title>
    <style>
        @page {
            size: F4 landscape;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #111827;
            position: relative;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }
        
        .watermark-text {
            position: absolute;
            font-size: 60pt;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.05);
            transform: rotate(-45deg);
            white-space: nowrap;
            font-family: Arial, sans-serif;
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
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 8px;
            object-fit: contain;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .header p {
            font-size: 12pt;
            margin-bottom: 8px;
        }
        
        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 8px;
            text-transform: uppercase;
        }
        
        /* Calendar Grid - 4 columns */
        .calendar-grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .month-box {
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        .month-header {
            background: linear-gradient(to right, #2563eb, #1e40af);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            padding: 6px 4px;
        }
        
        .month-body {
            padding: 4px;
        }
        
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        
        .day-header-cell {
            background: #f3f4f6;
            font-weight: bold;
            text-align: center;
            font-size: 7pt;
            padding: 2px;
            border: 0.5px solid #e5e7eb;
        }
        
        .day-cell {
            text-align: left;
            font-size: 8pt;
            padding: 2px 3px;
            border: 0.5px solid #e5e7eb;
            height: 24px;
            vertical-align: top;
            position: relative;
        }
        
        .day-cell.weekend {
            background: #f9fafb;
        }
        
        .day-cell.empty {
            background: #f3f4f6;
        }
        
        .activity-indicators {
            margin-top: 2px;
        }
        
        .activity-bar {
            height: 2px;
            margin: 1px 0;
        }
        
        .activity-list {
            border-top: 1px solid #e5e7eb;
            padding-top: 3px;
            font-size: 7pt;
        }
        
        .activity-list-item {
            margin-bottom: 1px;
            display: flex;
            align-items: flex-start;
            gap: 3px;
        }
        
        .activity-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        /* HALAMAN 2 - DAFTAR KEGIATAN */
        .page-2 {
            padding: 10mm;
        }
        
        .page-2 .header {
            margin-bottom: 12px;
        }
        
        .page-2 .header h1 {
            font-size: 16pt;
        }
        
        .kegiatan-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 10px;
        }
        
        .kegiatan-table th {
            background: linear-gradient(to right, #2563eb, #1e40af);
            color: white;
            border: 1pt solid #1e40af;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
        }
        
        .kegiatan-table td {
            border: 1pt solid #d1d5db;
            padding: 6px 8px;
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
        }
        
        .kegiatan-desc {
            font-size: 8pt;
            color: #6b7280;
            font-style: italic;
        }
        
        /* Signature */
        .signature-block {
            margin-top: 30px;
            text-align: right;
        }
        
        .signature-content {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .signature-place-date {
            margin-bottom: 5px;
        }
        
        .signature-title {
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .signature-space {
            height: 60px;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 11pt;
            text-decoration: underline;
        }
        
        .signature-niy {
            font-size: 10pt;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">
        @for($y = -100; $y < 800; $y += 150)
            @for($x = -200; $x < 1200; $x += 300)
                <div class="watermark-text" style="top: {{ $y }}px; left: {{ $x }}px;">
                    {{ strtoupper($schoolName) }}
                </div>
            @endfor
        @endfor
    </div>

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
        </div>

        <!-- Calendar Grid 12 Months (4 columns × 3 rows) -->
        @for($row = 0; $row < 3; $row++)
            <div class="calendar-grid-container">
                @for($col = 0; $col < 4; $col++)
                    @php
                        $index = $row * 4 + $col;
                        $month = $months[$index] ?? null;
                    @endphp
                    
                    @if($month)
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
                                                <td class="day-cell {{ $day ? ($day['isWeekend'] ? 'weekend' : '') : 'empty' }}">
                                                    @if($day)
                                                        {{ $day['date'] }}
                                                        @if($day['hasActivity'])
                                                            <div class="activity-indicators">
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
                                
                                <!-- Activity List for this month -->
                                @if($month['activities']->count() > 0)
                                    <div class="activity-list">
                                        @foreach($month['activities']->take(5) as $activity)
                                            <div class="activity-list-item">
                                                <span class="activity-dot" style="background-color: {{ $activity->color }};"></span>
                                                <span><strong>{{ $activity->start_date->format('d/m') }}</strong>: {{ $activity->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>

    <!-- HALAMAN 2: DAFTAR KEGIATAN -->
    <div class="page-2 page-break">
        <!-- Watermark (repeat on page 2) -->
        <div class="watermark">
            @for($y = -100; $y < 800; $y += 150)
                @for($x = -200; $x < 1200; $x += 300)
                    <div class="watermark-text" style="top: {{ $y }}px; left: {{ $x }}px;">
                        {{ strtoupper($schoolName) }}
                    </div>
                @endfor
            @endfor
        </div>
        
        <!-- Header -->
        <div class="header">
            <h1>DAFTAR KEGIATAN TAHUN AJARAN {{ $academicYear->year }}</h1>
        </div>

        <!-- Tabel Kegiatan -->
        <table class="kegiatan-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Tanggal, Bulan, Tahun</th>
                    <th style="width: 70%;">Nama Kegiatan</th>
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
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">
                            @if($activity->start_date->equalTo($activity->end_date))
                                {{ $activity->start_date->format('d/m/y') }}<br>
                                <span style="font-size: 7pt; color: #6b7280;">(1 hari)</span>
                            @else
                                {{ $activity->start_date->format('d/m/y') }} - {{ $activity->end_date->format('d/m/y') }}
                            @endif
                        </td>
                        <td>
                            <div class="kegiatan-name">{{ $activity->name }}</div>
                            @if($activity->description)
                                <div class="kegiatan-desc">{{ $activity->description }}</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                @if($allActivities->count() === 0)
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 30px; color: #9ca3af;">
                            Belum ada kegiatan yang terdaftar
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Signature -->
        @php
            // Extract city from address
            $addressParts = explode(',', $schoolAddress);
            $city = trim(end($addressParts));
            
            // Get month and year from academic year end date
            $signatureDate = \Carbon\Carbon::parse($academicYear->end_date)->locale('id')->isoFormat('MMMM YYYY');
        @endphp
        
        <div class="signature-block">
            <div class="signature-content">
                <div class="signature-place-date">{{ $city }}, {{ $signatureDate }}</div>
                <div class="signature-title">Kepala Sekolah</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $principalName }}</div>
                <div class="signature-niy">NIY. {{ $principalNiy }}</div>
            </div>
        </div>
    </div>
</body>
</html>

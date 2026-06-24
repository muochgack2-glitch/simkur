<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kalender Tahunan {{ $academicYear->year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 8px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #1F2937;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #6B7280;
        }
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
            border: 1px solid #D1D5DB;
            padding: 5px;
            background: #FFFFFF;
        }
        .month-box:nth-child(2) {
            margin-right: 0;
        }
        .month-name {
            font-weight: bold;
            font-size: 11px;
            color: white;
            text-align: center;
            background: #2563EB;
            padding: 4px;
            margin-bottom: 5px;
        }
        
        /* Calendar Grid */
        .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .calendar th {
            background: #F3F4F6;
            padding: 2px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #D1D5DB;
        }
        .calendar td {
            border: 1px solid #E5E7EB;
            padding: 2px;
            height: 20px;
            vertical-align: top;
            font-size: 7px;
            position: relative;
        }
        .calendar td.other-month {
            background: #F9FAFB;
            color: #9CA3AF;
        }
        .calendar td.weekend {
            background: #FEF2F2;
        }
        .day-number {
            font-weight: bold;
            margin-bottom: 1px;
            font-size: 7px;
        }
        .day-activity {
            width: 100%;
            height: 3px;
            margin-bottom: 1px;
            border-radius: 1px;
        }
        
        /* Activity Legend */
        .activity-legend {
            margin-top: 3px;
            padding: 4px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            font-size: 7px;
        }
        .legend-item {
            margin-bottom: 2px;
            padding-left: 2px;
        }
        .legend-color {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-right: 3px;
            vertical-align: middle;
            border: 1px solid #9CA3AF;
        }
        .legend-text {
            vertical-align: middle;
        }
        
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #6B7280;
            border-top: 1px solid #E5E7EB;
            padding-top: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $schoolName }}</h1>
        <p>Kalender Pendidikan Tahun Pelajaran {{ $academicYear->year }}</p>
        <p style="font-size: 9px;">{{ \Carbon\Carbon::parse($academicYear->start_date)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($academicYear->end_date)->format('d F Y') }}</p>
    </div>

    <!-- Months Grid -->
    <div class="months-container">
        @foreach($months->chunk(2) as $rowIndex => $chunk)
            <div class="month-row">
                @foreach($chunk as $monthData)
                    <div class="month-box">
                        <div class="month-name">{{ $monthData['name'] }}</div>
                        
                        <!-- Calendar Grid -->
                        <table class="calendar">
                            <thead>
                                <tr>
                                    <th>Sen</th>
                                    <th>Sel</th>
                                    <th>Rab</th>
                                    <th>Kam</th>
                                    <th>Jum</th>
                                    <th>Sab</th>
                                    <th>Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthData['calendar'] as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            <td class="{{ !$day['isCurrentMonth'] ? 'other-month' : '' }} {{ $day['isWeekend'] ? 'weekend' : '' }}">
                                                <div class="day-number">{{ $day['day'] }}</div>
                                                @foreach($day['activities'] as $activity)
                                                    <div class="day-activity" style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};" title="{{ $activity->name }}"></div>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Activity Legend for this month -->
                        @if($monthData['activities']->count() > 0)
                            <div class="activity-legend">
                                @foreach($monthData['activities'] as $activity)
                                    <div class="legend-item">
                                        <span class="legend-color" style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};"></span>
                                        <span class="legend-text">{{ \Carbon\Carbon::parse($activity->start_date)->format('d/m') }}: {{ $activity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
        <p>Dicetak pada: {{ $generatedAt }} | e-KALDIK - Kalender Pendidikan Digital</p>
    </div>
</body>
</html>

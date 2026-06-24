<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kalender {{ $monthName }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 8px;
        }
        .header h1 {
            margin: 3px 0;
            font-size: 18px;
            color: #1F2937;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #6B7280;
        }
        .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .calendar th {
            background: #2563EB;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #1E40AF;
        }
        .calendar td {
            border: 1px solid #D1D5DB;
            padding: 5px;
            vertical-align: top;
            height: 85px;
            width: 14.28%;
        }
        .date-number {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 3px;
            color: #1F2937;
        }
        .current-month {
            background: white;
        }
        .other-month {
            background: #F3F4F6;
        }
        .other-month .date-number {
            color: #9CA3AF;
        }
        .weekend {
            background: #FEF2F2;
        }
        .activity {
            font-size: 7px;
            padding: 2px 4px;
            margin: 2px 0;
            border-radius: 2px;
            white-space: normal;
            overflow: visible;
            word-wrap: break-word;
            line-height: 1.2;
            color: white;
            font-weight: bold;
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
        .activity-list {
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .activity-list-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            color: #1F2937;
            padding: 5px;
            background: #EFF6FF;
            border-left: 4px solid #2563EB;
        }
        .activity-list-item {
            padding: 6px 8px;
            margin: 4px 0;
            border-left: 4px solid;
            background: #F9FAFB;
            font-size: 9px;
        }
        .activity-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            color: white;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $schoolName }}</h1>
        <p>Kalender Kegiatan Bulan {{ $monthName }}</p>
    </div>

    <!-- Calendar Grid -->
    <table class="calendar">
        <thead>
            <tr>
                <th>Senin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Kamis</th>
                <th>Jumat</th>
                <th>Sabtu</th>
                <th>Minggu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calendar as $week)
                <tr>
                    @foreach($week as $day)
                        <td class="{{ $day['isCurrentMonth'] ? 'current-month' : 'other-month' }} {{ $day['isWeekend'] ? 'weekend' : '' }}">
                            <div class="date-number">{{ $day['day'] }}</div>
                            @foreach($day['activities'] as $activity)
                                <div class="activity" style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};" title="{{ $activity->name }}">
                                    {{ $activity->name }}
                                </div>
                            @endforeach
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Activity List -->
    @if($activities->isNotEmpty())
        <div class="activity-list">
            <div class="activity-list-title">Daftar Kegiatan Bulan Ini</div>
            @foreach($activities as $activity)
                <div class="activity-list-item" style="border-left-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};">
                    <strong>{{ \Carbon\Carbon::parse($activity->start_date)->format('d M Y') }}</strong>
                    @if($activity->start_date !== $activity->end_date)
                        - <strong>{{ \Carbon\Carbon::parse($activity->end_date)->format('d M Y') }}</strong>
                    @endif
                    : {{ $activity->name }}
                    <span class="activity-type" style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }};">
                        {{ $activity->activityType->name }}
                    </span>
                    @if($activity->description)
                        <br><span style="color: #6B7280; font-size: 8px; margin-left: 10px;">{{ $activity->description }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ $generatedAt }} | e-KALDIK - Kalender Pendidikan Digital</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Kegiatan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 18px;
            color: #1F2937;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
            color: #6B7280;
        }
        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background: #F3F4F6;
            border-radius: 4px;
        }
        .filters p {
            margin: 3px 0;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #2563EB;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #1E40AF;
        }
        td {
            border: 1px solid #D1D5DB;
            padding: 6px 5px;
            font-size: 9px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background: #F9FAFB;
        }
        tr:hover {
            background: #EFF6FF;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #6B7280;
            border-top: 1px solid #E5E7EB;
            padding-top: 10px;
        }
        .color-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            color: white;
            font-weight: bold;
            white-space: nowrap;
        }
        .summary {
            margin-top: 15px;
            padding: 12px;
            background: #EFF6FF;
            border: 2px solid #2563EB;
            border-radius: 4px;
        }
        .summary p {
            margin: 4px 0;
            font-size: 10px;
        }
        .summary strong {
            color: #1F2937;
        }
        .activity-name {
            font-weight: bold;
            color: #1F2937;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $schoolName }}</h1>
        <p>Daftar Kegiatan Kalender Pendidikan</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <p><strong>Total Kegiatan:</strong> {{ $activities->count() }} kegiatan</p>
        @if($activities->isNotEmpty())
            <p><strong>Periode:</strong> 
                {{ \Carbon\Carbon::parse($activities->first()->start_date)->locale('id')->isoFormat('DD MMMM YYYY') }} - 
                {{ \Carbon\Carbon::parse($activities->last()->end_date)->locale('id')->isoFormat('DD MMMM YYYY') }}
            </p>
        @endif
        @if(!empty($filters['academic_year_id']))
            <p><strong>Tahun Pelajaran:</strong> {{ $activities->first()->academicYear->year ?? '-' }}</p>
        @endif
    </div>

    <!-- Activity Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 30%;">Nama Kegiatan</th>
                <th style="width: 15%;">Jenis Kegiatan</th>
                <th style="width: 13%;">Tanggal Mulai</th>
                <th style="width: 13%;">Tanggal Selesai</th>
                <th style="width: 10%;">Semester</th>
                <th style="width: 14%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $index => $activity)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>
                        <span class="activity-name">{{ $activity->name }}</span>
                    </td>
                    <td>
                        <span class="color-badge" style="background-color: {{ $activity->activityType->default_color ?? '#6B7280' }};">
                            {{ $activity->activityType->name ?? '-' }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($activity->start_date)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($activity->end_date)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                    <td style="text-align: center;">{{ $activity->semester->name ?? '-' }}</td>
                    <td style="font-size: 8px; color: #6B7280;">{{ Str::limit($activity->description ?? '-', 50) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #9CA3AF; padding: 20px;">
                        Tidak ada data kegiatan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ $generatedAt }} | e-KALDIK - Kalender Pendidikan Digital</p>
    </div>
</body>
</html>

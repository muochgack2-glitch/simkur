<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Mengajar - {{ $teacher->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 3px 0;
            color: #666;
        }
        .teacher-info {
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 3px solid #2196F3;
        }
        .journal-entry {
            margin-bottom: 25px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 12px;
        }
        .journal-header {
            background: #4CAF50;
            color: white;
            padding: 8px;
            margin: -12px -12px 10px -12px;
            font-weight: bold;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 25%;
            padding: 4px;
            font-weight: bold;
        }
        .info-value {
            display: table-cell;
            padding: 4px;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        .attendance-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
        .status-hadir { color: #4CAF50; }
        .status-sakit { color: #FF9800; }
        .status-izin { color: #2196F3; }
        .status-alpha { color: #F44336; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK PGRI BLORA</h2>
        <h2>JURNAL MENGAJAR</h2>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <div class="teacher-info">
        <strong>Guru:</strong> {{ $teacher->name }}<br>
        <strong>NIP/NUPTK:</strong> {{ $teacher->nip_nuptk ?? '-' }}<br>
        <strong>Total Jurnal:</strong> {{ $journals->count() }} jurnal
    </div>

    @forelse($journals as $index => $journal)
        <div class="journal-entry">
            <div class="journal-header">
                Jurnal #{{ $index + 1 }} - {{ $journal->date->format('d F Y') }}
            </div>
            
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tanggal:</div>
                    <div class="info-value">{{ $journal->date->format('l, d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jam Pelajaran:</div>
                    <div class="info-value">{{ $journal->time_slot }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kelas:</div>
                    <div class="info-value">{{ $journal->schoolClass->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Mata Pelajaran:</div>
                    <div class="info-value">{{ $journal->subject->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tujuan Pembelajaran:</div>
                    <div class="info-value">{{ $journal->learning_objective ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Materi Pokok:</div>
                    <div class="info-value">{{ $journal->topic }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Metode Mengajar:</div>
                    <div class="info-value">{{ $journal->teaching_method ?? '-' }}</div>
                </div>
                @if($journal->notes)
                    <div class="info-row">
                        <div class="info-label">Catatan:</div>
                        <div class="info-value">{{ $journal->notes }}</div>
                    </div>
                @endif
            </div>

            <strong style="margin-top: 10px; display: block;">Daftar Hadir Siswa:</strong>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">NIS</th>
                        <th width="50%">Nama Siswa</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journal->attendances as $i => $attendance)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $attendance->student->nisn ?? '-' }}</td>
                            <td>{{ $attendance->student->name }}</td>
                            <td class="text-center status-{{ $attendance->status }}">
                                @switch($attendance->status)
                                    @case('hadir') <strong>✓ Hadir</strong> @break
                                    @case('sakit') <strong>⚠ Sakit</strong> @break
                                    @case('izin') <strong>ⓘ Izin</strong> @break
                                    @case('alpha') <strong>✗ Alpha</strong> @break
                                @endswitch
                            </td>
                            <td>{{ $attendance->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background: #f9f9f9;">
                        <td colspan="3" class="text-center">RINGKASAN KEHADIRAN</td>
                        <td colspan="2">
                            <span class="status-hadir">Hadir: {{ $journal->present_count }}</span> | 
                            <span class="status-sakit">Sakit: {{ $journal->sick_count }}</span> | 
                            <span class="status-izin">Izin: {{ $journal->permission_count }}</span> | 
                            <span class="status-alpha">Alpha: {{ $journal->absent_count }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <p style="text-align: center; padding: 20px; color: #999;">
            Tidak ada jurnal dalam periode ini
        </p>
    @endforelse

    <div class="footer">
        Dicetak: {{ $generatedAt->format('d F Y, H:i') }} WIB<br>
        SIM Kurikulum SMK PGRI Blora
    </div>
</body>
</html>

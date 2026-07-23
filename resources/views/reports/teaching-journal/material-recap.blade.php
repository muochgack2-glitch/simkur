<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Materi Ajar</title>
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
        .class-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .class-header {
            background: #4CAF50;
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
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
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK PGRI BLORA</h2>
        <h2>LAPORAN REKAP MATERI AJAR</h2>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    @foreach($materialsGrouped as $classSubject => $journals)
        <div class="class-section">
            <div class="class-header">
                {{ $classSubject }}
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="8%">Jam</th>
                        <th width="20%">Guru</th>
                        <th width="25%">Tujuan Pembelajaran</th>
                        <th width="30%">Materi Pokok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journals as $index => $journal)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $journal->date->format('d/m/Y') }}</td>
                            <td>{{ $journal->time_slot }}</td>
                            <td>{{ $journal->teacher->name }}</td>
                            <td>{{ $journal->learning_objective ?? '-' }}</td>
                            <td>{{ $journal->topic }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($materialsGrouped->isEmpty())
        <p style="text-align: center; padding: 20px; color: #999;">
            Tidak ada data materi ajar dalam periode ini
        </p>
    @endif

    <div class="footer">
        Dicetak: {{ $generatedAt->format('d F Y, H:i') }} WIB<br>
        SIM Kurikulum SMK PGRI Blora
    </div>
</body>
</html>

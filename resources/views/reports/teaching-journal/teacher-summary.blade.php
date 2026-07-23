<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Jurnal Per Guru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
        .summary-box {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-left: 3px solid #4CAF50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK PGRI BLORA</h2>
        <h2>LAPORAN REKAP JURNAL MENGAJAR PER GURU</h2>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="25%">Nama Guru</th>
                <th width="10%" class="text-center">Total Jurnal</th>
                <th width="10%" class="text-center">Total JP</th>
                <th width="25%">Kelas yang Diampu</th>
                <th width="25%">Mata Pelajaran</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalJournals = 0; $totalHours = 0; @endphp
            @foreach($summary as $data)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $data['teacher']->name }}</td>
                    <td class="text-center">{{ $data['total_journals'] }}</td>
                    <td class="text-center">{{ $data['total_hours'] }}</td>
                    <td>{{ $data['classes']->implode(', ') ?: '-' }}</td>
                    <td>{{ $data['subjects']->implode(', ') ?: '-' }}</td>
                </tr>
                @php 
                    $totalJournals += $data['total_journals'];
                    $totalHours += $data['total_hours'];
                @endphp
            @endforeach
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-center">{{ $totalJournals }}</td>
                <td class="text-center">{{ $totalHours }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        Total Guru: {{ $summary->count() }} orang<br>
        Total Jurnal: {{ $totalJournals }} jurnal<br>
        Total Jam Pelajaran: {{ $totalHours }} JP<br>
        Rata-rata Jurnal per Guru: {{ $summary->count() > 0 ? round($totalJournals / $summary->count(), 1) : 0 }} jurnal
    </div>

    <div class="footer">
        Dicetak: {{ $generatedAt->format('d F Y, H:i') }} WIB<br>
        SIM Kurikulum SMK PGRI Blora
    </div>
</body>
</html>

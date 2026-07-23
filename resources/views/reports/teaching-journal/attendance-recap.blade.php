<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Kehadiran Siswa</title>
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
        .legend {
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 3px solid #2196F3;
        }
        .status-hadir { color: #4CAF50; font-weight: bold; }
        .status-sakit { color: #FF9800; font-weight: bold; }
        .status-izin { color: #2196F3; font-weight: bold; }
        .status-alpha { color: #F44336; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK PGRI BLORA</h2>
        <h2>LAPORAN REKAP KEHADIRAN SISWA</h2>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
        <p>Kelas: {{ $className }}</p>
    </div>

    <div class="legend">
        <strong>Keterangan:</strong> 
        <span class="status-hadir">H</span> = Hadir | 
        <span class="status-sakit">S</span> = Sakit | 
        <span class="status-izin">I</span> = Izin | 
        <span class="status-alpha">A</span> = Alpha
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="10%">NIS</th>
                <th width="25%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="8%" class="text-center status-hadir">H</th>
                <th width="8%" class="text-center status-sakit">S</th>
                <th width="8%" class="text-center status-izin">I</th>
                <th width="8%" class="text-center status-alpha">A</th>
                <th width="8%" class="text-center">Total</th>
                <th width="10%" class="text-center">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($attendanceData as $data)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $data['student']->nisn ?? '-' }}</td>
                    <td>{{ $data['student']->name }}</td>
                    <td>{{ $data['student']->schoolClass->name ?? '-' }}</td>
                    <td class="text-center status-hadir">{{ $data['hadir'] }}</td>
                    <td class="text-center status-sakit">{{ $data['sakit'] }}</td>
                    <td class="text-center status-izin">{{ $data['izin'] }}</td>
                    <td class="text-center status-alpha">{{ $data['alpha'] }}</td>
                    <td class="text-center">{{ $data['total'] }}</td>
                    <td class="text-center">
                        {{ $data['total'] > 0 ? round(($data['hadir'] / $data['total']) * 100, 1) : 0 }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data kehadiran</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak: {{ $generatedAt->format('d F Y, H:i') }} WIB<br>
        SIM Kurikulum SMK PGRI Blora
    </div>
</body>
</html>

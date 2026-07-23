<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Jurnal Kosong</title>
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
        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
        }
        .status-low {
            background: #ffebee;
        }
        .status-medium {
            background: #fff3e0;
        }
        .status-good {
            background: #e8f5e9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK PGRI BLORA</h2>
        <h2>LAPORAN MONITORING JURNAL MENGAJAR</h2>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <div class="alert-box">
        <strong>⚠️ Perhatian:</strong> Laporan ini menampilkan guru yang jurnal mengajarnya SEDIKIT atau BELUM mengisi dalam periode yang dipilih. Data diurutkan dari yang paling sedikit ke yang paling banyak.
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="10%" class="text-center">NIP/NUPTK</th>
                <th width="30%">Nama Guru</th>
                <th width="35%">Mata Pelajaran</th>
                <th width="10%" class="text-center">Total Jurnal</th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($missingData as $data)
                <tr class="
                    @if($data['journal_count'] == 0) status-low
                    @elseif($data['journal_count'] < 5) status-medium
                    @else status-good
                    @endif
                ">
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $data['teacher']->nip_nuptk ?? '-' }}</td>
                    <td>{{ $data['teacher']->name }}</td>
                    <td>{{ $data['subjects'] ?: '-' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $data['journal_count'] }}</td>
                    <td class="text-center">
                        @if($data['journal_count'] == 0)
                            <strong style="color: #d32f2f;">❌ Belum Isi</strong>
                        @elseif($data['journal_count'] < 5)
                            <strong style="color: #f57c00;">⚠️ Kurang</strong>
                        @else
                            <strong style="color: #388e3c;">✓ Baik</strong>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 20px; width: 50%;">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center;">RINGKASAN</th>
            </tr>
        </thead>
        <tbody>
            <tr class="status-low">
                <td>Guru Belum Mengisi (0 jurnal)</td>
                <td class="text-center" style="font-weight: bold;">
                    {{ $missingData->where('journal_count', 0)->count() }} orang
                </td>
            </tr>
            <tr class="status-medium">
                <td>Guru Kurang Mengisi (1-4 jurnal)</td>
                <td class="text-center" style="font-weight: bold;">
                    {{ $missingData->whereBetween('journal_count', [1, 4])->count() }} orang
                </td>
            </tr>
            <tr class="status-good">
                <td>Guru Sudah Baik (≥5 jurnal)</td>
                <td class="text-center" style="font-weight: bold;">
                    {{ $missingData->where('journal_count', '>=', 5)->count() }} orang
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak: {{ $generatedAt->format('d F Y, H:i') }} WIB<br>
        SIM Kurikulum SMK PGRI Blora
    </div>
</body>
</html>

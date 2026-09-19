<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor ASTS - {{ $student->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
        }

        .page {
            width: 215mm;
            /* min-height removed - konten menentukan tinggi */
            margin: 0 auto;
            padding: 10mm 15mm 15mm 20mm;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 12px;
        }

        .header-logo-placeholder {
            width: 70px;
            height: 70px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #666;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .header-text {
            flex: 1;
            text-align: center;
        }

        .header-text h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-text h2 {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-text p {
            font-size: 9pt;
            margin-top: 2px;
        }

        /* ===== JUDUL RAPOR ===== */
        .rapor-title {
            text-align: center;
            margin: 8px 0 6px;
        }

        .rapor-title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .rapor-title p {
            font-size: 10pt;
            margin-top: 2px;
        }

        /* ===== DATA SISWA ===== */
        .student-info {
            margin: 6px 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 20px;
        }

        .info-row {
            display: flex;
            gap: 4px;
            padding: 1px 0;
        }

        .info-label {
            width: 120px;
            flex-shrink: 0;
            font-size: 10.5pt;
        }

        .info-sep {
            flex-shrink: 0;
        }

        .info-value {
            font-size: 10.5pt;
            font-weight: bold;
        }

        /* ===== TABEL NILAI ===== */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            margin: 8px 0 4px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 3px 7px;
        }

        table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        td.center { text-align: center; }
        td.number { text-align: center; font-weight: bold; }

        .tuntas { color: #000; }
        .belum-tuntas { color: #000; font-style: italic; }

        /* ===== TTD ===== */
        .ttd-section {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
        }

        .ttd-box {
            text-align: center;
            width: 200px;
        }

        .ttd-box .ttd-title {
            font-size: 10.5pt;
            margin-bottom: 80px;
        }

        .ttd-box .ttd-name {
            font-weight: bold;
            font-size: 10.5pt;
            border-top: none;
            padding-top: 4px;
        }

        .ttd-box .ttd-nip {
            font-size: 9.5pt;
        }

        .catatan-section {
            margin-top: 16px;
            border: 1px solid #000;
            padding: 6px 10px;
        }

        .catatan-section .catatan-label {
            font-weight: bold;
            font-size: 10.5pt;
        }

        .catatan-section .catatan-content {
            min-height: 30px;
            font-size: 10.5pt;
        }

        /* ===== PRINT ===== */
        @media print {
            body { background: #fff; }
            .page { margin: 0; padding: 10mm 15mm 15mm 20mm; }
            .no-print { display: none !important; }

            @page {
                size: F4 portrait;
                margin: 0;
            }
        }

        /* Tombol cetak (hanya di layar) */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-family: sans-serif;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 999;
        }

        .print-btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
<button class="print-btn no-print" onclick="window.print()">🖨️ Cetak</button>

<div class="page">

    {{-- HEADER SEKOLAH --}}
    @if(!empty($kopSuratUrl))
    {{-- Kop surat gambar (upload dari halaman rapor) --}}
    <div class="kop-surat-img">
        <img src="{{ $kopSuratUrl }}" alt="Kop Surat" style="width:100%;display:block;">
    </div>
    @else
    {{-- Fallback: header teks dari setting sekolah --}}
    <div class="header">
        @if($schoolLogo && file_exists(public_path($schoolLogo)))
            <img src="{{ asset($schoolLogo) }}" class="header-logo" alt="Logo">
        @else
            <div class="header-logo-placeholder">LOGO</div>
        @endif
        <div class="header-text">
            <h1>{{ $schoolName }}</h1>
            @if($schoolAddress)
                <p>{{ $schoolAddress }}</p>
            @endif
            @if($schoolPhone)
                <p>Telp: {{ $schoolPhone }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- JUDUL --}}
    <div class="rapor-title">
        <h3>Rapor Asesmen Sumatif Tengah Semester (ASTS)</h3>
    {{-- DATA SISWA --}}
    <div class="student-info">
        <table>
            <colgroup>
                <col style="width:135px">
                <col style="width:12px">
                <col>
                <col style="width:20px">
                <col style="width:135px">
                <col style="width:12px">
                <col>
            </colgroup>
            <tr>
                <td class="si-label">Nama Siswa</td>
                <td class="si-sep">:</td>
                <td class="si-value">{{ $student->name }}</td>
                <td class="si-gap"></td>
                <td class="si-label">Kelas</td>
                <td class="si-sep">:</td>
                <td class="si-value">{{ $myClass->name }}</td>
            </tr>
            <tr>
                <td class="si-label">NIS</td>
                <td class="si-sep">:</td>
                <td class="si-value">{{ $student->nis ?? '-' }}</td>
                <td class="si-gap"></td>
                <td class="si-label">Semester</td>
                <td class="si-sep">:</td>
                <td class="si-value">{{ $semShort ?? ($semester?->name ?? '-') }}</td>
            </tr>
            <tr>
                <td class="si-label">NISN</td>
                <td class="si-sep">:</td>
                <td class="si-value">{{ $student->nisn ?? '-' }}</td>
                <td class="si-gap"></td>
                <td class="si-label">Tahun Pelajaran</td>
                <td class="si-sep">:</td>
                <td class="si-value">{{ $semester?->academicYear?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- TABEL NILAI --}}
    <div class="section-title">Nilai Asesmen Sumatif Tengah Semester</div>

    <table>
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Mata Pelajaran</th>
                <th style="width:80px">Nilai ASTS</th>
                <th style="width:120px">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $i => $subject)
                @php $nilai = $nilaiPerMapel[$subject->id] ?? 0; @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $subject->name }}</td>
                    <td class="number">{{ $nilai > 0 ? $nilai : '-' }}</td>
                    <td class="center {{ $nilai >= 75 ? 'tuntas' : 'belum-tuntas' }}">
                        @if($nilai === 0)
                            Belum Mengerjakan
                        @elseif($nilai >= 75)
                            Tuntas
                        @else
                            Belum Tuntas
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center" style="padding:12px; font-style:italic; color:#666;">
                        Belum ada asesmen ASTS untuk semester ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CATATAN --}}
    <div class="catatan-section" style="margin-top:6px">
        <div class="catatan-label">Catatan Wali Kelas:</div>
        <div class="catatan-content">&nbsp;</div>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="ttd-title">
                Mengetahui,<br>Kepala {{ $schoolName }}
            </div>
            <div class="ttd-name">{{ $principalName ?: '____________________________' }}</div>
            <div class="ttd-nip">NIY. {{ $principalNiy ?: '-' }}</div>
        </div>

        <div class="ttd-box">
            <div class="ttd-title">
                {{ \Carbon\Carbon::now()->locale("id")->translatedFormat("d F Y") }},<br>Wali Kelas
            </div>
            <div class="ttd-name">{{ $waliKelas->name }}</div>
            <div class="ttd-nip">NIY. {{ $waliKelas->nip_nuptk ?? '-' }}</div>
        </div>
    </div>

</div>

<script>
    // Auto print jika ada query ?print=1
    const url = new URL(window.location.href);
    if (url.searchParams.get("print") === "1") {
        window.onload = () => setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>

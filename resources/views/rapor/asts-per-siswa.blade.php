<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor ASTS - {{ $student->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ===== SCREEN BASE ===== */
        @media screen {
            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #f0f4f8 0%, #e8edf5 100%);
                min-height: 100vh;
                padding: 24px 16px 40px;
                color: #1a202c;
            }

            /* Screen hero bar */
            .screen-hero {
                max-width: 215mm;
                margin: 0 auto 16px;
                background: linear-gradient(135deg, #1e3a5f 0%, #0f2d5a 60%, #1a3a6b 100%);
                border-radius: 14px;
                padding: 18px 24px;
                color: white;
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .screen-hero-icon { font-size: 28px; }
            .screen-hero-title { font-size: 17px; font-weight: 700; }
            .screen-hero-sub { font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 2px; }

            .page {
                max-width: 215mm;
                margin: 0 auto;
                background: white;
                border-radius: 14px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.12);
                padding: 28px 32px 32px;
            }

            table th { background: #1e3a5f !important; color: white !important; }
            table tr:nth-child(even) td { background: #f8fafc; }
            table tr:hover td { background: #eff6ff !important; transition: background 0.15s; }
            td.number { font-weight: 700; color: #1e3a5f; }
            .belum-tuntas { color: #dc2626 !important; font-style: italic; font-size: 10px; }

            .catatan-section {
                background: #fafafa;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
            }
            .catatan-label { color: #374151; }
            .catatan-content { min-height: 36px; }

            .info-label { color: #6b7280; }
            .info-value { color: #111827; }

            .rapor-title h3 { color: #1e3a5f; }

            /* Mobile responsive */
            @media (max-width: 600px) {
                body { padding: 12px 8px 24px; }
                .page { padding: 16px; border-radius: 10px; }
                .screen-hero { padding: 14px 16px; }
                .screen-hero-title { font-size: 15px; }
                .student-info { grid-template-columns: 1fr !important; }
                table { font-size: 9pt; }
                table th, table td { padding: 4px 6px; }
                .info-label { width: 90px !important; font-size: 10pt !important; }
                .info-value { font-size: 10pt !important; }
            }
        }

        /* ===== PRINT ===== */
        @media print {
            body {
                font-family: "Times New Roman", Times, serif;
                font-size: 10pt;
                color: #000;
                background: #fff;
            }
            .screen-hero { display: none !important; }
            .no-print { display: none !important; }
            .page { margin: 0; padding: 10mm 15mm 15mm 20mm; box-shadow: none; border-radius: 0; }
            @page { size: F4 portrait; margin: 0; }
            .kop-surat-img { margin-top: -10mm; margin-bottom: 4mm; }
            table th { background: #f0f0f0 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* ===== SHARED STYLES (print + screen) ===== */
        .page {
            width: 215mm;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-logo { width: 70px; height: 70px; object-fit: contain; margin-right: 12px; }
        .header-logo-placeholder {
            width: 70px; height: 70px;
            border: 1px solid #000;
            display: flex; align-items: center; justify-content: center;
            font-size: 8pt; color: #666; margin-right: 12px; flex-shrink: 0;
        }
        .header-text { flex: 1; text-align: center; }
        .header-text h1 { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-text h2 { font-size: 10pt; font-weight: bold; text-transform: uppercase; }
        .header-text p { font-size: 9pt; margin-top: 2px; }

        .kop-surat-img { margin-top: -10mm; margin-bottom: 2mm; line-height: 0; }
        .kop-surat-img img { width: 100%; display: block; }

        .rapor-title { text-align: center; margin: 4px 0 10px; }
        .rapor-title h3 { font-size: 13pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 1px; }
        .rapor-title p { font-size: 10pt; margin-top: 2px; }

        .student-info {
            margin: 3px 0 10px;
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 2px 20px;
        }
        .info-row { display: flex; gap: 4px; padding: 1px 0; }
        .info-label { width: 120px; flex-shrink: 0; font-size: 11pt; }
        .info-sep { flex-shrink: 0; }
        .info-value { font-size: 11pt; font-weight: bold; }

        .section-title { font-size: 10pt; font-weight: bold; margin: 4px 0 8px; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; font-size: 11pt; }
        table th, table td { border: 1px solid #000; padding: 2px 5px; }
        table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        td.center { text-align: center; }
        td.number { text-align: center; font-weight: bold; }
        .tuntas { color: #000; }
        .belum-tuntas { color: #000; font-style: italic; }

        .catatan-section { margin-top: 8px; border: 1px solid #000; padding: 6px 10px; }
        .catatan-label { font-weight: bold; font-size: 11pt; }
        .catatan-content { min-height: 20px; font-size: 11pt; }
    </style>
</head>
<body>

{{-- Screen-only hero bar --}}
<div class="screen-hero no-print" style="display:none">
    <div class="screen-hero-icon">📄</div>
    <div>
        <div class="screen-hero-title">Rapor ASTS — {{ $student->name }}</div>
        <div class="screen-hero-sub">{{ $myClass->name }} &bull; {{ $semester?->name ?? '-' }}</div>
    </div>
</div>

<div class="page {{ !empty($kopSuratUrl) ? 'with-kop' : '' }}">

    {{-- KOP SURAT --}}
    @if(!empty($kopSuratUrl))
    <div class="kop-surat-img">
        <img src="{{ $kopSuratUrl }}" alt="Kop Surat">
    </div>
    @else
    <div class="header">
        @if($schoolLogo && file_exists(public_path($schoolLogo)))
            <img src="{{ asset($schoolLogo) }}" class="header-logo" alt="Logo">
        @else
            <div class="header-logo-placeholder">LOGO</div>
        @endif
        <div class="header-text">
            <h1>{{ $schoolName }}</h1>
            @if($schoolAddress)<p>{{ $schoolAddress }}</p>@endif
            @if($schoolPhone)<p>Telp: {{ $schoolPhone }}</p>@endif
        </div>
    </div>
    @endif

    {{-- JUDUL --}}
    <div class="rapor-title">
        <h3>Rapor Asesmen Sumatif Tengah Semester (ASTS)</h3>
        @php $semShort = $semester ? preg_replace('/\s+\d{4}\/\d{4}$/', '', $semester->name) : ''; @endphp
    </div>

    {{-- DATA SISWA --}}
    <div class="student-info">
        <div class="info-row">
            <span class="info-label">Nama Siswa</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $student->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Semester</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $semShort ?? ($semester?->name ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">NIS</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $student->nis ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tahun Pelajaran</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $semester?->academicYear?->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kelas</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $myClass->name }}</span>
        </div>
        <div class="info-row"></div>
    </div>

    {{-- TABEL NILAI --}}
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
                    <td class="center belum-tuntas">
                        @if($nilai === 0) Belum Mengerjakan @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center" style="padding:12px;font-style:italic;color:#666">
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

</div>

<script>
    // Tampilkan screen hero saat di screen
    const hero = document.querySelector('.screen-hero');
    if (hero) hero.style.display = 'flex';

    // Auto print jika ada query ?print=1
    const url = new URL(window.location.href);
    if (url.searchParams.get("print") === "1") {
        window.onload = () => setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>

<div>
    {{-- STYLE --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        @media print {
            body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
            .no-print { display: none !important; }
            @page { size: F4 landscape; margin: 10mm 12mm 10mm 12mm; }
            .rapor-wrap { box-shadow: none !important; border: none !important; background: white !important; }
            table { page-break-inside: auto; font-size: 9pt; width: 100%; }
            tr { page-break-inside: avoid; }
            th, td { border: 1px solid #374151 !important; }
            .rapor-print-header { display: block !important; }
        }
        @media screen {
            .rapor-print-header { display: none; }
        }

        .asts-page { font-family: Inter, sans-serif; }

        .asts-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f2d5a 50%, #1a3a6b 100%);
            border-radius: 16px; padding: 24px 28px; color: white;
            margin-bottom: 20px; position: relative; overflow: hidden;
        }
        .asts-hero::before {
            content:''; position:absolute; top:-40px; right:-40px;
            width:180px; height:180px; background:rgba(255,255,255,0.05); border-radius:50%;
        }
        .asts-hero-title { font-size:22px; font-weight:700; margin:0; }
        .asts-hero-sub { font-size:13px; color:rgba(255,255,255,0.75); margin-top:4px; }
        .asts-hero-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(255,255,255,0.15); backdrop-filter:blur(4px);
            border-radius:20px; padding:4px 12px; font-size:12px; font-weight:500;
            margin-top:10px; border:1px solid rgba(255,255,255,0.2);
        }

        .asts-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px; }
        @media (max-width: 640px) {
            .asts-hero { padding:16px 18px; border-radius:12px; }
            .asts-hero-title { font-size:18px; }
            .asts-hero-sub { font-size:12px; }
            .asts-stats { grid-template-columns:1fr; gap:8px; }
            .asts-stat-card { padding:12px 16px; }
            .asts-stat-value { font-size:18px; }
            .rapor-table th { font-size:9px; padding:5px 6px; }
            .rapor-table td { font-size:11px; padding:5px 6px; }
            .nilai-badge { min-width:28px; height:18px; font-size:10px; }
        }
        .asts-stat-card {
            background:white; border-radius:12px; padding:16px 20px;
            box-shadow:0 1px 4px rgba(0,0,0,0.08); border:1px solid #e5e7eb;
            display:flex; align-items:center; gap:14px;
        }
        .asts-stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .asts-stat-label { font-size:11px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; }
        .asts-stat-value { font-size:22px; font-weight:700; color:#111827; line-height:1.2; }

        .asts-table-card { background:white; border-radius:14px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); border:1px solid #e5e7eb; }
        .asts-table-card-header {
            background:linear-gradient(135deg,#1e3a5f,#0f2d5a);
            padding:14px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
        }
        .asts-table-card-title { font-size:13px; font-weight:600; color:white; }

        .rapor-table { border-collapse:collapse; width:100%; min-width:600px; }
        .rapor-table th {
            background:#1e3a5f; color:white; font-weight:600;
            padding:8px 10px; font-size:11px; border:1px solid #2d4d7a;
            text-align:center; vertical-align:bottom;
        }
        .rapor-table th.col-avg { background:#0f2d5a; }
        .rapor-table td {
            border:1px solid #e5e7eb; padding:7px 10px; font-size:12px;
            text-align:center; color:#374151;
        }
        .rapor-table td.name-col { text-align:left; white-space:nowrap; font-weight:500; color:#111827; }
        .rapor-table tr:nth-child(even) td { background:#f8fafc; }
        .rapor-table tr:hover td { background:#eff6ff !important; }
        .rapor-table td.no-col { color:#9ca3af; font-size:11px; }

        .nilai-badge { display:inline-flex; align-items:center; justify-content:center; min-width:36px; height:22px; border-radius:6px; font-size:11px; font-weight:600; padding:0 6px; }
        .nilai-zero { background:#fee2e2; color:#dc2626; }
        .nilai-low  { background:#fef3c7; color:#d97706; }
        .nilai-mid  { background:#dbeafe; color:#2563eb; }
        .nilai-good { background:#dcfce7; color:#16a34a; }
        .nilai-avg-badge { display:inline-flex; align-items:center; justify-content:center; min-width:40px; height:24px; border-radius:8px; font-size:12px; font-weight:700; padding:0 8px; background:#eff6ff; color:#1e3a5f; border:1px solid #bfdbfe; }

        .btn-print {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(255,255,255,0.2); color:white;
            border:1px solid rgba(255,255,255,0.35); border-radius:8px;
            padding:7px 14px; font-size:12px; font-weight:600; cursor:pointer;
            text-decoration:none; backdrop-filter:blur(4px);
        }
        .btn-print:hover { background:rgba(255,255,255,0.3); color:white; }
        .btn-cetak-siswa {
            display:inline-flex; align-items:center; gap:4px;
            background:#2563eb; color:white; border-radius:6px;
            padding:3px 10px; font-size:10px; font-weight:600; text-decoration:none;
        }
        .btn-cetak-siswa:hover { background:#1d4ed8; color:white; }

        .asts-empty {
            background:linear-gradient(135deg,#fffbeb,#fef3c7);
            border:1px solid #fde68a; border-radius:12px; padding:32px; text-align:center;
        }

        .rapor-print-header { text-align:center; margin-bottom:14px; }
        .rapor-print-header h2 { font-size:13pt; font-weight:700; color:#1e3a5f; margin:0 0 4px; }
        .rapor-print-header p { font-size:10pt; margin:2px 0; color:#374151; }

        @media print {
            .rapor-table th { background:#1e3a5f !important; color:white !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .rapor-table tr:nth-child(even) td { background:#f8fafc !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .asts-hero, .asts-stats, .asts-table-card-header { display:none !important; }
            .asts-table-card { box-shadow:none; border:none; border-radius:0; }
        }
    </style>

    <div class="asts-page">

        {{-- HERO TOOLBAR --}}
        <div class="asts-hero no-print">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;position:relative;z-index:1">
                <div>
                    <h1 class="asts-hero-title">📊 Rapor ASTS</h1>
                    <p class="asts-hero-sub">Rekap Nilai Asesmen Sumatif Tengah Semester</p>
                    <div class="asts-hero-badge">
                        🏫 {{ $this->myClass->name }} &mdash; {{ $this->myClass->getMajorLabel() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS --}}
        @if($this->subjects->isNotEmpty())
        <div class="asts-stats no-print">
            <div class="asts-stat-card">
                <div class="asts-stat-icon" style="background:#eff6ff">👩‍🎓</div>
                <div>
                    <div class="asts-stat-label">Total Siswa</div>
                    <div class="asts-stat-value">{{ $this->students->count() }}</div>
                </div>
            </div>
            <div class="asts-stat-card">
                <div class="asts-stat-icon" style="background:#f0fdf4">📚</div>
                <div>
                    <div class="asts-stat-label">Mata Pelajaran</div>
                    <div class="asts-stat-value">{{ $this->subjects->count() }}</div>
                </div>
            </div>
            <div class="asts-stat-card">
                <div class="asts-stat-icon" style="background:#fefce8">🎯</div>
                @php
                    $allNilai = [];
                    foreach ($this->students as $s) {
                        foreach ($this->subjects as $subj) {
                            $nx = $this->getNilai($s->id, $subj->id);
                            if ($nx > 0) { $allNilai[] = $nx; }
                        }
                    }
                    $rataKelas = count($allNilai) > 0 ? round(array_sum($allNilai) / count($allNilai)) : 0;
                @endphp
                <div>
                    <div class="asts-stat-label">Rata-rata Kelas</div>
                    <div class="asts-stat-value">{{ $rataKelas }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- WARNING KOSONG --}}
        @if($this->subjects->isEmpty())
        <div class="asts-empty no-print">
            <div style="font-size:40px;margin-bottom:10px">⚠️</div>
            <p style="font-weight:600;color:#92400e;font-size:15px;margin:0">Belum ada asesmen ASTS semester aktif untuk kelas ini.</p>
            <p style="color:#b45309;font-size:13px;margin-top:6px">Pastikan guru sudah mempublikasikan asesmen ASTS dengan target kelas yang sesuai.</p>
        </div>
        @else

        {{-- PRINT HEADER --}}
        <div class="rapor-print-header">
            <h2>REKAP NILAI ASESMEN SUMATIF TENGAH SEMESTER (ASTS)</h2>
            <p><strong>{{ $this->myClass->name }}</strong> &mdash; {{ $this->myClass->getMajorLabel() }}</p>
            <p>
                Wali Kelas: <strong>{{ auth()->user()->name }}</strong>
                @if($this->semester)
                    @php $semShortName = preg_replace('/\s+\d{4}\/\d{4}$/', '', $this->semester->name); @endphp
                    &nbsp;|&nbsp; Semester: <strong>{{ $semShortName }}</strong>
                    &nbsp;|&nbsp; Tahun Pelajaran: <strong>{{ $this->semester->academicYear->name ?? $this->semester->name }}</strong>
                @endif
            </p>
        </div>

        {{-- TABLE CARD --}}
        <div class="asts-table-card">
            <div class="asts-table-card-header no-print">
                <div>
                    <div class="asts-table-card-title">📋 Rekap Nilai per Mata Pelajaran</div>
                    @if($this->semester)
                        @php $semShortName2 = preg_replace('/\s+\d{4}\/\d{4}$/', '', $this->semester->name); @endphp
                        <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-top:2px">{{ $semShortName2 }} &bull; {{ $this->semester->academicYear->name ?? '' }}</div>
                    @endif
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                    <span style="font-size:10px;color:rgba(255,255,255,0.65);display:flex;align-items:center;gap:4px"><span style="display:inline-block;width:10px;height:10px;background:#fee2e2;border-radius:2px"></span>0</span>
                    <span style="font-size:10px;color:rgba(255,255,255,0.65);display:flex;align-items:center;gap:4px"><span style="display:inline-block;width:10px;height:10px;background:#fef3c7;border-radius:2px"></span>&lt;75</span>
                    <span style="font-size:10px;color:rgba(255,255,255,0.65);display:flex;align-items:center;gap:4px"><span style="display:inline-block;width:10px;height:10px;background:#dcfce7;border-radius:2px"></span>&ge;75</span>
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="rapor-table">
                    <thead>
                        <tr>
                            <th style="width:32px">No</th>
                            <th style="min-width:160px;text-align:left">Nama Siswa</th>
                            <th style="width:72px">NIS</th>
                            @foreach($this->subjects as $subject)
                                <th style="min-width:72px;font-size:9px;padding-bottom:8px;line-height:1.3">{{ $subject->name }}</th>
                            @endforeach
                            <th class="col-avg" style="min-width:60px">Rata-rata</th>
                            <th class="col-avg no-print" style="min-width:64px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->students as $index => $student)
                            @php
                                $nilaiArr = [];
                                foreach ($this->subjects as $subj) {
                                    $nilaiArr[$subj->id] = $this->getNilai($student->id, $subj->id);
                                }
                                $nilaiPositif = array_filter($nilaiArr, fn($v) => $v > 0);
                                $rataRata = count($nilaiPositif) > 0 ? round(array_sum($nilaiPositif) / count($nilaiPositif)) : 0;
                            @endphp
                            <tr>
                                <td class="no-col">{{ $index + 1 }}</td>
                                <td class="name-col">{{ $student->name }}</td>
                                <td>{{ $student->nis ?? '-' }}</td>
                                @foreach($this->subjects as $subj)
                                    @php
                                        $n = $nilaiArr[$subj->id];
                                        $cls = $n === 0 ? 'nilai-zero' : ($n >= 75 ? 'nilai-good' : ($n >= 60 ? 'nilai-mid' : 'nilai-low'));
                                    @endphp
                                    <td><span class="nilai-badge {{ $cls }}">{{ $n }}</span></td>
                                @endforeach
                                <td style="background:#f0f9ff">
                                    @php $avgCls = $rataRata === 0 ? 'nilai-zero' : ($rataRata >= 75 ? 'nilai-good' : 'nilai-low'); @endphp
                                    <span class="nilai-avg-badge {{ $avgCls }}">{{ $rataRata }}</span>
                                </td>
                                <td class="no-print" style="padding:4px 8px">
                                    <a href="{{ route('teacher.assessment.rapor-asts.cetak', $student->id) }}" target="_blank" class="btn-cetak-siswa">
                                        🖨️ Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + $this->subjects->count() }}" style="text-align:center;padding:24px;color:#9ca3af;font-size:13px">Belum ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @endif
    </div>
</div>

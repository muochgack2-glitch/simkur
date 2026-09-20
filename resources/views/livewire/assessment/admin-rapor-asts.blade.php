<div>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            @page { size: F4 landscape; margin: 10mm 12mm 10mm 12mm; }
        }
        @media screen { .print-only { display: none; } }

        /* ===== BASE ===== */
        .adm-page { font-family: Inter, sans-serif; }

        /* ===== HERO ===== */
        .adm-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f2d5a 50%, #1a3a6b 100%);
            border-radius: 16px; padding: 22px 26px; color: white;
            margin-bottom: 18px; position: relative; overflow: hidden;
        }
        .adm-hero::before {
            content:''; position:absolute; top:-40px; right:-40px;
            width:180px; height:180px; background:rgba(255,255,255,0.05); border-radius:50%;
        }
        .adm-hero-title { font-size:21px; font-weight:700; margin:0; position:relative; z-index:1; }
        .adm-hero-sub { font-size:12px; color:rgba(255,255,255,0.72); margin-top:3px; position:relative; z-index:1; }

        /* ===== STATS ROW ===== */
        .adm-stats { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
        .adm-stat {
            flex:1; min-width:120px;
            background:white; border-radius:12px; padding:14px 18px;
            box-shadow:0 1px 4px rgba(0,0,0,0.07); border:1px solid #e5e7eb;
            display:flex; align-items:center; gap:12px;
        }
        .adm-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
        .adm-stat-label { font-size:10px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; }
        .adm-stat-value { font-size:20px; font-weight:700; color:#111827; line-height:1.2; }

        /* ===== CARDS ===== */
        .adm-card {
            background:white; border-radius:14px; border:1px solid #e5e7eb;
            box-shadow:0 1px 4px rgba(0,0,0,0.07); margin-bottom:14px;
        }
        .adm-card-header {
            display:flex; align-items:center; gap:10px;
            padding:12px 18px; border-bottom:1px solid #f3f4f6;
        }
        .adm-card-header-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
        .adm-card-header-title { font-size:13px; font-weight:600; color:#374151; }
        .adm-card-body { padding:14px 18px; }

        /* Kelas select */
        .adm-kelas-select {
            border:1.5px solid #d1d5db; border-radius:8px; padding:7px 12px;
            font-size:13px; color:#374151; background:white; outline:none;
            transition:border-color 0.2s; min-width:160px;
        }
        .adm-kelas-select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }

        /* Kop surat img */
        .kop-preview { max-height:90px; width:auto; border-radius:6px; border:1px solid #e5e7eb; }

        /* Input tanggal */
        .adm-input {
            flex:1; border:1.5px solid #d1d5db; border-radius:8px;
            padding:7px 12px; font-size:13px; color:#374151; outline:none;
            transition:border-color 0.2s;
        }
        .adm-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
        .adm-btn {
            background:#1e3a5f; color:white; border:none; border-radius:8px;
            padding:7px 16px; font-size:12px; font-weight:600; cursor:pointer; transition:background 0.2s;
        }
        .adm-btn:hover { background:#0f2d5a; }
        .adm-btn-danger { background:#dc2626; }
        .adm-btn-danger:hover { background:#b91c1c; }
        .adm-btn-upload { background:#6366f1; }
        .adm-btn-upload:hover { background:#4f46e5; }
        .adm-btn:disabled { background:#d1d5db; cursor:not-allowed; }

        /* ===== TABLE CARD ===== */
        .adm-table-card { background:white; border-radius:14px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); border:1px solid #e5e7eb; margin-bottom:14px; }
        .adm-table-header {
            background:linear-gradient(135deg,#1e3a5f,#0f2d5a);
            padding:13px 18px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
        }
        .adm-table-title { font-size:13px; font-weight:600; color:white; }
        .adm-table-sub { font-size:11px; color:rgba(255,255,255,0.65); margin-top:2px; }

        /* Tabel */
        .rapor-table { border-collapse:collapse; width:100%; min-width:560px; }
        .rapor-table th {
            background:#1e3a5f; color:white; font-weight:600;
            padding:7px 8px; font-size:10px; border:1px solid #2d4d7a;
            text-align:center; vertical-align:bottom;
        }
        .rapor-table th.col-name { text-align:left; min-width:150px; }
        .rapor-table td {
            border:1px solid #e5e7eb; padding:6px 8px; font-size:11px;
            text-align:center; color:#374151;
        }
        .rapor-table td.name-col { text-align:left; white-space:nowrap; font-weight:500; color:#111827; }
        .rapor-table tr:nth-child(even) td { background:#f8fafc; }
        .rapor-table tr:hover td { background:#eff6ff !important; transition:background 0.15s; }
        .rapor-table td.no-col { color:#9ca3af; font-size:10px; }

        /* Nilai badges */
        .nb { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:20px; border-radius:5px; font-size:10px; font-weight:600; padding:0 5px; }
        .nb-0    { background:#fee2e2; color:#dc2626; }
        .nb-low  { background:#fef3c7; color:#d97706; }
        .nb-mid  { background:#dbeafe; color:#2563eb; }
        .nb-good { background:#dcfce7; color:#16a34a; }
        .nb-avg  { background:#eff6ff; color:#1e3a5f; border:1px solid #bfdbfe; font-weight:700; min-width:38px; height:22px; font-size:11px; }

        /* Cetak per siswa button */
        .btn-cetak {
            display:inline-flex; align-items:center; gap:3px;
            background:#2563eb; color:white; border-radius:5px;
            padding:3px 9px; font-size:10px; font-weight:600; text-decoration:none;
        }
        .btn-cetak:hover { background:#1d4ed8; color:white; }

        /* Legend */
        .legend-dot { display:inline-block; width:9px; height:9px; border-radius:2px; }

        /* Empty state */
        .adm-empty { background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1px solid #fde68a; border-radius:12px; padding:28px; text-align:center; }

        /* ===== MOBILE ===== */
        @media (max-width: 640px) {
            .adm-hero { padding:14px 16px; border-radius:12px; }
            .adm-hero-title { font-size:17px; }
            .adm-stats { flex-direction:column; gap:8px; }
            .adm-stat { min-width:unset; }
            .adm-card-body { padding:12px 14px; }
            .rapor-table th { font-size:8px; padding:5px 4px; }
            .rapor-table td { font-size:10px; padding:5px 4px; }
            .nb { min-width:26px; height:17px; font-size:9px; }
        }

        /* Print overrides */
        @media print {
            .adm-hero, .adm-stats, .adm-card, .adm-table-header { display:none !important; }
            .adm-table-card { box-shadow:none; border:none; border-radius:0; }
            .rapor-table th { background:#1e3a5f !important; color:white !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .rapor-table tr:nth-child(even) td { background:#f8fafc !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }
    </style>

    <div class="adm-page">

        {{-- HERO --}}
        <div class="adm-hero no-print">
            <h1 class="adm-hero-title">🗂️ Admin — Rapor ASTS</h1>
            <p class="adm-hero-sub">Lihat &amp; cetak rapor ASTS semua kelas</p>
        </div>

        {{-- STATS --}}
        <div class="adm-stats no-print">
            <div class="adm-stat">
                <div class="adm-stat-icon" style="background:#eff6ff">👩‍🎓</div>
                <div>
                    <div class="adm-stat-label">Total Siswa</div>
                    <div class="adm-stat-value">{{ $this->students->count() }}</div>
                </div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-icon" style="background:#f0fdf4">📚</div>
                <div>
                    <div class="adm-stat-label">Mata Pelajaran</div>
                    <div class="adm-stat-value">{{ $this->subjects->count() }}</div>
                </div>
            </div>
            <div class="adm-stat" style="flex:2">
                <div class="adm-stat-icon" style="background:#fafaf5">🏫</div>
                <div>
                    <div class="adm-stat-label">Kelas Aktif</div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:4px">
                        <select wire:model.live="selectedClassId" class="adm-kelas-select">
                            @foreach($this->classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                        @if($this->semester)
                            <span style="font-size:11px;color:#6b7280">{{ $this->semester->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD KOP SURAT --}}
        <div class="adm-card no-print">
            <div class="adm-card-header">
                <div class="adm-card-header-icon" style="background:#eff6ff">🖼️</div>
                <span class="adm-card-header-title">Kop Surat Rapor Cetak</span>
            </div>
            <div class="adm-card-body">
                @if(session("kop_success"))
                    <div style="margin-bottom:10px;font-size:12px;color:#15803d;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:7px 12px">
                        {{ session("kop_success") }}
                    </div>
                @endif

                @if($this->kopSuratUrl)
                    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:12px">
                        <div>
                            <p style="font-size:11px;color:#6b7280;margin-bottom:6px">Kop surat aktif (akan muncul di rapor cetak):</p>
                            <img src="{{ $this->kopSuratUrl }}" alt="Kop Surat" class="kop-preview">
                        </div>
                        <button wire:click="deleteKopSurat" wire:confirm="Hapus kop surat ini?"
                            class="adm-btn adm-btn-danger" style="font-size:11px;padding:5px 12px">
                            🗑 Hapus
                        </button>
                    </div>
                @else
                    <p style="font-size:12px;color:#9ca3af;margin-bottom:10px">Belum ada kop surat. Upload gambar JPG/PNG kop surat sekolah (maks. 2MB).</p>
                @endif

                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <input type="file" wire:model="kopSuratFile" accept="image/jpeg,image/png"
                        style="font-size:12px;color:#374151">
                    <button wire:click="uploadKopSurat"
                        @if(!$kopSuratFile) disabled @endif
                        class="adm-btn adm-btn-upload" style="font-size:11px;padding:6px 14px">
                        ⬆️ Upload Kop Surat
                    </button>
                </div>
                @if($kopSuratFile)
                    @error("kopSuratFile")
                        <p style="font-size:11px;color:#dc2626;margin-top:4px">{{ $message }}</p>
                    @enderror
                @endif
                <div wire:loading wire:target="kopSuratFile,uploadKopSurat" style="font-size:11px;color:#6366f1;margin-top:4px">Memproses...</div>
            </div>
        </div>

        {{-- CARD TANGGAL CETAK --}}
        <div class="adm-card no-print">
            <div class="adm-card-header">
                <div class="adm-card-header-icon" style="background:#fef3c7">📅</div>
                <span class="adm-card-header-title">Tanggal Cetak Rapor</span>
            </div>
            <div class="adm-card-body">
                @if(session("kop_success") && str_contains(session("kop_success"), "Tanggal"))
                    <div style="font-size:12px;color:#15803d;margin-bottom:8px">{{ session("kop_success") }}</div>
                @endif
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <input type="text" wire:model="tanggalCetak"
                        placeholder="Contoh: 19 September 2026"
                        class="adm-input">
                    <button wire:click="saveTanggalCetak" class="adm-btn" style="font-size:11px;padding:7px 16px">
                        💾 Simpan
                    </button>
                </div>
                <p style="font-size:11px;color:#9ca3af;margin-top:6px">Tanggal ini akan muncul di halaman TTD rapor cetak. Kosongkan untuk pakai tanggal hari ini otomatis.</p>
            </div>
        </div>

        {{-- TABEL REKAP --}}
        @if($this->selectedClass && $this->students->isNotEmpty() && $this->subjects->isNotEmpty())

            <div class="adm-table-card">
                <div class="adm-table-header no-print">
                    <div>
                        <div class="adm-table-title">📋 Rekapitulasi Nilai ASTS — {{ $this->selectedClass->name }}</div>
                        @if($this->semester)
                            <div class="adm-table-sub">{{ $this->semester->name }}</div>
                        @endif
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <span style="font-size:10px;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:3px"><span class="legend-dot" style="background:#fee2e2"></span>0</span>
                        <span style="font-size:10px;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:3px"><span class="legend-dot" style="background:#fef3c7"></span>&lt;75</span>
                        <span style="font-size:10px;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:3px"><span class="legend-dot" style="background:#dcfce7"></span>&ge;75</span>
                    </div>
                </div>

                {{-- Print header --}}
                <div class="print-only" style="text-align:center;padding:10px 0 6px">
                    <div style="font-size:13pt;font-weight:700;color:#1e3a5f">REKAPITULASI NILAI ASTS — {{ $this->selectedClass->name }}</div>
                    @if($this->semester)<div style="font-size:10pt;color:#374151">{{ $this->semester->name }}</div>@endif
                </div>

                <div style="overflow-x:auto">
                    <table class="rapor-table">
                        <thead>
                            <tr>
                                <th style="width:28px">No</th>
                                <th class="col-name">Nama Siswa</th>
                                @foreach($this->subjects as $subject)
                                    <th style="min-width:66px;font-size:8px;padding-bottom:6px;line-height:1.3">{{ $subject->name }}</th>
                                @endforeach
                                <th style="min-width:54px;background:#0f2d5a">Rata-rata</th>
                                <th class="no-print" style="min-width:58px;background:#0f2d5a">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->students as $idx => $student)
                                @php
                                    $nilaiList = $this->subjects->map(fn($sub) => $this->getNilai($student->id, $sub->id));
                                    $rataRata  = $nilaiList->filter(fn($n) => $n > 0)->avg() ?? 0;
                                    $avgCls    = $rataRata === 0 ? 'nb-0' : ($rataRata >= 75 ? 'nb-good' : 'nb-low');
                                @endphp
                                <tr>
                                    <td class="no-col">{{ $idx + 1 }}</td>
                                    <td class="name-col">{{ $student->name }}</td>
                                    @foreach($nilaiList as $nilai)
                                        @php $cls = $nilai === 0 ? 'nb-0' : ($nilai >= 75 ? 'nb-good' : ($nilai >= 60 ? 'nb-mid' : 'nb-low')); @endphp
                                        <td><span class="nb {{ $cls }}">{{ $nilai > 0 ? $nilai : '-' }}</span></td>
                                    @endforeach
                                    <td style="background:#f0f9ff">
                                        <span class="nb nb-avg">{{ $rataRata > 0 ? number_format($rataRata, 1) : '-' }}</span>
                                    </td>
                                    <td class="no-print" style="padding:4px 6px">
                                        <a href="{{ route('admin.rapor-asts.cetak', ['classId' => $this->selectedClassId, 'studentId' => $student->id]) }}"
                                           target="_blank" class="btn-cetak">
                                            🖨 Cetak
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($this->selectedClass && $this->students->isEmpty())
            <div class="adm-empty">
                <div style="font-size:36px;margin-bottom:8px">👥</div>
                <p style="font-weight:600;color:#92400e;font-size:14px;margin:0">Tidak ada siswa di kelas {{ $this->selectedClass->name }}.</p>
            </div>
        @elseif($this->selectedClass && $this->subjects->isEmpty())
            <div class="adm-empty">
                <div style="font-size:36px;margin-bottom:8px">📚</div>
                <p style="font-weight:600;color:#92400e;font-size:14px;margin:0">Belum ada jadwal mengajar untuk kelas {{ $this->selectedClass->name }}.</p>
            </div>
        @endif

    </div>
</div>

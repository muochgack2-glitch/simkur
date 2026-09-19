<div>
    {{-- PRINT STYLE: F4 Landscape --}}
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            @page {
                size: F4 landscape;
                margin: 10mm 12mm 10mm 12mm;
            }
            .rapor-wrap { box-shadow: none !important; border: none !important; }
            table { page-break-inside: auto; font-size: 9pt; }
            tr { page-break-inside: avoid; }
            th, td { border: 1px solid #333 !important; }
        }
        @media screen {
            .print-only { display: none; }
        }
        .rapor-wrap {
            background: white;
            max-width: 100%;
            margin: 0 auto;
        }
        .rapor-table { border-collapse: collapse; width: 100%; }
        .rapor-table th, .rapor-table td {
            border: 1px solid #374151;
            padding: 4px 6px;
            text-align: center;
            font-size: 11px;
        }
        .rapor-table th { background: #1e3a5f; color: white; font-weight: 600; }
        .rapor-table td.name-col { text-align: left; white-space: nowrap; }
        .rapor-table tr:nth-child(even) td { background: #f8fafc; }
        .rapor-table tr:hover td { background: #dbeafe; }
        .nilai-0 { color: #dc2626; font-weight: 600; }
        .nilai-good { color: #166534; }
        .rapor-header { text-align: center; margin-bottom: 12px; }
        .rapor-header h2 { font-size: 14pt; font-weight: 700; color: #1e3a5f; margin: 0; }
        .rapor-header p { font-size: 10pt; margin: 2px 0; color: #374151; }
        .rapor-meta { display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 8px; color: #374151; }
    </style>

    {{-- Toolbar: hanya tampil di screen --}}
    <div class="no-print mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rapor ASTS</h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelas {{ $this->myClass->name }} &mdash; {{ $this->myClass->getMajorLabel() }}
            </p>
        </div>
        <button onclick="window.print()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak F4
        </button>
    </div>

    {{-- Warning jika tidak ada asesmen ASTS --}}
    @if($this->subjects->isEmpty())
        <div class="no-print bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-yellow-700 font-medium">Belum ada asesmen ASTS semester aktif untuk kelas ini.</p>
            <p class="text-yellow-500 text-sm mt-1">Pastikan guru sudah mempublikasikan asesmen ASTS dengan target kelas yang sesuai.</p>
        </div>
    @else
        {{-- Area rapor (dicetak) --}}
        <div class="rapor-wrap p-2">
            {{-- Header Rapor --}}
            <div class="rapor-header">
                <h2>REKAP NILAI ASESMEN SUMATIF TENGAH SEMESTER (ASTS)</h2>
                <p><strong>{{ $this->myClass->name }}</strong> &mdash; {{ $this->myClass->getMajorLabel() }}</p>
                <p>
                    Wali Kelas: <strong>{{ auth()->user()->name }}</strong>
                    @if($this->semester)
                        @php
                            $semShortName = preg_replace('/\s+\d{4}\/\d{4}$/', '', $this->semester->name);
                        @endphp
                        &nbsp;|&nbsp; Semester: <strong>{{ $semShortName }}</strong>
                        &nbsp;|&nbsp; Tahun Ajaran: <strong>{{ $this->semester->academicYear->name ?? ($this->semester->name) }}</strong>
                    @endif
                </p>
            </div>

            {{-- Tabel Nilai --}}
            <div style="overflow-x: auto;">
                <table class="rapor-table">
                    <thead>
                        <tr>
                            <th style="width:28px">No</th>
                            <th style="min-width:160px; text-align:left">Nama Siswa</th>
                            <th style="width:70px">NIS</th>
                            @foreach($this->subjects as $subject)
                                <th style="min-width:70px; font-size:9px; vertical-align:bottom; padding-bottom:6px;">
                                    {{ $subject->name }}
                                </th>
                            @endforeach
                            <th style="min-width:55px; background:#0f2d5a;">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->students as $index => $student)
                            @php
                                $nilaiArr = [];
                                foreach ($this->subjects as $subject) {
                                    $nilaiArr[$subject->id] = $this->getNilai($student->id, $subject->id);
                                }
                                $rataRata = count($nilaiArr) > 0
                                    ? round(array_sum($nilaiArr) / count($nilaiArr))
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="name-col">{{ $student->name }}</td>
                                <td>{{ $student->nis ?? '-' }}</td>
                                @foreach($this->subjects as $subject)
                                    @php $n = $nilaiArr[$subject->id]; @endphp
                                    <td class="{{ $n == 0 ? 'nilai-0' : ($n >= 75 ? 'nilai-good' : '') }}">
                                        {{ $n }}
                                    </td>
                                @endforeach
                                <td style="font-weight:700; background:#eff6ff;">
                                    {{ $rataRata }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + $this->subjects->count() + 1 }}" class="text-center py-4 text-gray-400">
                                    Belum ada siswa di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer tanda tangan --}}
            <div style="margin-top: 24px; display: flex; justify-content: space-between; font-size: 10px; color: #374151;">
                <div style="text-align:center; width:200px;">
                    <div>Mengetahui,</div>
                    <div>Kepala Sekolah</div>
                    <div style="height:50px;"></div>
                    <div style="border-top:1px solid #333; padding-top:2px;">____________________</div>
                </div>
                <div style="text-align:center; width:200px;">
                    <div>Blora, {{ now()->translatedFormat('d F Y') }}</div>
                    <div>Wali Kelas</div>
                    <div style="height:50px;"></div>
                    <div style="border-top:1px solid #333; padding-top:2px;">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    @endif
</div>
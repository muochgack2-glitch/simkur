<style>
@media (min-width: 768px) {
    .asts-mobile-cards { display: none !important; }
    .asts-desktop-table { display: block !important; }
}
@media (max-width: 767px) {
    .asts-mobile-cards { display: block !important; }
    .asts-desktop-table { display: none !important; }
}
</style>
<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">📊 Monitoring Jadwal ASTS</h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-500">Cocokkan jadwal ujian dengan soal yang sudah dibuat guru</p>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('admin.asts-monitoring.template') }}"
               style="display:inline-flex;align-items:center;gap:6px;border-radius:8px;border:1px solid #86efac;background:#f0fdf4;padding:6px 12px;font-size:12px;font-weight:600;color:#15803d;text-decoration:none">
                ⬇️ <span class="hidden sm:inline">Template</span> XLS
            </a>
            <button wire:click="toggleImport"
                    style="display:inline-flex;align-items:center;gap:6px;border-radius:8px;border:1px solid #93c5fd;background:#eff6ff;padding:6px 12px;font-size:12px;font-weight:600;color:#1d4ed8;cursor:pointer">
                📂 <span class="hidden sm:inline">Update dari</span> Excel
            </button>
        </div>
    </div>

    {{-- DEBUG: Test Livewire wire:click --}}
    <div style="margin-bottom:12px;padding:8px 12px;background:#fef9c3;border:1px solid #fde047;border-radius:8px;font-size:12px">
        🔧 Debug Livewire:
        <button wire:click="toggleImport"
                style="margin-left:8px;padding:4px 10px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px">
            Test Klik (showImport={{ $showImport ? 'true' : 'false' }})
        </button>
        &nbsp;|&nbsp; filterStatus: <b>{{ $filterStatus ?: '(kosong)' }}</b>
        &nbsp;|&nbsp; filtered: <b>{{ count($filteredRows) }}</b>/{{ count($rows) }}
    </div>

    {{-- Panel re-import Excel --}}
    @if($showImport)
        <div style="margin-bottom:20px;border-radius:12px;border:1px solid #93c5fd;background:#eff6ff;padding:16px">
            <p style="margin-bottom:8px;font-size:14px;font-weight:600;color:#1e40af">Upload Excel untuk update jadwal</p>
            <label style="cursor:pointer">
                <input type="file" wire:model="file" accept=".xlsx,.xls" class="sr-only">
                <span style="display:inline-flex;align-items:center;gap:8px;border-radius:8px;background:#2563eb;padding:8px 16px;font-size:13px;font-weight:600;color:#fff">
                    Pilih File Excel
                </span>
            </label>
            <div wire:loading wire:target="file" style="margin-top:8px;font-size:12px;color:#2563eb">Memproses...</div>
            @if($importMsg)
                <p style="margin-top:8px;font-size:13px;font-weight:500;color:{{ str_starts_with($importMsg,'✅') ? '#15803d' : '#dc2626' }}">{{ $importMsg }}</p>
            @endif
        </div>
    @endif

    @php
        $rows     = $this->rows;
        $summary  = $this->summary;
        $filtered = $this->filteredRows;
        $total    = count($rows);
        $pct      = $total > 0 ? round($summary['ok'] / $total * 100) : 0;
        $barColor = $pct >= 80 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-500');
    @endphp

    @if($total === 0)
        <div class="rounded-xl border border-orange-200 bg-orange-50 p-6 text-center">
            <p class="text-orange-700 font-semibold">Belum ada data jadwal.</p>
            <p class="text-xs text-orange-500 mt-1">Jalankan: <code class="bg-orange-100 px-1.5 py-0.5 rounded">php artisan db:seed --class=AstsScheduleSeeder</code></p>
        </div>
    @else
        {{-- Summary cards — klik untuk filter --}}
        @php
            $cardDefs = [
                ['status'=>'ok',            'count'=>$summary['ok'],   'label'=>'Soal Lengkap',       'icon'=>'✅',
                 'bg'=>'#f0fdf4','bgA'=>'#dcfce7','border'=>'#86efac','borderA'=>'#22c55e','num'=>'#15803d','lbl'=>'#166534'],
                ['status'=>'no_questions',  'count'=>$summary['noQ'],  'label'=>'Belum Buat Soal',    'icon'=>'⚠️',
                 'bg'=>'#fefce8','bgA'=>'#fef9c3','border'=>'#fde047','borderA'=>'#eab308','num'=>'#a16207','lbl'=>'#854d0e'],
                ['status'=>'no_assessment', 'count'=>$summary['noA'],  'label'=>'Belum Asesmen',      'icon'=>'❌',
                 'bg'=>'#fff1f2','bgA'=>'#ffe4e6','border'=>'#fca5a5','borderA'=>'#ef4444','num'=>'#dc2626','lbl'=>'#991b1b'],
                ['status'=>'not_found',     'count'=>$summary['notF'], 'label'=>'Guru Tdk Ditemukan', 'icon'=>'🔍',
                 'bg'=>'#f9fafb','bgA'=>'#f3f4f6','border'=>'#d1d5db','borderA'=>'#9ca3af','num'=>'#4b5563','lbl'=>'#6b7280'],
            ];
        @endphp
        <div class="mb-4 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
            @foreach($cardDefs as $card)
                @php $active = $filterStatus === $card['status']; @endphp
                <button wire:click="setStatusFilter('{{ $card['status'] }}')"
                        title="{{ $active ? 'Klik untuk hapus filter' : 'Klik untuk filter: '.$card['label'] }}"
                        style="
                            border-radius:12px;
                            border:2px solid {{ $active ? $card['borderA'] : $card['border'] }};
                            background:{{ $active ? $card['bgA'] : $card['bg'] }};
                            padding:12px 8px;
                            text-align:center;
                            cursor:pointer;
                            transition:all .15s;
                            {{ $active ? 'box-shadow:0 0 0 3px '.($card['borderA']).'55;' : '' }}
                            outline:none;
                        ">
                    <div style="font-size:26px;font-weight:800;color:{{ $card['num'] }};line-height:1.2">
                        {{ $card['count'] }}
                    </div>
                    <div style="font-size:11px;font-weight:600;color:{{ $card['lbl'] }};margin-top:2px;line-height:1.3">
                        {{ $card['icon'] }} {{ $card['label'] }}
                    </div>
                    @if($active)
                        <div style="font-size:10px;color:{{ $card['borderA'] }};margin-top:4px;font-weight:600">
                            ← aktif · klik reset
                        </div>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Progress bar --}}
        @php
            $pctColor = $pct >= 80 ? '#15803d' : ($pct >= 50 ? '#a16207' : '#dc2626');
            $barBg    = $pct >= 80 ? '#22c55e' : ($pct >= 50 ? '#eab308' : '#ef4444');
        @endphp
        <div style="margin-bottom:16px;border-radius:12px;background:#fff;border:1px solid #e5e7eb;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.06)">
            <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px">
                <span>Progress Persiapan Soal ASTS</span>
                <span style="color:{{ $pctColor }}">{{ $summary['ok'] }}/{{ $total }} ({{ $pct }}%)</span>
            </div>
            <div style="width:100%;background:#f3f4f6;border-radius:9999px;height:10px;overflow:hidden">
                <div style="background:{{ $barBg }};height:10px;border-radius:9999px;width:{{ $pct }}%;transition:width .7s"></div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <select wire:model.live="filterHari"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;font-size:12px;color:#374151;background:#fff;outline:none">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterKelas"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;font-size:12px;color:#374151;background:#fff;outline:none">
                <option value="">Semua Kelas</option>
                <option value="X">X</option>
                <option value="XI">XI</option>
            </select>
            <select wire:model.live="filterJurusan"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;font-size:12px;color:#374151;background:#fff;outline:none">
                <option value="">Semua Jurusan</option>
                <option value="AKL">AKL</option>
                <option value="BUSANA">BUSANA</option>
                <option value="MPLB">MPLB</option>
            </select>
            @if($filterHari || $filterKelas || $filterJurusan || $filterStatus)
                <button wire:click="resetFilter"
                        style="border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;font-size:12px;color:#6b7280;background:#fff;cursor:pointer">
                    ✕ Reset ({{ count($filtered) }}/{{ $total }})
                </button>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════
             MOBILE: Card list — dikelompok per Hari
             ═══════════════════════════════════════════════════════ --}}
        @php
            // warna hari: [gradient-start, gradient-end, text, bg, border]
            $hariStyles = [
                'Senin'  => ['#2563eb','#1d4ed8','#1e40af','#eff6ff','#bfdbfe'],
                'Selasa' => ['#4f46e5','#4338ca','#312e81','#eef2ff','#c7d2fe'],
                'Rabu'   => ['#7c3aed','#6d28d9','#4c1d95','#f5f3ff','#ddd6fe'],
                'Kamis'  => ['#9333ea','#7e22ce','#581c87','#faf5ff','#e9d5ff'],
                'Jumat'  => ['#c026d3','#a21caf','#701a75','#fdf4ff','#f0abfc'],
            ];
            $prevHariM = null;
        @endphp
        <div class="asts-mobile-cards space-y-2 mb-4">
            @forelse($filtered as $row)
                @php
                    $badge = match($row['status']) {
                        'ok'            => ['text'=>'✅ Lengkap',        'cls'=>'bg-green-100 text-green-700 border-green-200'],
                        'no_questions'  => ['text'=>'⚠️ Belum Soal',     'cls'=>'bg-yellow-100 text-yellow-700 border-yellow-200'],
                        'no_assessment' => ['text'=>'❌ Belum Asesmen',  'cls'=>'bg-red-100 text-red-700 border-red-200'],
                        'not_found'     => ['text'=>'🔍 Tdk Ditemukan',  'cls'=>'bg-gray-100 text-gray-600 border-gray-200'],
                        default         => ['text'=>'?', 'cls'=>'bg-gray-100 text-gray-500 border-gray-200'],
                    };
                    $cardBg = match($row['status']) {
                        'no_questions'  => 'bg-yellow-50 border-yellow-200',
                        'no_assessment' => 'bg-red-50 border-red-200',
                        'not_found'     => 'bg-gray-50 border-gray-200',
                        default         => 'bg-white border-gray-200',
                    };
                    $hc = $hariColors[$row['hari']] ?? 'blue';
                @endphp
                {{-- Pemisah hari --}}
                @if($row['hari'] !== $prevHariM)
                    @php $hs = $hariStyles[$row['hari']] ?? ['#374151','#1f2937','#111827','#f9fafb','#e5e7eb']; @endphp
                    <div class="pt-3 pb-1 flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest rounded-full px-3 py-0.5"
                              style="color:{{ $hs[2] }};background:{{ $hs[3] }};border:1px solid {{ $hs[4] }}">
                            📅 {{ $row['hari'] }}
                        </span>
                        <div class="flex-1 h-px" style="background:{{ $hs[4] }}"></div>
                    </div>
                    @php $prevHariM = $row['hari']; @endphp
                @endif
                <div class="rounded-xl border {{ $cardBg }} p-3 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $row['sesi'] }}</span>
                            <span class="text-xs font-bold text-gray-700">{{ $row['kelas'] }}</span>
                            <span class="rounded px-1.5 py-0.5 text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">{{ $row['jurusan'] }}</span>
                        </div>
                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $badge['cls'] }}">
                            {{ $badge['text'] }}
                        </span>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 mb-1">{{ $row['mapel'] }}</p>
                    <div class="flex items-center justify-between">
                        <div>
                            @if($row['guru_sistem'] !== '-')
                                <p class="text-xs font-medium text-indigo-700">👤 {{ $row['guru_sistem'] }}</p>
                            @else
                                <p class="text-xs text-gray-400 italic">Guru tidak ditemukan</p>
                            @endif
                            @if($row['status'] === 'ok')
                                <p class="text-xs text-green-600 mt-0.5">{{ $row['q_count'] }} soal</p>
                            @elseif($row['status'] === 'no_questions')
                                <p class="text-xs text-red-500 mt-0.5">0 soal</p>
                            @endif
                        </div>
                        @if($row['assessment_id'])
                            <a href="{{ route('teacher.assessment.questions', $row['assessment_id']) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs text-indigo-700 hover:bg-indigo-100 transition">
                                📝 Buka Soal
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-400 text-sm">
                    Tidak ada data sesuai filter.
                </div>
            @endforelse
        </div>

        {{-- ═══════════════════════════════════════════════════════
             DESKTOP: Tabel dikelompok per Hari → Kelas+Jurusan
             ═══════════════════════════════════════════════════════ --}}
        @php
            $prevHari  = null; $prevGroup = null;
            // warna solid per hari (background header hari)
            $hariBg = [
                'Senin'  => '#1e40af', // biru tua
                'Selasa' => '#3730a3', // indigo tua
                'Rabu'   => '#5b21b6', // ungu tua
                'Kamis'  => '#6b21a8', // violet tua
                'Jumat'  => '#86198f', // fuchsia tua
            ];
            // warna border-left untuk sub-header kelas
            $jurusanBorderColor = [
                'AKL'    => '#2563eb',
                'BUSANA' => '#7c3aed',
                'MPLB'   => '#16a34a',
            ];
            $jurusanColors = [
                'AKL'    => ['bg'=>'#f0f9ff','border'=>'#2563eb','text'=>'#1e3a8a'],
                'BUSANA' => ['bg'=>'#f5f3ff','border'=>'#7c3aed','text'=>'#4c1d95'],
                'MPLB'   => ['bg'=>'#f0fdf4','border'=>'#16a34a','text'=>'#14532d'],
            ];
        @endphp
        <div class="asts-desktop-table rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead style="position:sticky;top:0;z-index:10">
                        <tr style="background:#1e293b;font-size:11px;font-weight:700;color:#f1f5f9;text-transform:uppercase;letter-spacing:1px">
                            <th style="padding:10px 12px;text-align:center">Sesi</th>
                            <th style="padding:10px 12px;text-align:center">Kelas</th>
                            <th style="padding:10px 12px;text-align:center">Jurusan</th>
                            <th style="padding:10px 16px;text-align:left">Mata Pelajaran</th>
                            <th style="padding:10px 16px;text-align:left">Guru (Sistem)</th>
                            <th style="padding:10px 12px;text-align:center">Soal</th>
                            <th style="padding:10px 16px;text-align:center">Status</th>
                            <th style="padding:10px 12px;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filtered as $row)
                            @php
                                $group  = $row['kelas'].' '.$row['jurusan'];
                                $jc     = $jurusanColors[$row['jurusan']] ?? ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#374151'];
                                $badge  = match($row['status']) {
                                    'ok'            => ['text'=>'✅ Lengkap',       'cls'=>'bg-green-100 text-green-700 border-green-200'],
                                    'no_questions'  => ['text'=>'⚠️ Belum Soal',    'cls'=>'bg-yellow-100 text-yellow-700 border-yellow-200'],
                                    'no_assessment' => ['text'=>'❌ Belum Asesmen', 'cls'=>'bg-red-100 text-red-700 border-red-200'],
                                    'not_found'     => ['text'=>'🔍 Tdk Ditemukan', 'cls'=>'bg-gray-100 text-gray-600 border-gray-200'],
                                    default         => ['text'=>'?',                'cls'=>'bg-gray-100 text-gray-500 border-gray-200'],
                                };
                                $bgRow  = match($row['status']) {
                                    'no_questions'  => 'background:#fffbeb',
                                    'no_assessment' => 'background:#fff1f2',
                                    'not_found'     => 'background:#f9fafb',
                                    default         => 'background:#ffffff',
                                };
                            @endphp

                            {{-- ── PEMISAH HARI ── --}}
                            @if($row['hari'] !== $prevHari)
                                @php $hbg = $hariBg[$row['hari']] ?? '#374151'; @endphp
                                <tr>
                                    <td colspan="8" style="background:{{ $hbg }};padding:8px 16px">
                                        <strong style="color:#ffffff;font-size:12px;letter-spacing:2px;text-transform:uppercase">
                                            &#128197; {{ $row['hari'] }}
                                        </strong>
                                    </td>
                                </tr>
                                @php $prevHari = $row['hari']; $prevGroup = null; @endphp
                            @endif

                            {{-- ── PEMISAH KELAS + JURUSAN ── --}}
                            @if($group !== $prevGroup)
                                @php $jbc = $jurusanBorderColor[$row['jurusan']] ?? '#6b7280'; @endphp
                                <tr>
                                    <td colspan="8" style="background:#f8fafc;border-left:4px solid {{ $jbc }};padding:6px 16px;border-bottom:1px solid #e2e8f0">
                                        <strong style="color:#1e293b;font-size:11px;letter-spacing:1.5px;text-transform:uppercase">
                                            Kelas {{ $row['kelas'] }} &mdash; {{ $row['jurusan'] }}
                                        </strong>
                                    </td>
                                </tr>
                                @php $prevGroup = $group; @endphp
                            @endif

                            {{-- ── DATA ROW ── --}}
                            <tr class="border-b border-gray-100 hover:brightness-95 transition" style="{{ $bgRow }}">
                                <td class="px-3 py-2.5 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $row['sesi'] }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-gray-700">{{ $row['kelas'] }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="rounded px-1.5 py-0.5 text-xs font-semibold border"
                                          style="background:{{ $jc['bg'] }};color:{{ $jc['text'] }};border-color:{{ $jc['border'] }}">
                                        {{ $row['jurusan'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 font-medium text-gray-800">{{ $row['mapel'] }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    @if($row['guru_sistem'] !== '-')
                                        <span class="font-semibold text-indigo-700">{{ $row['guru_sistem'] }}</span>
                                    @else
                                        <span class="text-gray-300 italic">Tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold">
                                    @if($row['status'] === 'ok')
                                        <span class="text-green-600">{{ $row['q_count'] }}</span>
                                    @elseif($row['status'] === 'no_questions')
                                        <span class="text-red-500">0</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $badge['cls'] }}">{{ $badge['text'] }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($row['assessment_id'])
                                        <a href="{{ route('teacher.assessment.questions', $row['assessment_id']) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-100 transition">
                                            📝 Soal
                                        </a>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-gray-400">Tidak ada data sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 bg-gray-50 px-4 py-2 text-xs text-gray-400 flex justify-between">
                <span>Menampilkan {{ count($filtered) }} dari {{ $total }} sesi jadwal</span>
                <span>🟦 AKL &nbsp; 🟣 BUSANA &nbsp; 🟩 MPLB</span>
            </div>
        </div>
    @endif
</div>

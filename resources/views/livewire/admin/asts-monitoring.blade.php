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
               class="inline-flex items-center gap-1.5 rounded-lg border border-green-300 bg-green-50 px-3 py-2 text-xs sm:text-sm font-semibold text-green-700 hover:bg-green-100 transition">
                ⬇️ <span class="hidden sm:inline">Template</span> XLS
            </a>
            <button wire:click="$toggle('showImport')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-blue-300 bg-blue-50 px-3 py-2 text-xs sm:text-sm font-semibold text-blue-700 hover:bg-blue-100 transition">
                📂 <span class="hidden sm:inline">Update dari</span> Excel
            </button>
        </div>
    </div>

    {{-- Panel re-import Excel --}}
    @if($showImport)
        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-4">
            <p class="mb-2 text-sm font-semibold text-blue-800">Upload Excel untuk update jadwal</p>
            <label class="cursor-pointer">
                <input type="file" wire:model="file" accept=".xlsx,.xls" class="sr-only">
                <span class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                    Pilih File Excel
                </span>
            </label>
            <div wire:loading wire:target="file" class="mt-2 text-xs text-blue-600">Memproses...</div>
            @if($importMsg)
                <p class="mt-2 text-sm font-medium {{ str_starts_with($importMsg,'✅') ? 'text-green-700' : 'text-red-600' }}">{{ $importMsg }}</p>
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
        {{-- Summary cards --}}
        <div class="mb-4 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
            @foreach([
                ['status'=>'ok',            'count'=>$summary['ok'],   'label'=>'Soal Lengkap',       'icon'=>'✅', 'color'=>'green'],
                ['status'=>'no_questions',  'count'=>$summary['noQ'],  'label'=>'Belum Buat Soal',    'icon'=>'⚠️', 'color'=>'yellow'],
                ['status'=>'no_assessment', 'count'=>$summary['noA'],  'label'=>'Belum Asesmen',      'icon'=>'❌', 'color'=>'red'],
                ['status'=>'not_found',     'count'=>$summary['notF'], 'label'=>'Guru Tdk Ditemukan', 'icon'=>'🔍', 'color'=>'gray'],
            ] as $card)
                @php
                    $active = $filterStatus === $card['status'];
                    $c = $card['color'];
                    $border = $active ? "border-{$c}-400 bg-{$c}-100" : "border-{$c}-200 bg-{$c}-50 hover:border-{$c}-300";
                @endphp
                <button wire:click="$set('filterStatus', '{{ $active ? '' : $card['status'] }}')"
                        class="rounded-xl border-2 p-3 sm:p-4 text-center transition {{ $border }}">
                    <div class="text-2xl sm:text-3xl font-bold text-{{ $c }}-{{ $c === 'gray' ? '600' : '700' }}">{{ $card['count'] }}</div>
                    <div class="text-xs font-semibold text-{{ $c }}-{{ $c === 'gray' ? '500' : '600' }} mt-0.5 leading-tight">
                        {{ $card['icon'] }} {{ $card['label'] }}
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Progress bar --}}
        <div class="mb-4 rounded-xl bg-white border border-gray-200 p-3 sm:p-4 shadow-sm">
            <div class="flex justify-between text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                <span>Progress Persiapan Soal ASTS</span>
                <span class="{{ $pct >= 80 ? 'text-green-700' : ($pct >= 50 ? 'text-yellow-700' : 'text-red-700') }}">
                    {{ $summary['ok'] }}/{{ $total }} ({{ $pct }}%)
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <select wire:model.live="filterHari"
                    class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterKelas"
                    class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Kelas</option>
                <option value="X">X</option>
                <option value="XI">XI</option>
            </select>
            <select wire:model.live="filterJurusan"
                    class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Jurusan</option>
                <option value="AKL">AKL</option>
                <option value="BUSANA">BUSANA</option>
                <option value="MPLB">MPLB</option>
            </select>
            @if($filterHari || $filterKelas || $filterJurusan || $filterStatus)
                <button wire:click="resetFilter"
                        class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 transition">
                    ✕ Reset
                </button>
                <span class="text-xs text-gray-400">{{ count($filtered) }}/{{ $total }}</span>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════
             MOBILE: Card list — dikelompok per Hari
             ═══════════════════════════════════════════════════════ --}}
        @php
            $hariColors = ['Senin'=>'blue','Selasa'=>'indigo','Rabu'=>'violet','Kamis'=>'purple','Jumat'=>'fuchsia'];
            $prevHariM  = null;
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
                    <div class="pt-2 pb-1 flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-{{ $hc }}-600 bg-{{ $hc }}-50 border border-{{ $hc }}-200 rounded-full px-3 py-0.5">
                            📅 {{ $row['hari'] }}
                        </span>
                        <div class="flex-1 h-px bg-{{ $hc }}-100"></div>
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
            $prevHari = null; $prevGroup = null;
            $jurusanColors = ['AKL'=>['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1d4ed8'],
                               'BUSANA'=>['bg'=>'#faf5ff','border'=>'#d8b4fe','text'=>'#7e22ce'],
                               'MPLB'=>['bg'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#15803d']];
            $hariGrad = ['Senin'=>'from-blue-600 to-blue-500','Selasa'=>'from-indigo-600 to-indigo-500',
                         'Rabu'=>'from-violet-600 to-violet-500','Kamis'=>'from-purple-600 to-purple-500',
                         'Jumat'=>'from-fuchsia-600 to-fuchsia-500'];
        @endphp
        <div class="asts-desktop-table rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gray-800 text-xs font-semibold text-gray-200 uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">Hari</th>
                            <th class="px-3 py-3 text-center">Sesi</th>
                            <th class="px-3 py-3 text-center">Kelas</th>
                            <th class="px-3 py-3 text-center">Jurusan</th>
                            <th class="px-4 py-3 text-left">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left">Guru (Sistem)</th>
                            <th class="px-3 py-3 text-center">Soal</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filtered as $row)
                            @php
                                $group  = $row['kelas'].' '.$row['jurusan'];
                                $jc     = $jurusanColors[$row['jurusan']] ?? ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#374151'];
                                $hg     = $hariGrad[$row['hari']] ?? 'from-gray-600 to-gray-500';
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
                                <tr>
                                    <td colspan="9" class="p-0">
                                        <div class="bg-gradient-to-r {{ $hg }} px-4 py-2 flex items-center gap-2">
                                            <span class="text-white font-bold text-sm tracking-wide">📅 {{ $row['hari'] }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @php $prevHari = $row['hari']; $prevGroup = null; @endphp
                            @endif

                            {{-- ── PEMISAH KELAS + JURUSAN ── --}}
                            @if($group !== $prevGroup)
                                <tr>
                                    <td colspan="9" class="px-4 py-1.5 border-b"
                                        style="background:{{ $jc['bg'] }}; border-color:{{ $jc['border'] }}">
                                        <span class="text-xs font-bold uppercase tracking-widest"
                                              style="color:{{ $jc['text'] }}">
                                            🏫 Kelas {{ $row['kelas'] }} — {{ $row['jurusan'] }}
                                        </span>
                                    </td>
                                </tr>
                                @php $prevGroup = $group; @endphp
                            @endif

                            {{-- ── DATA ROW ── --}}
                            <tr class="border-b border-gray-100 hover:brightness-95 transition" style="{{ $bgRow }}">
                                <td class="px-4 py-2.5 text-gray-400 text-xs italic">{{ $row['hari'] }}</td>
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
                                <td colspan="9" class="px-4 py-10 text-center text-gray-400">Tidak ada data sesuai filter.</td>
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

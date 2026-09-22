<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Monitoring Jadwal ASTS</h1>
            <p class="mt-1 text-sm text-gray-500">Upload jadwal ASTS (Excel) → sistem otomatis cocokkan dengan soal yang sudah dibuat guru</p>
        </div>
        <a href="{{ route('admin.asts-monitoring.template') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-50 px-4 py-2 text-sm font-semibold text-green-700 hover:bg-green-100 transition shrink-0">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Template Excel
        </a>
    </div>

    {{-- Upload area --}}
    <div class="mb-6 rounded-xl border-2 border-dashed border-blue-200 bg-blue-50 p-6">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-600 text-white text-2xl shrink-0">
                📂
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-blue-800">Upload file Excel jadwal ASTS</p>
                <p class="text-xs text-blue-600 mt-0.5">Format: .xlsx atau .xls • Kolom: Hari, Sesi, Kelas, Jurusan, Nama Mapel, Nama Guru</p>
            </div>
            <label class="cursor-pointer">
                <input type="file" wire:model="file" accept=".xlsx,.xls" class="sr-only">
                <span class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    Pilih File
                </span>
            </label>
        </div>
        <div wire:loading wire:target="file" class="mt-3 flex items-center gap-2 text-sm text-blue-600">
            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Sedang membaca dan mencocokkan data...
        </div>
    </div>

    {{-- Error --}}
    @if($error)
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">⚠️ {{ $error }}</div>
    @endif

    @if($parsed && count($rows) > 0)
        @php
            $summary = $this->summary;
            $total   = count($rows);
        @endphp

        {{-- Summary cards --}}
        <div class="mb-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-center cursor-pointer hover:border-green-400 transition"
                 wire:click="$set('filterStatus', '{{ $filterStatus === 'ok' ? '' : 'ok' }}')"
                 title="Klik untuk filter">
                <div class="text-3xl font-bold text-green-700">{{ $summary['ok'] }}</div>
                <div class="text-xs font-medium text-green-600 mt-1">✅ Soal Lengkap</div>
            </div>
            <div class="rounded-xl bg-yellow-50 border border-yellow-200 p-4 text-center cursor-pointer hover:border-yellow-400 transition"
                 wire:click="$set('filterStatus', '{{ $filterStatus === 'no_questions' ? '' : 'no_questions' }}')"
                 title="Klik untuk filter">
                <div class="text-3xl font-bold text-yellow-600">{{ $summary['noQ'] }}</div>
                <div class="text-xs font-medium text-yellow-600 mt-1">⚠️ Belum Buat Soal</div>
            </div>
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-center cursor-pointer hover:border-red-400 transition"
                 wire:click="$set('filterStatus', '{{ $filterStatus === 'no_assessment' ? '' : 'no_assessment' }}')"
                 title="Klik untuk filter">
                <div class="text-3xl font-bold text-red-700">{{ $summary['noA'] }}</div>
                <div class="text-xs font-medium text-red-600 mt-1">❌ Belum Buat Asesmen</div>
            </div>
            <div class="rounded-xl bg-gray-50 border border-gray-200 p-4 text-center cursor-pointer hover:border-gray-400 transition"
                 wire:click="$set('filterStatus', '{{ $filterStatus === 'not_found' ? '' : 'not_found' }}')"
                 title="Klik untuk filter">
                <div class="text-3xl font-bold text-gray-600">{{ $summary['notF'] }}</div>
                <div class="text-xs font-medium text-gray-500 mt-1">🔍 Guru Tidak Ditemukan</div>
            </div>
        </div>

        {{-- Progress bar --}}
        @php
            $pct = $total > 0 ? round($summary['ok'] / $total * 100) : 0;
            $barColor = $pct >= 80 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-500');
        @endphp
        <div class="mb-5 rounded-xl bg-white border border-gray-200 p-4 shadow-sm">
            <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
                <span>Progress Persiapan Soal ASTS</span>
                <span class="font-bold {{ $pct >= 80 ? 'text-green-700' : ($pct >= 50 ? 'text-yellow-700' : 'text-red-700') }}">{{ $summary['ok'] }} / {{ $total }} ({{ $pct }}%)</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="mb-4 flex flex-wrap gap-2">
            <select wire:model.live="filterHari" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterKelas" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Kelas</option>
                <option value="X">X</option>
                <option value="XI">XI</option>
                <option value="XII">XII</option>
            </select>
            <select wire:model.live="filterJurusan" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Jurusan</option>
                <option value="AKL">AKL</option>
                <option value="BUSANA">BUSANA</option>
                <option value="MPLB">MPLB</option>
            </select>
            @if($filterStatus || $filterHari || $filterKelas || $filterJurusan)
                <button wire:click="$set('filterStatus',''); $set('filterHari',''); $set('filterKelas',''); $set('filterJurusan','')"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 transition">
                    ✕ Reset Filter
                </button>
            @endif
        </div>

        {{-- Tabel hasil --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hari</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Jurusan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru (Jadwal)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru (Sistem)</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Soal</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $hariOrder = ['Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5];
                            $filtered  = collect($this->filteredRows)->sortBy([
                                fn($a,$b) => ($hariOrder[$a['hari']]??9) <=> ($hariOrder[$b['hari']]??9),
                                fn($a,$b) => $a['sesi'] <=> $b['sesi'],
                            ]);
                        @endphp
                        @forelse($filtered as $row)
                            @php
                                $bgRow = match($row['status']) {
                                    'ok'            => '',
                                    'no_questions'  => 'bg-yellow-50',
                                    'no_assessment' => 'bg-red-50',
                                    'not_found'     => 'bg-gray-50',
                                    default         => '',
                                };
                                $badge = match($row['status']) {
                                    'ok'            => ['text'=>'✅ Lengkap',          'cls'=>'bg-green-100 text-green-700 border-green-200'],
                                    'no_questions'  => ['text'=>'⚠️ Belum Soal',       'cls'=>'bg-yellow-100 text-yellow-700 border-yellow-200'],
                                    'no_assessment' => ['text'=>'❌ Belum Asesmen',    'cls'=>'bg-red-100 text-red-700 border-red-200'],
                                    'not_found'     => ['text'=>'🔍 Guru Tidak Ada',   'cls'=>'bg-gray-100 text-gray-600 border-gray-200'],
                                    default         => ['text'=>'?', 'cls'=>'bg-gray-100 text-gray-500'],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition {{ $bgRow }}">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $row['hari'] }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $row['sesi'] }}</span>
                                </td>
                                <td class="px-3 py-3 text-center text-gray-700 font-medium">{{ $row['kelas'] }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="rounded-lg bg-purple-50 border border-purple-200 px-2 py-0.5 text-xs font-medium text-purple-700">{{ $row['jurusan'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-800 font-medium">{{ $row['mapel'] }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $row['guru_input'] }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($row['guru_sistem'] !== '-')
                                        <span class="font-semibold text-indigo-700">{{ $row['guru_sistem'] }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['status'] === 'ok')
                                        <span class="font-bold text-green-700">{{ $row['q_count'] }}</span>
                                    @elseif($row['status'] === 'no_questions')
                                        <span class="font-bold text-red-600">0</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badge['cls'] }}">
                                        {{ $badge['text'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['assessment_id'])
                                        <a href="{{ route('teacher.assessment.questions', $row['assessment_id']) }}" wire:navigate
                                           class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs text-gray-700 hover:bg-gray-50 transition">
                                            Buka Soal
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-10 text-center text-gray-400">
                                    Tidak ada data yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($filtered) > 0)
                <div class="border-t border-gray-100 px-4 py-2 text-xs text-gray-400 text-right">
                    Menampilkan {{ count($filtered) }} dari {{ $total }} baris
                </div>
            @endif
        </div>

    @elseif($parsed && count($rows) === 0 && !$error)
        <div class="rounded-xl border border-gray-200 bg-white p-10 text-center text-gray-400">
            Tidak ada data valid di file. Pastikan format kolom sesuai template.
        </div>
    @endif
</div>

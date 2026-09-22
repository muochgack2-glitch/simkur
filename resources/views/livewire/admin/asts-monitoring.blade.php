<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Monitoring Jadwal ASTS</h1>
            <p class="mt-1 text-sm text-gray-500">
                Data jadwal dari seeder — sistem otomatis cocokkan dengan soal yang sudah dibuat guru
            </p>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('admin.asts-monitoring.template') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-green-300 bg-green-50 px-3 py-2 text-sm font-semibold text-green-700 hover:bg-green-100 transition">
                ⬇️ Template XLS
            </a>
            <button wire:click="$toggle('showImport')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-blue-300 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition">
                📂 Update dari Excel
            </button>
        </div>
    </div>

    {{-- Panel re-import Excel (collapsible) --}}
    @if($showImport)
        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-4">
            <p class="mb-2 text-sm font-semibold text-blue-800">Upload Excel untuk update jadwal (gunakan format template)</p>
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
            <p class="text-sm text-orange-500 mt-1">Jalankan: <code class="bg-orange-100 px-2 py-0.5 rounded">php artisan db:seed --class=AstsScheduleSeeder</code></p>
        </div>
    @else
        {{-- Summary cards --}}
        <div class="mb-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button wire:click="{{ $filterStatus === 'ok' ? '$set(\'filterStatus\',\'\')' : '$set(\'filterStatus\',\'ok\')' }}"
                    class="rounded-xl border-2 p-4 text-center transition {{ $filterStatus === 'ok' ? 'border-green-400 bg-green-100' : 'border-green-200 bg-green-50 hover:border-green-300' }}">
                <div class="text-3xl font-bold text-green-700">{{ $summary['ok'] }}</div>
                <div class="text-xs font-semibold text-green-600 mt-1">✅ Soal Lengkap</div>
            </button>
            <button wire:click="{{ $filterStatus === 'no_questions' ? '$set(\'filterStatus\',\'\')' : '$set(\'filterStatus\',\'no_questions\')' }}"
                    class="rounded-xl border-2 p-4 text-center transition {{ $filterStatus === 'no_questions' ? 'border-yellow-400 bg-yellow-100' : 'border-yellow-200 bg-yellow-50 hover:border-yellow-300' }}">
                <div class="text-3xl font-bold text-yellow-600">{{ $summary['noQ'] }}</div>
                <div class="text-xs font-semibold text-yellow-600 mt-1">⚠️ Belum Buat Soal</div>
            </button>
            <button wire:click="{{ $filterStatus === 'no_assessment' ? '$set(\'filterStatus\',\'\')' : '$set(\'filterStatus\',\'no_assessment\')' }}"
                    class="rounded-xl border-2 p-4 text-center transition {{ $filterStatus === 'no_assessment' ? 'border-red-400 bg-red-100' : 'border-red-200 bg-red-50 hover:border-red-300' }}">
                <div class="text-3xl font-bold text-red-700">{{ $summary['noA'] }}</div>
                <div class="text-xs font-semibold text-red-600 mt-1">❌ Belum Buat Asesmen</div>
            </button>
            <button wire:click="{{ $filterStatus === 'not_found' ? '$set(\'filterStatus\',\'\')' : '$set(\'filterStatus\',\'not_found\')' }}"
                    class="rounded-xl border-2 p-4 text-center transition {{ $filterStatus === 'not_found' ? 'border-gray-400 bg-gray-100' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                <div class="text-3xl font-bold text-gray-600">{{ $summary['notF'] }}</div>
                <div class="text-xs font-semibold text-gray-500 mt-1">🔍 Guru Tidak Ditemukan</div>
            </button>
        </div>

        {{-- Progress bar --}}
        <div class="mb-5 rounded-xl bg-white border border-gray-200 p-4 shadow-sm">
            <div class="flex justify-between text-sm font-semibold text-gray-700 mb-2">
                <span>Progress Persiapan Soal ASTS</span>
                <span class="{{ $pct >= 80 ? 'text-green-700' : ($pct >= 50 ? 'text-yellow-700' : 'text-red-700') }}">
                    {{ $summary['ok'] }} / {{ $total }} ({{ $pct }}%)
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <select wire:model.live="filterHari" id="filter-hari"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterKelas" id="filter-kelas"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Kelas</option>
                <option value="X">X</option>
                <option value="XI">XI</option>
            </select>
            <select wire:model.live="filterJurusan" id="filter-jurusan"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Semua Jurusan</option>
                <option value="AKL">AKL</option>
                <option value="BUSANA">BUSANA</option>
                <option value="MPLB">MPLB</option>
            </select>
            @if($filterHari || $filterKelas || $filterJurusan || $filterStatus)
                <button wire:click="resetFilter"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 transition">
                    ✕ Reset Filter
                </button>
                <span class="text-xs text-gray-400">{{ count($filtered) }} dari {{ $total }} baris</span>
            @endif
        </div>

        {{-- Tabel --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-4 py-3 text-left">Hari</th>
                            <th class="px-3 py-3 text-center">Sesi</th>
                            <th class="px-3 py-3 text-center">Kelas</th>
                            <th class="px-3 py-3 text-center">Jurusan</th>
                            <th class="px-4 py-3 text-left">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left">Guru (Jadwal)</th>
                            <th class="px-4 py-3 text-left">Guru (Sistem)</th>
                            <th class="px-3 py-3 text-center">Soal</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($filtered as $row)
                            @php
                                $bgRow = match($row['status']) {
                                    'no_questions'  => 'bg-yellow-50',
                                    'no_assessment' => 'bg-red-50',
                                    'not_found'     => 'bg-gray-50',
                                    default         => '',
                                };
                                $badge = match($row['status']) {
                                    'ok'            => ['text'=>'✅ Lengkap',        'cls'=>'bg-green-100 text-green-700 border-green-200'],
                                    'no_questions'  => ['text'=>'⚠️ Belum Soal',     'cls'=>'bg-yellow-100 text-yellow-700 border-yellow-200'],
                                    'no_assessment' => ['text'=>'❌ Belum Asesmen',  'cls'=>'bg-red-100 text-red-700 border-red-200'],
                                    'not_found'     => ['text'=>'🔍 Tidak Ditemukan','cls'=>'bg-gray-100 text-gray-600 border-gray-200'],
                                    default         => ['text'=>'?','cls'=>'bg-gray-100 text-gray-500 border-gray-200'],
                                };
                            @endphp
                            <tr class="hover:bg-opacity-70 transition {{ $bgRow }}">
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $row['hari'] }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $row['sesi'] }}</span>
                                </td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-700">{{ $row['kelas'] }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="rounded-md bg-purple-50 border border-purple-200 px-2 py-0.5 text-xs font-semibold text-purple-700">{{ $row['jurusan'] }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $row['mapel'] }}</td>
                                <td class="px-4 py-3 text-xs text-gray-400 italic">{{ $row['guru_input'] }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($row['guru_sistem'] !== '-')
                                        <span class="font-semibold text-indigo-700">{{ $row['guru_sistem'] }}</span>
                                    @else
                                        <span class="text-gray-300">Tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center font-bold">
                                    @if($row['status'] === 'ok')
                                        <span class="text-green-600">{{ $row['q_count'] }}</span>
                                    @elseif($row['status'] === 'no_questions')
                                        <span class="text-red-500">0</span>
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
                                <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                                    Tidak ada data yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 bg-gray-50 px-4 py-2 text-xs text-gray-400 flex justify-between">
                <span>Total: {{ $total }} sesi jadwal</span>
                <span>Klik kartu summary di atas untuk filter cepat</span>
            </div>
        </div>
    @endif
</div>

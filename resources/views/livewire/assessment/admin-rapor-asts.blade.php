<div>
    {{-- PRINT STYLE --}}
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            @page { size: F4 landscape; margin: 10mm 12mm 10mm 12mm; }
        }
        @media screen { .print-only { display: none; } }
        .rapor-wrap { background: white; max-width: 100%; margin: 0 auto; }
        .rapor-table { border-collapse: collapse; width: 100%; }
        .rapor-table th, .rapor-table td { border: 1px solid #374151; padding: 4px 6px; text-align: center; font-size: 11px; }
        .rapor-table th { background: #1e3a5f; color: white; font-weight: 600; }
        .rapor-table td.name-col { text-align: left; white-space: nowrap; }
        .rapor-table tr:nth-child(even) td { background: #f8fafc; }
        .nilai-0 { color: #dc2626; font-weight: 600; }
        .nilai-good { color: #166534; }
        .rapor-header { text-align: center; margin-bottom: 12px; }
        .rapor-header h2 { font-size: 14pt; font-weight: 700; color: #1e3a5f; margin: 0; }
        .rapor-header p { font-size: 10pt; margin: 2px 0; color: #374151; }
    </style>

    {{-- Toolbar --}}
    <div class="no-print mb-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin — Rapor ASTS</h1>
                <p class="text-sm text-gray-500 mt-1">Lihat &amp; cetak rapor ASTS semua kelas</p>
            </div>
        </div>

        {{-- Card: Pilih Kelas --}}
        <div class="mb-4 p-4 bg-white border border-gray-200 rounded-xl shadow-sm flex items-center gap-4">
            <label class="font-semibold text-gray-700 text-sm whitespace-nowrap">Pilih Kelas:</label>
            <select wire:model.live="selectedClassId"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($this->classes as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </select>
            <span class="text-sm text-gray-500">
                {{ $this->students->count() }} siswa
                &nbsp;&bull;&nbsp;
                {{ $this->subjects->count() }} mapel
            </span>
        </div>

        {{-- Card: Upload Kop Surat --}}
        <div class="mb-4 p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="font-semibold text-gray-700 text-sm">Kop Surat Rapor Cetak</h3>
            </div>

            @if(session("kop_success"))
                <div class="mb-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                    {{ session("kop_success") }}
                </div>
            @endif

            @if($this->kopSuratUrl)
                <div class="mb-3 flex items-start gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Kop surat aktif (akan muncul di rapor cetak):</p>
                        <img src="{{ $this->kopSuratUrl }}" alt="Kop Surat" class="rounded border border-gray-200" style="max-height: 100px; width: auto;">
                    </div>
                    <button wire:click="deleteKopSurat" wire:confirm="Hapus kop surat ini?"
                        class="text-xs text-red-600 hover:text-red-800 underline mt-1">
                        Hapus
                    </button>
                </div>
            @else
                <p class="text-xs text-gray-400 mb-3">Belum ada kop surat. Upload gambar JPG/PNG kop surat sekolah (maks. 2MB).</p>
            @endif

            <div class="flex items-center gap-3">
                <input type="file" wire:model="kopSuratFile" accept="image/jpeg,image/png"
                    class="text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <button wire:click="uploadKopSurat"
                    @if(!$kopSuratFile) disabled title="Pilih file dulu" @endif
                    class="flex-shrink-0 text-white text-xs font-medium px-4 py-2 rounded-lg transition {{ $kopSuratFile ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300 cursor-not-allowed' }}">
                    Upload Kop Surat
                </button>
            </div>
            @if($kopSuratFile)
                @error("kopSuratFile")
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            @endif
            <div wire:loading wire:target="kopSuratFile,uploadKopSurat" class="text-xs text-indigo-500 mt-1">Memproses...</div>
            @if(!$kopSuratFile)
                <p class="text-xs text-gray-400 mt-1">Pilih file JPG/PNG untuk mengganti kop surat.</p>
            @endif
        </div>
    </div>

    {{-- Card: Tanggal Cetak Rapor --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <h3 class="font-semibold text-gray-700 text-sm mb-3">Tanggal Cetak Rapor</h3>
        @if(session("kop_success") && str_contains(session("kop_success"), "Tanggal"))
            <div class="text-green-600 text-xs mb-2">{{ session("kop_success") }}</div>
        @endif
        <div class="flex items-center gap-3">
            <input type="text" wire:model="tanggalCetak"
                placeholder="Contoh: 19 September 2026"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button wire:click="saveTanggalCetak"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                Simpan
            </button>
        </div>
        <p class="text-xs text-gray-400 mt-1">Tanggal ini akan muncul di halaman TTD rapor cetak. Kosongkan untuk pakai tanggal hari ini otomatis.</p>
    </div>
    {{-- Tabel Rekap Nilai --}}
    @if($this->selectedClass && $this->students->isNotEmpty() && $this->subjects->isNotEmpty())
        <div class="rapor-wrap">
            <div class="rapor-header no-print">
                <h2>REKAPITULASI NILAI ASTS — {{ $this->selectedClass->name }}</h2>
                @if($this->semester)
                    <p>{{ $this->semester->name }}</p>
                @endif
            </div>

            <div style="overflow-x: auto;">
                <table class="rapor-table">
                    <thead>
                        <tr>
                            <th style="width:30px">No</th>
                            <th class="name-col" style="text-align:left">Nama Siswa</th>
                            @foreach($this->subjects as $subject)
                                <th style="max-width:80px;font-size:9px;word-break:break-word;">{{ $subject->name }}</th>
                            @endforeach
                            <th>Rata-rata</th>
                            <th class="no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->students as $idx => $student)
                            @php
                                $nilaiList = $this->subjects->map(fn($sub) => $this->getNilai($student->id, $sub->id));
                                $rataRata  = $nilaiList->filter(fn($n) => $n > 0)->avg() ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="name-col">{{ $student->name }}</td>
                                @foreach($nilaiList as $nilai)
                                    <td class="{{ $nilai == 0 ? "nilai-0" : ($nilai >= 75 ? "nilai-good" : "") }}">
                                        {{ $nilai > 0 ? $nilai : "-" }}
                                    </td>
                                @endforeach
                                <td>{{ $rataRata > 0 ? number_format($rataRata, 1) : "-" }}</td>
                                <td class="no-print">
                                    <a href="{{ route("admin.rapor-asts.cetak", ["classId" => $this->selectedClassId, "studentId" => $student->id]) }}"
                                        target="_blank"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap">
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-700 font-medium">Tidak ada siswa di kelas {{ $this->selectedClass->name }}.</p>
        </div>
    @elseif($this->selectedClass && $this->subjects->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-700 font-medium">Belum ada jadwal mengajar untuk kelas {{ $this->selectedClass->name }}.</p>
        </div>
    @endif
</div>
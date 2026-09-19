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
            <button onclick="window.print()"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Rekap F4
            </button>
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
                        <img src="{{ $this->kopSuratUrl }}" alt="Kop Surat" class="max-h-24 border rounded shadow-sm">
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
                    class="flex-shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                    Upload Kop Surat
                </button>
            </div>
            @error("kopSuratFile")
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <div wire:loading wire:target="kopSuratFile,uploadKopSurat" class="text-xs text-indigo-500 mt-1">Memproses...</div>
        </div>
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
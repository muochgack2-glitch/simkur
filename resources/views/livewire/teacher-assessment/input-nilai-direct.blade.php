<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('teacher.assessment.index') }}" wire:navigate
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-xl font-bold text-gray-800">Input Nilai Langsung</h1>
        <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <span class="font-semibold text-gray-700">{{ $this->assessment->title }}</span>
            @if($this->assessment->subject)
                <span class="text-gray-400">•</span>
                <span>{{ $this->assessment->subject->name }}</span>
            @endif
            @if($this->assessment->assessmentLabel)
                <span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-semibold">
                    {{ $this->assessment->assessmentLabel->name }}
                </span>
            @endif
        </div>
        <p class="mt-1 text-xs text-gray-400">
            Target: {{ $this->assessment->getTargetGradesLabel() }}
            @if(!$this->assessment->isForAllMajors())
                &mdash; {{ $this->assessment->getTargetMajorsLabel() }}
            @endif
        </p>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Info --}}
    <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-blue-700 text-xs">
        <strong>Petunjuk:</strong> Isi nilai akhir (0&ndash;100) untuk setiap siswa, lalu klik
        <strong>Simpan Semua</strong>. Nilai yang sudah ada (dari jawaban online) terisi otomatis &mdash;
        Anda bisa mengoreksinya. Kosongkan kolom jika ingin mengabaikan siswa tersebut.
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-10 text-center">No</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3 w-28">NIS</th>
                        <th class="px-4 py-3 w-28">Kelas</th>
                        <th class="px-4 py-3 w-36 text-center">Nilai (0&ndash;100)</th>
                        <th class="px-4 py-3 w-24 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->students as $index => $student)
                        @php $hasScore = array_key_exists($student->id, $this->scores); @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $student->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $student->nis ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $student->schoolClass?->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <input
                                    type="number" min="0" max="100"
                                    wire:model="scores.{{ $student->id }}"
                                    class="w-20 rounded-lg border border-gray-300 px-2 py-1 text-center text-sm focus:border-indigo-400 focus:ring focus:ring-indigo-100 transition"
                                    placeholder="&mdash;"
                                />
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($hasScore)
                                    <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs font-medium">
                                        Terisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-400 px-2 py-0.5 text-xs">
                                        Kosong
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                <svg class="mx-auto mb-2 w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Tidak ada siswa yang sesuai target asesmen ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->students->isNotEmpty())
            <div class="border-t border-gray-100 bg-gray-50 px-4 py-3 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    {{ $this->students->count() }} siswa &bull;
                    {{ count(array_filter($this->scores, fn($v) => $v !== null && $v !== '')) }} sudah terisi
                </p>
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60 transition">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Semua
                </button>
            </div>
        @endif
    </div>
</div>

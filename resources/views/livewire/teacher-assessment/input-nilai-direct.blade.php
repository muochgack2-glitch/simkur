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
                <span class="text-gray-400">&bull;</span>
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
        <strong>Petunjuk:</strong> Pilih kelas terlebih dahulu, kemudian isi nilai (0-100) lalu klik <strong>Simpan Semua</strong>.
    </div>

    {{-- Dropdown Pilih Kelas --}}
    <div class="mb-5 bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
        @if($this->availableClasses->isEmpty())
            <p class="text-sm text-red-500">Tidak ada kelas yang sesuai dengan asesmen ini.</p>
        @else
            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="selectedClassId"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-400 focus:ring focus:ring-indigo-100 transition min-w-[200px]">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($this->availableClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
                @if($selectedClassId)
                    <span class="text-sm text-gray-500">{{ $this->students->count() }} siswa</span>
                @endif
            </div>
        @endif
    </div>

    @if($selectedClassId)
        @if($this->students->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-gray-400 text-sm">
                Tidak ada siswa di kelas ini.
            </div>
        @else
            <form wire:submit.prevent="save">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 w-10 text-center">No</th>
                                    <th class="px-4 py-3">Nama Siswa</th>
                                    <th class="px-4 py-3 w-28">NIS</th>
                                    <th class="px-4 py-3 w-36 text-center">Nilai (0-100)</th>
                                    <th class="px-4 py-3 w-24 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($this->students as $index => $student)
                                    @php $hasScore = array_key_exists($student->id, $this->scores); @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-2 text-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 font-medium text-gray-800">{{ $student->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $student->nis ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="number" min="0" max="100"
                                                   wire:model="scores.{{ $student->id }}"
                                                   class="w-20 rounded-lg border border-gray-300 px-2 py-1 text-center text-sm focus:border-indigo-400 focus:ring focus:ring-indigo-100 transition"
                                                   placeholder="--"/>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if($hasScore)
                                                <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5">Terisi</span>
                                            @else
                                                <span class="text-gray-300 text-xs">Kosong</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-5 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Kelas: <strong>{{ $this->availableClasses->firstWhere('id', $selectedClassId)?->name }}</strong>
                        &mdash; {{ $this->students->count() }} siswa
                    </p>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Semua
                    </button>
                </div>
            </form>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-dashed px-6 py-16 text-center text-gray-300">
            <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm">Pilih kelas untuk menampilkan daftar siswa</p>
        </div>
    @endif

</div>


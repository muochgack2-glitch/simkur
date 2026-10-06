<div class="max-w-5xl mx-auto px-4 py-6"
     x-data="{
         dirty: false,
         draftKey: 'input-nilai-{{ $this->assessment->id }}',
         init() {
             // Restore drafts from localStorage
             try {
                 const saved = localStorage.getItem(this.draftKey);
                 if (saved) {
                     const drafts = JSON.parse(saved);
                     Object.entries(drafts).forEach(([id, val]) => {
                         const input = document.querySelector(`input[data-student-id='${id}']`);
                         if (input && !input.value) {
                             input.value = val;
                             input.dispatchEvent(new Event('input', { bubbles: true }));
                         }
                     });
                 }
             } catch(e) {}
         },
         saveDraft() {
             this.dirty = true;
             const inputs = document.querySelectorAll('input[data-student-id]');
             const data = {};
             inputs.forEach(el => {
                 if (el.value !== '') data[el.dataset.studentId] = el.value;
             });
             try { localStorage.setItem(this.draftKey, JSON.stringify(data)); } catch(e) {}
         },
         clearDraft() {
             this.dirty = false;
             try { localStorage.removeItem(this.draftKey); } catch(e) {}
         },
         confirmClassSwitch(e) {
             if (this.dirty) {
                 if (!confirm('Ada nilai yang belum disimpan. Yakin ingin ganti kelas?')) {
                     e.preventDefault();
                     e.stopPropagation();
                     return false;
                 }
                 this.clearDraft();
             }
         }
     }"
     x-on:keydown.window="if ($event.key === 's' && ($event.ctrlKey || $event.metaKey)) { $event.preventDefault(); document.querySelector('#btn-save-bottom')?.click(); }"
>

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

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700 text-sm"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Import result --}}
    @if(!empty($importResult))
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm">
            @if(!empty($importResult['imported']))
                <p class="font-semibold text-blue-700 mb-1">&#10003; Berhasil diimport: {{ count($importResult['imported']) }} siswa</p>
            @endif
            @if(!empty($importResult['skipped']))
                <p class="font-semibold text-amber-700 mb-1 mt-2">&#9888; Dilewati ({{ count($importResult['skipped']) }}):</p>
                <ul class="list-disc list-inside text-amber-600 text-xs space-y-0.5">
                    @foreach($importResult['skipped'] as $s)
                        <li>{{ $s }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Info --}}
    <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-blue-700 text-xs">
        <strong>Petunjuk:</strong>
        @if($this->availableClasses->count() > 1)
            Pilih kelas terlebih dahulu, kemudian isi nilai (0-100) lalu klik <strong>Simpan Semua</strong>.
        @else
            Isi nilai (0-100) lalu klik <strong>Simpan Semua</strong>.
        @endif
        Atau gunakan fitur <strong>Import Excel</strong> untuk upload nilai sekaligus.
        <span class="block mt-1 text-blue-500">&#128161; Tips: Tekan <kbd class="px-1.5 py-0.5 bg-blue-100 rounded text-xs font-mono">Tab</kbd> untuk pindah antar input, <kbd class="px-1.5 py-0.5 bg-blue-100 rounded text-xs font-mono">Ctrl+S</kbd> untuk simpan cepat.</span>
    </div>

    {{-- Pilih Kelas --}}
    <div class="mb-5 bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4">
        @if($this->availableClasses->isEmpty())
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
            <p class="text-sm text-red-500">Tidak ada kelas yang sesuai dengan asesmen ini.</p>

        @elseif($this->availableClasses->count() === 1)
            {{-- 1 kelas: auto-selected, tampil sebagai label --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-200 px-3 py-1.5 text-sm font-semibold text-indigo-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $this->availableClasses->first()->name }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $this->students->count() }} siswa</span>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('teacher.assessment.template-nilai', [$this->assessment->id, $selectedClassId]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-600 text-emerald-700 hover:bg-emerald-50 text-xs font-semibold px-3 py-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Template
                    </a>
                    <button type="button" wire:click="$toggle('showImport')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-500 text-indigo-600 hover:bg-indigo-50 text-xs font-semibold px-3 py-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Excel
                    </button>
                </div>
            </div>

        @else
            {{-- Multi kelas: dropdown --}}
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="selectedClassId"
                        x-on:change="confirmClassSwitch($event)"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-400 focus:ring focus:ring-indigo-100 transition min-w-[200px]">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($this->availableClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
                @if($selectedClassId)
                    <span class="text-sm text-gray-500">{{ $this->students->count() }} siswa</span>
                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{ route('teacher.assessment.template-nilai', [$this->assessment->id, $selectedClassId]) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-600 text-emerald-700 hover:bg-emerald-50 text-xs font-semibold px-3 py-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Template
                        </a>
                        <button type="button" wire:click="$toggle('showImport')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-500 text-indigo-600 hover:bg-indigo-50 text-xs font-semibold px-3 py-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Import Excel
                        </button>
                    </div>
                @endif
            </div>
        @endif

        {{-- Panel Import (shared) --}}
        @if($showImport && $selectedClassId)
            <div class="mt-4 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-4">
                <p class="text-xs font-semibold text-indigo-700 mb-3">
                    &#128229; Upload file Excel (.xlsx/.xls) &mdash; gunakan template yang sudah di-download
                </p>
                <form wire:submit.prevent="importNilai" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls"
                               class="block w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                        @error('importFile') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Proses Import
                    </button>
                    <button type="button" wire:click="$toggle('showImport')"
                            class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1.5">Batal</button>
                </form>
            </div>
        @endif
    </div>

    @if($selectedClassId)
        @if($this->students->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-gray-400 text-sm">
                Tidak ada siswa di kelas ini.
            </div>
        @else
            @php
                $filledCount = 0;
                $emptyCount = 0;
                foreach ($this->students as $st) {
                    if (array_key_exists($st->id, $this->scores) && $this->scores[$st->id] !== '' && $this->scores[$st->id] !== null) {
                        $filledCount++;
                    } else {
                        $emptyCount++;
                    }
                }
                $totalStudents = $this->students->count();
                $pct = $totalStudents > 0 ? round($filledCount / $totalStudents * 100) : 0;
            @endphp

            <form wire:submit.prevent="save" x-on:submit="clearDraft()">
                {{-- Summary bar + Save button top (sticky) --}}
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border border-gray-200 rounded-xl shadow-sm px-5 py-3 mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-700">{{ $this->availableClasses->firstWhere('id', $selectedClassId)?->name }}</span>
                            <span class="text-gray-400">&bull;</span>
                            <span class="text-gray-500">{{ $totalStudents }} siswa</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 text-green-600">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                {{ $filledCount }} terisi
                            </span>
                            @if($emptyCount > 0)
                                <span class="inline-flex items-center gap-1 text-amber-500">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    {{ $emptyCount }} kosong
                                </span>
                            @endif
                        </div>
                        {{-- Progress bar --}}
                        <div class="hidden sm:flex items-center gap-2 min-w-[120px]">
                            <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300 {{ $pct === 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs font-medium {{ $pct === 100 ? 'text-green-600' : 'text-gray-500' }}">{{ $pct }}%</span>
                        </div>
                    </div>
                    <button type="submit" id="btn-save-top"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 shadow transition disabled:opacity-60">
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Simpan Semua</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>

                {{-- Unsaved indicator --}}
                <div x-show="dirty" x-transition class="mb-3 flex items-center gap-2 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Ada perubahan yang belum disimpan
                </div>

                {{-- Table --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200 sticky top-[72px] z-[5]">
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
                                    @php $hasScore = array_key_exists($student->id, $this->scores) && $this->scores[$student->id] !== '' && $this->scores[$student->id] !== null; @endphp
                                    <tr class="hover:bg-gray-50/80 transition {{ !$hasScore ? 'bg-amber-50/30' : '' }}">
                                        <td class="px-4 py-2 text-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 font-medium text-gray-800">{{ $student->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $student->nis ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="number" min="0" max="100"
                                                   wire:model.blur="scores.{{ $student->id }}"
                                                   data-student-id="{{ $student->id }}"
                                                   x-on:input="saveDraft()"
                                                   x-on:focus="$el.select()"
                                                   tabindex="{{ $index + 1 }}"
                                                   class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-center text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition {{ $hasScore ? 'bg-green-50 border-green-300' : '' }}"
                                                   placeholder="--"/>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if($hasScore)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Terisi
                                                </span>
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

                {{-- Bottom save bar --}}
                <div class="mt-5 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="text-green-600 font-semibold">{{ $filledCount }}/{{ $totalStudents }}</span> terisi
                            @if($emptyCount > 0)
                                &mdash; <span class="text-amber-500 font-semibold">{{ $emptyCount }}</span> belum diisi
                            @else
                                &mdash; <span class="text-green-600 font-semibold">Semua terisi!</span>
                            @endif
                        </span>
                    </p>
                    <button type="submit" id="btn-save-bottom"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 shadow transition disabled:opacity-60">
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Simpan Semua</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
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
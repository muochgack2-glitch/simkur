<div x-data="{ confirmDelete: null }">
    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.assessment.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Kelola Soal</h1>
                <p class="text-sm text-gray-500">{{ $assessment->title }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('teacher.assessment.edit', $assessment->id) }}" wire:navigate
               class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                Edit Info
            </a>
            <button wire:click="showAddForm"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Soal
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Daftar soal --}}
    <div class="mb-6">
        @if($questions->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-200 bg-white p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-3 text-gray-500">Belum ada soal. Klik <strong>Tambah Soal</strong> untuk mulai.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($questions as $i => $question)
                    @php
                        $typeColors = [
                            'multiple_choice'=>['bg'=>'bg-blue-100','text'=>'text-blue-700'],
                            'true_false'     =>['bg'=>'bg-purple-100','text'=>'text-purple-700'],
                            'matching'       =>['bg'=>'bg-orange-100','text'=>'text-orange-700'],
                            'essay'          =>['bg'=>'bg-teal-100','text'=>'text-teal-700'],
                            'file_upload'    =>['bg'=>'bg-pink-100','text'=>'text-pink-700'],
                        ];
                        $tc = $typeColors[$question->question_type] ?? ['bg'=>'bg-gray-100','text'=>'text-gray-700'];
                    @endphp
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition">
                        {{-- Nomor + reorder --}}
                        <div class="flex flex-col items-center gap-1 mt-0.5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">
                                {{ $i + 1 }}
                            </span>
                            <button wire:click="moveUp({{ $question->id }})" class="text-gray-300 hover:text-gray-500 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button wire:click="moveDown({{ $question->id }})" class="text-gray-300 hover:text-gray-500 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Konten soal --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tc['bg'] }} {{ $tc['text'] }}">
                                    {{ $question->getTypeLabel() }}
                                </span>
                                <span class="text-xs text-gray-400">Skor maks: {{ $question->getEffectiveMaxScore() }}</span>
                            </div>
                            <p class="text-sm text-gray-800 font-medium">{{ Str::limit($question->question_text, 150) }}</p>

                            {{-- Preview opsi PG / T-F --}}
                            @if($question->isMultipleChoice() || $question->isTrueFalse())
                                <div class="mt-2 grid grid-cols-2 gap-1">
                                    @foreach($question->options as $opt)
                                        <div class="flex items-center gap-1.5 text-xs {{ $opt->score_value > 0 ? 'text-green-700 font-semibold' : 'text-gray-500' }}">
                                            @if($opt->score_value > 0)
                                                <svg class="h-3.5 w-3.5 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <span class="h-3.5 w-3.5 shrink-0 rounded-full border border-gray-300 inline-block"></span>
                                            @endif
                                            {{ $opt->option_text }}
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->isMatching())
                                <div class="mt-2 space-y-1">
                                    @foreach($question->matching_pairs ?? [] as $pair)
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <span class="font-medium">{{ $pair['left'] }}</span>
                                            <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                            <span>{{ $pair['right'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col gap-1.5 shrink-0">
                            <button wire:click="editQuestion({{ $question->id }})"
                                class="rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                Edit
                            </button>
                            <button wire:click="deleteQuestion({{ $question->id }})"
                                    wire:confirm="Hapus soal ini?"
                                class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ======================= FORM TAMBAH / EDIT SOAL ======================== --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 py-10 px-4">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ $editingQuestionId ? 'Edit Soal' : 'Tambah Soal Baru' }}
                    </h2>
                    <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Errors --}}
                    @if($errors->any())
                        <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
                        </div>
                    @endif

                    {{-- Tipe soal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal <span class="text-red-500">*</span></label>
                        <select wire:model.live="questionType"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="true_false">Benar / Salah</option>
                            <option value="matching">Menjodohkan</option>
                            <option value="essay">Esai (Dinilai Guru)</option>
                            <option value="file_upload">Upload File (Dinilai Guru)</option>
                        </select>
                    </div>

                    {{-- Teks soal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan / Teks Soal <span class="text-red-500">*</span></label>
                        <textarea wire:model="questionText" rows="3"
                            placeholder="Tuliskan pertanyaan di sini..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('questionText') border-red-400 @enderror"></textarea>
                        @error('questionText') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Skor maks --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Skor Maksimal</label>
                        <input wire:model="maxScore" type="number" min="1" max="100"
                            class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-xs text-gray-400">poin</span>
                    </div>

                    {{-- Pilihan jawaban PG --}}
                    @if($questionType === 'multiple_choice')
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Pilihan Jawaban</label>
                                <button wire:click="addOption" type="button"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Tambah Pilihan</button>
                            </div>
                            <div class="space-y-2">
                                @foreach($options as $i => $opt)
                                    <div class="flex items-center gap-2">
                                        <button wire:click="setCorrectOption({{ $i }})" type="button"
                                            class="shrink-0 h-5 w-5 rounded-full border-2 flex items-center justify-center transition
                                                {{ $opt['is_correct'] ? 'border-green-500 bg-green-500' : 'border-gray-300 bg-white hover:border-green-400' }}">
                                            @if($opt['is_correct'])
                                                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </button>
                                        <span class="shrink-0 inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">{{ chr(65+$i) }}</span>
                                        <input wire:model="options.{{ $i }}.text" type="text"
                                            placeholder="Pilihan {{ chr(65+$i) }}"
                                            class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @if(count($options) > 2)
                                            <button wire:click="removeOption({{ $i }})" type="button" class="text-gray-300 hover:text-red-500 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Klik lingkaran hijau untuk menandai jawaban benar</p>
                        </div>
                    @endif

                    {{-- Benar/Salah — tampil otomatis --}}
                    @if($questionType === 'true_false')
                        <div class="rounded-lg bg-purple-50 border border-purple-100 p-3">
                            <p class="text-sm text-purple-700 font-medium mb-2">Pilihan jawaban:</p>
                            <div class="flex gap-3">
                                @foreach($options as $i => $opt)
                                    <button wire:click="setCorrectOption({{ $i }})" type="button"
                                        class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium border-2 transition
                                            {{ $opt['is_correct'] ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-600 hover:border-green-300' }}">
                                        @if($opt['is_correct'])
                                            <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ $opt['text'] }}
                                    </button>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-purple-500">Klik untuk menandai jawaban yang benar</p>
                        </div>
                    @endif

                    {{-- Menjodohkan --}}
                    @if($questionType === 'matching')
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Pasangan Kiri — Kanan</label>
                                <button wire:click="addMatchingPair" type="button"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Tambah Pasangan</button>
                            </div>
                            <div class="space-y-2">
                                @foreach($matchingPairs as $i => $pair)
                                    <div class="flex items-center gap-2">
                                        <span class="shrink-0 text-xs text-gray-400 w-4">{{ $i+1 }}.</span>
                                        <input wire:model="matchingPairs.{{ $i }}.left" type="text"
                                            placeholder="Kiri..."
                                            class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        <input wire:model="matchingPairs.{{ $i }}.right" type="text"
                                            placeholder="Kanan..."
                                            class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @if(count($matchingPairs) > 2)
                                            <button wire:click="removeMatchingPair({{ $i }})" type="button" class="text-gray-300 hover:text-red-500 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- File accept --}}
                    @if($questionType === 'file_upload')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format File Diterima</label>
                            <input wire:model="fileAccept" type="text"
                                placeholder="Contoh: image/*,application/pdf"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-400">Contoh: <code>image/*</code> untuk gambar, <code>application/pdf</code> untuk PDF</p>
                        </div>
                    @endif

                    {{-- Esai info --}}
                    @if($questionType === 'essay')
                        <div class="rounded-lg bg-teal-50 border border-teal-100 p-3 text-sm text-teal-700">
                            <strong>Soal Esai</strong> — Siswa mengetik jawaban bebas. Guru menilai secara manual setelah submission.
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="resetForm" type="button"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="saveQuestion"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editingQuestionId ? 'Simpan Perubahan' : 'Tambahkan Soal' }}</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

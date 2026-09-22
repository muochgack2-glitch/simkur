<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('teacher.assessment.results', $assessment->id) }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Penilaian — {{ $student->name }}</h1>
            <p class="text-sm text-gray-500">{{ $assessment->title }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="space-y-6">
        @foreach($questions as $i => $question)
            @php $response = $responses[$question->id] ?? null; @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                {{-- Nomor + tipe --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                        {{ $i + 1 }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $question->isAutoScored() ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ $question->getTypeLabel() }}
                    </span>
                    <span class="text-xs text-gray-400">Maks: {{ $question->getEffectiveMaxScore() }} poin</span>
                    @if($question->isAutoScored())
                        <span class="text-xs text-green-600 font-medium">✓ Dinilai otomatis</span>
                    @else
                        <span class="text-xs text-orange-600 font-medium">✏️ Perlu penilaian guru</span>
                    @endif
                </div>

                {{-- Pertanyaan --}}
                <x-question-image :question="$question">
                    <p class="text-sm font-semibold text-gray-800 mb-3">{{ $question->question_text }}</p>
                </x-question-image>

                {{-- Jawaban siswa --}}
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 mb-3">
                    <p class="text-xs font-medium text-gray-500 mb-1">Jawaban Siswa:</p>
                    @if($response)
                        @if($question->isMultipleChoice() || $question->isTrueFalse())
                            <p class="text-sm text-gray-800">{{ $response->selectedOption?->option_text ?? 'Tidak dijawab' }}</p>
                            @php $isCorrect = $response->selectedOption && $response->selectedOption->score_value > 0; @endphp
                            <span class="mt-1 inline-flex items-center text-xs font-medium {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                {{ $isCorrect ? '✓ Benar' : '✗ Salah' }}
                            </span>
                        @elseif($question->isMatching())
                            <p class="text-sm text-gray-800">Skor otomatis: {{ $response->score ?? 0 }} / {{ $question->getEffectiveMaxScore() }}</p>
                        @elseif($question->isEssay())
                            <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $response->text_answer ?? '(tidak ada jawaban)' }}</p>
                        @elseif($question->isFileUpload())
                            @if($response->file_path)
                                <a href="{{ Storage::url($response->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Lihat File Upload
                                </a>
                            @else
                                <p class="text-sm text-gray-400">Belum ada file</p>
                            @endif
                        @endif
                    @else
                        <p class="text-sm text-red-500 italic">Tidak dijawab</p>
                    @endif
                </div>

                {{-- Form nilai guru (hanya untuk esai/file) --}}
                @if($question->isManualScored())
                    <div class="space-y-3 border-t border-gray-100 pt-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="text-sm font-medium text-gray-700">Nilai (0–{{ $question->getEffectiveMaxScore() }}):</label>
                            <input wire:model="scores.{{ $question->id }}"
                                type="number" min="0" max="{{ $question->getEffectiveMaxScore() }}" step="0.5"
                                class="w-24 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Komentar Guru (opsional):</label>
                            <textarea wire:model="feedbacks.{{ $question->id }}" rows="2"
                                placeholder="Tulis feedback untuk siswa..."
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Tombol simpan --}}
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('teacher.assessment.results', $assessment->id) }}" wire:navigate
           class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            Kembali
        </a>
        <button wire:click="saveGrades"
            class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition disabled:opacity-60"
            wire:loading.attr="disabled">
            <span wire:loading.remove>Simpan Penilaian</span>
            <span wire:loading>Menyimpan...</span>
        </button>
    </div>
</div>

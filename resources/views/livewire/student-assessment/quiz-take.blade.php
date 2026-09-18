<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h1 class="text-xl font-bold text-gray-800">{{ $assessment->title }}</h1>
        @if($assessment->description)
            <p class="text-sm text-gray-600 mt-1">{{ $assessment->description }}</p>
        @endif
        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
            <span>📋 {{ $total }} soal</span>
            <span>✅ {{ $answered }} dijawab</span>
            <span class="ml-auto text-blue-600 font-medium">
                Soal {{ $currentPage + 1 }} dari {{ $total }}
            </span>
        </div>
        {{-- Progress bar --}}
        <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-blue-500 transition-all duration-300 rounded-full"
                style="width: {{ $total > 0 ? round(($answered/$total)*100) : 0 }}%"></div>
        </div>
    </div>

    {{-- Nomor soal navigator --}}
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach($questions as $idx => $q)
            <button wire:click="goToPage({{ $idx }})"
                class="w-9 h-9 rounded-lg text-sm font-semibold border transition
                    {{ $idx === $currentPage
                        ? 'bg-blue-600 text-white border-blue-600'
                        : (isset($answers[$q->id]) && $answers[$q->id] !== null && $answers[$q->id] !== ''
                            ? 'bg-green-100 text-green-700 border-green-300'
                            : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400')
                    }}">
                {{ $idx + 1 }}
            </button>
        @endforeach
    </div>

    {{-- Soal aktif --}}
    @php $question = $questions[$currentPage] ?? null; @endphp
    @if($question)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-4">
        <div class="flex items-start gap-3 mb-5">
            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center">
                {{ $currentPage + 1 }}
            </span>
            <div class="flex-1">
                <span class="inline-block text-xs bg-gray-100 text-gray-500 rounded px-2 py-0.5 mb-2">{{ $question->getTypeLabel() }}</span>
                <x-question-image :question="$question">
                    <p class="text-gray-800 font-medium leading-relaxed">{{ $question->question_text }}</p>
                </x-question-image>
            </div>
        </div>

        {{-- PILIHAN GANDA --}}
        @if($question->isMultipleChoice())
            <div class="space-y-3">
                @foreach($question->options as $opt)
                    <label wire:click="saveAnswer({{ $question->id }}, {{ $opt->id }})"
                        class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition
                            {{ isset($answers[$question->id]) && (int)$answers[$question->id] === $opt->id
                                ? 'border-blue-500 bg-blue-50'
                                : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50' }}">
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                            {{ isset($answers[$question->id]) && (int)$answers[$question->id] === $opt->id
                                ? 'border-blue-500'
                                : 'border-gray-300' }}">
                            @if(isset($answers[$question->id]) && (int)$answers[$question->id] === $opt->id)
                                <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                            @endif
                        </div>
                        <span class="shrink-0 inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">{{ chr(65+$loop->index) }}</span>
                        <span class="text-sm text-gray-700">{{ $opt->option_text }}</span>
                    </label>
                @endforeach
            </div>

        {{-- BENAR / SALAH --}}
        @elseif($question->isTrueFalse())
            <div class="flex gap-3">
                @foreach($question->options as $opt)
                    <label wire:click="saveAnswer({{ $question->id }}, {{ $opt->id }})"
                        class="flex-1 flex items-center justify-center gap-2 p-4 rounded-lg border-2 cursor-pointer font-semibold transition
                            {{ isset($answers[$question->id]) && (int)$answers[$question->id] === $opt->id
                                ? ($opt->option_text === 'Benar' ? 'border-green-500 bg-green-50 text-green-700' : 'border-red-500 bg-red-50 text-red-700')
                                : 'border-gray-200 hover:border-gray-400 text-gray-600' }}">
                        <span>{{ $opt->option_text === 'Benar' ? '✅' : '❌' }}</span>
                        <span>{{ $opt->option_text }}</span>
                    </label>
                @endforeach
            </div>

        {{-- MENJODOHKAN --}}
        @elseif($question->isMatching())
            @php
                $pairs = $question->matching_pairs ?? [];
                $currentAnswer = isset($answers[$question->id]) ? (is_array($answers[$question->id]) ? $answers[$question->id] : json_decode($answers[$question->id], true) ?? []) : [];
            @endphp
            <div class="space-y-3">
                @foreach($pairs as $pair)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <span class="flex-1 text-sm bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-blue-800 font-medium">
                            {{ $pair['left'] }}
                        </span>
                        <span class="text-gray-400">→</span>
                        <select wire:change="saveMatchingAnswer({{ $question->id }}, '{{ $pair['left'] }}', $event.target.value)"
                            class="w-full sm:flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach($pairs as $p2)
                                <option value="{{ $p2['right'] }}"
                                    {{ ($currentAnswer[$pair['left']] ?? '') === $p2['right'] ? 'selected' : '' }}>
                                    {{ $p2['right'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

        {{-- ESAI --}}
        @elseif($question->isEssay())
            <textarea wire:model.lazy="answers.{{ $question->id }}"
                wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                rows="5" placeholder="Tulis jawaban Anda di sini..."
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ $answers[$question->id] ?? '' }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Jawaban esai akan dinilai oleh guru</p>

        {{-- UPLOAD FILE --}}
        @elseif($question->isFileUpload())
            <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center">
                <p class="text-sm text-gray-500">📎 Upload file jawaban</p>
                <p class="text-xs text-amber-600 mt-2">⚠️ Fitur upload file akan segera tersedia</p>
            </div>
        @endif
    </div>
    @endif

    {{-- Navigasi --}}
    <div class="flex items-center justify-between gap-2">
        <button wire:click="prevPage" @if($currentPage === 0) disabled @endif
            class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 transition">
            ← Sebelumnya
        </button>

        @if($currentPage < $total - 1)
            <button wire:click="nextPage"
                class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                Selanjutnya →
            </button>
        @else
            <button wire:click="submit"
                wire:confirm="Yakin ingin mengumpulkan jawaban? Anda tidak bisa mengubah setelah submit."
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition"
                wire:loading.attr="disabled">
                <span wire:loading.remove>✅ Kumpulkan Jawaban</span>
                <span wire:loading>Mengumpulkan...</span>
            </button>
        @endif
    </div>

    {{-- Info belum dijawab --}}
    @if($currentPage === $total - 1 && $answered < $total)
        <div class="mt-3 rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-700">
            ⚠️ Masih ada {{ $total - $answered }} soal yang belum dijawab.
        </div>
    @endif
</div>
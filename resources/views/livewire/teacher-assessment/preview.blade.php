<div class="max-w-3xl mx-auto">
    {{-- Banner: Belum Dimulai --}}
    <div class="mb-6 rounded-xl border-2 border-yellow-300 bg-yellow-50 p-4 flex items-start gap-3">
        <svg class="h-6 w-6 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-yellow-800">Mode Preview — Soal Belum Dibuka</p>
            <p class="text-sm text-yellow-700 mt-0.5">
                Asesmen ini dimulai pada
                <strong>{{ $assessment->start_date->translatedFormat('l, d F Y') }}
                pukul {{ $assessment->start_time ? substr($assessment->start_time,0,5) : '00:00' }} WIB</strong>.
                Anda sedang melihat soal untuk persiapan. Jawaban tidak bisa diisi sekarang.
            </p>
            {{-- Countdown --}}
            <div id="countdown-{{ $assessment->id }}"
                class="mt-2 text-lg font-bold text-yellow-800"
                data-target="{{ $assessment->getOpenDatetime()->toIso8601String() }}">
            </div>
        </div>
    </div>

    {{-- Info asesmen --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h1 class="text-xl font-bold text-gray-800">{{ $assessment->title }}</h1>
        @if($assessment->description)
            <p class="mt-2 text-sm text-gray-600">{{ $assessment->description }}</p>
        @endif
        <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">
            <span>📋 {{ $questions->count() }} soal</span>
            <span>📅 Berakhir: {{ $assessment->end_date->translatedFormat('d M Y') }}
                {{ $assessment->end_time ? substr($assessment->end_time,0,5) : '' }}</span>
            <span>👤 {{ $assessment->creator->name ?? '-' }}</span>
        </div>
    </div>

    {{-- Daftar soal (read-only) --}}
    <div class="space-y-4">
        @foreach($questions as $i => $question)
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm opacity-90">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">
                        {{ $i + 1 }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                        {{ $question->getTypeLabel() }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $question->getEffectiveMaxScore() }} poin</span>
                </div>

                <p class="text-sm font-semibold text-gray-800 mb-3">{{ $question->question_text }}</p>

                {{-- Opsi PG (disabled) --}}
                @if($question->isMultipleChoice())
                    <div class="space-y-2">
                        @foreach($question->options->sortBy('order_number') as $opt)
                            <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-2.5 cursor-not-allowed opacity-70">
                                <input type="radio" disabled class="h-4 w-4 border-gray-300 text-blue-600">
                                <span class="text-sm text-gray-700">{{ $opt->option_text }}</span>
                            </label>
                        @endforeach
                    </div>

                {{-- Benar/Salah (disabled) --}}
                @elseif($question->isTrueFalse())
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-5 py-2 cursor-not-allowed opacity-70">
                            <input type="radio" disabled class="h-4 w-4 border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Benar</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-5 py-2 cursor-not-allowed opacity-70">
                            <input type="radio" disabled class="h-4 w-4 border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Salah</span>
                        </label>
                    </div>

                {{-- Menjodohkan (disabled) --}}
                @elseif($question->isMatching())
                    <div class="space-y-2">
                        @foreach($question->matching_pairs ?? [] as $j => $pair)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 font-medium">
                                    {{ $pair['left'] }}
                                </div>
                                <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                <select disabled class="flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                    <option>Pilih pasangan...</option>
                                </select>
                            </div>
                        @endforeach
                    </div>

                {{-- Esai (disabled) --}}
                @elseif($question->isEssay())
                    <textarea disabled rows="3" placeholder="Tulis jawaban esai di sini..."
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 resize-none cursor-not-allowed"></textarea>

                {{-- File Upload (disabled) --}}
                @elseif($question->isFileUpload())
                    <div class="rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center opacity-70">
                        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-1 text-xs text-gray-400">Upload file akan tersedia saat asesmen dimulai</p>
                        @if($question->file_accept)
                            <p class="text-xs text-gray-300 mt-0.5">Format: {{ $question->file_accept }}</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Tombol kembali --}}
    <div class="mt-6 flex justify-center">
        <a href="{{ route('student.assessment.index') }}" wire:navigate
           class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            ← Kembali ke Daftar Asesmen
        </a>
    </div>
</div>

{{-- Countdown script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const els = document.querySelectorAll('[data-target]');
    els.forEach(el => {
        const target = new Date(el.dataset.target).getTime();
        function update() {
            const now = Date.now();
            const diff = target - now;
            if (diff <= 0) {
                el.textContent = 'Asesmen sudah dibuka! Refresh halaman.';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = (d > 0 ? d + ' hari ' : '') + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            setTimeout(update, 1000);
        }
        update();
    });
});
</script>

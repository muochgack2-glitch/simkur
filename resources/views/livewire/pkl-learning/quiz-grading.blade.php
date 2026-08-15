<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.show', $quiz->course) }}" class="text-gray-500 hover:text-gray-700">⬅</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">❓ Penilaian Kuis: {{ $quiz->title }}</h1>
            <p class="text-sm text-gray-500">{{ $quiz->course->subject->name ?? '' }} - {{ $quiz->questions->count() }} soal</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ session('success') }}</div>
    @endif

    @forelse($responses as $resp)
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Student Header -->
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $resp['student_name'] }}</span>
                <span class="text-sm text-gray-500 ml-2">{{ $resp['student_class'] }}</span>
                <span class="text-xs text-gray-400 ml-2">{{ $resp['submitted_at'] }}</span>
            </div>
            <div class="flex items-center gap-3">
                @if($resp['is_graded'])
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">✅ Nilai: {{ $resp['total_score'] }}</span>
                @else
                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">Auto: {{ $resp['auto_score'] }}</span>
                @endif
            </div>
        </div>

        <!-- Questions & Answers -->
        <div class="p-5">
            @foreach($quiz->questions as $qi => $q)
            <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0 last:mb-0 last:pb-0">
                <div class="flex items-start gap-2 mb-2">
                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $qi + 1 }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $q->question }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst(str_replace('_', ' ', $q->question_type)) }} - {{ $q->score }} poin</p>
                    </div>
                </div>

                @php $answer = $resp['answers'][$q->id] ?? '-'; @endphp

                @if($q->question_type === 'multiple_choice')
                <div class="ml-9 space-y-1">
                    @foreach($q->options ?? [] as $oi => $opt)
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm
                        {{ $opt === $q->correct_answer ? 'bg-green-50 text-green-700 font-medium' : '' }}
                        {{ $answer === $opt && $opt !== $q->correct_answer ? 'bg-red-50 text-red-600' : '' }}">
                        {{ chr(65 + $oi) }}. {{ $opt }}
                        @if($opt === $q->correct_answer) ✅ @endif
                        @if($answer === $opt && $opt !== $q->correct_answer) (jawaban siswa) @endif
                        @if($answer === $opt && $opt === $q->correct_answer) (jawaban siswa ✅) @endif
                    </div>
                    @endforeach
                </div>

                @elseif($q->question_type === 'true_false')
                <div class="ml-9 flex gap-3">
                    @foreach(['benar' => 'Benar', 'salah' => 'Salah'] as $val => $label)
                    <div class="px-4 py-2 rounded-lg text-sm border
                        {{ $val === $q->correct_answer ? 'bg-green-50 border-green-300 text-green-700 font-medium' : 'border-gray-200' }}
                        {{ $answer === $val && $val !== $q->correct_answer ? 'bg-red-50 border-red-300 text-red-600' : '' }}">
                        {{ $label }}
                        @if($val === $q->correct_answer) ✅ @endif
                        @if($answer === $val) &larr; siswa @endif
                    </div>
                    @endforeach
                </div>

                @elseif($q->question_type === 'essay')
                <div class="ml-9">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-2">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $answer ?: '(tidak dijawab)' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500 font-medium">Nilai (maks {{ $q->score }}):</label>
                        <input type="number" wire:model.defer="essayScores.{{ $resp['id'] }}_{{ $q->id }}" min="0" max="{{ $q->score }}" class="w-20 px-2 py-1.5 border rounded-lg text-sm text-center">
                    </div>
                </div>
                @endif
            </div>
            @endforeach

            <!-- Grade Button -->
            <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                <button wire:click="gradeResponse({{ $resp['id'] }})" class="px-5 py-2 bg-green-600 text-white rounded-lg font-medium text-sm hover:shadow-lg transition">
                    ✅ Simpan Nilai
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border p-8 text-center text-gray-400">Belum ada siswa yang mengerjakan kuis ini</div>
    @endforelse
</div>
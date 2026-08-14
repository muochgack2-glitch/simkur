<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.student.course', $quiz->course) }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left text-lg"></i></a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $quiz->title }}</h1>
            <p class="text-sm text-gray-500">{{ $quiz->questions->count() }} soal - {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' menit' : 'Tanpa batas' }}</p>
        </div>
        @if(!$isFinished)
        <button wire:click="submitQuiz" onclick="return confirm('Yakin ingin mengumpulkan kuis ini?')" class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition">
            <i class="fas fa-paper-plane mr-1"></i>Kumpulkan
        </button>
        @endif
    </div>
    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif
    @if($isFinished && $response)
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5">
        <h3 class="font-semibold text-green-800 dark:text-green-300"><i class="fas fa-check-circle mr-2"></i>Kuis Selesai</h3>
        <p class="text-sm text-green-700 mt-1">Nilai: <strong>{{ $response->score }}</strong></p>
    </div>
    @endif
    <div class="space-y-4">
        @foreach($quiz->questions as $index => $question)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start gap-3 mb-3">
                <span class="w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $index + 1 }}</span>
                <div class="flex-1">
                    <p class="text-gray-800 dark:text-white font-medium">{{ $question->question }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $question->score }} poin</p>
                </div>
            </div>
            @if($question->question_type === 'multiple_choice')
            <div class="space-y-2 ml-11">
                @foreach($question->options ?? [] as $oi => $opt)
                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition-all {{ ($answers[$question->id] ?? '') === $opt ? 'bg-blue-50 border-blue-400 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50' }} {{ $isFinished ? 'pointer-events-none' : '' }}">
                    <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $opt }}" class="text-blue-600" @if($isFinished) disabled @endif>
                    <span class="text-sm">{{ chr(65 + $oi) }}. {{ $opt }}</span>
                    @if($isFinished && $opt === $question->correct_answer)<i class="fas fa-check-circle text-green-500 ml-auto"></i>@endif
                </label>
                @endforeach
            </div>
            @elseif($question->question_type === 'true_false')
            <div class="flex gap-3 ml-11">
                @foreach(['benar' => 'Benar', 'salah' => 'Salah'] as $val => $label)
                <label class="flex items-center gap-2 px-5 py-3 rounded-xl border cursor-pointer transition-all {{ ($answers[$question->id] ?? '') === $val ? 'bg-blue-50 border-blue-400' : 'border-gray-200 hover:bg-gray-50' }} {{ $isFinished ? 'pointer-events-none' : '' }}">
                    <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $val }}" @if($isFinished) disabled @endif>
                    <span class="text-sm font-medium">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            @elseif($question->question_type === 'essay')
            <div class="ml-11">
                <textarea wire:model.defer="answers.{{ $question->id }}" rows="4" placeholder="Tulis jawaban..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm resize-none" @if($isFinished) disabled @endif>{{ $answers[$question->id] ?? '' }}</textarea>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @if(!$isFinished)
    <div class="flex justify-between mt-6">
        <button wire:click="saveProgress" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl"><i class="fas fa-save mr-1"></i>Simpan Progress</button>
        <button wire:click="submitQuiz" onclick="return confirm('Yakin ingin mengumpulkan?')" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg"><i class="fas fa-paper-plane mr-1"></i>Kumpulkan Kuis</button>
    </div>
    @endif
</div>
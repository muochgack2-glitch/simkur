@extends('layouts.app-simple')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $assessment->title }}</h1>
        <p class="mt-2 text-gray-600">Jawab semua pertanyaan dengan jujur sesuai kondisi Anda</p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('student.assessment.submit', $assessment->id) }}" id="assessmentForm">
        @csrf

        <!-- Questions -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <div class="space-y-8">
                @foreach($questions as $index => $question)
                    <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                        <div class="mb-4 flex items-start">
                            <span class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                {{ $question->order_number }}
                            </span>
                            <p class="flex-1 text-base font-medium text-gray-900">
                                {{ $question->question_text }}
                                <span class="text-red-600">*</span>
                            </p>
                        </div>

                        <div class="ml-11 space-y-3">
                            @if($assessment->isVark())
                                <!-- VARK: Radio buttons -->
                                @foreach($question->options as $option)
                                    <label class="flex cursor-pointer items-start rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-100 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option->id }}"
                                               required
                                               class="mt-1 h-4 w-4">
                                        <span class="ml-3 flex-1 text-sm">
                                            <strong>{{ chr(64 + $option->order_number) }}.</strong> {{ $option->option_text }}
                                        </span>
                                    </label>
                                @endforeach
                            @else
                                <!-- Diagnostic: Likert Scale -->
                                <div class="px-4">
                                    <div class="mb-3 flex justify-between text-xs text-gray-600">
                                        <span>Sangat Tidak Sesuai</span>
                                        <span>Sangat Sesuai</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        @foreach($question->options as $option)
                                            <label class="flex flex-1 cursor-pointer flex-col items-center">
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $option->id }}"
                                                       required
                                                       class="peer sr-only">
                                                <div class="flex h-12 w-full items-center justify-center rounded-lg border-2 border-gray-300 bg-white transition-all peer-checked:border-green-600 peer-checked:bg-green-600 peer-checked:text-white hover:bg-gray-100">
                                                    <span class="text-lg font-bold">{{ $option->score_value }}</span>
                                                </div>
                                                <span class="mt-2 text-xs text-center text-gray-600">
                                                    {{ ['Sangat Tidak', 'Tidak', 'Cukup', 'Sesuai', 'Sangat'][$option->score_value - 1] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Pastikan semua pertanyaan sudah dijawab sebelum submit
                </p>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Submit Asesmen
                </button>
            </div>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 rounded-lg bg-blue-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3 text-sm text-blue-700">
                <p class="font-medium">Tips Pengisian:</p>
                <ul class="mt-1 list-inside list-disc space-y-1">
                    <li>Jawab sesuai dengan kondisi Anda yang sebenarnya</li>
                    <li>Tidak ada jawaban benar atau salah</li>
                    <li>Pastikan semua pertanyaan terjawab</li>
                    <li>Anda tidak bisa mengubah jawaban setelah submit</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Simple validation before submit
document.getElementById('assessmentForm').addEventListener('submit', function(e) {
    const totalQuestions = {{ $questions->count() }};
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answeredQuestions < totalQuestions) {
        e.preventDefault();
        alert('Mohon jawab semua pertanyaan sebelum submit. (' + answeredQuestions + '/' + totalQuestions + ' terjawab)');
        return false;
    }
    
    if (!confirm('Apakah Anda yakin ingin submit? Anda tidak bisa mengubah jawaban setelah submit.')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection

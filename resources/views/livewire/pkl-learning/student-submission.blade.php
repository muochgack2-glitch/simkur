<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.student.course', $assignment->course) }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left text-lg"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $assignment->title }}</h1>
            <p class="text-sm text-gray-500">{{ $assignment->course->subject->name ?? '' }} · Nilai maks: {{ $assignment->max_score }}</p>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-4 px-5 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
    @endif

    <!-- Assignment Description -->
    @if($assignment->description)
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 mb-6 border border-gray-200 dark:border-gray-600">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Instruksi</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $assignment->description }}</p>
        <p class="text-xs text-gray-500 mt-3"><i class="fas fa-clock mr-1"></i>Deadline: {{ $assignment->deadline->translatedFormat('d M Y, H:i') }}
            @if($assignment->isOverdue())<span class="text-red-500 font-medium"> — Sudah lewat!</span>@endif
        </p>
    </div>
    @endif

    <!-- Already Submitted -->
    @if($submission && $submission->isSubmitted())
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5">
        <h3 class="font-semibold text-green-800 dark:text-green-300 mb-2"><i class="fas fa-check-circle mr-2"></i>Sudah Dikumpulkan</h3>
        <p class="text-sm text-green-700 dark:text-green-400">Waktu: {{ $submission->submitted_at->translatedFormat('d M Y H:i') }}
            @if($submission->is_late)<span class="text-red-500 font-medium ml-2">?? Terlambat</span>@endif
        </p>
        @if($submission->isGraded())
        <p class="text-sm text-green-700 mt-2"><i class="fas fa-star mr-1"></i>Nilai: <strong>{{ $submission->score }}/{{ $assignment->max_score }}</strong></p>
        @if($submission->feedback)<p class="text-sm text-gray-600 mt-1"><i class="fas fa-comment mr-1"></i>{{ $submission->feedback }}</p>@endif
        @endif
    </div>
    @endif

    <!-- Submission Form -->
    <form wire:submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">{{ $submission?->isSubmitted() ? 'Update Jawaban' : 'Kumpulkan Tugas' }}</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jawaban</label>
                <textarea wire:model="content" rows="6" placeholder="Tulis jawaban di sini..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            @if($assignment->allow_file_upload)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload File (opsional, maks 10MB)</label>
                <input type="file" wire:model="file" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-sm">
                <div wire:loading wire:target="file" class="text-sm text-blue-500 mt-1"><i class="fas fa-spinner fa-spin mr-1"></i>Uploading...</div>
            </div>
            @endif
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('pkl-learning.student.course', $assignment->course) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 rounded-xl shadow-lg" wire:loading.attr="disabled">
                <i class="fas fa-paper-plane mr-1"></i>Kumpulkan
            </button>
        </div>
    </form>
</div>

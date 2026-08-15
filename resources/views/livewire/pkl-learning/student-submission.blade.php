<div>
    <!-- Header -->
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-6">
        <div class="bg-purple-600 px-5 sm:px-6 py-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('pkl-learning.student.course', $assignment->course) }}" class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <p class="text-purple-100 text-xs font-medium">{{ $assignment->course->subject->name ?? '' }} · Nilai maks: {{ $assignment->max_score }}</p>
                    <h1 class="text-lg font-bold text-white mt-0.5">{{ $assignment->title }}</h1>
                </div>
            </div>
        </div>

        <!-- Deadline bar -->
        <div class="px-5 py-3 bg-purple-50 border-b flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-purple-800">
                <span>📅</span>
                <span>Deadline: <strong>{{ $assignment->deadline->translatedFormat('d M Y, H:i') }}</strong></span>
            </div>
            @if($assignment->isOverdue())
            <span class="px-2.5 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">⏰ Lewat Deadline</span>
            @else
            @php $remaining = now()->diffForHumans($assignment->deadline, ['parts' => 2, 'short' => true]); @endphp
            <span class="text-xs text-purple-500">Sisa {{ $remaining }}</span>
            @endif
        </div>
    </div>

    @if(session('error'))
    <div class="mb-4 px-5 py-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm flex items-center gap-2">⚠️ {{ session('error') }}</div>
    @endif

    <!-- Instruksi -->
    @if($assignment->description)
    <div class="bg-white rounded-2xl border shadow-sm p-5 mb-6">
        <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">📋 Instruksi</h3>
        <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ $assignment->description }}</p>
    </div>
    @endif

    <!-- Sudah Dikumpulkan -->
    @if($submission && $submission->isSubmitted())
    <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-lg">✅</div>
                <div>
                    <h3 class="font-bold text-green-800">Sudah Dikumpulkan</h3>
                    <p class="text-xs text-green-600">{{ $submission->submitted_at->translatedFormat('d M Y H:i') }}
                        @if($submission->is_late)<span class="px-1.5 py-0.5 bg-red-100 text-red-600 rounded text-[10px] font-bold ml-1">Terlambat</span>@endif
                    </p>
                </div>
            </div>
        </div>
        @if($submission->isGraded())
        <div class="px-5 py-4 bg-green-100/50 border-t border-green-200">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-xs text-green-600 font-medium">Nilai</p>
                    <p class="text-2xl font-bold text-green-800">{{ $submission->score }}<span class="text-sm text-green-500">/{{ $assignment->max_score }}</span></p>
                </div>
                @if($submission->feedback)
                <div class="flex-1 border-l border-green-300 pl-4">
                    <p class="text-xs text-green-600 font-medium">Feedback Guru</p>
                    <p class="text-sm text-green-800 mt-0.5">💬 {{ $submission->feedback }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Form Kumpulkan -->
    <form wire:submit.prevent="submit" class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                ✍️ {{ $submission?->isSubmitted() ? 'Update Jawaban' : 'Kumpulkan Tugas' }}
            </h3>
        </div>
        <div class="p-5 space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban <span class="text-red-500">*</span></label>
                <textarea wire:model="content" rows="8" placeholder="Tulis jawaban di sini..." class="w-full px-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-purple-500 resize-none"></textarea>
                @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @if($assignment->allow_file_upload)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">📎 Upload File <span class="text-gray-400 font-normal">(opsional, maks 10MB)</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-purple-400 transition-colors">
                    <input type="file" wire:model="file" class="w-full text-sm">
                    <div wire:loading wire:target="file" class="mt-2 text-sm text-purple-500 font-medium">⏳ Mengupload file...</div>
                </div>
                @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>
        <div class="bg-gray-50 border-t px-5 py-4 flex justify-between items-center">
            <a href="{{ route('pkl-learning.student.course', $assignment->course) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-lg transition-all" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">📤 Kumpulkan</span>
                <span wire:loading wire:target="submit">⏳ Mengirim...</span>
            </button>
        </div>
    </form>
</div>
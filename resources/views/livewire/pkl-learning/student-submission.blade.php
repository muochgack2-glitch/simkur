<div>
    <!-- Header -->
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-5">
        <div class="bg-purple-600 px-5 sm:px-6 py-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('pkl-learning.student.course', $assignment->course) }}" class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-purple-100 text-xs font-medium truncate">{{ $assignment->course->subject->name ?? '' }} &bull; Nilai maks: {{ $assignment->max_score }}</p>
                    <h1 class="text-lg font-bold text-white mt-0.5 leading-tight">{{ $assignment->title }}</h1>
                </div>
            </div>
        </div>
        <div class="px-5 py-3 bg-purple-50 border-b flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-purple-800">
                <span>📅</span>
                <span>Deadline: <strong>{{ $assignment->deadline->translatedFormat('d M Y, H:i') }}</strong></span>
            </div>
            @if($assignment->isOverdue())
            <span class="px-2.5 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">⏰ Lewat Deadline</span>
            @else
            @php $remaining = now()->diffForHumans($assignment->deadline, ['parts' => 2, 'short' => true]); @endphp
            <span class="text-xs text-purple-600 font-medium">⏳ Sisa {{ $remaining }}</span>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-5 py-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">❌ {{ session('error') }}</div>
    @endif

    @if($submission && $submission->isSubmitted())
    <div class="mb-5 bg-green-50 border border-green-200 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-xl">✅</div>
                <div>
                    <h3 class="font-bold text-green-800">Tugas Sudah Dikumpulkan</h3>
                    <p class="text-xs text-green-600">{{ $submission->submitted_at->translatedFormat('d M Y H:i') }}
                        @if($submission->is_late)<span class="px-1.5 py-0.5 bg-red-100 text-red-600 rounded text-[10px] font-bold ml-1">Terlambat</span>@endif
                    </p>
                </div>
            </div>
            @if($submission->isGraded())
            <div class="text-right">
                <p class="text-xs text-green-600">Nilaimu</p>
                <p class="text-2xl font-black text-green-700">{{ $submission->score }}<span class="text-sm font-normal text-green-500">/{{ $assignment->max_score }}</span></p>
            </div>
            @else
            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl font-medium">⏳ Menunggu penilaian guru</span>
            @endif
        </div>
        @if($submission->isGraded() && $submission->feedback)
        <div class="px-5 py-4 bg-green-100/50 border-t border-green-200">
            <p class="text-xs text-green-700 font-semibold mb-1">💬 Feedback Guru:</p>
            <p class="text-sm text-green-800">{{ $submission->feedback }}</p>
        </div>
        @endif
        @if($submission->content)
        <div class="px-5 py-4 border-t border-green-200">
            <p class="text-xs text-green-700 font-semibold mb-2">Jawaban yang kamu kumpulkan:</p>
            <div class="bg-white rounded-xl p-4 text-sm text-gray-700 whitespace-pre-line border border-green-100">{{ $submission->content }}</div>
        </div>
        @endif
        @if($submission->file_path)
        <div class="px-5 py-4 border-t border-green-200 flex items-center gap-3">
            <span class="text-xs text-green-700 font-semibold">File:</span>
            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="text-xs text-blue-600 underline">{{ basename($submission->file_path) }}</a>
        </div>
        @endif
    </div>
    @endif

    @if(!($submission && $submission->isSubmitted()))
    <div class="mb-5 bg-blue-50 border border-blue-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-blue-700 mb-3">📋 Cara Mengerjakan Tugas:</p>
        <div class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 border border-blue-200 text-xs text-blue-700 font-medium">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black">1</span> Baca instruksi
            </div>
            <span class="text-blue-300 font-bold">&#8594;</span>
            <div class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 border border-blue-200 text-xs text-blue-700 font-medium">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black">2</span> Tulis jawaban
            </div>
            @if($assignment->allow_file_upload)
            <span class="text-blue-300 font-bold">&#8594;</span>
            <div class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 border border-blue-200 text-xs text-blue-700 font-medium">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black">3</span> Upload file
            </div>
            @endif
            <span class="text-blue-300 font-bold">&#8594;</span>
            <div class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 border border-purple-200 text-xs text-purple-700 font-medium">
                <span class="w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-black">{{ $assignment->allow_file_upload ? '4' : '3' }}</span> Kumpulkan
            </div>
        </div>
    </div>
    @endif

    @if($assignment->description)
    <div class="bg-white rounded-2xl border shadow-sm p-5 mb-5">
        <h3 class="font-bold text-gray-800 mb-3">📋 Instruksi Tugas</h3>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $assignment->description }}</p>
        </div>
    </div>
    @endif

    <form wire:submit="submit" x-data
        @submit.prevent="confirm('Yakin mau mengumpulkan tugas ini? Pastikan jawabanmu sudah benar dan lengkap.') && $wire.submit()"
        class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gray-50 border-b flex items-center gap-2">
            <h3 class="font-bold text-gray-800">{{ $submission?->isSubmitted() ? 'Update Jawaban' : 'Kumpulkan Tugas' }}</h3>
            @if($submission?->isSubmitted())
            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Mengubah jawaban sebelumnya</span>
            @endif
        </div>
        <div class="p-5 space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban / Uraian <span class="text-red-500">*</span></label>
                <textarea wire:model="content" rows="8"
                    placeholder="Tulis jawaban lengkapmu di sini. Jelaskan dengan detail dan pastikan sesuai instruksi guru..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-400 focus:border-purple-400 resize-none transition-colors"></textarea>
                @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            @if($assignment->allow_file_upload)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File <span class="text-gray-400 font-normal">(opsional, maks 10MB)</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-purple-400 transition-colors">
                    <p class="text-xs text-gray-500 mb-2">Klik untuk pilih file atau drag & drop</p>
                    <input type="file" wire:model="file" class="w-full text-sm text-gray-500">
                    <div wire:loading wire:target="file" class="mt-2 text-sm text-purple-500 font-medium">Mengupload file...</div>
                </div>
                @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>
        <div class="bg-gray-50 border-t px-5 py-4 flex justify-between items-center">
            <a href="{{ route('pkl-learning.student.course', $assignment->course) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow transition-all" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">🚀 Kumpulkan Tugas</span>
                <span wire:loading wire:target="submit">⏳ Mengirim...</span>
            </button>
        </div>
    </form>
</div>
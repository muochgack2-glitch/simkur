<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.show', $assignment->course) }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left text-lg"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Penilaian: {{ $assignment->title }}</h1>
            <p class="text-sm text-gray-500">{{ $assignment->course->subject->name ?? '' }} - Nilai maks: {{ $assignment->max_score }}</p>
        </div>
    </div>
    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm"><i class="fas fa-check-circle text-green-500"></i>{{ session('success') }}</div>
    @endif
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <th class="text-left py-3 px-4 font-semibold">Siswa</th>
                <th class="text-left py-3 px-4 font-semibold">Jawaban</th>
                <th class="text-left py-3 px-4 font-semibold">File</th>
                <th class="text-left py-3 px-4 font-semibold">Waktu</th>
                <th class="text-left py-3 px-4 font-semibold">Nilai</th>
                <th class="text-left py-3 px-4 font-semibold">Feedback</th>
                <th class="text-center py-3 px-4 font-semibold">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($submissions as $sub)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                    <td class="py-3 px-4">
                        <p class="font-medium text-gray-800 dark:text-white">{{ $sub->student->name }}</p>
                        <p class="text-xs text-gray-500">{{ $sub->student->schoolClass->name ?? '-' }}</p>
                    </td>
                    <td class="py-3 px-4 max-w-[200px]"><p class="text-xs text-gray-600 truncate">{{ $sub->content ?: '-' }}</p></td>
                    <td class="py-3 px-4">
                        @if($sub->file_path)<a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="text-blue-500 text-xs"><i class="fas fa-download mr-1"></i>{{ $sub->file_name }}</a>@else<span class="text-gray-400 text-xs">-</span>@endif
                    </td>
                    <td class="py-3 px-4 text-xs text-gray-500">
                        {{ $sub->submitted_at?->translatedFormat('d/m H:i') }}
                        @if($sub->is_late)<span class="text-red-500 font-medium ml-1">Telat</span>@endif
                    </td>
                    <td class="py-3 px-4"><input type="number" wire:model.defer="scores.{{ $sub->id }}" min="0" max="{{ $assignment->max_score }}" class="w-20 px-2 py-1.5 border rounded-lg text-sm text-center"></td>
                    <td class="py-3 px-4"><input type="text" wire:model.defer="feedbacks.{{ $sub->id }}" placeholder="Komentar..." class="w-full px-2 py-1.5 border rounded-lg text-xs"></td>
                    <td class="py-3 px-4 text-center">
                        <button wire:click="grade({{ $sub->id }})" class="px-3 py-1.5 text-xs bg-green-500 text-white hover:bg-green-600 rounded-lg font-medium"><i class="fas fa-check mr-1"></i>Simpan</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-8 text-center text-gray-400">Belum ada submission</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
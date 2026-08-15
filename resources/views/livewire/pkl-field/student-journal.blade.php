<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📔 Jurnal Harian PKL</h1>
            @if($placement)
            <p class="text-gray-500 mt-1 text-sm">🏭 {{ $placement->company->name ?? '-' }}</p>
            @endif
        </div>
        @if($isStudent && $placement)
        <button wire:click="openForm()" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg">
            + Tulis Jurnal
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    @if(!$isStudent && !$placement)
    <!-- Filters for guru -->
    <div class="flex gap-3 mb-6">
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border rounded-xl bg-white text-sm">
            <option value="">Semua Status</option>
            <option value="submitted">Menunggu Review</option>
            <option value="approved">Disetujui</option>
            <option value="revision">Perlu Revisi</option>
            <option value="draft">Draft</option>
        </select>
    </div>
    @endif

    <!-- Weekly Groups -->
    @forelse($weeklyGroups as $weekStart => $items)
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-500 mb-3 flex items-center gap-2">
            📅 Minggu {{ \Carbon\Carbon::parse($weekStart)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($weekStart)->addDays(6)->translatedFormat('d M Y') }}
            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $items->count() }} jurnal</span>
        </h3>
        <div class="space-y-3">
            @foreach($items as $j)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        @if(!$isStudent)
                        <p class="text-xs text-purple-600 font-semibold mb-1">{{ $j->student->name ?? '-' }}</p>
                        @endif
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $j->journal_date->translatedFormat('l, d M Y') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $j->status === 'approved' ? 'bg-green-100 text-green-700' : ($j->status === 'submitted' ? 'bg-blue-100 text-blue-700' : ($j->status === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')) }}">
                                {{ $j->status === 'approved' ? '✅ Disetujui' : ($j->status === 'submitted' ? '📤 Dikirim' : ($j->status === 'revision' ? '🔄 Revisi' : '📝 Draft')) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Kegiatan:</strong> {{ $j->activities }}</p>
                        @if($j->learnings)
                        <p class="text-sm text-gray-600 mt-1"><strong>Pembelajaran:</strong> {{ $j->learnings }}</p>
                        @endif
                        @if($j->challenges)
                        <p class="text-sm text-amber-600 mt-1"><strong>Kendala:</strong> {{ $j->challenges }}</p>
                        @endif
                        @if($j->supervisor_notes)
                        <div class="mt-2 px-3 py-2 bg-blue-50 rounded-lg text-sm text-blue-800">
                            💬 <strong>Catatan pembimbing:</strong> {{ $j->supervisor_notes }}
                        </div>
                        @endif
                    </div>
                    <div class="flex gap-2 ml-3">
                        @if($isStudent && in_array($j->status, ['draft', 'revision']))
                        <button wire:click="openForm({{ $j->id }})" class="text-blue-600 text-xs font-medium">Edit</button>
                        @endif
                        @if(!$isStudent && $j->status === 'submitted')
                        <button wire:click="openReview({{ $j->id }})" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs">Review</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-400">Belum ada jurnal</h3>
        @if($isStudent)
        <p class="text-sm text-gray-400 mt-1">Klik "Tulis Jurnal" untuk mencatat kegiatan harian</p>
        @endif
    </div>
    @endforelse

    <!-- Write Journal Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h2 class="text-lg font-bold mb-4">{{ $editingId ? 'Edit Jurnal' : 'Tulis Jurnal Harian' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" wire:model="journal_date" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan <span class="text-red-500">*</span></label>
                    <textarea wire:model="activities" rows="4" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none" placeholder="Jelaskan kegiatan hari ini..."></textarea>
                    @error('activities') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hal yang Dipelajari</label>
                    <textarea wire:model="learnings" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kendala</label>
                    <textarea wire:model="challenges" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="save(true)" class="px-5 py-2.5 border border-gray-400 rounded-xl text-sm">Simpan Draft</button>
                <button wire:click="save(false)" class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold">Kirim</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Review Modal -->
    @if($showReview)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showReview', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4">Review Jurnal</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea wire:model="reviewNotes" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none" placeholder="Komentar untuk siswa..."></textarea>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showReview', false)" class="px-5 py-2.5 border rounded-xl text-sm">Batal</button>
                <button wire:click="submitReview('revision')" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold">Minta Revisi</button>
                <button wire:click="submitReview('approve')" class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold">Setujui ✅</button>
            </div>
        </div>
    </div>
    @endif
</div>
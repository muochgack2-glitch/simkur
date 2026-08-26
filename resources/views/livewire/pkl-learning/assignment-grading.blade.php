<style>
@media (max-width: 767px) { .ag-desktop { display: none !important; } }
@media (min-width: 768px) { .ag-mobile  { display: none !important; } }
[x-cloak] { display: none !important; }
</style>

<div x-data="{
    modalImg: null,
    modalAnswer: null,
    modalName: '',
    showRevision: false,
    revisionId: null,
    revisionNote: '',
    openRevision(id) { this.revisionId = id; this.revisionNote = ''; this.showRevision = true; },
    async submitRevision() {
        await $wire.call('requestRevision', this.revisionId, this.revisionNote);
        this.showRevision = false;
    }
}">

    {{-- IMAGE MODAL --}}
    <div x-cloak x-show="modalImg" @click.self="modalImg=null" @keydown.escape.window="modalImg=null"
        style="position:fixed;inset:0;z-index:50;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center;padding:16px;">
        <div style="position:relative;background:white;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);max-width:672px;width:100%;padding:16px;">
            <button @click="modalImg=null" style="position:absolute;top:12px;right:12px;width:32px;height:32px;background:#f3f4f6;border-radius:50%;border:none;cursor:pointer;font-weight:bold;">&#10005;</button>
            <img :src="modalImg" style="width:100%;max-height:70vh;object-fit:contain;border-radius:12px;" alt="Preview">
        </div>
    </div>

    {{-- ANSWER MODAL --}}
    <div x-cloak x-show="modalAnswer" @click.self="modalAnswer=null" @keydown.escape.window="modalAnswer=null"
        style="position:fixed;inset:0;z-index:50;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;padding:16px;">
        <div style="background:white;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);max-width:600px;width:100%;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-weight:700;font-size:15px;color:#111827;">Jawaban Siswa</p>
                    <p x-text="modalName" style="font-size:12px;color:#6b7280;margin-top:2px;"></p>
                </div>
                <button @click="modalAnswer=null" style="width:32px;height:32px;background:#f3f4f6;border-radius:50%;border:none;cursor:pointer;font-weight:bold;font-size:16px;">&#10005;</button>
            </div>
            <div style="padding:20px;overflow-y:auto;flex:1;">
                <p x-text="modalAnswer" style="font-size:14px;color:#374151;white-space:pre-wrap;line-height:1.7;word-break:break-word;"></p>
            </div>
        </div>
    </div>

    {{-- REVISION MODAL --}}
    <div x-cloak x-show="showRevision" @click.self="showRevision=false" @keydown.escape.window="showRevision=false"
        style="position:fixed;inset:0;z-index:50;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;padding:16px;">
        <div style="background:white;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);max-width:480px;width:100%;overflow:hidden;">
            <div style="background:#f59e0b;padding:14px 20px;display:flex;align-items:center;gap:10px;">
                <span style="font-size:20px;">✏️</span>
                <div>
                    <p style="font-weight:700;color:white;font-size:14px;margin:0;">Minta Kerjakan Ulang</p>
                    <p style="color:#fef3c7;font-size:12px;margin:0;">Siswa akan diminta merevisi & kumpulkan ulang</p>
                </div>
                <button @click="showRevision=false" style="margin-left:auto;width:28px;height:28px;background:rgba(255,255,255,0.3);border-radius:50%;border:none;cursor:pointer;color:white;font-weight:bold;">&#10005;</button>
            </div>
            <div style="padding:20px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Catatan untuk siswa <span style="color:#9ca3af;font-weight:normal;">(opsional)</span></label>
                <textarea x-model="revisionNote" rows="3"
                    placeholder="Contoh: Jawaban kurang lengkap, tambahkan penjelasan tentang..."
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px;font-size:13px;resize:none;box-sizing:border-box;"></textarea>
                <div style="display:flex;gap:10px;margin-top:16px;">
                    <button @click="showRevision=false"
                        style="flex:1;padding:10px;border:1px solid #d1d5db;border-radius:10px;font-size:13px;cursor:pointer;background:white;color:#374151;">
                        Batal
                    </button>
                    <button @click="submitRevision()"
                        style="flex:1;padding:10px;background:#f59e0b;border:none;border-radius:10px;font-size:13px;font-weight:600;color:white;cursor:pointer;">
                        ✏️ Kirim Permintaan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.show', $assignment->course) }}" class="text-gray-500 hover:text-gray-700">⬅</a>
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Penilaian: {{ $assignment->title }}</h1>
            <p class="text-sm text-gray-500">{{ $assignment->course->subject->name ?? '' }} - Nilai maks: {{ $assignment->max_score }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    {{-- MOBILE: Card layout --}}
    <div class="ag-mobile space-y-4">
        @forelse($submissions as $sub)
        @php $ext=strtolower(pathinfo($sub->file_path??'',PATHINFO_EXTENSION)); $isImg=in_array($ext,['jpg','jpeg','png','gif','webp']); @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-3"
             style="{{ $sub->revision_requested ? 'border-color:#fcd34d;background:#fffbeb;' : '' }}">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white">{{ $sub->student->name }}</p>
                    <p class="text-xs text-gray-500">{{ $sub->student->schoolClass->name ?? '-' }}</p>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @if($sub->revision_requested)
                    <span style="font-size:11px;font-weight:600;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;border-radius:999px;padding:2px 8px;">✏️ Menunggu Revisi</span>
                    @endif
                    @if($sub->is_late)<span class="text-xs font-semibold text-red-500 bg-red-50 border border-red-200 rounded-full px-2 py-0.5">Telat</span>@endif
                </div>
            </div>
            @if($sub->revision_requested && $sub->revision_note)
            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:8px 12px;">
                <p style="font-size:11px;font-weight:600;color:#92400e;margin-bottom:2px;">Catatan Revisi:</p>
                <p style="font-size:12px;color:#78350f;">{{ $sub->revision_note }}</p>
            </div>
            @endif
            <hr class="border-gray-100">
            @if($sub->content)
            <div>
                <p class="text-xs font-semibold text-gray-400 mb-1">Jawaban</p>
                <p class="text-sm text-gray-700" style="white-space:pre-wrap;">{{ $sub->content }}</p>
                <button @click="modalAnswer={{ json_encode($sub->content) }}; modalName='{{ addslashes($sub->student->name) }}'"
                    style="margin-top:4px;font-size:11px;color:#6366f1;text-decoration:underline;background:none;border:none;cursor:pointer;padding:0;">Lihat semua →</button>
            </div>
            @endif
            @if($sub->file_path)
            <div>
                <p class="text-xs font-semibold text-gray-400 mb-1">File</p>
                @if($isImg)<button @click="modalImg='{{ Storage::url($sub->file_path) }}'" class="text-blue-500 text-sm underline">{{ $sub->file_name ?? basename($sub->file_path) }}</button>
                @else<a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="text-blue-500 text-sm underline">{{ $sub->file_name ?? basename($sub->file_path) }}</a>@endif
                <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="ml-2 text-xs text-gray-400">[buka]</a>
            </div>
            @endif
            <p class="text-xs text-gray-400">⏰ {{ $sub->submitted_at?->translatedFormat('d/m H:i') ?? '-' }}</p>
            <hr class="border-gray-100">
            @if(!$sub->revision_requested)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="text-xs font-semibold text-gray-500 mb-1 block">Nilai (maks {{ $assignment->max_score }})</label>
                    <input type="number" wire:model.defer="scores.{{ $sub->id }}" min="0" max="{{ $assignment->max_score }}"
                        style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;text-align:center;font-size:14px;">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 mb-1 block">Feedback</label>
                    <input type="text" wire:model.defer="feedbacks.{{ $sub->id }}" placeholder="Komentar..."
                        style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:12px;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <button wire:click="grade({{ $sub->id }})"
                    style="padding:10px;background:#22c55e;color:white;border-radius:10px;font-weight:600;font-size:13px;border:none;cursor:pointer;">
                    ✅ Simpan Nilai
                </button>
                <button @click="openRevision({{ $sub->id }})"
                    style="padding:10px;background:#f59e0b;color:white;border-radius:10px;font-weight:600;font-size:13px;border:none;cursor:pointer;">
                    ✏️ Kerjakan Ulang
                </button>
            </div>
            @else
            <p style="font-size:12px;color:#b45309;text-align:center;font-style:italic;">Menunggu siswa mengerjakan ulang...</p>
            @endif
        </div>
        @empty
        <div class="py-10 text-center text-gray-400">Belum ada submission</div>
        @endforelse
    </div>

    {{-- DESKTOP: Table layout --}}
    <div class="ag-desktop bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div style="overflow-x:auto;">
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
                    @php $gExt=strtolower(pathinfo($sub->file_path??'',PATHINFO_EXTENSION)); $gIsImg=in_array($gExt,['jpg','jpeg','png','gif','webp']); @endphp
                    <tr style="{{ $sub->revision_requested ? 'background:#fffbeb;' : '' }}">
                        <td class="py-3 px-4">
                            <p class="font-medium text-gray-800 dark:text-white">{{ $sub->student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $sub->student->schoolClass->name ?? '-' }}</p>
                            @if($sub->revision_requested)
                            <span style="font-size:10px;font-weight:600;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;border-radius:999px;padding:1px 6px;display:inline-block;margin-top:3px;">✏️ Revisi</span>
                            @endif
                        </td>
                        <td class="py-3 px-4" style="max-width:200px;">
                            @if($sub->content)
                            <p class="text-xs text-gray-600" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $sub->content }}</p>
                            <button @click="modalAnswer={{ json_encode($sub->content) }}; modalName='{{ addslashes($sub->student->name) }}'"
                                style="margin-top:4px;font-size:11px;color:#6366f1;text-decoration:underline;background:none;border:none;cursor:pointer;padding:0;">
                                Lihat semua →
                            </button>
                            @else<span class="text-gray-400 text-xs">-</span>@endif
                        </td>
                        <td class="py-3 px-4">
                            @if($sub->file_path) @if($gIsImg) <button @click="modalImg='{{ Storage::url($sub->file_path) }}'" class="text-blue-500 text-xs underline">{{ $sub->file_name ?? basename($sub->file_path) }}</button> @else <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="text-blue-500 text-xs underline">{{ $sub->file_name ?? basename($sub->file_path) }}</a> @endif <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="ml-1 text-[10px] text-gray-400">[buka]</a> @else <span class="text-gray-400 text-xs">-</span> @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $sub->submitted_at?->translatedFormat('d/m H:i') }}@if($sub->is_late)<span class="text-red-500 font-medium ml-1">Telat</span>@endif</td>
                        <td class="py-3 px-4">
                            @if(!$sub->revision_requested)
                            <input type="number" wire:model.defer="scores.{{ $sub->id }}" min="0" max="{{ $assignment->max_score }}" class="w-20 px-2 py-1.5 border rounded-lg text-sm text-center">
                            @else<span class="text-xs text-amber-600">—</span>@endif
                        </td>
                        <td class="py-3 px-4">
                            @if(!$sub->revision_requested)
                            <input type="text" wire:model.defer="feedbacks.{{ $sub->id }}" placeholder="Komentar..." class="w-full px-2 py-1.5 border rounded-lg text-xs">
                            @else<span class="text-xs text-amber-700 italic">{{ $sub->revision_note ?: '—' }}</span>@endif
                        </td>
                        <td class="py-3 px-4 text-center" style="white-space:nowrap;">
                            @if(!$sub->revision_requested)
                            <button wire:click="grade({{ $sub->id }})" class="px-3 py-1.5 text-xs bg-green-500 text-white hover:bg-green-600 rounded-lg font-medium">✅ Simpan</button>
                            <button @click="openRevision({{ $sub->id }})" style="margin-left:4px;padding:5px 10px;font-size:11px;background:#f59e0b;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;">✏️ Ulang</button>
                            @else
                            <span style="font-size:11px;color:#b45309;font-weight:600;">Menunggu Revisi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">Belum ada submission</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

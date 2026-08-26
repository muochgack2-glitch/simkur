<div x-data="{ showEdit: false }">

    {{-- ═══ HEADER ═══ --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-5">
        <div class="bg-purple-600 px-5 sm:px-6 py-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('pkl-learning.student.course', $assignment->course) }}"
                   class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors flex-shrink-0">
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

    {{-- ═══ MODE 1: REVISI DIMINTA ═══ --}}
    @if($submission && $submission->revision_requested)
    <div class="mb-5 rounded-2xl overflow-hidden" style="border:2px solid #fcd34d;background:#fffbeb;">
        <div style="background:#f59e0b;padding:14px 20px;display:flex;align-items:center;gap:10px;">
            <span style="font-size:24px;">✏️</span>
            <div>
                <p style="font-weight:700;color:white;font-size:15px;margin:0;">Guru Meminta Kerjakan Ulang</p>
                <p style="color:#fef3c7;font-size:12px;margin:0;">Perbaiki jawabanmu dan kumpulkan kembali</p>
            </div>
            <span style="margin-left:auto;font-size:11px;color:#fef3c7;">{{ $submission->revision_requested_at?->translatedFormat('d M H:i') }}</span>
        </div>
        @if($submission->revision_note)
        <div style="padding:14px 20px;display:flex;gap:10px;align-items:flex-start;">
            <span style="font-size:16px;flex-shrink:0;">📝</span>
            <div>
                <p style="font-size:12px;font-weight:600;color:#92400e;margin:0 0 4px;">Catatan dari Guru:</p>
                <p style="font-size:13px;color:#78350f;margin:0;white-space:pre-wrap;">{{ $submission->revision_note }}</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ═══ MODE 2: SUDAH SUBMIT → TAMPILKAN HASIL ═══ --}}
    @if($submission && $submission->isSubmitted())

    {{-- Status Card --}}
    <div class="mb-5 bg-white rounded-2xl border shadow-sm overflow-hidden">
        {{-- Status header --}}
        @if($submission->isGraded())
        <div style="background:linear-gradient(135deg,#16a34a,#15803d);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
            <div class="flex items-center gap-3">
                <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">🏆</div>
                <div>
                    <p style="font-weight:700;color:white;font-size:15px;margin:0;">Sudah Dinilai</p>
                    <p style="color:#bbf7d0;font-size:12px;margin:0;">{{ $submission->graded_at?->translatedFormat('d M Y H:i') }}</p>
                </div>
            </div>
            <div style="text-align:right;background:rgba(255,255,255,0.15);border-radius:16px;padding:10px 18px;">
                <p style="font-size:11px;color:#bbf7d0;margin:0;">Nilaimu</p>
                <p style="font-size:32px;font-weight:900;color:white;margin:0;line-height:1;">{{ $submission->score }}<span style="font-size:14px;font-weight:400;color:#bbf7d0;">/{{ $assignment->max_score }}</span></p>
            </div>
        </div>
        @elseif($submission->revision_requested)
        <div style="background:linear-gradient(135deg,#d97706,#b45309);padding:16px 20px;display:flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">✏️</div>
            <div>
                <p style="font-weight:700;color:white;font-size:15px;margin:0;">Perlu Direvisi</p>
                <p style="color:#fef3c7;font-size:12px;margin:0;">Dikumpulkan {{ $submission->submitted_at->translatedFormat('d M Y H:i') }}</p>
            </div>
        </div>
        @else
        <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
            <div class="flex items-center gap-3">
                <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">✅</div>
                <div>
                    <p style="font-weight:700;color:white;font-size:15px;margin:0;">Tugas Dikumpulkan</p>
                    <p style="color:#bfdbfe;font-size:12px;margin:0;">{{ $submission->submitted_at->translatedFormat('d M Y H:i') }}{{ $submission->is_late ? ' · Terlambat' : '' }}</p>
                </div>
            </div>
            <span style="font-size:11px;font-weight:600;color:#bfdbfe;background:rgba(255,255,255,0.1);border-radius:999px;padding:4px 12px;">⏳ Menunggu penilaian</span>
        </div>
        @endif

        {{-- Feedback guru (jika ada) --}}
        @if($submission->isGraded() && $submission->feedback)
        <div style="padding:14px 20px;background:#f0fdf4;border-top:1px solid #dcfce7;">
            <p style="font-size:12px;font-weight:600;color:#15803d;margin:0 0 4px;">💬 Feedback Guru:</p>
            <p style="font-size:13px;color:#166534;margin:0;">{{ $submission->feedback }}</p>
        </div>
        @endif
    </div>

    {{-- Jawaban yang dikumpulkan --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-5">
        <div style="padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-weight:700;font-size:14px;color:#1e293b;margin:0;">📄 Jawaban yang Dikumpulkan</h3>
            @if(!$submission->isGraded() && !$submission->revision_requested)
            <button @click="showEdit = !showEdit"
                style="font-size:12px;font-weight:600;padding:6px 14px;border:1.5px solid #6366f1;color:#6366f1;background:white;border-radius:8px;cursor:pointer;"
                x-text="showEdit ? '× Tutup Edit' : '✏️ Edit Jawaban'">
            </button>
            @endif
        </div>

        {{-- Jawaban teks --}}
        @if($submission->content)
        <div style="padding:16px 20px;">
            <p style="font-size:12px;font-weight:600;color:#64748b;margin:0 0 8px;">Jawaban Tertulis:</p>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px;">
                <p style="font-size:13px;color:#334155;white-space:pre-wrap;line-height:1.7;margin:0;word-break:break-word;">{{ $submission->content }}</p>
            </div>
        </div>
        @endif

        {{-- File yang diunggah --}}
        @if($submission->file_path)
        @php
            $fileUrl = \Storage::url($submission->file_path);
            $fullUrl = url($fileUrl);
            $ext = strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION));
            $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $isPdf = $ext === 'pdf';
            $isOffice = in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx']);
        @endphp
        <div style="padding:0 20px 16px;{{ $submission->content ? 'border-top:1px solid #e2e8f0;padding-top:16px;' : '' }}">
            <p style="font-size:12px;font-weight:600;color:#64748b;margin:0 0 8px;">File Terlampir:</p>
            @if($isImg)
            <div style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                <img src="{{ $fileUrl }}" alt="File jawaban" style="width:100%;max-height:400px;object-fit:contain;background:#f8fafc;">
                <div style="padding:10px 14px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:12px;color:#64748b;">{{ $submission->file_name ?? basename($submission->file_path) }}</span>
                    <a href="{{ $fileUrl }}" target="_blank" download style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none;">⬇ Unduh</a>
                </div>
            </div>
            @elseif($isPdf)
            <div style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                <iframe src="{{ $fileUrl }}" style="width:100%;height:400px;border:none;" title="PDF Preview"></iframe>
                <div style="padding:10px 14px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:12px;color:#64748b;">📄 {{ $submission->file_name ?? basename($submission->file_path) }}</span>
                    <a href="{{ $fileUrl }}" target="_blank" style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none;">🔗 Buka</a>
                </div>
            </div>
            @elseif($isOffice)
            <div style="border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;">
                <div class="flex items-center gap-3">
                    <span style="font-size:28px;">📊</span>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">{{ $submission->file_name ?? basename($submission->file_path) }}</p>
                        <p style="font-size:11px;color:#94a3b8;margin:0;text-transform:uppercase;">{{ $ext }} file</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="https://docs.google.com/gview?url={{ urlencode($fullUrl) }}&embedded=true" target="_blank"
                       style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none;padding:6px 12px;border:1px solid #6366f1;border-radius:8px;">👁 Preview</a>
                    <a href="{{ $fileUrl }}" target="_blank" download
                       style="font-size:12px;color:white;font-weight:600;text-decoration:none;padding:6px 12px;background:#6366f1;border-radius:8px;">⬇ Unduh</a>
                </div>
            </div>
            @else
            <div style="border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;">
                <div class="flex items-center gap-3">
                    <span style="font-size:28px;">📎</span>
                    <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">{{ $submission->file_name ?? basename($submission->file_path) }}</p>
                </div>
                <a href="{{ $fileUrl }}" target="_blank"
                   style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none;padding:6px 12px;border:1px solid #6366f1;border-radius:8px;">🔗 Buka</a>
            </div>
            @endif
        </div>
        @endif

        @if(!$submission->content && !$submission->file_path)
        <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Tidak ada jawaban atau file yang dikumpulkan</div>
        @endif
    </div>

    {{-- ═══ FORM EDIT (toggle, hanya jika belum dinilai & bukan revisi) ═══ --}}
    @if(!$submission->isGraded())
    <div x-show="showEdit || {{ $submission->revision_requested ? 'true' : 'false' }}"
         style="display:none;" x-cloak>
    @endif

    @endif {{-- end @if submitted --}}

    {{-- ═══ MODE 3: BELUM SUBMIT / FORM EDIT / REVISI ═══ --}}
    @php
        $showForm = !($submission && $submission->isSubmitted() && $submission->isGraded());
    @endphp
    @if($showForm)

    {{-- Instruksi (hanya saat belum submit) --}}
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
            @if($submission && $submission->revision_requested)
            <h3 class="font-bold text-gray-800">✏️ Kerjakan Ulang</h3>
            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Revisi dari guru</span>
            @elseif($submission && $submission->isSubmitted())
            <h3 class="font-bold text-gray-800">✏️ Edit Jawaban</h3>
            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Mengubah jawaban sebelumnya</span>
            @else
            <h3 class="font-bold text-gray-800">📝 Kumpulkan Tugas</h3>
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
            <div x-data="{ preview: null, fileName: '', fileExt: '' }">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File <span class="text-gray-400 font-normal">(opsional, maks 10MB)</span></label>
                @if($submission?->file_path && !$submission->revision_requested)
                <div class="mb-2 flex items-center gap-2 text-xs text-gray-500 bg-gray-50 rounded-xl p-3 border">
                    <span>📎</span>
                    <span>File sebelumnya: <strong>{{ $submission->file_name ?? basename($submission->file_path) }}</strong></span>
                    <a href="{{ \Storage::url($submission->file_path) }}" target="_blank" class="text-blue-500 underline ml-1">Lihat</a>
                </div>
                @endif
                <div class="border-2 border-dashed rounded-xl p-4 text-center transition-colors"
                    :class="fileName ? 'border-purple-400 bg-purple-50' : 'border-gray-300 hover:border-purple-400'"
                    @dragover.prevent @drop.prevent="const f=$event.dataTransfer.files[0]; if(f){fileName=f.name;fileExt=f.name.split('.').pop().toLowerCase();if(['jpg','jpeg','png','gif','webp'].includes(fileExt)){const r=new FileReader();r.onload=e=>{preview=e.target.result};r.readAsDataURL(f)}else{preview=null}}">
                    <p class="text-xs text-gray-500 mb-2" x-show="!fileName">Klik untuk pilih file atau drag &amp; drop</p>
                    <div x-show="fileName" class="flex items-center justify-center gap-2 mb-2">
                        <span class="text-xs font-bold px-2 py-0.5 bg-purple-100 text-purple-700 rounded uppercase" x-text="fileExt"></span>
                        <span class="text-sm font-medium text-gray-700 truncate max-w-xs" x-text="fileName"></span>
                        <button type="button" @click="fileName='';preview=null;" class="text-red-400 hover:text-red-600 text-xs">&#10005;</button>
                    </div>
                    <input type="file" wire:model="file" class="w-full text-sm text-gray-500"
                        @change="const f=$event.target.files[0];if(f){fileName=f.name;fileExt=f.name.split('.').pop().toLowerCase();if(['jpg','jpeg','png','gif','webp'].includes(fileExt)){const r=new FileReader();r.onload=e=>{preview=e.target.result};r.readAsDataURL(f)}else{preview=null}}">
                    <div wire:loading wire:target="file" class="mt-2 text-xs text-purple-500 font-medium">Mengupload file...</div>
                </div>
                <div x-show="preview" class="mt-3"><p class="text-xs text-gray-500 mb-1">Preview:</p><img :src="preview" class="max-h-48 rounded-xl border object-contain w-full" alt="preview"></div>
                <div x-show="fileName && !preview" class="mt-2 flex items-center gap-2 text-xs text-gray-500"><span>&#128196;</span><span x-text="'File dipilih: ' + fileName"></span></div>
                @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>
        <div class="bg-gray-50 border-t px-5 py-4 flex justify-between items-center">
            @if($submission && $submission->isSubmitted() && !$submission->revision_requested)
            <button type="button" @click="showEdit = false"
                class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-xl transition-colors">
                × Batal Edit
            </button>
            @else
            <a href="{{ route('pkl-learning.student.course', $assignment->course) }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Kembali
            </a>
            @endif
            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow transition-all" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">🚀 {{ ($submission && $submission->revision_requested) ? 'Kumpulkan Ulang' : 'Kumpulkan Tugas' }}</span>
                <span wire:loading wire:target="submit">⏳ Mengirim...</span>
            </button>
        </div>
    </form>

    @if($submission && $submission->isSubmitted() && !$submission->isGraded() && !$submission->revision_requested)
    </div> {{-- close x-show="showEdit" --}}
    @endif

    @endif {{-- end showForm --}}

</div>

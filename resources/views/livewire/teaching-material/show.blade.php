<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📄 {{ $material->title }}</h1>
            <p class="text-gray-600 mt-1">SIMKUR SMK PGRI Blora</p>
        </div>
        
        <a href="{{ route('teaching-materials.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            ← Kembali
        </a>
    </div>

    <!-- Status & Metadata -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📊 STATUS & METADATA</h2>
        
        <div class="space-y-3">
            <div class="flex items-center">
                <span class="w-40 text-sm font-medium text-gray-700">Status:</span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    @if($material->status === 'approved') bg-green-100 text-green-800
                    @elseif($material->status === 'pending_approval') bg-yellow-100 text-yellow-800
                    @elseif($material->status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $material->status_label }}
                </span>
            </div>

            @if($material->status === 'approved')
                <div class="flex items-center">
                    <span class="w-40 text-sm font-medium text-gray-700">Approved oleh:</span>
                    <span class="text-sm text-gray-800">{{ $material->approver->name }} • {{ $material->approved_at->format('d M Y') }}</span>
                </div>
            @endif

            @if($material->status === 'rejected' && $material->approval_notes)
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Catatan Penolakan:</span>
                    <div class="p-3 bg-red-50 border border-red-200 rounded">
                        <p class="text-sm text-red-800">{{ $material->approval_notes }}</p>
                    </div>
                </div>
            @endif

            <div class="flex items-center">
                <span class="w-40 text-sm font-medium text-gray-700">Dibuat oleh:</span>
                <span class="text-sm text-gray-800">{{ $material->creator->name }}</span>
            </div>

            <div class="flex items-center">
                <span class="w-40 text-sm font-medium text-gray-700">Tanggal Upload:</span>
                <span class="text-sm text-gray-800">{{ $material->created_at->format('d M Y H:i') }}</span>
            </div>

            @if($material->updated_at != $material->created_at)
                <div class="flex items-center">
                    <span class="w-40 text-sm font-medium text-gray-700">Terakhir Diupdate:</span>
                    <span class="text-sm text-gray-800">{{ $material->updated_at->format('d M Y H:i') }}</span>
                </div>
            @endif

            <div class="flex items-center">
                <span class="w-40 text-sm font-medium text-gray-700">Statistik:</span>
                <span class="text-sm text-gray-600">👁️ {{ $material->view_count }} views • ⬇️ {{ $material->download_count }} downloads</span>
            </div>
        </div>
    </div>

    <!-- Informasi Dokumen -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 INFORMASI DOKUMEN</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">Kategori:</span>
                <span class="text-sm text-gray-800">{{ $material->category_label }}</span>
            </div>

            @if($material->subject)
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran:</span>
                    <span class="text-sm text-gray-800">{{ $material->subject->name }}</span>
                </div>
            @endif

            @if($material->grade)
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Tingkat:</span>
                    <span class="text-sm text-gray-800">Kelas {{ $material->grade }}</span>
                </div>
            @endif

            @if($material->phase)
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Fase:</span>
                    <span class="text-sm text-gray-800">Fase {{ $material->phase }}</span>
                </div>
            @endif

            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran:</span>
                <span class="text-sm text-gray-800">{{ $material->academicYear->year }}</span>
            </div>

            @if($material->semester)
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Semester:</span>
                    <span class="text-sm text-gray-800">Semester {{ $material->semester }}</span>
                </div>
            @endif

            @if($material->description)
                <div class="md:col-span-2">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Deskripsi:</span>
                    <p class="text-sm text-gray-800">{{ $material->description }}</p>
                </div>
            @endif

            @if($material->tags)
                <div class="md:col-span-2">
                    <span class="block text-sm font-medium text-gray-700 mb-2">Tags:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($material->tags as $tag)
                            <span class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                🏷️ {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Dimensi Profil Lulusan -->
    @if(count($material->selected_dimensions) > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🎯 DIMENSI PROFIL LULUSAN</h2>
            
            <div class="flex flex-wrap gap-2">
                @foreach($material->selected_dimensions as $dimension)
                    <span class="px-4 py-2 text-sm bg-purple-100 text-purple-800 rounded-lg font-medium">
                        {{ $dimension }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- File -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📎 FILE</h2>
        
        @if($material->file_type === 'link')
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800 mb-3">🔗 Link Eksternal</p>
                <a href="{{ $material->external_link }}" target="_blank" 
                   class="text-blue-600 hover:text-blue-800 underline break-all">
                    {{ $material->external_link }}
                </a>
            </div>
            
            <div class="mt-4 flex space-x-2">
                <button wire:click="previewMainFile"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    👁️ Preview
                </button>
                <a href="{{ $material->external_link }}" target="_blank"
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    🔗 Buka Link
                </a>
            </div>
        @else
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-700 mb-1">📄 <strong>{{ basename($material->file_path) }}</strong></p>
                <p class="text-xs text-gray-600">Tipe: {{ strtoupper($material->file_type) }} • Ukuran: {{ $material->file_size_formatted }}</p>
            </div>
            
            <div class="mt-4 flex space-x-2">
                @if(in_array(strtolower($material->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                    <button wire:click="previewMainFile"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        👁️ Preview
                    </button>
                @endif
                <a href="{{ route('teaching-materials.download', $material->id) }}"
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    ⬇️ Download
                </a>
            </div>
        @endif
    </div>

    <!-- Attachments / Lampiran -->
    <div class="bg-white rounded-lg shadow p-6 mb-6 
        @if(session('show_attachment_hint')) ring-4 ring-blue-500 ring-offset-4 @endif" 
        id="attachments-section">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                📎 LAMPIRAN ({{ $material->attachments->count() }})
                @if(session('show_attachment_hint'))
                    <span class="ml-2 text-sm font-normal text-blue-600 animate-pulse">← Tambahkan lampiran di sini</span>
                @endif
            </h2>
            
            @if($this->canManageAttachments())
                <button wire:click="openAttachmentModal" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition flex items-center space-x-2
                        @if(session('show_attachment_hint')) animate-bounce @endif">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Lampiran</span>
                </button>
            @endif
        </div>

        @if(session('show_attachment_hint'))
            <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-800 mb-1">💡 Lengkapi Perangkat Ajar Anda</p>
                        <p class="text-sm text-blue-700">
                            Tambahkan lampiran pendukung untuk membuat perangkat ajar lebih lengkap:
                        </p>
                        <ul class="text-sm text-blue-700 mt-2 ml-4 list-disc space-y-1">
                            <li>📝 LKPD (Lembar Kerja Peserta Didik)</li>
                            <li>📊 Presentasi/Slide pembelajaran</li>
                            <li>🎬 Video pembelajaran (upload atau link YouTube)</li>
                            <li>📋 Instrumen Asesmen & Rubrik Penilaian</li>
                            <li>🔑 Kunci Jawaban</li>
                            <li>📚 Bahan Bacaan tambahan</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if($material->attachments->count() > 0)
            <!-- Download All Button -->
            <div class="mb-4">
                <a href="{{ route('teaching-materials.attachments.download-all', $material->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Semua (ZIP)
                </a>
                <span class="ml-2 text-xs text-gray-600">Total: {{ $material->total_file_size_formatted }}</span>
            </div>

            <!-- Attachments List -->
            <div class="space-y-3">
                @foreach($material->attachments->sortBy('is_primary')->reverse() as $attachment)
                    <div class="p-4 border @if($attachment->is_primary) border-blue-500 bg-blue-50 @else border-gray-200 bg-gray-50 @endif rounded-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-2xl">{{ $attachment->file_icon }}</span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $attachment->file_name }}
                                            @if($attachment->is_primary)
                                                <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded">Primary</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            {{ $attachment->attachment_type_label }}
                                            @if(!$attachment->isLink())
                                                • {{ $attachment->file_size_formatted }}
                                            @endif
                                            • ⬇️ {{ $attachment->download_count }} downloads
                                        </p>
                                    </div>
                                </div>
                                
                                @if($attachment->description)
                                    <p class="text-xs text-gray-700 mt-1 ml-10">{{ $attachment->description }}</p>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2 ml-4">
                                <!-- Preview Button -->
                                @if($attachment->isLink() || in_array(strtolower($attachment->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                                    <button wire:click="previewAttachment({{ $attachment->id }})"
                                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition">
                                        👁️ Preview
                                    </button>
                                @endif
                                
                                <!-- Download/Open Link Button -->
                                @if($attachment->isLink())
                                    <a href="{{ route('teaching-materials.attachment.download', [$material->id, $attachment->id]) }}" 
                                       target="_blank"
                                       class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition">
                                        🔗 Buka Link
                                    </a>
                                @else
                                    <a href="{{ route('teaching-materials.attachment.download', [$material->id, $attachment->id]) }}"
                                       class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition">
                                        ⬇️ Download
                                    </a>
                                @endif

                                @if($this->canManageAttachments())
                                    <button wire:click="deleteAttachment({{ $attachment->id }})"
                                            wire:confirm="Hapus lampiran '{{ $attachment->file_name }}'?"
                                            class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition">
                                        🗑️ Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-sm">Belum ada lampiran</p>
                @if($this->canManageAttachments())
                    <p class="text-xs mt-1">Klik "Tambah Lampiran" untuk menambahkan file pendukung</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal Add Attachment -->
    @if($showAttachmentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">➕ Tambah Lampiran</h3>
                        <button wire:click="closeAttachmentModal" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveAttachment">
                        <!-- Attachment Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Lampiran <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="attachmentType" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="main">📄 Dokumen Utama</option>
                                <option value="lkpd">📝 LKPD (Lembar Kerja)</option>
                                <option value="presentation">📊 Presentasi/Slide</option>
                                <option value="video">🎬 Video Pembelajaran</option>
                                <option value="assessment">📋 Instrumen Asesmen</option>
                                <option value="rubric">📏 Rubrik Penilaian</option>
                                <option value="answer_key">🔑 Kunci Jawaban</option>
                                <option value="reading_material">📚 Bahan Bacaan</option>
                                <option value="other">📎 Lainnya</option>
                            </select>
                            @error('attachmentType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Upload Type Toggle -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Upload</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="uploadType" value="file" class="mr-2">
                                    <span class="text-sm">📁 Upload File</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="uploadType" value="link" class="mr-2">
                                    <span class="text-sm">🔗 Link Eksternal</span>
                                </label>
                            </div>
                        </div>

                        <!-- File Upload -->
                        @if($uploadType === 'file')
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    File <span class="text-red-500">*</span>
                                </label>
                                <input type="file" wire:model="attachmentFile" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <p class="text-xs text-gray-500 mt-1">Max 100MB. Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, MP4, ZIP</p>
                                @error('attachmentFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                
                                <div wire:loading wire:target="attachmentFile" class="text-sm text-blue-600 mt-2">
                                    Uploading...
                                </div>
                            </div>
                        @else
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Link Eksternal <span class="text-red-500">*</span>
                                </label>
                                <input type="url" wire:model="attachmentLink" 
                                       placeholder="https://example.com/file.pdf"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <p class="text-xs text-gray-500 mt-1">YouTube, Google Drive, dll</p>
                                @error('attachmentLink') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                            <textarea wire:model="attachmentDescription" rows="2"
                                      placeholder="Keterangan tambahan tentang lampiran ini..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                            @error('attachmentDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Is Primary -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isPrimary" class="mr-2">
                                <span class="text-sm text-gray-700">Tandai sebagai lampiran utama</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeAttachmentModal"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                💾 Simpan Lampiran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Comments -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">💬 KOMENTAR ({{ $material->comments->count() }})</h2>
        
        <!-- Comment List -->
        @if($material->comments->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($material->comments as $comment)
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-semibold text-sm text-gray-800">{{ $comment->user->name }}</span>
                            <span class="text-xs text-gray-500">• {{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ $comment->comment }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 mb-6">Belum ada komentar.</p>
        @endif

        <!-- Add Comment Form -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Komentar:</label>
            <textarea 
                wire:model="newComment"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Tulis komentar Anda..."
            ></textarea>
            @error('newComment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            
            <button 
                wire:click="addComment"
                class="mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                💬 Kirim Komentar
            </button>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($material->created_by === auth()->id() || auth()->user()->canManageUsers())
        <div class="flex items-center space-x-3">
            @if($material->status === 'draft')
                <a href="{{ route('teaching-materials.edit', $material->id) }}"
                   class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                    ✏️ Edit
                </a>
            @endif
        </div>
    @endif

    <!-- Preview Modal -->
    @if($showPreviewModal)
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" wire:click="closePreviewModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl h-[90vh] mx-4 flex flex-col" wire:click.stop>
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800 truncate">👁️ {{ $previewTitle }}</h3>
                    <button wire:click="closePreviewModal" class="text-gray-500 hover:text-gray-700 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-hidden p-4">
                    @if($previewType === 'pdf')
                        <iframe src="{{ $previewUrl }}" class="w-full h-full border rounded"></iframe>
                    
                    @elseif($previewType === 'image')
                        <div class="h-full flex items-center justify-center bg-gray-100 rounded">
                            <img src="{{ $previewUrl }}" alt="{{ $previewTitle }}" class="max-w-full max-h-full object-contain">
                        </div>
                    
                    @elseif($previewType === 'video')
                        <div class="h-full flex items-center justify-center bg-black rounded">
                            <video controls class="max-w-full max-h-full">
                                <source src="{{ $previewUrl }}" type="video/{{ $previewFileType }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    
                    @elseif($previewType === 'link')
                        <div class="h-full flex flex-col items-center justify-center bg-gray-100 rounded">
                            <svg class="w-20 h-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <p class="text-lg font-semibold text-gray-700 mb-2">Link Eksternal</p>
                            <a href="{{ $previewUrl }}" target="_blank" 
                               class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Buka di Tab Baru
                            </a>
                            <p class="text-sm text-gray-600 mt-4 max-w-lg break-all">{{ $previewUrl }}</p>
                        </div>
                    
                    @else
                        <div class="h-full flex flex-col items-center justify-center bg-gray-100 rounded p-6">
                            <svg class="w-20 h-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-semibold text-gray-700 mb-2">📄 Preview Tidak Tersedia</p>
                            <p class="text-sm text-gray-600 mb-2">File Office (Word, PowerPoint, Excel) tidak dapat di-preview langsung di browser.</p>
                            <p class="text-sm font-medium text-gray-700 mb-4">Silakan download file untuk melihat isinya.</p>
                            
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg max-w-md">
                                <p class="text-xs text-blue-800">
                                    <strong>💡 Kenapa tidak bisa preview?</strong><br>
                                    Untuk keamanan data, file Office memerlukan aplikasi khusus (Microsoft Office, LibreOffice, atau Google Docs) untuk dibuka dengan aman.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Auto-scroll script for attachment hint -->
    @if(session('show_attachment_hint'))
        <script>
            setTimeout(() => {
                const section = document.getElementById('attachments-section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 500);
        </script>
    @endif
</div>
@if(session('show_attachment_hint'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const attachmentsSection = document.getElementById('attachments-section');
            if (attachmentsSection) {
                attachmentsSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }, 500);
    });
</script>
@endif

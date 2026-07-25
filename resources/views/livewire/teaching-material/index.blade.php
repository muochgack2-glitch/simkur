<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📚 Perangkat Ajar</h1>
            <p class="text-gray-600 mt-1">SIMKUR SMK PGRI Blora - Sistem Informasi Manajemen Kurikulum</p>
        </div>
        
        <div class="flex items-center space-x-3">
            @if(count($selectedMaterials) > 0)
                <span class="text-sm font-semibold text-gray-700">
                    {{ count($selectedMaterials) }} terpilih
                </span>
                <button 
                    wire:click="openBulkDeleteModal"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Hapus Semua</span>
                </button>
            @endif
            
            <a href="{{ route('teaching-materials.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Upload Perangkat Ajar</span>
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <!-- Search Bar -->
        <div class="mb-4">
            <input 
                type="text" 
                wire:model.live="search"
                placeholder="🔍 Cari judul, deskripsi, atau tags..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>

        <!-- Filters Row 1 -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3">
            <select wire:model.live="filterCategory" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="all">Semua Kategori</option>
                
                <optgroup label="📂 Perencanaan Pembelajaran (7)">
                    <option value="cp">CP</option>
                    <option value="atp">ATP</option>
                    <option value="kktp">KKTP</option>
                    <option value="prota">PROTA</option>
                    <option value="prosem">PROSEM</option>
                    <option value="modul_ajar">Modul Ajar</option>
                    <option value="modul_projek">Modul Projek</option>
                </optgroup>
                
                <optgroup label="📚 Media & Bahan Ajar (4)">
                    <option value="buku_teks">Buku Teks</option>
                    <option value="video_pembelajaran">Video Pembelajaran</option>
                    <option value="presentasi_infografis">Presentasi/Infografis</option>
                    <option value="bahan_bacaan">Bahan Bacaan</option>
                </optgroup>
                
                <optgroup label="📝 Asesmen (4)">
                    <option value="bank_soal">Bank Soal</option>
                    <option value="rubrik_penilaian_umum">Rubrik Penilaian</option>
                    <option value="asesmen_diagnostik">Asesmen Diagnostik</option>
                    <option value="instrumen_uji_kompetensi">Instrumen Uji Kompetensi</option>
                </optgroup>
                
                <optgroup label="🔄 Remedial & Pengayaan (2)">
                    <option value="program_remedial">Program Remedial</option>
                    <option value="program_pengayaan">Program Pengayaan</option>
                </optgroup>
                
                <optgroup label="🏭 Kokurikuler SMK (3)">
                    <option value="job_sheet">Job Sheet</option>
                    <option value="teaching_factory">Teaching Factory</option>
                    <option value="pkl">PKL</option>
                </optgroup>
            </select>

            <select wire:model.live="filterSubject" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="all">Semua Mata Pelajaran</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterGrade" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="all">Semua Kelas</option>
                <option value="X">Kelas X</option>
                <option value="XI">Kelas XI</option>
                <option value="XII">Kelas XII</option>
            </select>

            <select wire:model.live="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="all">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="pending_approval">Pending Approval</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <select wire:model.live="filterAcademicYear" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="all">Semua Tahun Ajaran</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                @endforeach
            </select>
        </div>

        <!-- 8 Dimensi Filters -->
        <div class="border-t pt-3">
            <p class="text-sm font-semibold text-gray-700 mb-2">🎯 Filter berdasarkan 8 Dimensi Profil Lulusan:</p>
            <div class="flex flex-wrap gap-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension1" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Beriman</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension2" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Kebinekaan</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension3" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Gotong Royong</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension4" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Mandiri</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension5" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Bernalar Kritis</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension6" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Kreatif</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension7" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Numerasi</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="filterDimension8" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Literasi</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Materials List (Grouped by Category) -->
    <div class="space-y-6">
        <!-- Select All Header -->
        @if($materials->count() > 0)
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectAll"
                            class="form-checkbox h-5 w-5 text-blue-600 rounded">
                        <span class="ml-3 text-sm font-semibold text-blue-800">
                            Pilih Semua Draft (Untuk bulk delete)
                        </span>
                    </label>
                    <p class="text-xs text-blue-600">
                        ℹ️ Hanya perangkat ajar berstatus <strong>Draft</strong> yang dapat dihapus
                    </p>
                </div>
            </div>
        @endif

        @forelse($groupedMaterials as $category => $categoryMaterials)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        📂 {{ $categoryMaterials->first()->category_label }} ({{ $categoryMaterials->count() }} dokumen)
                    </h2>
                </div>
                
                <div class="p-6 space-y-4">
                    @foreach($categoryMaterials as $material)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start space-x-3">
                                <!-- Checkbox (only for draft items) -->
                                @if($material->status === 'draft' && ($material->created_by === auth()->id() || auth()->user()->canManageUsers()))
                                    <div class="pt-1">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="selectedMaterials"
                                            value="{{ $material->id }}"
                                            class="form-checkbox h-5 w-5 text-blue-600 rounded">
                                    </div>
                                @else
                                    <div class="w-5"></div>
                                @endif

                                <div class="flex-1">
                                    <!-- Title & Status -->
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                {{ $material->title }}
                                            </h3>
                                            
                                            {{-- Version Badge --}}
                                            @if($material->version_number > 1 || $material->hasRevisions())
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800">
                                                    {{ $material->version_label }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($material->status === 'approved') bg-green-100 text-green-800
                                            @elseif($material->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                            @elseif($material->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $material->status_label }}
                                        </span>
                                    </div>

                                    <!-- Metadata -->
                                    <div class="text-sm text-gray-600 mb-2">
                                        @if($material->subject)
                                            <span>{{ $material->subject->name }}</span>
                                        @endif
                                        @if($material->grade)
                                            • <span>Kelas {{ $material->grade }}</span>
                                        @endif
                                        @if($material->phase)
                                            • <span>Fase {{ $material->phase }}</span>
                                        @endif
                                        @if($material->semester)
                                            • <span>Semester {{ $material->semester }}</span>
                                        @endif
                                    </div>

                                    <div class="text-sm text-gray-600 mb-3">
                                        👤 {{ $material->creator->name }} • 
                                        📅 {{ $material->created_at->format('d M Y') }}
                                        @if($material->file_type === 'link')
                                            • 🔗 Link Eksternal
                                        @else
                                            • 📎 {{ strtoupper($material->file_type) }} ({{ $material->file_size_formatted }})
                                        @endif
                                    </div>

                                    <!-- Tags -->
                                    @if($material->tags)
                                        <div class="flex flex-wrap gap-1 mb-3">
                                            @foreach($material->tags as $tag)
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                    🏷️ {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Dimensions -->
                                    @if(count($material->selected_dimensions) > 0)
                                        <div class="flex flex-wrap gap-1 mb-3">
                                            <span class="text-xs text-gray-600">🎯 Dimensi:</span>
                                            @foreach($material->selected_dimensions as $dimension)
                                                <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                                                    {{ $dimension }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Stats -->
                                    <div class="text-xs text-gray-500 mb-3">
                                        👁️ {{ $material->view_count }} views • 
                                        ⬇️ {{ $material->download_count }} downloads
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('teaching-materials.show', $material->id) }}" 
                                           class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                            👁️ Lihat
                                        </a>

                                        {{-- Version History Button --}}
                                        @if($material->version_number > 1 || $material->hasRevisions())
                                            <a href="{{ route('teaching-materials.versions', $material->id) }}" 
                                               class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded transition">
                                                📜 History
                                            </a>
                                        @endif
                                        
                                        @if($material->created_by === auth()->id() || auth()->user()->canManageUsers())
                                            {{-- Draft & Rejected: Direct Edit --}}
                                            @if($material->canBeEdited())
                                                <a href="{{ route('teaching-materials.edit', $material->id) }}" 
                                                   class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded transition">
                                                    ✏️ Edit
                                                </a>
                                            @endif

                                            {{-- Approved: Create Revision --}}
                                            @if($material->canCreateRevision())
                                                <button 
                                                    wire:click="openRevisionModal({{ $material->id }})"
                                                    class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded transition">
                                                    📝 Buat Revisi
                                                </button>
                                            @endif

                                            {{-- Pending: Withdraw --}}
                                            @if($material->canBeWithdrawn())
                                                <button 
                                                    wire:click="withdrawMaterial({{ $material->id }})"
                                                    wire:confirm="Tarik material dari approval untuk diedit kembali?"
                                                    class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded transition">
                                                    ↩️ Tarik
                                                </button>
                                            @endif

                                            {{-- Draft Only: Delete --}}
                                            @if($material->status === 'draft')
                                                <button 
                                                    wire:click="delete({{ $material->id }})"
                                                    wire:confirm="Hapus perangkat ajar '{{ $material->title }}'?"
                                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                                    🗑️ Hapus
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600">Tidak ada perangkat ajar yang ditemukan</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($materials->hasPages())
        <div class="mt-6">
            {{ $materials->links() }}
        </div>
    @endif

    <!-- Bulk Delete Modal -->
    @if($showBulkDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    🗑️ Hapus Multiple Perangkat Ajar
                </h3>
                
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm font-semibold text-red-800">
                        ⚠️ Anda akan menghapus <strong>{{ count($selectedMaterials) }} perangkat ajar</strong>
                    </p>
                    <p class="text-xs text-red-600 mt-2">
                        File dan lampiran akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    Hanya perangkat ajar berstatus <strong>Draft</strong> yang akan dihapus. Apakah Anda yakin?
                </p>

                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="closeBulkDeleteModal"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        Batal
                    </button>
                    <button 
                        wire:click="bulkDelete"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        🗑️ Ya, Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Revision Notes Modal -->
    @if($showRevisionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    📝 Buat Revisi Baru
                </h3>
                
                <div class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <p class="text-sm text-purple-800">
                        Material asli akan tetap tersimpan sebagai Approved. Revisi baru akan dibuat sebagai Draft untuk Anda edit.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Revisi <span class="text-gray-500 text-xs">(Opsional)</span>
                    </label>
                    <textarea 
                        wire:model="revisionNotes"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        placeholder="Contoh: Update materi terbaru untuk semester 2, Perbaikan typo, Tambah lampiran video..."
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Catatan ini akan membantu Anda mengingat apa yang diubah di versi ini.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="closeRevisionModal"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        Batal
                    </button>
                    <button 
                        wire:click="createRevision"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        📝 Buat Revisi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

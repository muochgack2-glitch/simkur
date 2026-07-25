<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">⏳ Approval Perangkat Ajar</h1>
        <p class="text-gray-600 mt-1">SIMKUR SMK PGRI Blora - Kelola Approval Perangkat Ajar</p>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col space-y-3">
            <!-- Search Bar -->
            <div class="flex-1">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="🔍 Cari judul atau deskripsi..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
            
            <!-- Filters -->
            <div class="flex gap-3">
                <select wire:model.live="filterCategory" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="all">Semua Kategori</option>
                    
                    <optgroup label="📂 Perencanaan (7)">
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
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">
                        {{ $materials->total() }} Perangkat Ajar Menunggu Approval
                    </p>
                    <p class="text-xs text-yellow-600">Diurutkan dari yang paling lama menunggu</p>
                </div>
            </div>

            <!-- Bulk Actions Buttons -->
            @if(count($selectedMaterials) > 0)
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold text-yellow-800">
                        {{ count($selectedMaterials) }} terpilih
                    </span>
                    <button 
                        wire:click="openBulkModal('approve')"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                        ✅ Setujui Semua
                    </button>
                    <button 
                        wire:click="openBulkModal('reject')"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                        ❌ Tolak Semua
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Materials List -->
    <div class="space-y-4">
        <!-- Select All Header -->
        @if($materials->count() > 0)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <label class="inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectAll"
                        class="form-checkbox h-5 w-5 text-blue-600 rounded">
                    <span class="ml-3 text-sm font-semibold text-gray-700">
                        Pilih Semua ({{ $materials->count() }} item di halaman ini)
                    </span>
                </label>
            </div>
        @endif

        @forelse($materials as $material)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <!-- Checkbox + Header -->
                <div class="flex items-start space-x-4 mb-4">
                    <!-- Checkbox -->
                    <div class="pt-1">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedMaterials"
                            value="{{ $material->id }}"
                            class="form-checkbox h-5 w-5 text-blue-600 rounded">
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $material->title }}
                                    </h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ Pending
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-2">
                                    📂 {{ $material->category_label }}
                                    @if($material->subject)
                                        • {{ $material->subject->name }}
                                    @endif
                                    @if($material->grade)
                                        • Kelas {{ $material->grade }}
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-600">
                                    👤 Dibuat oleh: <strong>{{ $material->creator->name }}</strong> • 
                                    📅 {{ $material->created_at->format('d M Y H:i') }} 
                                    ({{ $material->created_at->diffForHumans() }})
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($material->description)
                            <div class="mb-4 p-3 bg-gray-50 rounded border border-gray-200">
                                <p class="text-sm text-gray-700">{{ $material->description }}</p>
                            </div>
                        @endif

                        <!-- Metadata -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2 mb-2">
                                @if($material->file_type === 'link')
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                        🔗 Link Eksternal
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                        📎 {{ strtoupper($material->file_type) }} ({{ $material->file_size_formatted }})
                                    </span>
                                @endif
                                
                                @if($material->is_public)
                                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                                        🌐 Public
                                    </span>
                                @endif
                            </div>

                            <!-- Dimensi -->
                            @if(count($material->selected_dimensions) > 0)
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-xs text-gray-600">🎯 Dimensi:</span>
                                    @foreach($material->selected_dimensions as $dimension)
                                        <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded">
                                            {{ $dimension }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('teaching-materials.show', $material->id) }}" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition"
                               target="_blank">
                                👁️ Lihat Detail
                            </a>
                            
                            <button 
                                wire:click="openApprovalModal({{ $material->id }}, 'approve')"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                                ✅ Setujui
                            </button>
                            
                            <button 
                                wire:click="openApprovalModal({{ $material->id }}, 'reject')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                                ❌ Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 text-lg font-semibold">Tidak Ada Perangkat Ajar yang Menunggu Approval</p>
                <p class="text-gray-500 text-sm mt-2">Semua submission sudah diproses</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($materials->hasPages())
        <div class="mt-6">
            {{ $materials->links() }}
        </div>
    @endif

    <!-- Approval Modal -->
    @if($showApprovalModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    @if($approvalAction === 'approve')
                        ✅ Setujui Perangkat Ajar
                    @else
                        ❌ Tolak Perangkat Ajar
                    @endif
                </h3>
                
                <p class="text-sm text-gray-600 mb-4">
                    @if($approvalAction === 'approve')
                        Apakah Anda yakin ingin menyetujui perangkat ajar ini? Setelah disetujui, perangkat ajar akan dapat diakses oleh guru lain.
                    @else
                        Berikan catatan revisi kepada guru pembuat. Catatan ini akan membantu guru untuk memperbaiki perangkat ajar.
                    @endif
                </p>

                @if($approvalAction === 'reject')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Revisi <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            wire:model="approvalNotes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                            placeholder="Contoh: Mohon dilengkapi dengan rubrik penilaian yang lebih detail..."
                        ></textarea>
                        @error('approvalNotes') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="closeApprovalModal"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        Batal
                    </button>
                    <button 
                        wire:click="submitApproval"
                        class="px-4 py-2 {{ $approvalAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg transition">
                        @if($approvalAction === 'approve')
                            ✅ Ya, Setujui
                        @else
                            ❌ Ya, Tolak
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Modal -->
    @if($showBulkModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    @if($bulkAction === 'approve')
                        ✅ Setujui Multiple Perangkat Ajar
                    @else
                        ❌ Tolak Multiple Perangkat Ajar
                    @endif
                </h3>
                
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm font-semibold text-yellow-800">
                        Anda akan {{ $bulkAction === 'approve' ? 'menyetujui' : 'menolak' }} 
                        <strong>{{ count($selectedMaterials) }} perangkat ajar</strong>
                    </p>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    @if($bulkAction === 'approve')
                        Setelah disetujui, semua perangkat ajar akan dapat diakses oleh guru lain. Apakah Anda yakin?
                    @else
                        Berikan catatan revisi kepada guru pembuat. Catatan yang sama akan dikirim ke semua guru.
                    @endif
                </p>

                @if($bulkAction === 'reject')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Revisi <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            wire:model="bulkNotes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                            placeholder="Contoh: Mohon dilengkapi dengan rubrik penilaian yang lebih detail..."
                        ></textarea>
                        @error('bulkNotes') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="closeBulkModal"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        Batal
                    </button>
                    <button 
                        wire:click="submitBulkOperation"
                        class="px-4 py-2 {{ $bulkAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg transition">
                        @if($bulkAction === 'approve')
                            ✅ Ya, Setujui Semua
                        @else
                            ❌ Ya, Tolak Semua
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

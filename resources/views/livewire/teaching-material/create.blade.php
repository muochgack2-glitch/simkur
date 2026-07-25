<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">⬆️ Upload Perangkat Ajar</h1>
        <p class="text-gray-600 mt-1">SIMKUR SMK PGRI Blora</p>
    </div>

    <form wire:submit.prevent="submitForApproval">
        <!-- Informasi Dasar -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 INFORMASI DASAR</h2>
            
            <div class="space-y-4">
                <!-- Judul -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="title"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Modul Ajar: Sistem Persamaan Linear Pertemuan 1"
                    >
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Pilih Kategori --</option>
                        
                        <optgroup label="📂 Perencanaan Pembelajaran (7)">
                            <option value="cp">CP (Capaian Pembelajaran)</option>
                            <option value="atp">ATP (Alur Tujuan Pembelajaran)</option>
                            <option value="kktp">KKTP (Kriteria Ketercapaian Tujuan Pembelajaran)</option>
                            <option value="prota">PROTA (Program Tahunan)</option>
                            <option value="prosem">PROSEM (Program Semester)</option>
                            <option value="modul_ajar">Modul Ajar ⭐ (Lengkap: LKPD, Asesmen, Rubrik)</option>
                            <option value="modul_projek">Modul Projek</option>
                        </optgroup>
                        
                        <optgroup label="📚 Media & Bahan Ajar (4)">
                            <option value="buku_teks">Buku Teks / E-Book</option>
                            <option value="video_pembelajaran">Video Pembelajaran</option>
                            <option value="presentasi_infografis">Presentasi / Infografis</option>
                            <option value="bahan_bacaan">Bahan Bacaan / Artikel</option>
                        </optgroup>
                        
                        <optgroup label="📝 Asesmen (4)">
                            <option value="bank_soal">Bank Soal / Paket Soal</option>
                            <option value="rubrik_penilaian_umum">Rubrik Penilaian Umum</option>
                            <option value="asesmen_diagnostik">Asesmen Diagnostik</option>
                            <option value="instrumen_uji_kompetensi">Instrumen Uji Kompetensi</option>
                        </optgroup>
                        
                        <optgroup label="🔄 Remedial & Pengayaan (2)">
                            <option value="program_remedial">Program Remedial</option>
                            <option value="program_pengayaan">Program Pengayaan</option>
                        </optgroup>
                        
                        <optgroup label="🏭 Kokurikuler SMK (3)">
                            <option value="job_sheet">Job Sheet / Panduan Praktikum</option>
                            <option value="teaching_factory">Teaching Factory</option>
                            <option value="pkl">PKL (Praktik Kerja Lapangan)</option>
                        </optgroup>
                    </select>
                    @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea 
                        wire:model="description"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Deskripsi singkat tentang perangkat ajar ini..."
                    ></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Relasi Kurikulum -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🎓 RELASI KURIKULUM</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <select wire:model="subject_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Pilih --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Tingkat Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kelas</label>
                    <select wire:model="grade" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Pilih --</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                    @error('grade') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Fase -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fase</label>
                    <select wire:model="phase" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Pilih --</option>
                        <option value="E">Fase E (Kelas X)</option>
                        <option value="F">Fase F (Kelas XI-XII)</option>
                    </select>
                    @error('phase') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Semester -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select wire:model="semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Pilih --</option>
                        <option value="1">Semester 1 (Ganjil)</option>
                        <option value="2">Semester 2 (Genap)</option>
                    </select>
                    @error('semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Tahun Ajaran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="academic_year_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Pilih --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                    @error('academic_year_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- File / Link -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📎 FILE / LINK</h2>
            
            <!-- Upload Type Selection -->
            <div class="mb-4">
                <label class="inline-flex items-center mr-6">
                    <input type="radio" wire:model.live="uploadType" value="file" class="form-radio text-blue-600">
                    <span class="ml-2">Upload File</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" wire:model.live="uploadType" value="link" class="form-radio text-blue-600">
                    <span class="ml-2">Link Eksternal</span>
                </label>
            </div>

            @if($uploadType === 'file')
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="file" 
                        wire:model="file"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, MP4, ZIP (Max: 100MB)
                    </p>
                    @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    
                    @if($category === 'modul_ajar')
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded">
                            <p class="text-sm text-blue-800">
                                ℹ️ <strong>Tips:</strong> Untuk Modul Ajar, pastikan sudah include LKPD, Instrumen Asesmen, dan Rubrik di dalam 1 file PDF.
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <!-- External Link -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Link Eksternal <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="url" 
                        wire:model="external_link"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        placeholder="https://youtube.com/... atau https://drive.google.com/..."
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Contoh: YouTube, Google Drive, Google Docs, Google Slides, dll.
                    </p>
                    @error('external_link') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif
        </div>

        <!-- 8 Dimensi Profil Lulusan -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">🎯 DIMENSI PROFIL LULUSAN (8 Dimensi)</h2>
            <p class="text-sm text-gray-600 mb-4">Pilih dimensi yang dikembangkan dalam perangkat ajar ini:</p>
            
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_1_beriman" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Beriman, Bertakwa kepada Tuhan YME, Berakhlak Mulia</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_2_kebinekaan" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Berkebinekaan Global</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_3_gotong_royong" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Bergotong Royong</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_4_mandiri" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Mandiri</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_5_bernalar_kritis" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Bernalar Kritis</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_6_kreatif" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Kreatif</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_7_numerasi" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Literasi Numerasi</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="dimension_8_literasi" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm">Literasi (Baca-Tulis)</span>
                </label>
            </div>
        </div>

        <!-- Akses & Tags -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🔒 AKSES & TAGS</h2>
            
            <div class="space-y-4">
                <!-- Visibilitas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visibilitas</label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_public" class="form-checkbox text-blue-600">
                        <span class="ml-2 text-sm">Public (Semua guru bisa lihat setelah approved)</span>
                    </label>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tags (pisahkan dengan koma)</label>
                    <input 
                        type="text" 
                        wire:model="tags"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        placeholder="Contoh: kewirausahaan, digital, diferensiasi"
                    >
                    <p class="text-xs text-gray-500 mt-1">Tags memudahkan pencarian perangkat ajar</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            <button 
                type="button"
                wire:click="saveDraft"
                class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                💾 Simpan sebagai Draft
            </button>
            
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                📤 Submit untuk Approval
            </button>
            
            <a 
                href="{{ route('teaching-materials.index') }}"
                class="px-6 py-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border border-gray-300 transition">
                Batal
            </a>
        </div>
    </form>
</div>

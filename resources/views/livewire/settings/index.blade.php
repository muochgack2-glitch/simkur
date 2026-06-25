<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
        <p class="text-gray-600 mt-1">Kelola pengaturan aplikasi e-KALDIK</p>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button 
                    wire:click="$set('activeTab', 'school')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'school' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Sekolah
                    </span>
                </button>
                
                <button 
                    wire:click="$set('activeTab', 'calendar')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'calendar' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Kalender
                    </span>
                </button>
                
                <button 
                    wire:click="$set('activeTab', 'system')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'system' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Sistem
                    </span>
                </button>
                
                <button 
                    wire:click="$set('activeTab', 'import')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'import' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import
                    </span>
                </button>
                
                <button 
                    wire:click="$set('activeTab', 'export')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'export' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Export
                    </span>
                </button>
                
                <button 
                    wire:click="$set('activeTab', 'signature')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'signature' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Tanda Tangan
                    </span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow">
        <form wire:submit="save" class="p-6 space-y-6">
            
            <!-- School Settings -->
            @if($activeTab === 'school')
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sekolah</h3>
                        
                        <div class="space-y-4">
                            <!-- School Name -->
                            <div>
                                <label for="school_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="school_name"
                                    wire:model="school_name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('school_name') border-red-300 @enderror"
                                >
                                @error('school_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- School Address -->
                            <div>
                                <label for="school_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Sekolah <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="school_address"
                                    wire:model="school_address"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('school_address') border-red-300 @enderror"
                                ></textarea>
                                @error('school_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- School Phone & Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="school_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="school_phone"
                                        wire:model="school_phone"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('school_phone') border-red-300 @enderror"
                                    >
                                    @error('school_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        id="school_email"
                                        wire:model="school_email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('school_email') border-red-300 @enderror"
                                    >
                                    @error('school_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- School Logo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Logo Sekolah
                                </label>
                                
                                <!-- Current Logo Preview -->
                                @if($school_logo)
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-600 mb-2">Logo saat ini:</p>
                                        <div class="flex items-center gap-4">
                                            <img src="{{ asset($school_logo) }}" alt="Logo" class="w-20 h-20 object-contain border border-gray-300 rounded">
                                            <div class="text-sm text-gray-600">
                                                <p class="font-mono text-xs">{{ $school_logo }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Upload New Logo -->
                                <div class="flex items-center justify-center w-full">
                                    <label for="logo_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 @error('logo_file') border-red-300 @enderror">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            @if($logo_file)
                                                <svg class="w-10 h-10 mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="mb-1 text-sm text-gray-700"><span class="font-semibold">File dipilih:</span> {{ $logo_file->getClientOriginalName() }}</p>
                                                <p class="text-xs text-gray-500">Klik untuk mengganti file</p>
                                            @else
                                                <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <p class="mb-1 text-sm text-gray-700"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                                <p class="text-xs text-gray-500">PNG, JPG atau JPEG (Max 2MB)</p>
                                            @endif
                                        </div>
                                        <input 
                                            id="logo_file" 
                                            type="file" 
                                            wire:model="logo_file"
                                            accept="image/*"
                                            class="hidden"
                                        />
                                    </label>
                                </div>
                                
                                @error('logo_file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <div wire:loading wire:target="logo_file" class="mt-2 text-sm text-blue-600 flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mengupload...
                                </div>
                                
                                <p class="mt-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Logo akan digunakan di kalender resmi dan dokumen export
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Principal Information -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Kepala Sekolah</h3>
                        
                        <div class="space-y-4">
                            <!-- Principal Name -->
                            <div>
                                <label for="principal_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Kepala Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="principal_name"
                                    wire:model="principal_name"
                                    placeholder="Drs. H. Ahmad Suryadi, M.Pd"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('principal_name') border-red-300 @enderror"
                                >
                                @error('principal_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Akan ditampilkan di kalender resmi dan dokumen</p>
                            </div>

                            <!-- Principal NIY -->
                            <div>
                                <label for="principal_niy" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIY Kepala Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="principal_niy"
                                    wire:model="principal_niy"
                                    placeholder="123456789"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('principal_niy') border-red-300 @enderror"
                                >
                                @error('principal_niy')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Nomor Induk Yayasan</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Calendar Settings -->
            @if($activeTab === 'calendar')
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Kalender</h3>
                        
                        <div class="space-y-4">
                            <!-- Weekend Days -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Hari Libur Akhir Pekan
                                </label>
                                <div class="space-y-2">
                                    @foreach(['saturday' => 'Sabtu', 'sunday' => 'Minggu'] as $day => $label)
                                        <label class="inline-flex items-center mr-6">
                                            <input 
                                                type="checkbox" 
                                                wire:model="weekend_days"
                                                value="{{ $day }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Hari yang ditandai akan dihitung sebagai hari libur</p>
                            </div>

                            <!-- Default Calendar View -->
                            <div>
                                <label for="default_calendar_view" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tampilan Kalender Default <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="default_calendar_view"
                                    wire:model="default_calendar_view"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="month">Bulan (Month)</option>
                                    <option value="year">Tahun (Year)</option>
                                    <option value="list">Daftar (List)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- System Settings -->
            @if($activeTab === 'system')
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Sistem</h3>
                        
                        <div class="space-y-4">
                            <!-- Session Timeout -->
                            <div>
                                <label for="session_timeout" class="block text-sm font-medium text-gray-700 mb-2">
                                    Session Timeout (menit) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="session_timeout"
                                    wire:model="session_timeout"
                                    min="5"
                                    max="480"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('session_timeout') border-red-300 @enderror"
                                >
                                @error('session_timeout')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Durasi sesi user sebelum auto logout (5-480 menit)</p>
                            </div>

                            <!-- Items Per Page -->
                            <div>
                                <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                    Item Per Halaman <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="items_per_page"
                                    wire:model="items_per_page"
                                    min="5"
                                    max="100"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('items_per_page') border-red-300 @enderror"
                                >
                                @error('items_per_page')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Jumlah data per halaman di tabel (5-100)</p>
                            </div>

                            <!-- Date Format -->
                            <div>
                                <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                                    Format Tanggal <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="date_format"
                                    wire:model="date_format"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="d/m/Y">DD/MM/YYYY (25/06/2026)</option>
                                    <option value="m/d/Y">MM/DD/YYYY (06/25/2026)</option>
                                    <option value="Y-m-d">YYYY-MM-DD (2026-06-25)</option>
                                    <option value="d-m-Y">DD-MM-YYYY (25-06-2026)</option>
                                </select>
                            </div>

                            <!-- Conflict Warning -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        type="checkbox" 
                                        id="enable_activity_conflict_warning"
                                        wire:model="enable_activity_conflict_warning"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                </div>
                                <div class="ml-3">
                                    <label for="enable_activity_conflict_warning" class="text-sm font-medium text-gray-700">
                                        Aktifkan Peringatan Bentrok Kegiatan
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Tampilkan peringatan saat membuat kegiatan yang bertabrakan tanggal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Import Settings -->
            @if($activeTab === 'import')
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Import</h3>
                        
                        <div class="space-y-4">
                            <!-- Max Import Rows -->
                            <div>
                                <label for="max_import_rows" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maksimal Baris Import <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="max_import_rows"
                                    wire:model="max_import_rows"
                                    min="100"
                                    max="10000"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_import_rows') border-red-300 @enderror"
                                >
                                @error('max_import_rows')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Maksimal baris data yang dapat diimport (100-10.000)</p>
                            </div>

                            <!-- Allowed Extensions -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ekstensi File yang Diizinkan
                                </label>
                                <div class="space-y-2">
                                    @foreach(['xlsx' => 'Excel (.xlsx)', 'xls' => 'Excel Legacy (.xls)', 'csv' => 'CSV (.csv)'] as $ext => $label)
                                        <label class="inline-flex items-center mr-6">
                                            <input 
                                                type="checkbox" 
                                                wire:model="allowed_import_extensions"
                                                value="{{ $ext }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Max File Size -->
                            <div>
                                <label for="max_import_file_size" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maksimal Ukuran File (KB) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="max_import_file_size"
                                    wire:model="max_import_file_size"
                                    min="512"
                                    max="10240"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_import_file_size') border-red-300 @enderror"
                                >
                                @error('max_import_file_size')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file import (512 KB - 10 MB)</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Export Settings -->
            @if($activeTab === 'export')
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Export</h3>
                        
                        <div class="space-y-4">
                            <!-- PDF Orientation -->
                            <div>
                                <label for="pdf_orientation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Orientasi PDF <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="pdf_orientation"
                                    wire:model="pdf_orientation"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="landscape">Landscape (Horizontal)</option>
                                    <option value="portrait">Portrait (Vertikal)</option>
                                </select>
                            </div>

                            <!-- PDF Paper Size -->
                            <div>
                                <label for="pdf_paper_size" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ukuran Kertas PDF <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="pdf_paper_size"
                                    wire:model="pdf_paper_size"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="a4">A4 (210 x 297 mm)</option>
                                    <option value="a3">A3 (297 x 420 mm)</option>
                                    <option value="letter">Letter (8.5 x 11 inch)</option>
                                    <option value="legal">Legal (8.5 x 14 inch)</option>
                                </select>
                            </div>

                            <!-- Include Logo -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        type="checkbox" 
                                        id="include_logo_in_export"
                                        wire:model="include_logo_in_export"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                </div>
                                <div class="ml-3">
                                    <label for="include_logo_in_export" class="text-sm font-medium text-gray-700">
                                        Sertakan Logo di Export
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Logo sekolah akan ditampilkan di header PDF dan Excel
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Signature Settings -->
            @if($activeTab === 'signature')
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Tanda Tangan Dokumen</h3>
                        <p class="text-sm text-gray-600 mb-6">Konfigurasi tanda tangan yang akan ditampilkan di dokumen PDF (Kalender, Laporan, dll)</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column: Form Inputs -->
                            <div class="space-y-4">
                                <!-- City/Place -->
                                <div>
                                    <label for="signature_city" class="block text-sm font-medium text-gray-700 mb-2">
                                        Kota/Tempat <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="signature_city"
                                        wire:model.live="signature_city"
                                        placeholder="Contoh: Blora"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('signature_city') border-red-300 @enderror"
                                    >
                                    @error('signature_city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date -->
                                <div>
                                    <label for="signature_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Tanda Tangan <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="signature_date"
                                        wire:model.live="signature_date"
                                        placeholder="Contoh: Juni 2027 atau 15 Juni 2027"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('signature_date') border-red-300 @enderror"
                                    >
                                    <p class="mt-1 text-xs text-gray-500">Format bebas: bisa bulan-tahun saja atau tanggal lengkap</p>
                                    @error('signature_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Position/Title -->
                                <div>
                                    <label for="signature_position" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jabatan Penandatangan <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="signature_position"
                                        wire:model.live="signature_position"
                                        placeholder="Contoh: Kepala Sekolah"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('signature_position') border-red-300 @enderror"
                                    >
                                    @error('signature_position')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div>
                                    <label for="signature_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap Penandatangan <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="signature_name"
                                        wire:model.live="signature_name"
                                        placeholder="Contoh: Meiranti Trisnaning Savitri"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('signature_name') border-red-300 @enderror"
                                    >
                                    @error('signature_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Degree (Optional) -->
                                <div>
                                    <label for="signature_degree" class="block text-sm font-medium text-gray-700 mb-2">
                                        Gelar Akademik (Opsional)
                                    </label>
                                    <input 
                                        type="text" 
                                        id="signature_degree"
                                        wire:model.live="signature_degree"
                                        placeholder="Contoh: S.Pd, M.Pd"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    <p class="mt-1 text-xs text-gray-500">Akan ditambahkan setelah nama</p>
                                </div>

                                <!-- NIY/NIP -->
                                <div>
                                    <label for="signature_niy" class="block text-sm font-medium text-gray-700 mb-2">
                                        NIY/NIP <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="signature_niy"
                                        wire:model.live="signature_niy"
                                        placeholder="Contoh: 112060"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('signature_niy') border-red-300 @enderror"
                                    >
                                    @error('signature_niy')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column: Preview -->
                            <div>
                                <div class="sticky top-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Preview Tanda Tangan</h4>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                                        <div class="text-right space-y-3">
                                            <p class="text-sm text-gray-700">
                                                {{ $signature_city ?: '(Kota)' }}, {{ $signature_date ?: '(Tanggal)' }}
                                            </p>
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $signature_position ?: '(Jabatan)' }}
                                            </p>
                                            <div class="h-16 border-b border-gray-300 my-4"></div>
                                            <p class="text-sm font-bold text-gray-900 underline">
                                                {{ $signature_name ?: '(Nama Lengkap)' }}{{ $signature_degree ? ', ' . $signature_degree : '' }}
                                            </p>
                                            <p class="text-sm text-gray-700">
                                                NIY. {{ $signature_niy ?: '(NIY/NIP)' }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-3">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Tanda tangan ini akan muncul di PDF Kalender, Laporan, dan dokumen lainnya
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @error('error')
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ $message }}
                </div>
            @enderror

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                @if(auth()->user()->isAdmin())
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2"
                    >
                        <span wire:loading.remove>
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Pengaturan
                        </span>
                        <span wire:loading class="flex items-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                @else
                    <p class="text-sm text-gray-500 italic">Hanya Admin yang dapat mengubah pengaturan</p>
                @endif
            </div>
        </form>
    </div>
</div>

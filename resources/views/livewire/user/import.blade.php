<div>
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Pengguna
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📥 Import Data Pengguna (Excel)</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Upload file Excel untuk import data guru atau siswa secara bulk</p>
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-4 rounded-lg bg-yellow-50 p-4 text-sm text-yellow-800 dark:bg-gray-800 dark:text-yellow-400" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-gray-800 dark:text-red-400" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="mb-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-gray-800 dark:text-blue-400" role="alert">
            {{ session('info') }}
        </div>
    @endif

    <!-- Step 1: Select Type & Download Template -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">📋 Langkah 1: Pilih Tipe & Download Template</h2>
        
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Select Type -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tipe Data yang Akan Di-import
                </label>
                <select wire:model.live="importType" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="guru">👨‍🏫 Data Guru</option>
                    <option value="siswa">👨‍🎓 Data Siswa</option>
                </select>
            </div>

            <!-- Download Template -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Download Template Excel
                </label>
                <button wire:click="downloadTemplate" 
                        class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center dark:bg-green-500 dark:hover:bg-green-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Template {{ ucfirst($importType) }}
                </button>
            </div>
        </div>

        <!-- Template Info -->
        <div class="mt-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
            <p class="text-sm text-blue-800 dark:text-blue-300">
                <strong>ℹ️ Info:</strong> Download template Excel, isi data sesuai format, lalu upload kembali di langkah 2.
            </p>
        </div>
    </div>

    <!-- Step 2: Upload File -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">📤 Langkah 2: Upload File Excel</h2>
        
        <div class="flex items-center justify-center w-full">
            <label for="file-upload" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Excel (.xlsx, .xls) maksimal 2MB</p>
                </div>
                <input id="file-upload" type="file" wire:model="file" accept=".xlsx,.xls" class="hidden" />
            </label>
        </div>

        @if($file)
            <div class="mt-4 flex items-center justify-between rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium text-green-800 dark:text-green-300">
                        File: {{ $file->getClientOriginalName() }}
                    </span>
                </div>
                <button wire:click="$set('file', null)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Step 3: Preview Data -->
    @if(!empty($preview))
        <div class="mb-6 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">👁️ Langkah 3: Preview Data (5 Baris Pertama)</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            @if($importType === 'guru')
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Username</th>
                                <th scope="col" class="px-6 py-3">NIP/NUPTK</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Beban Mengajar</th>
                                <th scope="col" class="px-6 py-3">Mata Pelajaran</th>
                                <th scope="col" class="px-6 py-3">Jurusan</th>
                            @else
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Username</th>
                                <th scope="col" class="px-6 py-3">NIS</th>
                                <th scope="col" class="px-6 py-3">NIS</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">No HP</th>
                                <th scope="col" class="px-6 py-3">Kelas</th>
                                <th scope="col" class="px-6 py-3">Jurusan</th>
                                <th scope="col" class="px-6 py-3">Nama Ortu</th>
                                <th scope="col" class="px-6 py-3">HP Ortu</th>
                                <th scope="col" class="px-6 py-3">PKL</th>
                                <th scope="col" class="px-6 py-3">TeFa</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview as $row)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                @foreach($row as $cell)
                                    <td class="px-6 py-4">{{ $cell ?: '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Import Button -->
            <div class="mt-6 flex justify-end">
                <button wire:click="import" 
                        wire:loading.attr="disabled"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-8 py-3 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-600">
                    <span wire:loading.remove>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Mulai Import
                    </span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    @endif

    <!-- Step 4: Results -->
    @if($successCount > 0 || $errorCount > 0)
        <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">📊 Hasil Import</h2>
            
            <div class="grid gap-4 md:grid-cols-2 mb-4">
                <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-green-800 dark:text-green-300">Berhasil</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-200">{{ $successCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-red-800 dark:text-red-300">Error</p>
                            <p class="text-2xl font-bold text-red-900 dark:text-red-200">{{ $errorCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Details -->
            @if(!empty($errors))
                <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                    <h3 class="mb-2 text-sm font-semibold text-red-800 dark:text-red-300">Detail Error:</h3>
                    <ul class="max-h-48 overflow-y-auto space-y-1">
                        @foreach($errors as $error)
                            <li class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>

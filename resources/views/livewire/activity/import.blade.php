<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Import Kegiatan</h1>
                <p class="mt-1 text-sm text-gray-600">Import kegiatan dari file Excel</p>
            </div>
            <button wire:click="backToActivities" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </span>
            </button>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <!-- Step 1 -->
            <div class="flex items-center flex-1">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    @if($step > 1)
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        1
                    @endif
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Upload File</p>
                    <p class="text-xs text-gray-500">Pilih file Excel</p>
                </div>
            </div>
            
            <div class="flex-1 h-1 mx-4 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            
            <!-- Step 2 -->
            <div class="flex items-center flex-1">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    @if($step > 2)
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        2
                    @endif
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Preview Data</p>
                    <p class="text-xs text-gray-500">Review sebelum import</p>
                </div>
            </div>
            
            <div class="flex-1 h-1 mx-4 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            
            <!-- Step 3 -->
            <div class="flex items-center flex-1">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    3
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Hasil Import</p>
                    <p class="text-xs text-gray-500">Summary & log</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Upload File -->
    @if($step === 1)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Download Template</h3>
                <p class="text-sm text-gray-600 mb-4">Download template Excel terlebih dahulu, isi data kegiatan sesuai format, lalu upload kembali.</p>
                <button wire:click="downloadTemplate" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template Excel
                    </span>
                </button>
            </div>

            <hr class="my-6">

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Upload File Import</h3>
                <p class="text-sm text-gray-600 mb-4">Upload file Excel yang sudah diisi. Format: .xlsx atau .xls (Maksimal 2MB)</p>
                
                <form wire:submit.prevent="uploadFile">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                        <input type="file" wire:model="file" accept=".xlsx,.xls" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('file') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if($file)
                            <p class="mt-2 text-sm text-gray-600">
                                File terpilih: <span class="font-medium">{{ $file->getClientOriginalName() }}</span>
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="uploadFile">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Upload & Validasi
                                </span>
                            </span>
                            <span wire:loading wire:target="uploadFile">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Step 2: Preview Data -->
    @if($step === 2)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Data Import</h3>
            
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-700 font-medium">Total Data</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $summary['total'] ?? 0 }}</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-700 font-medium">Valid</p>
                    <p class="text-2xl font-bold text-green-900">{{ $summary['valid'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-700 font-medium">Warning</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $summary['warning'] ?? 0 }}</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-700 font-medium">Error</p>
                    <p class="text-2xl font-bold text-red-900">{{ $summary['error'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Baris</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kegiatan</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Error/Warning</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($previewData as $row)
                            <tr class="{{ $row['status'] === 'error' ? 'bg-red-50' : ($row['status'] === 'warning' ? 'bg-yellow-50' : 'bg-white') }}">
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $row['row'] }}</td>
                                <td class="px-3 py-4 text-sm">
                                    @if($row['status'] === 'valid')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Valid</span>
                                    @elseif($row['status'] === 'warning')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Warning</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Error</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $row['name'] }}</td>
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $row['activity_type_name'] }}</td>
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $row['start_date'] }} s/d {{ $row['end_date'] }}</td>
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $row['semester_type'] }}</td>
                                <td class="px-3 py-4 text-sm text-red-600">
                                    @if(!empty($row['errors']))
                                        <ul class="list-disc list-inside">
                                            @foreach($row['errors'] as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center space-x-3">
                <button wire:click="processImport" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="processImport">Proses Import</span>
                    <span wire:loading wire:target="processImport">Memproses...</span>
                </button>
                <button wire:click="resetForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </div>
    @endif

    <!-- Step 3: Result -->
    @if($step === 3)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Import</h3>
            
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-green-700 font-medium">Berhasil Diimport</p>
                            <p class="text-2xl font-bold text-green-900">{{ $importResult['imported'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-red-700 font-medium">Gagal</p>
                            <p class="text-2xl font-bold text-red-900">{{ $importResult['failed'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($importResult['errors']))
                <div class="mb-6">
                    <button wire:click="downloadErrorLog" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Error Log
                        </span>
                    </button>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <button wire:click="backToActivities" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Lihat Data Kegiatan
                </button>
                <button wire:click="resetForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Import Lagi
                </button>
            </div>
        </div>
    @endif
</div>

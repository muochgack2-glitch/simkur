<div x-data="{}" 
     @openPreview.window="$wire.openPreview($event.detail.type, $event.detail.params || {})">
    @if($showModal)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 transition-opacity" wire:click="closeModal"></div>
        
        <!-- Modal Container -->
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal Content -->
                <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] flex flex-col" @click.stop>
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                Preview Export - 
                                @if($exportType === 'yearly')
                                    Kalender Tahunan
                                @elseif($exportType === 'monthly')
                                    Kalender Bulanan
                                @else
                                    Daftar Kegiatan
                                @endif
                            </h3>
                            <p class="text-sm text-gray-700 mt-1">
                                Preview sebelum download • Format: {{ strtoupper($paperSize) }} • {{ ucfirst($orientation) }}
                            </p>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable Preview) -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gray-100">
                        <div class="max-w-5xl mx-auto">
                            <!-- Paper Size & Print Options -->
                            <div class="bg-white rounded-lg shadow-sm p-4 mb-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Paper Size Selector -->
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mr-2">Ukuran Kertas:</label>
                                        <select wire:model.live="paperSize" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            <option value="a4">A4 (210 x 297 mm)</option>
                                            <option value="letter">Letter (8.5 x 11 in)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Orientation Info -->
                                    <div class="text-sm text-gray-800 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Orientasi: <strong class="ml-1">{{ $orientation === 'portrait' ? 'Tegak' : 'Mendatar' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Content (Paper Simulation) -->
                            <div class="bg-white shadow-lg mx-auto print-preview" 
                                 style="
                                    @if($paperSize === 'a4' && $orientation === 'portrait')
                                        width: 210mm; min-height: 297mm;
                                    @elseif($paperSize === 'a4' && $orientation === 'landscape')
                                        width: 297mm; min-height: 210mm;
                                    @elseif($paperSize === 'letter' && $orientation === 'portrait')
                                        width: 8.5in; min-height: 11in;
                                    @else
                                        width: 11in; min-height: 8.5in;
                                    @endif
                                    padding: 15mm; box-sizing: border-box;">
                                
                                @if($exportType === 'yearly')
                                    @include('pdf.preview.calendar-yearly', $previewData)
                                @elseif($exportType === 'monthly')
                                    @include('pdf.preview.calendar-monthly', $previewData)
                                @else
                                    @include('pdf.preview.activity-list', $previewData)
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">
                        <button wire:click="closeModal" class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition">
                            Tutup
                        </button>
                        
                        <div class="flex items-center space-x-3">
                            <!-- Print Button -->
                            <button onclick="window.print()" class="flex items-center space-x-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span>Print</span>
                            </button>
                            
                            <!-- Download PDF Button -->
                            <button wire:click="downloadPdf" class="flex items-center space-x-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Download PDF</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print-specific styles -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                .print-preview, .print-preview * {
                    visibility: visible;
                }
                .print-preview {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100% !important;
                    box-shadow: none !important;
                }
            }
        </style>
    @endif
</div>

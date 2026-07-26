<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Riwayat Kenaikan Kelas</h1>
                <p class="mt-1 text-sm text-gray-800">History proses kenaikan kelas dan kelulusan siswa</p>
            </div>
            <a href="{{ route('class-promotion.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Proses Kenaikan Baru
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Naik Kelas</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Lulus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Diproses Oleh</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($promotions as $promotion)
                    <tr class="hover:bg-gray-50 {{ $promotion->is_rolled_back ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $promotion->processed_at->format('d M Y, H:i') }}
                            @if($promotion->is_rolled_back)
                                <div class="text-xs text-red-600 font-medium mt-1">
                                    🔄 Di-undo {{ $promotion->rolled_back_at->format('d M Y, H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $promotion->fromAcademicYear->year }}</div>
                            <div class="text-gray-500 text-xs">→ {{ $promotion->toAcademicYear->year }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $promotion->is_rolled_back ? 'bg-gray-100 text-gray-600 line-through' : 'bg-green-100 text-green-800' }}">
                                ⬆️ {{ $promotion->total_promoted }} siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $promotion->is_rolled_back ? 'bg-gray-100 text-gray-600 line-through' : 'bg-purple-100 text-purple-800' }}">
                                🎓 {{ $promotion->total_graduated }} siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $promotion->processedBy->name }}
                            @if($promotion->is_rolled_back && $promotion->rolledBackBy)
                                <div class="text-xs text-red-600 mt-1">
                                    Undo: {{ $promotion->rolledBackBy->name }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                            <button 
                                wire:click="viewDetail({{ $promotion->id }})"
                                class="text-blue-600 hover:text-blue-900"
                            >
                                👁️ Detail
                            </button>
                            @if(!$promotion->is_rolled_back && $promotion->canRollback())
                                <button 
                                    wire:click="confirmRollback({{ $promotion->id }})"
                                    class="text-red-600 hover:text-red-900"
                                    title="Undo kenaikan kelas ini"
                                >
                                    🔄 Undo
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada riwayat kenaikan kelas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $promotions->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($selectedPromotion)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" wire:click="closeDetail">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Detail Kenaikan Kelas</h3>
                        <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-sm text-gray-600">Tanggal Proses</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedPromotion->processed_at->format('d F Y, H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Diproses Oleh</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedPromotion->processedBy->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Tahun Ajaran Sumber</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedPromotion->fromAcademicYear->year }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Tahun Ajaran Tujuan</div>
                            <div class="text-base font-semibold text-gray-900">{{ $selectedPromotion->toAcademicYear->year }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-sm text-green-600 font-medium">Siswa Naik Kelas</div>
                            <div class="text-2xl font-bold text-green-900 mt-1">{{ $selectedPromotion->total_promoted }}</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-sm text-purple-600 font-medium">Siswa Lulus (Alumni)</div>
                            <div class="text-2xl font-bold text-purple-900 mt-1">{{ $selectedPromotion->total_graduated }}</div>
                        </div>
                    </div>

                    @if($selectedPromotion->promotion_summary)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Detail Per Kelas:</h4>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Kelas Sumber</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Kelas Tujuan</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-700">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($selectedPromotion->promotion_summary as $summary)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $summary['source'] }}</td>
                                                <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                                    @if($summary['target'] === 'ALUMNI')
                                                        <span class="text-purple-600">🎓 {{ $summary['target'] }}</span>
                                                    @else
                                                        <span class="text-green-600">⬆️ {{ $summary['target'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center font-semibold text-gray-900">{{ $summary['count'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($selectedPromotion->notes)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Catatan:</h4>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $selectedPromotion->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button 
                        wire:click="closeDetail"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirmation Modal for Rollback -->
    @if($confirmingRollback)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">⚠️ Konfirmasi Undo Kenaikan Kelas</h3>
                </div>

                <div class="px-6 py-4">
                    <div class="mb-4">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Peringatan:</strong> Proses ini akan:
                                    </p>
                                </div>
                            </div>
                        </div>

                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-2 mb-4">
                            <li>Mengembalikan semua siswa ke kelas sebelumnya</li>
                            <li>Mengembalikan siswa alumni menjadi siswa kelas XII</li>
                            <li>Mengaktifkan kembali tahun ajaran lama</li>
                            <li>Menonaktifkan tahun ajaran baru</li>
                        </ul>

                        <p class="text-sm text-red-600 font-medium">
                            Proses ini tidak dapat di-undo lagi!
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                    <button 
                        wire:click="cancelRollback"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400"
                        wire:loading.attr="disabled"
                    >
                        Batal
                    </button>
                    <button 
                        wire:click="rollbackPromotion"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        <span wire:loading.remove wire:target="rollbackPromotion">Ya, Undo Kenaikan Kelas</span>
                        <span wire:loading wire:target="rollbackPromotion">
                            <svg class="inline animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>



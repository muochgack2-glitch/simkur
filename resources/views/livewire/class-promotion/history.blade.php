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
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $promotion->processed_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $promotion->fromAcademicYear->year }}</div>
                            <div class="text-gray-500 text-xs">→ {{ $promotion->toAcademicYear->year }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ⬆️ {{ $promotion->total_promoted }} siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                🎓 {{ $promotion->total_graduated }} siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $promotion->processedBy->name }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium">
                            <button 
                                wire:click="viewDetail({{ $promotion->id }})"
                                class="text-blue-600 hover:text-blue-900"
                            >
                                👁️ Detail
                            </button>
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
</div>

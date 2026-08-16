<div>
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">WhatsApp Gateway</h2>
                <p class="text-sm text-gray-500 mt-1">Monitor status dan log pesan WhatsApp</p>
            </div>
            <button wire:click="refreshStatus" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" wire:loading.class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-{{ $statusColor }}-100 flex items-center justify-center">
                        @if($statusColor === 'green')
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($statusColor === 'red')
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <svg class="w-6 h-6 text-{{ $statusColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Status Koneksi</h3>
                        <p class="text-sm text-gray-500">{{ config('services.whatsapp.url') }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        <!-- Log Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Log Pesan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-md font-medium {{ $log->type === 'group' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $log->type === 'group' ? 'Grup' : 'Personal' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-[200px] truncate">{{ $log->recipient }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-[300px] truncate">{{ $log->message }}</td>
                                <td class="px-4 py-3">
                                    @if($log->status === 'sent')
                                        <span class="px-2 py-1 text-xs rounded-md font-medium bg-green-100 text-green-700">Terkirim</span>
                                    @elseif($log->status === 'failed')
                                        <span class="px-2 py-1 text-xs rounded-md font-medium bg-red-100 text-red-700">Gagal</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-md font-medium bg-gray-100 text-gray-700">{{ $log->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada log pesan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</div>
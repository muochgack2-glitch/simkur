<div>
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">WhatsApp Gateway</h2>
                <p class="text-sm text-gray-500 mt-1">Monitor status dan log pesan WhatsApp</p>
            </div>
            <button wire:click="refreshStatus" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" wire:loading.class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Gateway Selector --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">🔌 Pilih Gateway Aktif</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($availableGateways as $url => $label)
                    <button
                        wire:click="$set('activeGateway', '{{ $url }}')"
                        class="px-4 py-2.5 rounded-lg border-2 text-sm font-medium transition-all
                            {{ $activeGateway === $url
                                ? 'border-blue-600 bg-blue-50 text-blue-700'
                                : 'border-gray-200 bg-white text-gray-600 hover:border-blue-300 hover:bg-blue-50' }}">
                        <span class="flex items-center gap-2">
                            @if($activeGateway === $url)
                                <span class="w-2 h-2 rounded-full {{ $statusColor === 'green' ? 'bg-green-500' : ($statusColor === 'yellow' ? 'bg-yellow-500' : 'bg-red-400') }}"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            @endif
                            {{ $label }}
                            @if($activeGateway === $url)
                                <span class="text-xs text-blue-500">(aktif)</span>
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-gray-400">Gateway yang dipilih langsung aktif dan status dicek otomatis.</p>
        </div>

        {{-- Status Card --}}
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
                        <p class="text-sm text-gray-500">{{ $status['gateway_name'] ?? $activeGateway }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                    {{ $statusLabel }}
                </span>
            </div>

            @if(!empty($status['error']))
                <div class="mt-3 p-2 bg-red-50 rounded text-xs text-red-600 font-mono">{{ $status['error'] }}</div>
            @endif
        </div>

        {{-- Group Setting --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">⚙️ Pengaturan Grup &amp; Template Notifikasi PKL</h3>
            <div class="space-y-4">
                {{-- Group selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grup WA untuk Notifikasi Materi PKL</label>
                    @if(count($groups) > 0)
                        <select wire:model="pklGroupId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Grup --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group['id'] }}">{{ $group['name'] ?: 'Grup tanpa nama' }} ({{ $group['participants'] }} anggota) — {{ Str::limit($group['id'], 20) }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" wire:model="pklGroupId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" placeholder="ID Grup WA (misal: 120363xxx@g.us)">
                        <p class="mt-1 text-xs text-gray-400">Klik Refresh di atas untuk memuat daftar grup (butuh status Terhubung)</p>
                    @endif
                </div>
                {{-- Template --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Pesan</label>
                    <p class="text-xs text-gray-400 mb-2">Placeholder yang tersedia: <code class="bg-gray-100 px-1 rounded">{judul}</code> <code class="bg-gray-100 px-1 rounded">{mapel}</code> <code class="bg-gray-100 px-1 rounded">{guru}</code> <code class="bg-gray-100 px-1 rounded">{kelas}</code> <code class="bg-gray-100 px-1 rounded">{deadline_tugas}</code> <code class="bg-gray-100 px-1 rounded">{link}</code></p>
                    <textarea wire:model="pklTemplate" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-mono" placeholder="Tulis template pesan di sini..."></textarea>
                </div>
                {{-- Save button --}}
                <div class="flex justify-end">
                    <button wire:click="saveSettings" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        💾 Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>

        {{-- Log Table --}}
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
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-[300px] truncate" title="{{ $log->message }}">{{ $log->message }}</td>
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

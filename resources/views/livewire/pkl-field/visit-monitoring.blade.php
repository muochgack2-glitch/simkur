<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                📅 Kunjungan Monitoring
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Pantau jadwal & realisasi kunjungan guru ke DU/DI</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($isAdmin)
            <button wire:click="$set('showGenerate', true)" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
                ⚡ Generate Jadwal
            </button>
            @endif
            <button wire:click="openForm()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
                + Tambah
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-600 rounded-2xl p-4 text-white shadow-lg">
            <div class="text-2xl font-bold">{{ $visits->count() }}</div>
            <div class="text-blue-100 text-xs font-medium mt-1">Total</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">📅 Terjadwal</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">✅ Selesai</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-red-600">{{ $stats['missed'] }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">⚠️ Terlewat</div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($visits->count() > 0)
    <div class="bg-white rounded-xl border p-4 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-600">Progres Kunjungan</span>
            <span class="text-xs font-bold text-green-600">{{ round(($stats['completed'] / $visits->count()) * 100) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full bg-green-500 transition-all duration-500" style="width: {{ ($stats['completed'] / $visits->count()) * 100 }}%"></div>
        </div>
    </div>
    @endif

    <!-- Filter -->
    <div class="flex gap-3 mb-4">
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="scheduled">📅 Terjadwal</option>
            <option value="completed">✅ Selesai</option>
            <option value="missed">⚠️ Terlewat</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        @if($isAdmin)<th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Guru</th>@endif
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">DU/DI</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Jadwal</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Aktual</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-data="{ expandedRow: null }">
                    @forelse($visits as $v)
                    <tr class="hover:bg-blue-50/50 transition-colors cursor-pointer" @click="expandedRow = expandedRow === {{ $v->id }} ? null : {{ $v->id }}">
                        @if($isAdmin)
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $v->teacher->name ?? '-' }}</div>
                        </td>
                        @endif
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $v->company->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ $v->company->address ?? '' }}</div>
                            @if($v->company->contact_person)
                            <div class="text-xs text-gray-500 mt-0.5">👤 {{ $v->company->contact_person }} @if($v->company->contact_phone) · {{ $v->company->contact_phone }} @endif</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            <div class="font-medium">{{ $v->scheduled_date->translatedFormat('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($v->actual_date)
                            <span class="text-green-600 font-medium">{{ $v->actual_date->translatedFormat('d M Y') }}</span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $v->status === 'completed' ? 'bg-green-100 text-green-700' : ($v->status === 'missed' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $v->status === 'completed' ? '✅ Selesai' : ($v->status === 'missed' ? '⚠️ Terlewat' : '📅 Terjadwal') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center" @click.stop>
                            <div class="flex items-center justify-center gap-1">
                                @if($v->status === 'scheduled')
                                <button wire:click="markCompleted({{ $v->id }})" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition-colors" title="Selesaikan">
                                    ✅ Selesaikan
                                </button>
                                @endif
                                <button wire:click="openForm({{ $v->id }})" class="px-3 py-1.5 border border-gray-300 hover:bg-gray-100 rounded-lg text-xs font-medium text-gray-600 transition-colors" title="Edit">
                                    ✏️
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Expand Row: Laporan Detail -->
                    <tr x-show="expandedRow === {{ $v->id }}" x-collapse>
                        <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-4 py-0">
                            <div class="py-4 pl-4 border-l-4 {{ $v->status === 'completed' ? 'border-green-400' : 'border-gray-300' }}">
                                @if($v->notes || $v->findings || $v->recommendations || $v->photo)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @if($v->notes)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs font-bold text-gray-400 uppercase mb-1">📝 Catatan</div>
                                        <p class="text-sm text-gray-700">{{ $v->notes }}</p>
                                    </div>
                                    @endif
                                    @if($v->findings)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs font-bold text-gray-400 uppercase mb-1">🔍 Temuan</div>
                                        <p class="text-sm text-gray-700">{{ $v->findings }}</p>
                                    </div>
                                    @endif
                                    @if($v->recommendations)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs font-bold text-gray-400 uppercase mb-1">💡 Rekomendasi</div>
                                        <p class="text-sm text-gray-700">{{ $v->recommendations }}</p>
                                    </div>
                                    @endif
                                </div>
                                @if($v->photo)
                                <div class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
                                    <div class="text-xs font-bold text-gray-400 uppercase mb-2">📸 Foto Kunjungan</div>
                                    <img src="{{ Storage::url($v->photo) }}" alt="Foto Kunjungan" class="max-h-64 rounded-lg border border-blue-200 shadow-sm cursor-pointer" onclick="document.getElementById('modal-visit-{{ $v->id }}').classList.remove('hidden')">
                                </div>
                                @endif
                                @else
                                <p class="text-sm text-gray-400 italic">Belum ada catatan / laporan</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-4 py-16 text-center">
                            <div class="text-4xl mb-3">📅</div>
                            <h3 class="text-lg font-bold text-gray-400">Belum ada jadwal kunjungan</h3>
                            <p class="text-sm text-gray-400 mt-1">Klik "Generate Jadwal" atau "Tambah"</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complete Visit Modal (focused) -->
    @if($showComplete)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showComplete', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="bg-green-600 hover:bg-green-700 px-6 py-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl">✅</div>
                    <div>
                        <h2 class="text-lg font-bold">Selesaikan Kunjungan</h2>
                        <p class="text-green-100 text-sm">{{ $completeCompanyName }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-2 px-4 py-3 bg-green-50 rounded-xl border border-green-200">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm text-green-700 font-medium">Tanggal: <strong>{{ now()->translatedFormat('d M Y') }}</strong></span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📝 Catatan</label>
                    <textarea wire:model="completeNotes" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500" placeholder="Catatan umum..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">🔍 Temuan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="completeFindings" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500" placeholder="Kondisi siswa, lingkungan kerja..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">💡 Rekomendasi <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="completeRecommendations" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500" placeholder="Saran & tindak lanjut..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📸 Foto Kunjungan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="file" wire:model="completePhoto" accept="image/*" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                    <div wire:loading wire:target="completePhoto" class="mt-1 text-xs text-blue-500 font-medium">⏳ Mengupload foto...</div>
                    @if($completePhoto)
                    <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2">
                        <span class="text-sm">✅</span>
                        <span class="text-xs text-green-700 font-medium">Foto siap diupload</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('showComplete', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="submitComplete" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitComplete">✅ Selesaikan</span>
                    <span wire:loading wire:target="submitComplete">⏳ Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 rounded-t-2xl">
                <h2 class="text-lg font-bold">{{ $editingId ? '✏️ Edit Kunjungan' : '📅 Tambah Kunjungan' }}</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">DU/DI <span class="text-red-500">*</span></label>
                    <select wire:model="form_company_id" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('form_company_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Jadwal <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="scheduled_date" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Aktual</label>
                        <input type="date" wire:model="actual_date" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select wire:model.live="status" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="scheduled">📅 Terjadwal</option>
                        <option value="completed">✅ Selesai</option>
                        <option value="missed">⚠️ Terlewat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📝 Catatan</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                @if($status === 'completed')
                <div class="pt-2 border-t">
                    <p class="text-xs font-bold text-green-600 uppercase mb-3">📋 Laporan</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">🔍 Temuan</label>
                            <textarea wire:model="findings" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none" placeholder="Kondisi siswa, lingkungan kerja..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">💡 Rekomendasi</label>
                            <textarea wire:model="recommendations" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">📸 Foto Kunjungan</label>
                            @if($existingPhoto && !$editPhoto)
                            <div class="mb-2 p-2 bg-blue-50 border border-blue-200 rounded-xl">
                                <img src="{{ Storage::url($existingPhoto) }}" alt="Foto" class="max-h-32 rounded-lg">
                                <p class="text-[10px] text-blue-500 mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                            </div>
                            @endif
                            <input type="file" wire:model="editPhoto" accept="image/*" class="w-full px-4 py-2.5 border rounded-xl text-sm">
                            <div wire:loading wire:target="editPhoto" class="mt-1 text-xs text-blue-500">⏳ Mengupload...</div>
                            @if($editPhoto)
                            <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2">
                                <span>✅</span><span class="text-xs text-green-700 font-medium">Foto baru siap</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="sticky bottom-0 bg-gray-50 border-t px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="save" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">💾 Simpan</span>
                    <span wire:loading wire:target="save">⏳ Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Generate Modal -->
    @if($showGenerate)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showGenerate', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-amber-100 flex items-center justify-center text-3xl">⚡</div>
                <h2 class="text-lg font-bold text-center mb-2">Generate Jadwal Bulanan</h2>
                <p class="text-sm text-gray-500 text-center mb-6">Otomatis buat jadwal kunjungan untuk setiap guru pembimbing ke DU/DI.</p>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bulan</label>
                    <input type="month" wire:model="generateMonth" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                <button wire:click="$set('showGenerate', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="generateSchedule" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="generateSchedule">⚡ Generate</span>
                    <span wire:loading wire:target="generateSchedule">⏳ Membuat...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Photo Modals -->
    @foreach($visits as $v)
    @if($v->photo)
    <div id="modal-visit-{{ $v->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-auto">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="font-bold text-gray-800">📸 Foto Kunjungan - {{ $v->company->name ?? '' }}</h3>
                <button onclick="this.closest('[id^=modal-visit]').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">✕</button>
            </div>
            <div class="p-4">
                <img src="{{ Storage::url($v->photo) }}" alt="Foto Kunjungan" class="w-full rounded-xl">
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
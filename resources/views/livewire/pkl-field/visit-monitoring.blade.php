<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                📅 Kunjungan Monitoring
                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $visits->count() }} kunjungan</span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Pantau jadwal & realisasi kunjungan guru ke DU/DI</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($isAdmin)
            <button wire:click="$set('showGenerate', true)" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm flex items-center gap-1.5">
                ⚡ Generate Jadwal
            </button>
            @endif
            <button wire:click="openForm()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2 animate-fade-in">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold">{{ $visits->count() }}</div>
                    <div class="text-blue-100 text-sm font-medium mt-1">Total Kunjungan</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl">📋</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['scheduled'] }}</div>
                    <div class="text-gray-500 text-sm font-medium mt-1">Terjadwal</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl">📅</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                    <div class="text-gray-500 text-sm font-medium mt-1">Selesai</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-2xl">✅</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-red-600">{{ $stats['missed'] }}</div>
                    <div class="text-gray-500 text-sm font-medium mt-1">Terlewat</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-2xl">⚠️</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($visits->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border p-5 mb-8 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-700">Progres Kunjungan</span>
            <span class="text-sm font-bold text-green-600">{{ $visits->count() > 0 ? round(($stats['completed'] / $visits->count()) * 100) : 0 }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-green-400 to-emerald-500 transition-all duration-500" style="width: {{ $visits->count() > 0 ? ($stats['completed'] / $visits->count()) * 100 : 0 }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-400">
            <span>{{ $stats['completed'] }} selesai dari {{ $visits->count() }} total</span>
            <span>{{ $stats['scheduled'] }} tersisa</span>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border border-gray-300 rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            <option value="">📋 Semua Status</option>
            <option value="scheduled">📅 Terjadwal</option>
            <option value="completed">✅ Selesai</option>
            <option value="missed">⚠️ Terlewat</option>
        </select>
    </div>

    <!-- Visit Cards -->
    <div class="space-y-4">
        @forelse($visits as $v)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden" x-data="{ expanded: false }">
            <div class="p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-4">
                        <!-- Status Icon -->
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl flex-shrink-0
                            {{ $v->status === 'completed' ? 'bg-green-100 text-green-600' : ($v->status === 'missed' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600') }}">
                            {{ $v->status === 'completed' ? '✅' : ($v->status === 'missed' ? '⚠️' : '📅') }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-gray-800 dark:text-white">{{ $v->company->name ?? '-' }}</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $v->status === 'completed' ? 'bg-green-100 text-green-700' : ($v->status === 'missed' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $v->status === 'completed' ? 'Selesai' : ($v->status === 'missed' ? 'Terlewat' : 'Terjadwal') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $v->company->address ?? '' }}</p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
                                @if($isAdmin)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $v->teacher->name ?? '-' }}
                                </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Jadwal: <strong>{{ $v->scheduled_date->translatedFormat('d M Y') }}</strong>
                                </span>
                                @if($v->actual_date)
                                <span class="flex items-center gap-1 text-green-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Aktual: <strong>{{ $v->actual_date->translatedFormat('d M Y') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($v->status === 'scheduled')
                        <button wire:click="markCompleted({{ $v->id }})" class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-xl text-xs font-semibold shadow-sm hover:shadow transition-all">
                            ✅ Selesaikan
                        </button>
                        @endif
                        <button wire:click="openForm({{ $v->id }})" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 rounded-xl text-xs font-medium text-gray-600 transition-all">
                            ✏️ Edit
                        </button>
                        @if($v->notes || $v->findings || $v->recommendations)
                        <button @click="expanded = !expanded" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Expandable Details -->
            <div x-show="expanded" x-collapse class="border-t border-gray-100 bg-gray-50/50 px-5 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($v->notes)
                    <div class="bg-white rounded-xl p-4 border">
                        <div class="text-xs font-bold text-gray-400 uppercase mb-2">📝 Catatan</div>
                        <p class="text-sm text-gray-700">{{ $v->notes }}</p>
                    </div>
                    @endif
                    @if($v->findings)
                    <div class="bg-white rounded-xl p-4 border">
                        <div class="text-xs font-bold text-gray-400 uppercase mb-2">🔍 Temuan</div>
                        <p class="text-sm text-gray-700">{{ $v->findings }}</p>
                    </div>
                    @endif
                    @if($v->recommendations)
                    <div class="bg-white rounded-xl p-4 border">
                        <div class="text-xs font-bold text-gray-400 uppercase mb-2">💡 Rekomendasi</div>
                        <p class="text-sm text-gray-700">{{ $v->recommendations }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-blue-50 flex items-center justify-center text-4xl">📅</div>
            <h3 class="text-lg font-bold text-gray-400">Belum ada jadwal kunjungan</h3>
            <p class="text-sm text-gray-400 mt-2">Klik "Generate Jadwal" untuk membuat jadwal otomatis<br>atau "Tambah" untuk menambah manual</p>
        </div>
        @endforelse
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800">{{ $editingId ? ($status === 'completed' ? '📋 Laporan Kunjungan' : '✏️ Edit Kunjungan') : '📅 Tambah Kunjungan' }}</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">DU/DI <span class="text-red-500">*</span></label>
                    <select wire:model="form_company_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih DU/DI...</option>
                        @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('form_company_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Jadwal <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="scheduled_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        @error('scheduled_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Aktual</label>
                        <input type="date" wire:model="actual_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 px-3 py-2.5 border rounded-xl cursor-pointer transition-all {{ $status === 'scheduled' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-300 hover:bg-gray-50' }}">
                            <input type="radio" wire:model.live="status" value="scheduled" class="sr-only">
                            <span class="text-sm font-medium {{ $status === 'scheduled' ? 'text-blue-700' : 'text-gray-600' }}">📅 Terjadwal</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2.5 border rounded-xl cursor-pointer transition-all {{ $status === 'completed' ? 'border-green-500 bg-green-50 ring-2 ring-green-200' : 'border-gray-300 hover:bg-gray-50' }}">
                            <input type="radio" wire:model.live="status" value="completed" class="sr-only">
                            <span class="text-sm font-medium {{ $status === 'completed' ? 'text-green-700' : 'text-gray-600' }}">✅ Selesai</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2.5 border rounded-xl cursor-pointer transition-all {{ $status === 'missed' ? 'border-red-500 bg-red-50 ring-2 ring-red-200' : 'border-gray-300 hover:bg-gray-50' }}">
                            <input type="radio" wire:model.live="status" value="missed" class="sr-only">
                            <span class="text-sm font-medium {{ $status === 'missed' ? 'text-red-700' : 'text-gray-600' }}">⚠️ Terlewat</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📝 Catatan</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500" placeholder="Catatan umum kunjungan..."></textarea>
                </div>
                @if($status === 'completed')
                <div class="pt-2 border-t">
                    <p class="text-xs font-bold text-green-600 uppercase mb-3">📋 Laporan Kunjungan</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">🔍 Temuan</label>
                            <textarea wire:model="findings" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500" placeholder="Kondisi siswa, lingkungan kerja, kegiatan yang dilakukan..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">💡 Rekomendasi</label>
                            <textarea wire:model="recommendations" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500" placeholder="Saran & tindak lanjut..."></textarea>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="sticky bottom-0 bg-gray-50 border-t px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">Batal</button>
                <button wire:click="save" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">💾 Simpan</span>
                    <span wire:loading wire:target="save">⏳ Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    <!-- Complete Visit Modal (focused, simple) -->
    @if($showComplete)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showComplete', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <!-- Header with green accent -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-5 text-white">
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
                    <span class="text-sm text-green-700 font-medium">Tanggal kunjungan: <strong>{{ now()->translatedFormat('d M Y') }}</strong></span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📝 Catatan Kunjungan</label>
                    <textarea wire:model="completeNotes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Catatan umum..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">🔍 Temuan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="completeFindings" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Kondisi siswa, lingkungan kerja, kegiatan..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">💡 Rekomendasi <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="completeRecommendations" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Saran & tindak lanjut..."></textarea>
                </div>
            </div>

            <div class="bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('showComplete', false)" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">Batal</button>
                <button wire:click="submitComplete" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitComplete">✅ Selesaikan</span>
                    <span wire:loading wire:target="submitComplete">⏳ Menyimpan...</span>
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
                <p class="text-sm text-gray-500 text-center mb-6">Otomatis buat jadwal kunjungan untuk setiap guru pembimbing ke DU/DI yang di-assign.</p>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bulan</label>
                    <input type="month" wire:model="generateMonth" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                <button wire:click="$set('showGenerate', false)" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">Batal</button>
                <button wire:click="generateSchedule" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="generateSchedule">⚡ Generate</span>
                    <span wire:loading wire:target="generateSchedule">⏳ Membuat...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
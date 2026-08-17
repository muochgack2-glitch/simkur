<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📔 Jurnal Harian PKL</h1>
            @if($placement)
            <p class="text-gray-500 mt-1 text-sm">🏭 {{ $placement->company->name ?? '-' }}</p>
            @endif
        </div>
        @if($isStudent && $placement)
        <button wire:click="openForm()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all text-sm">
            ✏️ Tulis Jurnal
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- PANEL GURU: Info DU/DI yang dibimbing + daftar siswa --}}
    @if(isset($isGuru) && $isGuru && $myCompanies->count() > 0)
    <div class="mb-6">
        <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">📍 DU/DI yang Saya Bimbing</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($myCompanies as $sup)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-indigo-600 px-4 py-3 flex items-center gap-2">
                    <span class="text-white text-base">🏢</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-sm truncate">{{ $sup->company->name }}</p>
                        <p class="text-indigo-200 text-xs truncate">{{ $sup->company->address }}</p>
                    </div>
                    <span class="bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full whitespace-nowrap">
                        {{ $sup->company->placements->count() }} siswa
                    </span>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sup->company->placements as $pl)
                    <li class="px-4 py-2.5 flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 text-xs font-bold flex items-center justify-center flex-shrink-0">
                            {{ $loop->iteration }}
                        </span>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                            {{ $pl->student->name ?? '-' }}
                        </span>
                    </li>
                    @empty
                    <li class="px-4 py-3 text-xs text-gray-400 italic">Belum ada siswa</li>
                    @endforelse
                </ul>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- REKAP ABSENSI untuk Guru --}}
    @if(isset($isGuru) && $isGuru && $myCompanies->count() > 0)
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <span class="text-lg">📊</span>
            <h2 class="font-bold text-gray-800 dark:text-white">Rekap Absensi PKL</h2>
        </div>
        @foreach($myCompanies as $sup)
        <div class="px-5 py-3 border-b border-gray-50 dark:border-gray-700 last:border-0">
            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-2">🏢 {{ $sup->company->name }}</p>
            @foreach($sup->company->placements as $pl)
            @php
                $studentJournals = \App\Models\PklJournal::where('student_id', $pl->student_id)
                    ->whereIn('attendance_status', ['hadir','sakit','izin','alpha'])
                    ->orderByDesc('journal_date')->limit(14)->get();
                $hadir = $studentJournals->where('attendance_status','hadir')->count();
                $sakit = $studentJournals->where('attendance_status','sakit')->count();
                $izin  = $studentJournals->where('attendance_status','izin')->count();
                $alpha = $studentJournals->where('attendance_status','alpha')->count();
            @endphp
            <div class="flex items-center gap-3 py-2 border-b border-gray-50 dark:border-gray-700 last:border-0">
                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 w-48 truncate">{{ $pl->student->name ?? '-' }}</span>
                <div class="flex gap-1.5 flex-1 flex-wrap">
                    @foreach($studentJournals->take(7) as $jn)
                    <span title="{{ $jn->journal_date->format('d/m') }} - {{ ucfirst($jn->attendance_status) }}"
                        class="w-4 h-4 rounded-full {{ $jn->attendance_status==='hadir' ? 'bg-green-500' : ( + [char]36 + jn->attendance_status==='sakit' ? 'bg-yellow-400' : ( + [char]36 + jn->attendance_status==='izin' ? 'bg-blue-400' : 'bg-red-500')) }}">
                    </span>
                    @endforeach
                </div>
                <div class="flex gap-1.5 text-xs">
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">✅ {{ $hadir }}</span>
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">🤒 {{ $sakit }}</span>
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">📝 {{ $izin }}</span>
                    @if($alpha > 0)
                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">❌ {{ $alpha }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
    @endif

    {{-- Student Info Card --}}
    @if($isStudent && $placement)
    <div class="bg-white rounded-2xl border shadow-sm p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-lg flex-shrink-0">🏭</div>
                <div>
                    <div class="text-xs text-gray-400 font-semibold uppercase">Tempat PKL</div>
                    <div class="font-bold text-gray-800">{{ $placement->company->name ?? '-' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $placement->company->address ?? '' }}</div>
                    @if($placement->company->contact_person)
                    <div class="text-xs text-gray-500 mt-1">
                        👤 PIC: <strong>{{ $placement->company->contact_person }}</strong>
                        @if($placement->company->contact_phone) · 📱 {{ $placement->company->contact_phone }} @endif
                    </div>
                    @endif
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-lg flex-shrink-0">👨‍🏫</div>
                <div>
                    <div class="text-xs text-gray-400 font-semibold uppercase">Guru Pembimbing</div>
                    @if($supervisor)
                    <div class="font-bold text-gray-800">{{ $supervisor->name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $supervisor->email ?? '' }}</div>
                    @else
                    <div class="text-sm text-gray-400 italic">Belum di-assign</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- NO PLACEMENT WARNING --}}
    @if($isStudent && !$placement)
    <div class="text-center py-20 bg-white rounded-2xl border shadow-sm">
        <div class="text-5xl mb-4">🏭</div>
        <h3 class="text-lg font-bold text-gray-500">Belum Ditempatkan di DU/DI</h3>
        <p class="text-sm text-gray-400 mt-2">Hubungi admin atau waka kurikulum untuk penempatan PKL</p>
    </div>
    @else

    {{-- STUDENT STATS --}}
    @if($isStudent)
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-blue-600 rounded-2xl p-4 text-white shadow-lg">
            <div class="text-2xl font-bold">{{ $journals->count() }}</div>
            <div class="text-blue-100 text-xs font-medium mt-1">Total Jurnal</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $journals->where('status', 'approved')->count() }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">✅ Disetujui</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $journals->where('status', 'submitted')->count() }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">📤 Menunggu</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border shadow-sm">
            <div class="text-2xl font-bold text-amber-600">{{ $journals->where('status', 'revision')->count() + $journals->where('status', 'draft')->count() }}</div>
            <div class="text-gray-500 text-xs font-medium mt-1">📝 Perlu Aksi</div>
        </div>
    </div>
    @endif

    <!-- Info Alur -->
    @if($isStudent)
    <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 flex items-start gap-2">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <div>
            <strong>Alur jurnal:</strong> Tulis → Kirim ke pembimbing → Pembimbing setujui/minta revisi.
            <span class="text-blue-600">Jurnal yang sudah dikirim hanya bisa dibuka kembali oleh Admin.</span>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
        <select wire:model.live="filterStatus" class="px-4 py-2.5 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="submitted">📤 Menunggu Review</option>
            <option value="approved">✅ Disetujui</option>
            <option value="revision">🔄 Perlu Revisi</option>
            <option value="draft">📝 Draft</option>
        </select>
    </div>

    <!-- Weekly Groups -->
    @forelse($weeklyGroups as $weekStart => $items)
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-500 flex items-center gap-2">
                📅 Senin, {{ \Carbon\Carbon::parse($weekStart)->translatedFormat('d M') }} — Sabtu, {{ \Carbon\Carbon::parse($weekStart)->addDays(5)->translatedFormat('d M Y') }}
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $items->count() }} jurnal</span>
            </h3>
            @if($isStudent)
            <div class="flex gap-0.5">
                @for($d = 0; $d < 7; $d++)
                @php
                    $date = \Carbon\Carbon::parse($weekStart)->addDays($d);
                    $hasJournal = $items->contains(fn($j) => $j->journal_date->format('Y-m-d') === $date->format('Y-m-d'));
                    $isWeekend = $date->isWeekend();
                @endphp
                <div class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold
                    {{ $isWeekend ? 'bg-gray-100 text-gray-300' : ($hasJournal ? 'bg-green-500 text-white' : ($date->isPast() ? 'bg-red-100 text-red-400' : 'bg-gray-100 text-gray-400')) }}"
                    title="{{ $date->translatedFormat('l, d M') }}">
                    {{ $date->translatedFormat('D')[0] }}
                </div>
                @endfor
            </div>
            @endif
        </div>
        <div class="space-y-3">
            @foreach($items as $j)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3 flex-1">
                            <!-- Day indicator -->
                            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex flex-col items-center justify-center text-xs font-bold
                                {{ $j->status === 'approved' ? 'bg-green-100 text-green-700' : ($j->status === 'submitted' ? 'bg-blue-100 text-blue-700' : ($j->status === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500')) }}">
                                <span class="text-lg leading-none">{{ $j->journal_date->format('d') }}</span>
                                <span class="text-[8px] uppercase">{{ $j->journal_date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                @if(!$isStudent)
                                <p class="text-xs text-purple-600 font-bold mb-0.5">{{ $j->student->name ?? '-' }}</p>
                                @endif
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $j->journal_date->translatedFormat('l') }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $j->status === 'approved' ? 'bg-green-100 text-green-700' : ($j->status === 'submitted' ? 'bg-blue-100 text-blue-700' : ($j->status === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ $j->status === 'approved' ? '✅ Disetujui' : ($j->status === 'submitted' ? '📤 Dikirim' : ($j->status === 'revision' ? '🔄 Revisi' : '📝 Draft')) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ $j->activities }}</p>
                                @if($j->learnings)
                                <p class="text-xs text-gray-500 mt-1">💡 {{ Str::limit($j->learnings, 80) }}</p>
                                @endif
                                @if($j->challenges)
                                <p class="text-xs text-amber-600 mt-1">⚠️ {{ Str::limit($j->challenges, 80) }}</p>
                                @endif
                                @if($j->supervisor_notes)
                                <div class="mt-2 px-3 py-2 bg-blue-50 rounded-lg border border-blue-100">
                                    <p class="text-xs text-blue-800"><strong>💬 Pembimbing:</strong> {{ $j->supervisor_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2 ml-3 flex-shrink-0">
                            @if($isStudent && in_array($j->status, ['draft', 'revision']))
                            <button wire:click="openForm({{ $j->id }})" class="px-3 py-1.5 border border-blue-300 hover:bg-blue-50 rounded-lg text-xs font-medium text-blue-600 transition-colors">
                                ✏️ Edit
                            </button>
                            @endif
                            @if(!$isStudent && $j->status === 'submitted')
                            <button wire:click="openReview({{ $j->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-colors">
                                📋 Review
                            </button>
                            @endif
                            @if(!$isStudent && in_array(auth()->user()->role, ['admin', 'waka_kurikulum', 'kepsek']) && $j->status === 'approved')
                            <button wire:click="unlockJournal({{ $j->id }})" wire:confirm="Yakin buka kunci jurnal ini? Siswa akan bisa mengedit kembali." class="px-3 py-1.5 border border-amber-300 hover:bg-amber-50 rounded-lg text-xs font-medium text-amber-600 transition-colors" title="Buka kunci agar siswa bisa edit">
                                🔓 Buka
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-20 bg-white rounded-2xl border shadow-sm">
        <div class="text-5xl mb-4">📔</div>
        <h3 class="text-lg font-bold text-gray-400">Belum ada jurnal</h3>
        @if($isStudent)
        <p class="text-sm text-gray-400 mt-2">Klik "✏️ Tulis Jurnal" untuk mencatat kegiatan harian PKL</p>
        <button wire:click="openForm()" class="mt-4 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all">
            ✏️ Tulis Jurnal Pertama
        </button>
        @else
        <p class="text-sm text-gray-400 mt-2">Belum ada jurnal siswa untuk direview</p>
        @endif
    </div>
    @endforelse

    @endif

    <!-- Write Journal Modal -->
    @if($showForm && !($showConfirmSend ?? false))
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-green-600 hover:bg-green-700 px-6 py-5 text-white">
                <h2 class="text-lg font-bold">{{ $editingId ? '✏️ Edit Jurnal' : '📔 Tulis Jurnal Harian' }}</h2>
                @if($placement)
                <p class="text-green-100 text-sm mt-1">🏭 {{ $placement->company->name ?? '' }}</p>
                @endif
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📅 Tanggal</label>
                    <input type="date" wire:model="journal_date" class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📍 Status Kehadiran <span class="text-red-500">*</span></label>
                    <div class="flex gap-2 flex-wrap">
                        <button type="button" wire:click="$set('attendanceStatus','hadir')"
                            class="flex-1 py-2.5 px-3 rounded-xl text-sm font-bold border-2 transition-all {{ $attendanceStatus==='hadir' ? 'bg-green-500 border-green-500 text-white shadow-lg' : 'bg-white border-gray-200 text-gray-600 hover:border-green-400' }}">
                            ✅ Hadir
                        </button>
                        <button type="button" wire:click="$set('attendanceStatus','sakit')"
                            class="flex-1 py-2.5 px-3 rounded-xl text-sm font-bold border-2 transition-all {{ $attendanceStatus==='sakit' ? 'bg-yellow-400 border-yellow-400 text-white shadow-lg' : 'bg-white border-gray-200 text-gray-600 hover:border-yellow-400' }}">
                            🤒 Sakit
                        </button>
                        <button type="button" wire:click="$set('attendanceStatus','izin')"
                            class="flex-1 py-2.5 px-3 rounded-xl text-sm font-bold border-2 transition-all {{ $attendanceStatus==='izin' ? 'bg-blue-500 border-blue-500 text-white shadow-lg' : 'bg-white border-gray-200 text-gray-600 hover:border-blue-400' }}">
                            📝 Izin
                        </button>
                        <button type="button" wire:click="$set('attendanceStatus','alpha')"
                            class="flex-1 py-2.5 px-3 rounded-xl text-sm font-bold border-2 transition-all {{ $attendanceStatus==='alpha' ? 'bg-red-500 border-red-500 text-white shadow-lg' : 'bg-white border-gray-200 text-gray-600 hover:border-red-400' }}">
                            ❌ Alpha
                        </button>
                    </div>
                    @error('attendanceStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📷 Foto Kegiatan <span class="text-gray-400 font-normal">(opsional, maks. 2MB)</span></label>
                    @if($existingPhoto)
                    <div class="mb-2 flex items-center gap-3">
                        <img src="{{ asset('storage/' . $existingPhoto) }}" class="w-16 h-16 object-cover rounded-xl border-2 border-gray-200" alt="Foto jurnal">
                        <span class="text-xs text-gray-500">Foto sebelumnya (upload baru untuk ganti)</span>
                    </div>
                    @endif
                    <input type="file" wire:model="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:font-semibold hover:file:bg-green-100">
                    @if($photo)
                    <div class="mt-2">
                        <img src="{{ $photo->temporaryUrl() }}" class="w-24 h-24 object-cover rounded-xl border-2 border-green-300" alt="Preview">
                    </div>
                    @endif
                    @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">📋 Kegiatan Hari Ini <span class="text-red-500">*</span></label>
                    <textarea wire:model="activities" rows="4" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500" placeholder="Jelaskan kegiatan yang kamu lakukan hari ini..."></textarea>
                    @error('activities') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">💡 Hal yang Dipelajari <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="learnings" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500" placeholder="Apa yang kamu pelajari hari ini?"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">⚠️ Kendala <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="challenges" rows="2" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500" placeholder="Ada kendala atau kesulitan?"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 flex justify-between">
                <button wire:click="$set('showForm', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <div class="flex gap-2">
                    <button wire:click="save(true)" class="px-5 py-2.5 border border-gray-400 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100" wire:loading.attr="disabled">
                        📝 Draft
                    </button>
                    <button wire:click="$set('showConfirmSend', true)" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">📤 Kirim</span>
                        <span wire:loading wire:target="save">⏳ Mengirim...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    
    <!-- Confirm Send Modal -->
    @if($showConfirmSend ?? false)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-100 flex items-center justify-center text-3xl">📤</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Kirim Jurnal?</h3>
                <p class="text-sm text-gray-500 mb-4">Setelah dikirim, jurnal <strong>tidak bisa diedit</strong> sampai guru pembimbing meminta revisi atau admin membuka kunci.</p>
                <div class="px-4 py-3 bg-blue-50 rounded-xl text-xs text-blue-700 text-left space-y-1">
                    <div class="flex items-center gap-2">👨‍🏫 <span>Pembimbing bisa <strong>menyetujui</strong> atau <strong>minta revisi</strong></span></div>
                    <div class="flex items-center gap-2">🔓 <span>Hanya <strong>Admin</strong> yang bisa membuka kunci</span></div>
                </div>
            </div>
            <div class="border-t px-6 py-4 flex gap-3">
                <button wire:click="$set('showConfirmSend', false)" class="flex-1 px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                    Kembali
                </button>
                <button wire:click="save(false)" class="flex-1 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-lg transition-all" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">📤 Ya, Kirim</span>
                    <span wire:loading wire:target="save">⏳ Mengirim...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    <!-- Review Modal (guru) -->
    @if($showReview)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showReview', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 hover:bg-blue-700 px-6 py-5 text-white">
                <h2 class="text-lg font-bold">📋 Review Jurnal</h2>
            </div>
            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">💬 Catatan untuk Siswa</label>
                <textarea wire:model="reviewNotes" rows="3" class="w-full px-4 py-2.5 border rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500" placeholder="Komentar atau arahan..."></textarea>
            </div>
            <div class="bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('showReview', false)" class="px-5 py-2.5 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                <button wire:click="submitReview('revision')" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold transition-colors">
                    🔄 Minta Revisi
                </button>
                <button wire:click="submitReview('approve')" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition-colors">
                    ✅ Setujui
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
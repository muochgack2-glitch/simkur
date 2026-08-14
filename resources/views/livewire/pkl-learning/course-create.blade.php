<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pkl-learning.dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Buat Course Pembelajaran PKL</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Lengkapi form berikut untuk membuat materi pembelajaran baru</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
        <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form wire:submit.prevent="save(false)">
        <!-- Section 1: Informasi Dasar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-info-circle text-blue-500 mr-2"></i>Informasi Dasar</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode PKL <span class="text-red-500">*</span></label>
                    <select wire:model="activity_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih periode PKL</option>
                        @foreach($pklActivities as $act)
                        <option value="{{ $act->id }}">{{ $act->name }} ({{ $act->start_date->format('d/m/Y') }} - {{ $act->end_date->format('d/m/Y') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select wire:model="subject_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih mapel</option>
                        @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Course <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="title" placeholder="contoh: Materi 1 - Pengenalan Industri" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="3" placeholder="Deskripsi singkat..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kompetensi</label>
                    <textarea wire:model="competency" rows="2" placeholder="Kompetensi yang ingin dicapai..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="start_date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deadline <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="deadline" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Target Classes -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kelas PKL Target <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-2">
                    @foreach($availableClasses as $cls)
                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border cursor-pointer transition-all
                        {{ in_array($cls->id, $target_classes) ? 'bg-blue-50 border-blue-400 text-blue-700 dark:bg-blue-900/30 dark:border-blue-600 dark:text-blue-300' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                        <input type="checkbox" wire:model="target_classes" value="{{ $cls->id }}" class="rounded">
                        <span class="text-sm font-medium">{{ $cls->name }}</span>
                    </label>
                    @endforeach
                </div>
                @if($availableClasses->isEmpty())
                <p class="text-sm text-amber-600 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Belum ada kelas PKL aktif. Aktifkan via Manajemen PKL terlebih dahulu.</p>
                @endif
            </div>
        </div>

        <!-- Section 2: Materi -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-book text-green-500 mr-2"></i>Materi Pembelajaran</h2>
                <button type="button" wire:click="addMaterial" class="text-sm text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-plus mr-1"></i>Tambah Materi</button>
            </div>
            @foreach($materials as $i => $mat)
            <div class="p-4 mb-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Materi #{{ $i + 1 }}</span>
                    @if(count($materials) > 1)
                    <button type="button" wire:click="removeMaterial({{ $i }})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-times"></i></button>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <input type="text" wire:model="materials.{{ $i }}.title" placeholder="Judul materi" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div>
                        <select wire:model="materials.{{ $i }}.type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                            <option value="pdf">PDF</option>
                            <option value="video">Video</option>
                            <option value="link">Link Eksternal</option>
                            <option value="document">Dokumen Word</option>
                            <option value="image">Gambar</option>
                        </select>
                    </div>
                    <div>
                        @if(($materials[$i]['type'] ?? 'pdf') === 'link' || ($materials[$i]['type'] ?? 'pdf') === 'video')
                        <input type="url" wire:model="materials.{{ $i }}.external_url" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                        @else
                        <input type="file" wire:model="materialFiles.{{ $i }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Section 3: Tugas -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-tasks text-purple-500 mr-2"></i>Tugas</h2>
                <button type="button" wire:click="addAssignment" class="text-sm text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-plus mr-1"></i>Tambah Tugas</button>
            </div>
            @foreach($assignments as $i => $asg)
            <div class="p-4 mb-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Tugas #{{ $i + 1 }}</span>
                    @if(count($assignments) > 1)
                    <button type="button" wire:click="removeAssignment({{ $i }})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-times"></i></button>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" wire:model="assignments.{{ $i }}.title" placeholder="Judul tugas" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    <input type="datetime-local" wire:model="assignments.{{ $i }}.deadline" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    <textarea wire:model="assignments.{{ $i }}.description" placeholder="Instruksi tugas..." rows="2" class="md:col-span-2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm resize-none"></textarea>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="assignments.{{ $i }}.allow_late" class="rounded"> Boleh telat</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="assignments.{{ $i }}.allow_file_upload" class="rounded" checked> Upload file</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Nilai maks:</span>
                            <input type="number" wire:model="assignments.{{ $i }}.max_score" class="w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Section 4: Kuis -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-question-circle text-pink-500 mr-2"></i>Kuis</h2>
                <button type="button" wire:click="addQuiz" class="text-sm text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-plus mr-1"></i>Tambah Kuis</button>
            </div>
            @foreach($quizzes as $qi => $quiz)
            <div class="p-4 mb-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Kuis #{{ $qi + 1 }}</span>
                    <button type="button" wire:click="removeQuiz({{ $qi }})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-times"></i></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <input type="text" wire:model="quizzes.{{ $qi }}.title" placeholder="Judul kuis" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    <input type="datetime-local" wire:model="quizzes.{{ $qi }}.deadline" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Durasi:</span>
                        <input type="number" wire:model="quizzes.{{ $qi }}.duration_minutes" class="w-20 px-2 py-1 border rounded-lg text-sm" placeholder="menit">
                        <label class="flex items-center gap-1 text-sm ml-2"><input type="checkbox" wire:model="quizzes.{{ $qi }}.shuffle_questions" class="rounded"> Acak</label>
                    </div>
                </div>
                <!-- Questions -->
                <div class="space-y-3">
                    @foreach($quiz['questions'] ?? [] as $qj => $q)
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-gray-500">Soal #{{ $qj + 1 }}</span>
                            <div class="flex items-center gap-2">
                                <select wire:model="quizzes.{{ $qi }}.questions.{{ $qj }}.question_type" class="px-2 py-1 border rounded text-xs">
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="essay">Uraian</option>
                                    <option value="true_false">Benar/Salah</option>
                                </select>
                                <input type="number" wire:model="quizzes.{{ $qi }}.questions.{{ $qj }}.score" class="w-16 px-2 py-1 border rounded text-xs" placeholder="skor">
                                @if(count($quiz['questions']) > 1)
                                <button type="button" wire:click="removeQuestion({{ $qi }}, {{ $qj }})" class="text-red-400 hover:text-red-600"><i class="fas fa-times text-xs"></i></button>
                                @endif
                            </div>
                        </div>
                        <textarea wire:model="quizzes.{{ $qi }}.questions.{{ $qj }}.question" placeholder="Tulis soal..." rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm resize-none mb-2"></textarea>
                        @if(($q['question_type'] ?? 'multiple_choice') === 'multiple_choice')
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($q['options'] ?? ['','','',''] as $oi => $opt)
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-400">{{ chr(65 + $oi) }}.</span>
                                <input type="text" wire:model="quizzes.{{ $qi }}.questions.{{ $qj }}.options.{{ $oi }}" placeholder="Opsi {{ chr(65 + $oi) }}" class="flex-1 px-2 py-1.5 border border-gray-300 rounded-lg text-sm">
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <label class="text-xs text-gray-500">Jawaban benar:</label>
                            <select wire:model="quizzes.{{ $qi }}.questions.{{ $qj }}.correct_answer" class="ml-2 px-2 py-1 border rounded text-xs">
                                <option value="">Pilih</option>
                                @foreach($q['options'] ?? [] as $oi => $opt)
                                <option value="{{ $opt }}">{{ chr(65 + $oi) }}. {{ Str::limit($opt, 30) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @elseif(($q['question_type'] ?? '') === 'true_false')
                        <select wire:model="quizzes.{{ $qi }}.questions.{{ $qj }}.correct_answer" class="px-3 py-1.5 border rounded-lg text-sm">
                            <option value="">Jawaban benar:</option>
                            <option value="benar">Benar</option>
                            <option value="salah">Salah</option>
                        </select>
                        @endif
                    </div>
                    @endforeach
                </div>
                <button type="button" wire:click="addQuestion({{ $qi }})" class="mt-2 text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-plus mr-1"></i>Tambah Soal</button>
            </div>
            @endforeach
            @if(empty($quizzes))
            <p class="text-sm text-gray-400 text-center py-4">Belum ada kuis. Klik "Tambah Kuis" untuk menambahkan.</p>
            @endif
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('pkl-learning.dashboard') }}" class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 rounded-xl transition">Batal</a>
            <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-xl shadow transition">
                <i class="fas fa-save mr-1"></i> Simpan Draft
            </button>
            <button type="button" wire:click="save(true)" class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-xl shadow-lg transition">
                <i class="fas fa-rocket mr-1"></i> Publikasikan
            </button>
        </div>
    </form>
</div>

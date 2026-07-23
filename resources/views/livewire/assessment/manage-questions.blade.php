<div>
    <!-- Debug Info -->
    <div class="mb-4 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
        <p><strong>Assessment ID:</strong> {{ $assessment->id }}</p>
        <p><strong>Assessment Title:</strong> {{ $assessment->title }}</p>
        <p><strong>Questions Count:</strong> {{ $assessment->questions->count() }}</p>
        <p><strong>Show Add Modal:</strong> <span id="addModalStatus">{{ $showAddModal ? 'true' : 'false' }}</span></p>
        <p><strong>Show Edit Modal:</strong> <span id="editModalStatus">{{ $showEditModal ? 'true' : 'false' }}</span></p>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Kelola Soal: {{ $assessment->title }}
                </h1>
                <div class="mt-2 flex items-center gap-2">
                    <p class="text-gray-600">
                        Tambah, edit, atau hapus pertanyaan asesmen
                    </p>
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $assessment->isVark() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $assessment->getTypeLabel() }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <button wire:click="openAddModal" type="button"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Tambah Soal
                </button>
                <a href="{{ route('assessment.index') }}"
                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Questions List -->
    <div class="space-y-4">
        @if($assessment->questions->count() > 0)
            @foreach($assessment->questions as $question)
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                    {{ $question->order_number }}
                                </span>
                                @if($assessment->isVark())
                                    <span class="rounded-full px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $question->getLearningStyleLabel() }}
                                    </span>
                                @else
                                    <span class="rounded-full px-3 py-1 text-xs font-medium bg-green-100 text-green-800">
                                        {{ $question->getAspectLabel() }}
                                    </span>
                                    <span class="rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                                        Bobot: {{ $question->aspect_weight }}%
                                    </span>
                                @endif
                            </div>
                            <p class="mb-4 text-base font-medium text-gray-900">
                                {{ $question->question_text }}
                            </p>
                            <div class="ml-11 space-y-2">
                                @foreach($question->options as $option)
                                    <div class="flex items-center rounded-lg border border-gray-200 p-3">
                                        @if($assessment->isVark())
                                            <span class="mr-3 text-sm font-medium text-gray-500">{{ chr(64 + $option->order_number) }}.</span>
                                        @else
                                            <span class="mr-3 text-sm font-medium text-gray-500">{{ $option->order_number }}.</span>
                                        @endif
                                        <span class="flex-1 text-sm text-gray-700">{{ $option->option_text }}</span>
                                        <span class="text-xs text-gray-500">({{ $option->score_value }} poin)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="ml-4 flex gap-2">
                            <button wire:click="openEditModal({{ $question->id }})" type="button"
                                    class="rounded-lg px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 border border-blue-600"
                                    title="Edit">
                                Edit
                            </button>
                            <button wire:click="duplicateQuestion({{ $question->id }})" type="button"
                                    class="rounded-lg px-3 py-2 text-sm text-purple-600 hover:bg-purple-50 border border-purple-600"
                                    title="Duplikat">
                                Duplikat
                            </button>
                            <button wire:click="deleteQuestion({{ $question->id }})" type="button"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')"
                                    class="rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 border border-red-600"
                                    title="Hapus">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada soal</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan soal pertama</p>
                <button wire:click="openAddModal" type="button"
                        class="mt-4 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Tambah Soal Pertama
                </button>
            </div>
        @endif
    </div>

    <!-- Add/Edit Modal -->
    @if($showAddModal || $showEditModal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto bg-black bg-opacity-50" 
             wire:click.self="{{ $showAddModal ? 'closeAddModal' : 'closeEditModal' }}">
            
            <div class="flex min-h-screen items-center justify-center p-4">
                
                <!-- Modal Box -->
                <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-2xl" 
                     onclick="event.stopPropagation()">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ $showAddModal ? 'Tambah Soal Baru' : 'Edit Soal' }}
                        </h3>
                        <button type="button" 
                                wire:click="{{ $showAddModal ? 'closeAddModal' : 'closeEditModal' }}"
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        <form wire:submit.prevent="{{ $showAddModal ? 'saveQuestion' : 'updateQuestion' }}" id="questionForm">
                            <!-- Assessment Type Badge -->
                            <div class="mb-4 p-3 rounded-lg {{ $assessment->isVark() ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200' }}">
                                <p class="text-sm font-medium {{ $assessment->isVark() ? 'text-blue-800' : 'text-green-800' }}">
                                    <span class="font-bold">Tipe Asesmen:</span> {{ $assessment->getTypeLabel() }}
                                </p>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pertanyaan <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model.defer="question_text" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Tuliskan pertanyaan..."></textarea>
                                @error('question_text') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            @if($assessment->isVark())
                                <!-- VARK Form Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Learning Style -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Indikator Gaya Belajar
                                        </label>
                                        <select wire:model.defer="learning_style_indicator"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="visual">Visual</option>
                                            <option value="auditory">Auditory</option>
                                            <option value="kinesthetic">Kinesthetic</option>
                                            <option value="reading_writing">Reading/Writing</option>
                                        </select>
                                    </div>

                                    <!-- Order -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                                        <input type="number" wire:model.defer="order_number" min="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Weight -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Bobot</label>
                                        <input type="number" wire:model.defer="weight" min="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Options -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilihan Jawaban <span class="text-xs text-gray-500">(skor 3 = jawaban dominan)</span>
                                    </label>
                                    
                                    <!-- Option A -->
                                    <div class="flex gap-2 mb-3">
                                        <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg font-medium text-gray-700">A</span>
                                        <input type="text" wire:model.defer="option_0_text"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Teks pilihan A">
                                        <input type="number" wire:model.defer="option_0_score"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Skor">
                                    </div>
                                    @error('option_0_text') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror
                                    
                                    <!-- Option B -->
                                    <div class="flex gap-2 mb-3">
                                        <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg font-medium text-gray-700">B</span>
                                        <input type="text" wire:model.defer="option_1_text"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Teks pilihan B">
                                        <input type="number" wire:model.defer="option_1_score"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Skor">
                                    </div>
                                    @error('option_1_text') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror
                                    
                                    <!-- Option C -->
                                    <div class="flex gap-2 mb-3">
                                        <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg font-medium text-gray-700">C</span>
                                        <input type="text" wire:model.defer="option_2_text"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Teks pilihan C">
                                        <input type="number" wire:model.defer="option_2_score"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Skor">
                                    </div>
                                    @error('option_2_text') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror
                                    
                                    <!-- Option D -->
                                    <div class="flex gap-2 mb-3">
                                        <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg font-medium text-gray-700">D</span>
                                        <input type="text" wire:model.defer="option_3_text"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Teks pilihan D">
                                        <input type="number" wire:model.defer="option_3_score"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="Skor">
                                    </div>
                                    @error('option_3_text') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror
                                </div>
                            @else
                                <!-- Diagnostic Form Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Aspect -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Aspek <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model.defer="aspect"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                            <option value="kesiapan">Kesiapan Belajar</option>
                                            <option value="motivasi">Motivasi Belajar</option>
                                            <option value="kemandirian">Kemandirian Belajar</option>
                                            <option value="kolaborasi">Kolaborasi & Komunikasi</option>
                                            <option value="preferensi">Preferensi Belajar</option>
                                            <option value="dunia_kerja">Kesiapan Dunia Kerja</option>
                                        </select>
                                        @error('aspect') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Aspect Weight -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Bobot Aspek (%)
                                        </label>
                                        <input type="number" wire:model.defer="aspect_weight" min="0" max="100"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="30">
                                        @error('aspect_weight') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Order -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                                        <input type="number" wire:model.defer="order_number" min="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Likert Info -->
                                <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200">
                                    <p class="text-sm text-green-800 font-medium mb-2">
                                        📊 Skala Likert (otomatis)
                                    </p>
                                    <p class="text-xs text-green-700">
                                        Pilihan jawaban skala Likert 1-5 akan dibuat otomatis saat menyimpan:
                                    </p>
                                    <ul class="mt-2 text-xs text-green-700 space-y-1 ml-4">
                                        <li>• 1 - Sangat Tidak Sesuai</li>
                                        <li>• 2 - Tidak Sesuai</li>
                                        <li>• 3 - Cukup Sesuai</li>
                                        <li>• 4 - Sesuai</li>
                                        <li>• 5 - Sangat Sesuai</li>
                                    </ul>
                                </div>
                            @endif
                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg">
                        <button type="button"
                                wire:click="{{ $showAddModal ? 'closeAddModal' : 'closeEditModal' }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            Batal
                        </button>
                        <button type="submit" form="questionForm"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span wire:loading.remove wire:target="{{ $showAddModal ? 'saveQuestion' : 'updateQuestion' }}">
                                {{ $showAddModal ? 'Simpan' : 'Perbarui' }}
                            </span>
                            <span wire:loading wire:target="{{ $showAddModal ? 'saveQuestion' : 'updateQuestion' }}">
                                <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

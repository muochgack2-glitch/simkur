<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('teacher.assessment.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $assessmentId ? 'Edit Asesmen' : 'Buat Asesmen Baru' }}
            </h1>
        </div>
        <p class="text-sm text-gray-500 ml-8">Isi informasi dasar asesmen, lalu tambahkan soal-soal.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <form wire:submit="save" class="space-y-6 bg-white rounded-xl border border-gray-200 shadow-sm p-6">

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Asesmen <span class="text-red-500">*</span></label>
            <input wire:model="title" type="text" placeholder="Contoh: Ulangan Harian Bab 3 — Trigonometri"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi / Petunjuk Pengerjaan</label>
            <textarea wire:model="description" rows="3" placeholder="Petunjuk untuk siswa sebelum mengerjakan..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>

        {{-- Waktu --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input wire:model="startDate" type="date"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('startDate') border-red-400 @enderror">
                @error('startDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                <input wire:model="startTime" type="time"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir <span class="text-red-500">*</span></label>
                <input wire:model="endDate" type="date"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('endDate') border-red-400 @enderror">
                @error('endDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Berakhir <span class="text-red-500">*</span></label>
                <input wire:model="endTime" type="time"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Opsi --}}
        <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 space-y-3">
            <p class="text-sm font-medium text-gray-700">Pengaturan</p>
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="shuffleQuestions" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Acak urutan soal per siswa (anti-contek)</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="shuffleOptions" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Acak pilihan jawaban (khusus Pilihan Ganda)</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="allowRetry" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Izinkan siswa mengerjakan ulang (retry)</span>
            </label>
        </div>


        {{-- Target Kelas & Jurusan --}}
        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 space-y-4">
            <p class="text-sm font-medium text-amber-800">🎯 Target Siswa</p>
            <p class="text-xs text-amber-600">Kosongkan = semua kelas / semua jurusan</p>

            <div>
                <p class="text-xs font-semibold text-gray-600 mb-2">Kelas</p>
                <div class="flex flex-wrap gap-3">
                    @foreach( as )
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="targetGrades"
                                value="{{  }}"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 font-medium">Kelas {{  }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-600 mb-2">Jurusan</p>
                <div class="flex flex-col gap-2">
                    @foreach( as  => )
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="targetMajors"
                                value="{{  }}"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{  }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- Publish --}}
        <div class="flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div>
                <p class="text-sm font-medium text-blue-800">Publikasikan sekarang?</p>
                <p class="text-xs text-blue-600 mt-0.5">Siswa dapat melihat asesmen ini setelah dipublikasikan</p>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
                <input wire:model="isPublished" type="checkbox" class="peer sr-only">
                <div class="peer h-6 w-11 rounded-full bg-gray-300 peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-500 transition
                    after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition after:content-['']
                    peer-checked:after:translate-x-full"></div>
            </label>
        </div>

        {{-- Action buttons --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('teacher.assessment.index') }}" wire:navigate
               class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition disabled:opacity-60"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $assessmentId ? 'Simpan Perubahan' : 'Simpan & Tambah Soal →' }}</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

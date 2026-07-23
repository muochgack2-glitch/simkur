<div>
    <div class="mb-6">
        <a href="{{ route('classes.index') }}" class="text-gray-800 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Kelas
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 ">Tambah Kelas Baru</h1>
        <p class="mt-1 text-sm text-gray-800 ">Isi form di bawah untuk menambahkan kelas baru</p>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm ">
        <form wire:submit="save">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <!-- Tingkat Kelas -->
                <div>
                    <label for="grade" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Tingkat Kelas <span class="text-red-500">*</span>
                    </label>
                    <select id="grade" wire:model.live="grade" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                    @error('grade') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Jurusan -->
                <div>
                    <label for="major" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Jurusan <span class="text-red-500">*</span>
                    </label>
                    <select id="major" wire:model.live="major" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="MPLB">MPLB - Manajemen Perkantoran dan Layanan Bisnis</option>
                        <option value="AKL">AKL - Akuntansi dan Keuangan Lembaga</option>
                        <option value="BUSANA">BUSANA - Tata Busana</option>
                    </select>
                    @error('major') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Kelas (Auto-generated) -->
                <div class="md:col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" 
                           class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 "
                           readonly>
                    <p class="mt-1 text-xs text-gray-700 ">Nama kelas dibuat otomatis berdasarkan tingkat dan jurusan</p>
                    @error('name') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Tahun Ajaran -->
                <div>
                    <label for="academic_year_id" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </label>
                    <select id="academic_year_id" wire:model="academic_year_id" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">
                                {{ $year->name }} @if($year->is_active) (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Wali Kelas -->
                <div>
                    <label for="homeroom_teacher_id" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Wali Kelas
                    </label>
                    <select id="homeroom_teacher_id" wire:model="homeroom_teacher_id" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="">Belum ditentukan</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('homeroom_teacher_id') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Kapasitas -->
                <div>
                    <label for="capacity" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Kapasitas Siswa <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="capacity" wire:model="capacity" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                           placeholder="36" min="1" max="50">
                    @error('capacity') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Ruangan -->
                <div>
                    <label for="room" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Ruangan
                    </label>
                    <input type="text" id="room" wire:model="room" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                           placeholder="Contoh: R.201">
                    @error('room') <span class="text-sm text-red-600 ">{{ $message }}</span> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 ">
                        Status
                    </label>
                    <select id="is_active" wire:model="is_active" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
                    Simpan
                </button>
                <a href="{{ route('classes.index') }}"
                   class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

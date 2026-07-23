<div>
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Pengguna Baru</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Isi form di bawah untuk menambahkan pengguna baru</p>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
        <form wire:submit="save">
            <!-- Role Selection (Prominent) -->
            <div class="mb-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Pilih Role <span class="text-red-500">*</span>
                </label>
                <select id="role" wire:model.live="role" 
                        class="bg-white border border-blue-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="guru">👨‍🏫 Guru</option>
                    <option value="siswa">👨‍🎓 Siswa</option>
                    <option value="waka_kurikulum">👔 Waka Kurikulum</option>
                    <option value="kepala_sekolah">🎓 Kepala Sekolah</option>
                    @if(auth()->user()->isAdmin())
                        <option value="admin">⚙️ Admin</option>
                    @endif
                </select>
                @error('role') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <!-- Common Fields -->
                <div class="md:col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" wire:change="generateUsername"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Nama lengkap">
                    @error('name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <!-- NIP/NUPTK for Teacher -->
                @if($role === 'guru')
                    <div>
                        <label for="nip_nuptk" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            NIP/NUPTK
                        </label>
                        <input type="text" id="nip_nuptk" wire:model="nip_nuptk"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="198012345678901234">
                        @error('nip_nuptk') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- NIS for Student -->
                @if($role === 'siswa')
                    <div>
                        <label for="nisn" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            NIS
                        </label>
                        <input type="text" id="nisn" wire:model="nisn"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="1234567890" maxlength="10">
                        @error('nisn') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="nis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            NIS
                        </label>
                        <input type="text" id="nis" wire:model="nis"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="12345" maxlength="10">
                        @error('nis') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Username -->
                <div>
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <input type="text" id="username" wire:model="username"
                               class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="username">
                        <button type="button" wire:click="generateUsername"
                                class="px-4 bg-gray-200 hover:bg-gray-300 rounded-r-lg dark:bg-gray-600 dark:hover:bg-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    @error('username') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Email
                    </label>
                    <input type="email" id="email" wire:model="email"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="email@example.com">
                    @error('email') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <!-- Phone for Student -->
                @if($role === 'siswa')
                    <div>
                        <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            No HP Siswa
                        </label>
                        <input type="text" id="no_hp" wire:model="no_hp"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="08123456789">
                        @error('no_hp') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="parent_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nama Orang Tua
                        </label>
                        <input type="text" id="parent_name" wire:model="parent_name"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Nama orang tua/wali">
                        @error('parent_name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="parent_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            No HP Orang Tua
                        </label>
                        <input type="text" id="parent_phone" wire:model="parent_phone"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="08123456789">
                        @error('parent_phone') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Password -->
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="password" wire:model="password"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="password">
                    <p class="mt-1 text-xs text-gray-500">Default: password</p>
                    @error('password') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <!-- Student: Grade & Major -->
                @if($role === 'siswa')
                    <div>
                        <label for="grade" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                        <select id="grade" wire:model="grade"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Kelas</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                        @error('grade') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="major" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Jurusan <span class="text-red-500">*</span>
                        </label>
                        <select id="major" wire:model="major"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Jurusan</option>
                            <option value="MPLB">MPLB</option>
                            <option value="AKL">AKL</option>
                            <option value="BUSANA">BUSANA</option>
                        </select>
                        @error('major') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="class_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Assign ke Kelas (Opsional)
                        </label>
                        <select id="class_id" wire:model="class_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Belum di-assign</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear->name }})</option>
                            @endforeach
                        </select>
                        @error('class_id') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <!-- PKL & Teaching Factory -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Program Khusus</label>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input id="is_pkl" type="checkbox" wire:model="is_pkl"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="is_pkl" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">PKL (Praktik Kerja Lapangan)</label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_teaching_factory" type="checkbox" wire:model="is_teaching_factory"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="is_teaching_factory" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Teaching Factory</label>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Teacher: Beban Mengajar & Taught Majors -->
                @if($role === 'guru')
                    <div>
                        <label for="beban_mengajar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Beban Mengajar (jam/minggu)
                        </label>
                        <input type="number" id="beban_mengajar" wire:model="beban_mengajar" min="0" max="40"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="24">
                        @error('beban_mengajar') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Jurusan yang Diampu
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="major_mplb" type="checkbox" value="MPLB" wire:model="taught_majors"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="major_mplb" class="ml-2 text-sm text-gray-900 dark:text-gray-300">MPLB</label>
                            </div>
                            <div class="flex items-center">
                                <input id="major_akl" type="checkbox" value="AKL" wire:model="taught_majors"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="major_akl" class="ml-2 text-sm text-gray-900 dark:text-gray-300">AKL</label>
                            </div>
                            <div class="flex items-center">
                                <input id="major_busana" type="checkbox" value="BUSANA" wire:model="taught_majors"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="major_busana" class="ml-2 text-sm text-gray-900 dark:text-gray-300">BUSANA</label>
                            </div>
                        </div>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Mata Pelajaran yang Diampu
                        </label>
                        <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($subjects as $subject)
                                    <div class="flex items-center">
                                        <input id="subject_{{ $subject->id }}" type="checkbox" value="{{ $subject->id }}" wire:model="subject_ids"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="subject_{{ $subject->id }}" class="ml-2 text-sm text-gray-900 dark:text-gray-300">
                                            {{ $subject->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status -->
                <div>
                    <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Status
                    </label>
                    <select id="is_active" wire:model="is_active"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600">
                    Simpan
                </button>
                <a href="{{ route('users.index') }}"
                   class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

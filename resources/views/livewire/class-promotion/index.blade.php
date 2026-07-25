<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">🎓 Kenaikan Kelas & Kelulusan</h1>
        <p class="mt-1 text-sm text-gray-800">Proses kenaikan kelas siswa dan kelulusan kelas XII</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Wizard Progress -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center {{ $step >= 1 ? 'text-blue-600' : 'text-gray-400' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    1
                </div>
                <span class="ml-2 font-medium">Pilih Tahun Ajaran</span>
            </div>
            <div class="flex-1 h-1 mx-4 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            <div class="flex items-center {{ $step >= 2 ? 'text-blue-600' : 'text-gray-400' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    2
                </div>
                <span class="ml-2 font-medium">Preview & Konfirmasi</span>
            </div>
            <div class="flex-1 h-1 mx-4 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            <div class="flex items-center {{ $step >= 3 ? 'text-blue-600' : 'text-gray-400' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    3
                </div>
                <span class="ml-2 font-medium">Selesai</span>
            </div>
        </div>
    </div>

    @if ($step === 1)
        <!-- Step 1: Select Academic Years -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Pilih Tahun Ajaran</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun Ajaran Sumber (Saat Ini) <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="fromAcademicYearId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">
                                {{ $year->name }} 
                                @if($year->is_active) (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('fromAcademicYearId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-gray-600">Siswa dari tahun ajaran ini akan diproses</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun Ajaran Tujuan (Baru) <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="toAcademicYearId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                    @error('toAcademicYearId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-gray-600">Siswa akan dipindahkan ke tahun ajaran ini</p>
                </div>
            </div>

            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Perhatian!</h3>
                        <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                            <li>Siswa kelas X & XI akan naik kelas otomatis</li>
                            <li>Siswa kelas XII akan lulus dan menjadi Alumni</li>
                            <li>Proses ini tidak dapat dibatalkan (irreversible)</li>
                            <li>Pastikan tahun ajaran sudah benar sebelum melanjutkan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button 
                    wire:click="goToPreview"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Lanjut ke Preview →
                </button>
            </div>
        </div>
    @endif

    @if ($step === 2)
        <!-- Step 2: Preview & Confirm -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">👀 Preview Kenaikan Kelas</h2>
            
            <div class="mb-6 grid grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-medium">Total Siswa</div>
                    <div class="text-2xl font-bold text-blue-900 mt-1">{{ $previewData['total_students'] ?? 0 }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-medium">Naik Kelas</div>
                    <div class="text-2xl font-bold text-green-900 mt-1">{{ $previewData['total_promoted'] ?? 0 }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-sm text-purple-600 font-medium">Lulus (Alumni)</div>
                    <div class="text-2xl font-bold text-purple-900 mt-1">{{ $previewData['total_graduated'] ?? 0 }}</div>
                </div>
            </div>

            <div class="mb-6">
                <div class="text-sm text-gray-600 mb-2">
                    <strong>Dari:</strong> {{ $previewData['from_year'] ?? '-' }} 
                    <span class="mx-2">→</span> 
                    <strong>Ke:</strong> {{ $previewData['to_year'] ?? '-' }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kelas Sumber</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Jurusan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Jumlah Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Aksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tujuan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($previewData['items'] ?? [] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['source_class'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item['major'] }}</td>
                                <td class="px-4 py-3 text-sm text-center font-semibold text-gray-900">{{ $item['student_count'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($item['action'] === 'graduate')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            🎓 Lulus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ⬆️ Naik Kelas
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($item['action'] === 'graduate')
                                        <span class="font-semibold text-purple-600">ALUMNI</span>
                                    @else
                                        <span class="font-semibold text-green-600">{{ $item['target_class'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada data siswa untuk diproses
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea 
                    wire:model="notes"
                    rows="3"
                    placeholder="Tambahkan catatan atau keterangan untuk proses kenaikan kelas ini..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>

            <div class="mt-6 flex justify-between">
                <button 
                    wire:click="$set('step', 1)"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                >
                    ← Kembali
                </button>
                <button 
                    wire:click="processPromotion"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    onclick="return confirm('Apakah Anda yakin? Proses ini tidak dapat dibatalkan!')"
                >
                    <span wire:loading.remove>✅ Proses Kenaikan Kelas</span>
                    <span wire:loading>⏳ Memproses...</span>
                </button>
            </div>
        </div>
    @endif

    @if ($step === 3)
        <!-- Step 3: Success -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">🎉 Kenaikan Kelas Berhasil!</h3>
                <p class="text-gray-600 mb-6">Proses kenaikan kelas telah selesai dilakukan</p>
                
                <div class="inline-grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-green-50 rounded-lg p-4 text-left">
                        <div class="text-sm text-green-600 font-medium">Siswa Naik Kelas</div>
                        <div class="text-3xl font-bold text-green-900 mt-1">{{ $previewData['total_promoted'] ?? 0 }}</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-left">
                        <div class="text-sm text-purple-600 font-medium">Siswa Lulus</div>
                        <div class="text-3xl font-bold text-purple-900 mt-1">{{ $previewData['total_graduated'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="flex gap-3 justify-center">
                    <a 
                        href="{{ route('class-promotion.history') }}"
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                    >
                        📊 Lihat Riwayat
                    </a>
                    <a 
                        href="{{ route('users.alumni') }}"
                        class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
                    >
                        👨‍🎓 Lihat Alumni
                    </a>
                    <button 
                        wire:click="resetPromotion"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        🔄 Proses Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

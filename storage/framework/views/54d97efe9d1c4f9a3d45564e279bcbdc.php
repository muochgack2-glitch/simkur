<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📓 Jurnal Mengajar</h1>
            <p class="text-gray-800 mt-1">Catat kehadiran siswa dan materi ajar</p>
        </div>
        
        <div class="flex gap-2">
            <!-- Button Laporan -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    @click="open = !open"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>📊 Laporan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                >
                    <!--[if BLOCK]><![endif]--><?php if(auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum() || auth()->user()->isKepalaSekolah()): ?>
                        <button wire:click="openReportModal('teacher-summary')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>👨‍🏫</span>
                            <span class="text-sm">Rekap Jurnal Per Guru</span>
                        </button>
                        <button wire:click="openReportModal('attendance-recap')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>📊</span>
                            <span class="text-sm">Rekap Kehadiran Siswa</span>
                        </button>
                        <button wire:click="openReportModal('material-recap')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>📚</span>
                            <span class="text-sm">Rekap Materi Ajar</span>
                        </button>
                        <button wire:click="openReportModal('missing-journals')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <span>⚠️</span>
                            <span class="text-sm">Monitoring Jurnal Kosong</span>
                        </button>
                        <hr class="my-2">
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <button wire:click="openReportModal('my-journals')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                        <span>📄</span>
                        <span class="text-sm">Export Jurnal Saya</span>
                    </button>
                </div>
            </div>

            <!-- Button Buat Jurnal Baru -->
            <a href="<?php echo e(route('teaching-journal.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Jurnal Baru</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="md:col-span-2">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Cari materi atau nama guru..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Filter Kelas -->
            <select wire:model.live="filterClass" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua Kelas</option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>

            <!-- Filter Mata Pelajaran -->
            <select wire:model.live="filterSubject" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua Mapel</option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>

            <!-- Filter Tanggal -->
            <input 
                type="date" 
                wire:model.live="filterDate"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kelas & Mapel</th>
                        <?php if(auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum()): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Guru</th>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Materi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kehadiran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $journals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900"><?php echo e($journal->date->format('d M Y')); ?></div>
                                    <div class="text-xs text-gray-700"><?php echo e($journal->time_slot); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900"><?php echo e($journal->schoolClass->name); ?></div>
                                    <div class="text-xs text-gray-700"><?php echo e($journal->subject->name); ?></div>
                                </div>
                            </td>
                            <?php if(auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum()): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo e($journal->teacher->name); ?></div>
                                </td>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo e(Str::limit($journal->topic, 50)); ?></div>
                                <!--[if BLOCK]><![endif]--><?php if($journal->learning_objective): ?>
                                    <div class="text-xs text-gray-700 mt-1">Tujuan: <?php echo e(Str::limit($journal->learning_objective, 40)); ?></div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-600 font-medium">✓ <?php echo e($journal->present_count); ?></span>
                                        <span class="text-yellow-600">⚠ <?php echo e($journal->sick_count); ?></span>
                                        <span class="text-blue-600">ⓘ <?php echo e($journal->permission_count); ?></span>
                                        <span class="text-red-600">✗ <?php echo e($journal->absent_count); ?></span>
                                    </div>
                                    <div class="text-xs text-gray-700 mt-1">Total: <?php echo e($journal->total_students); ?> siswa</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="<?php echo e(route('teaching-journal.edit', $journal->id)); ?>" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <?php if(auth()->user()->isAdmin() || $journal->teacher_id === auth()->id()): ?>
                                        <button 
                                            wire:click="delete(<?php echo e($journal->id); ?>)"
                                            wire:confirm="Hapus jurnal mengajar tanggal <?php echo e($journal->date->format('d/m/Y')); ?>?"
                                            class="text-red-600 hover:text-red-900"
                                            title="Hapus"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-700">Belum ada jurnal mengajar</p>
                                <a href="<?php echo e(route('teaching-journal.create')); ?>" class="text-blue-600 hover:underline mt-2 inline-block">Buat jurnal pertama</a>
                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <!--[if BLOCK]><![endif]--><?php if($journals->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($journals->links()); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Legend -->
    <div class="mt-4 bg-white rounded-lg p-4">
        <p class="text-sm text-gray-800 font-medium mb-2">Keterangan Kehadiran:</p>
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="text-green-600">✓ Hadir</span>
            <span class="text-yellow-600">⚠ Sakit</span>
            <span class="text-blue-600">ⓘ Izin</span>
            <span class="text-red-600">✗ Alpha</span>
        </div>
    </div>

    <!-- Report Modal -->
    <!--[if BLOCK]><![endif]--><?php if($showReportModal): ?>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 mb-1">
                    <!--[if BLOCK]><![endif]--><?php switch($reportType):
                        case ('teacher-summary'): ?>
                            👨‍🏫 Rekap Jurnal Per Guru
                            <?php break; ?>
                        <?php case ('attendance-recap'): ?>
                            📊 Rekap Kehadiran Siswa
                            <?php break; ?>
                        <?php case ('material-recap'): ?>
                            📚 Rekap Materi Ajar
                            <?php break; ?>
                        <?php case ('missing-journals'): ?>
                            ⚠️ Monitoring Jurnal Kosong
                            <?php break; ?>
                        <?php case ('my-journals'): ?>
                            📄 Export Jurnal Saya
                            <?php break; ?>
                    <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                </h3>
                <p class="text-sm text-gray-800">Pilih periode laporan yang ingin di-export</p>
            </div>

            <div class="space-y-4">
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        wire:model="reportStartDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reportStartDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input 
                        type="date" 
                        wire:model="reportEndDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reportEndDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!-- Filter Guru (untuk report teacher-summary) -->
                <!--[if BLOCK]><![endif]--><?php if($reportType === 'teacher-summary' && (auth()->user()->canManageUsers() || auth()->user()->isWakaKurikulum())): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guru (opsional)</label>
                        <select wire:model="reportTeacher" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Guru</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Filter Kelas (untuk attendance-recap & material-recap) -->
                <!--[if BLOCK]><![endif]--><?php if(in_array($reportType, ['attendance-recap', 'material-recap'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas (opsional)</label>
                        <select wire:model="reportClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Kelas</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Quick Select Period -->
                <div class="flex gap-2">
                    <button 
                        type="button"
                        wire:click="$set('reportStartDate', '<?php echo e(now()->startOfMonth()->format('Y-m-d')); ?>'); $set('reportEndDate', '<?php echo e(now()->endOfMonth()->format('Y-m-d')); ?>')"
                        class="flex-1 px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded"
                    >
                        Bulan Ini
                    </button>
                    <button 
                        type="button"
                        wire:click="$set('reportStartDate', '<?php echo e(now()->subMonth()->startOfMonth()->format('Y-m-d')); ?>'); $set('reportEndDate', '<?php echo e(now()->subMonth()->endOfMonth()->format('Y-m-d')); ?>')"
                        class="flex-1 px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded"
                    >
                        Bulan Lalu
                    </button>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button 
                    wire:click="closeReportModal"
                    class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition"
                >
                    Batal
                </button>
                <button 
                    wire:click="generateReport"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                >
                    📄 Generate PDF
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>


<?php /**PATH C:\Users\DMCenter\Music\SPMB2\E-KALDIK\resources\views/livewire/teaching-journal/index.blade.php ENDPATH**/ ?>
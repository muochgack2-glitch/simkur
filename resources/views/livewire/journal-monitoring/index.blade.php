<div>
<div wire:poll.120s="refresh" class="min-h-screen bg-gray-50">
    <!-- Clean Sticky Header -->
    <div class="sticky top-0 z-50 bg-white shadow-md border-b-2 border-blue-500">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- Left: Title Section -->
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                            Monitoring Jadwal dan Jurnal Mengajar
                        </h1>
                        <p class="text-sm text-gray-600 mt-0.5">
                            📅 {{ $formattedDate }}
                        </p>
                    </div>
                </div>
                
                <!-- Right: Control Buttons -->
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Loading indicator -->
                    <div wire:loading class="bg-yellow-500 text-white px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="hidden sm:inline">Loading...</span>
                    </div>
                    
                    <!-- Speed control -->
                    <button id="speedBtn" onclick="cycleSpeed()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <span>⚡</span>
                        <span id="speedText">Normal</span>
                    </button>
                    
                    <!-- Auto-scroll toggle -->
                    <button id="autoScrollBtn" onclick="toggleAutoScroll()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <span id="autoScrollIcon">⏸</span>
                        <span id="autoScrollText">Pause</span>
                    </button>
                    
                    <!-- Countdown timer -->
                    <div class="bg-gray-100 border border-gray-300 px-3 py-2 rounded-lg">
                        <div class="text-center">
                            <p class="text-gray-600 text-[10px] font-semibold uppercase">Refresh</p>
                            <p class="font-mono font-bold text-gray-800 text-sm leading-tight" id="refreshCountdown">2:00</p>
                        </div>
                    </div>
                    
                    <!-- Manual refresh -->
                    <button wire:click="refresh" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-4">
        
        <!-- Auto-refresh notification (flash message) -->
        @if (session()->has('auto_refresh'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-lg flex items-center gap-2 shadow-sm">
                <span class="text-xl">🔄</span>
                <span class="text-sm font-medium">{{ session('auto_refresh') }}</span>
            </div>
        @endif
        
        <!-- SECTION 1: CARDS PER KELAS (Overview) -->
        @if(count($classSchedules) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-3">🏫 Jadwal per Kelas</h2>
            
            <!-- Grid Cards Kelas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                
                @foreach($classSchedules as $class)
                @php
                    $colorMap = [
                        'blue' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-500'],
                        'purple' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-500'],
                        'green' => ['border' => 'border-green-500', 'bg' => 'bg-green-500'],
                        'indigo' => ['border' => 'border-indigo-500', 'bg' => 'bg-indigo-500'],
                        'pink' => ['border' => 'border-pink-500', 'bg' => 'bg-pink-500'],
                        'teal' => ['border' => 'border-teal-500', 'bg' => 'bg-teal-500'],
                        'cyan' => ['border' => 'border-cyan-500', 'bg' => 'bg-cyan-500'],
                        'rose' => ['border' => 'border-rose-500', 'bg' => 'bg-rose-500'],
                        'emerald' => ['border' => 'border-emerald-500', 'bg' => 'bg-emerald-500'],
                    ];
                    $colors = $colorMap[$class['color']] ?? $colorMap['blue'];
                @endphp
                <div class="bg-white rounded-lg shadow {{ $colors['border'] }} border-t-4 hover:shadow-md transition cursor-pointer class-card"
                     onclick="openClassModal('{{ $class['class_name'] }}', {{ json_encode($class['subjects']) }}, {{ $class['filled_count'] }}, {{ $class['not_filled_count'] }})">
                    <div class="{{ $colors['bg'] }} px-3 py-2">
                        <h3 class="font-bold text-white text-sm">{{ $class['class_name'] }}</h3>
                    </div>
                    <div class="p-2 space-y-1.5">
                        @foreach($class['subjects'] as $subject)
                        <div class="text-xs">
                            <div class="flex items-start gap-1">
                                <span class="{{ $subject['is_filled'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $subject['is_filled'] ? '✓' : '✗' }}
                                </span>
                                <div class="flex-1">
                                    <span class="text-gray-700 font-medium">{{ $subject['name'] }}</span>
                                    <span class="text-gray-500"> • {{ $subject['jp_range'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="text-xs text-gray-400 pt-1 border-t">
                            <span class="text-green-600 font-bold">✓ {{ $class['filled_count'] }}</span>
                            <span class="text-red-600 font-bold ml-2">✗ {{ $class['not_filled_count'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-300">
        @endif

        <!-- SECTION 2: CARDS PER GURU - BELUM ISI LENGKAP -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <h2 class="text-xl font-bold text-gray-800">⚠️ Guru Belum Isi Lengkap</h2>
                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">{{ $notStartedCount }} Guru</span>
            </div>
            
            @if(count($teachersNotStarted) > 0)
            <!-- Grid Guru - Belum Lengkap -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                
                @foreach($teachersNotStarted as $teacher)
                @php
                    // Tentukan warna berdasarkan percentage
                    $isPartial = $teacher['percentage'] > 0; // Ada yang sudah diisi
                    $headerColorFrom = $isPartial ? 'from-yellow-500' : 'from-red-500';
                    $headerColorTo = $isPartial ? 'to-yellow-600' : 'to-red-600';
                    $borderColor = $isPartial ? 'border-yellow-500' : 'border-red-500';
                    $textColor = $isPartial ? 'text-yellow-600' : 'text-red-600';
                    $bgColor = $isPartial ? 'bg-yellow-200' : 'bg-red-200';
                    $bgColorLight = $isPartial ? 'bg-yellow-50' : 'bg-red-50';
                    $borderColorLight = $isPartial ? 'border-yellow-200' : 'border-red-200';
                    $progressColor = $isPartial ? 'bg-yellow-500' : 'bg-red-500';
                    $iconColor = $isPartial ? 'text-yellow-500' : 'text-red-500';
                    $icon = $isPartial ? '⚠' : '✗';
                @endphp
                <div class="bg-white rounded-lg shadow {{ $borderColor }} border-l-4 hover:shadow-md transition teacher-card">
                    <div class="bg-gradient-to-r {{ $headerColorFrom }} {{ $headerColorTo }} px-3 py-2">
                        <h3 class="text-white font-bold text-xs truncate" title="{{ $teacher['name'] }}">
                            {{ $teacher['name'] }}
                        </h3>
                    </div>
                    <div class="p-2">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-lg font-bold {{ $textColor }}">{{ $teacher['filled_jp'] }}/{{ $teacher['total_jp'] }} JP</p>
                            <span class="{{ $iconColor }} text-2xl">{{ $icon }}</span>
                        </div>
                        <div class="{{ $bgColor }} rounded-full h-1.5 mb-2">
                            <div class="{{ $progressColor }} h-full" style="width: {{ $teacher['percentage'] }}%"></div>
                        </div>
                        <div class="{{ $bgColorLight }} rounded p-1.5 border {{ $borderColorLight }} text-xs space-y-1 max-h-24 overflow-y-auto">
                            @foreach($teacher['schedules'] as $schedule)
                            <div class="text-xs leading-relaxed" title="{{ $schedule['class'] }} - {{ $schedule['subject'] }} ({{ $schedule['time_slots'] }})">
                                <span class="{{ $schedule['is_filled'] ? 'text-green-600' : 'text-red-600' }} font-bold">
                                    {{ $schedule['is_filled'] ? '✓' : '✗' }}
                                </span>
                                <span class="text-gray-700 font-medium">{{ $schedule['class'] }}</span>
                                <span class="text-gray-500">-</span>
                                <span class="text-gray-900">{{ $schedule['subject'] }}</span>
                                <span class="text-gray-500 text-[10px]">({{ $schedule['time_slots'] }})</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
            @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-green-400 text-6xl mb-3">🎉</div>
                <p class="text-gray-700 font-medium">Semua guru sudah mengisi jurnal lengkap!</p>
                <p class="text-gray-400 text-sm mt-1">100% completion</p>
            </div>
            @endif
        </div>

        <!-- SECTION 3: GURU SUDAH ISI LENGKAP (100%) -->
        <div>
            <div class="flex items-center gap-2 mb-3">
                <h2 class="text-xl font-bold text-gray-800">✅ Guru Sudah Isi Lengkap</h2>
                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold">{{ $completedCount }} Guru</span>
            </div>
            
            @if(count($teachersCompleted) > 0)
            <!-- Grid Guru - Sudah Lengkap -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                
                @foreach($teachersCompleted as $teacher)
                <div class="bg-white rounded-lg shadow border-l-4 border-green-500 hover:shadow-md transition teacher-card">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-3 py-2">
                        <h3 class="text-white font-bold text-xs truncate" title="{{ $teacher['name'] }}">
                            {{ $teacher['name'] }}
                        </h3>
                    </div>
                    <div class="p-2">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-lg font-bold text-green-600">{{ $teacher['filled_jp'] }}/{{ $teacher['total_jp'] }} JP</p>
                            <span class="text-green-500 text-2xl">{{ $teacher['percentage'] == 100 ? '✓' : '⚠' }}</span>
                        </div>
                        <div class="bg-green-200 rounded-full h-1.5 mb-2">
                            <div class="bg-green-500 h-full rounded-full" style="width: {{ $teacher['percentage'] }}%"></div>
                        </div>
                        <div class="bg-green-50 rounded p-1.5 border border-green-200 text-xs space-y-1 max-h-24 overflow-y-auto">
                            @foreach($teacher['schedules'] as $schedule)
                            <div class="text-xs leading-relaxed" title="{{ $schedule['class'] }} - {{ $schedule['subject'] }} ({{ $schedule['time_slots'] }})">
                                <span class="{{ $schedule['is_filled'] ? 'text-green-600' : 'text-red-600' }} font-bold">
                                    {{ $schedule['is_filled'] ? '✓' : '✗' }}
                                </span>
                                <span class="text-gray-700 font-medium">{{ $schedule['class'] }}</span>
                                <span class="text-gray-500">-</span>
                                <span class="text-gray-900">{{ $schedule['subject'] }}</span>
                                <span class="text-gray-500 text-[10px]">({{ $schedule['time_slots'] }})</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
            @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-gray-400 text-6xl mb-3">📝</div>
                <p class="text-gray-500 font-medium">Belum ada guru yang mengisi jurnal hari ini</p>
                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada yang mengisi</p>
            </div>
            @endif
        </div>

    </div>

    <!-- Modal Detail Kelas -->
    <div id="classModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 id="modalClassName" class="text-xl font-bold">📚 Jadwal Kelas</h3>
                    <p class="text-blue-100 text-sm">{{ $formattedDate }}</p>
                </div>
                <button onclick="closeClassModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="modalBody" class="p-6 overflow-y-auto max-h-[60vh]">
                <!-- Content will be populated by JavaScript -->
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button onclick="closeClassModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-300 text-sm">SMK PGRI Blora © 2026</p>
            <p class="text-gray-400 text-xs mt-1">Terakhir diperbarui: {{ $lastRefresh->locale('id')->diffForHumans() }}</p>
        </div>
    </div>
</div>

<!-- Floating Live JP Indicator - OUTSIDE Livewire component to prevent disappearing on refresh -->
<div id="liveJpIndicator" class="fixed bottom-6 right-6 z-40 hidden">
    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-2xl shadow-2xl border-4 border-red-300 animate-pulse-slow">
        <div class="flex items-center gap-3">
            <div class="text-3xl" id="jpIcon">🔴</div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider opacity-90" id="jpLabel">SEDANG BERLANGSUNG</div>
                <div class="text-xl font-bold" id="jpName">JP 1</div>
                <div class="text-sm opacity-90" id="jpTime">07:00 - 07:40</div>
                <div class="text-xs mt-1 bg-white/20 px-2 py-1 rounded-full inline-block" id="jpCountdown">Sisa 25 menit</div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
    <script>
        // Time schedule data from backend
        const timeSchedule = @json($timeSchedule);
        
        // ========================================
        // AUTO-SCROLL CONFIGURATION
        // ========================================
        // Ubah nilai berikut untuk adjust kecepatan:
        // scrollSpeed: 0.3 (sangat lambat), 0.5 (lambat), 1 (normal), 2 (cepat)
        // pauseAtBottom: 2000-5000 ms (pause di bawah sebelum scroll ke atas)
        // pauseAtTop: 3000-8000 ms (pause di atas sebelum mulai scroll lagi)
        
        let scrollSpeed = 0.5;      // Default: 0.5 pixel per frame (lebih lambat)
        let pauseAtBottom = 3000;   // Default: 3 detik di bawah
        let pauseAtTop = 5000;      // Default: 5 detik di atas (pause lebih lama)
        let isScrolling = false;
        let isPaused = false;

        // Preset kecepatan (diperlambat semua)
        const speedPresets = {
            slow: { speed: 0.3, name: 'Lambat' },
            normal: { speed: 0.5, name: 'Normal' },
            fast: { speed: 1, name: 'Cepat' }
        };
        let currentSpeed = 'normal';

        // Toggle auto-scroll on/off
        let autoScrollEnabled = true;

        function autoScroll() {
            if (!autoScrollEnabled || isPaused) {
                requestAnimationFrame(autoScroll);
                return;
            }

            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            const maxScroll = scrollHeight - clientHeight;
            const currentScroll = window.scrollY;

            if (!isScrolling) {
                if (currentScroll < maxScroll) {
                    window.scrollBy(0, scrollSpeed);
                    requestAnimationFrame(autoScroll);
                } else {
                    isScrolling = true;
                    setTimeout(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => {
                            isScrolling = false;
                            requestAnimationFrame(autoScroll);
                        }, pauseAtTop);
                    }, pauseAtBottom);
                }
            } else {
                // Tetap loop meskipun sedang isScrolling, biar responsive
                requestAnimationFrame(autoScroll);
            }
        }

        function toggleAutoScroll() {
            autoScrollEnabled = !autoScrollEnabled;
            const btn = document.getElementById('autoScrollBtn');
            const icon = document.getElementById('autoScrollIcon');
            const text = document.getElementById('autoScrollText');
            
            if (autoScrollEnabled) {
                btn.classList.remove('bg-gray-600');
                btn.classList.add('bg-blue-600');
                icon.textContent = '⏸';
                text.textContent = 'Pause Scroll';
                requestAnimationFrame(autoScroll);
            } else {
                btn.classList.remove('bg-blue-600');
                btn.classList.add('bg-gray-600');
                icon.textContent = '▶';
                text.textContent = 'Start Scroll';
            }
        }

        // Cycle through speed presets
        function cycleSpeed() {
            const speeds = ['slow', 'normal', 'fast'];
            const currentIndex = speeds.indexOf(currentSpeed);
            const nextIndex = (currentIndex + 1) % speeds.length;
            currentSpeed = speeds[nextIndex];
            scrollSpeed = speedPresets[currentSpeed].speed;
            
            // Update button text
            const speedBtn = document.getElementById('speedBtn');
            const speedText = document.getElementById('speedText');
            if (speedBtn && speedText) {
                speedText.textContent = speedPresets[currentSpeed].name;
                
                // Visual feedback
                speedBtn.classList.add('scale-110');
                setTimeout(() => speedBtn.classList.remove('scale-110'), 200);
            }
            
            // Speed berubah langsung efektif karena scrollSpeed sudah diupdate
            // Auto-scroll loop akan otomatis pakai speed baru di frame berikutnya
            console.log('[MONITORING] Speed changed to:', speedPresets[currentSpeed].name, '(' + scrollSpeed + 'px/frame)');
        }

        // Pause on hover
        document.addEventListener('mouseenter', (e) => {
            // Check if e.target is an Element before calling closest()
            if (e.target && e.target.nodeType === 1) {
                const card = e.target.closest('.teacher-card, .class-card');
                if (card) {
                    isPaused = true;
                }
            }
        }, true);

        document.addEventListener('mouseleave', (e) => {
            // Check if e.target is an Element before calling closest()
            if (e.target && e.target.nodeType === 1) {
                const card = e.target.closest('.teacher-card, .class-card');
                if (card) {
                    isPaused = false;
                }
            }
        }, true);

        // Modal functions
        function openClassModal(className, subjects, filledCount, notFilledCount) {
            document.getElementById('modalClassName').textContent = '📚 Jadwal Kelas ' + className;
            
            let html = '<div class="space-y-3">';
            
            subjects.forEach(subject => {
                const bgColor = subject.is_filled ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                const iconColor = subject.is_filled ? 'text-green-600' : 'text-red-600';
                const icon = subject.is_filled ? '✓' : '✗';
                const statusText = subject.is_filled ? 'Jurnal sudah diisi' : 'Belum mengisi jurnal';
                const statusColor = subject.is_filled ? 'text-green-700' : 'text-red-700';
                
                html += `
                    <div class="${bgColor} border rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold">${subject.jp_range}</span>
                                    <span class="${iconColor} text-xl">${icon}</span>
                                </div>
                                <h4 class="font-bold text-gray-800">${subject.name}</h4>
                                <p class="text-sm text-gray-600">Guru: ${subject.teacher}</p>
                            </div>
                        </div>
                        <p class="text-xs ${statusColor} font-semibold">${icon} ${statusText}</p>
                    </div>
                `;
            });
            
            html += '</div>';
            
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('classModal').classList.remove('hidden');
            autoScrollEnabled = false;
        }
        
        function closeClassModal() {
            document.getElementById('classModal').classList.add('hidden');
            autoScrollEnabled = true;
            requestAnimationFrame(autoScroll);
        }

        // Close modal on background click
        document.getElementById('classModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeClassModal();
            }
        });

        // Start auto-scroll on page load
        window.addEventListener('load', () => {
            setTimeout(() => {
                requestAnimationFrame(autoScroll);
            }, 2000);
        });

        // Listen for Livewire refresh
        Livewire.on('refreshed', () => {
            const timestamp = new Date().toLocaleTimeString();
            console.log('[MONITORING] Data refreshed at:', timestamp);
            
            // Visual feedback
            const countdownEl = document.getElementById('refreshCountdown');
            if (countdownEl) {
                countdownEl.classList.add('scale-110', 'text-green-300');
                setTimeout(() => {
                    countdownEl.classList.remove('scale-110', 'text-green-300');
                }, 500);
            }
            
            // Reset countdown
            resetRefreshCountdown();
            
            // Re-update Live JP Indicator after refresh
            updateLiveJpIndicator();
        });
        
        // Listen for Livewire component updates (polling)
        document.addEventListener('livewire:update', () => {
            console.log('[MONITORING] Livewire component updated at:', new Date().toLocaleTimeString());
            // Re-update Live JP Indicator after DOM update
            setTimeout(updateLiveJpIndicator, 100);
        });
        
        // Listen for Livewire init
        document.addEventListener('livewire:init', () => {
            console.log('[MONITORING] Livewire initialized');
            console.log('[MONITORING] Polling enabled: wire:poll.120s="refresh" (2 minutes)');
        });
        
        // Listen for Livewire finished (after any update)
        document.addEventListener('livewire:navigated', () => {
            console.log('[MONITORING] Livewire navigated - re-initializing JP indicator');
            updateLiveJpIndicator();
        });
        
        // Countdown timer for auto-refresh (2 minutes = 120 seconds)
        let refreshCountdownSeconds = 120;
        let countdownInterval;
        
        function startRefreshCountdown() {
            refreshCountdownSeconds = 120; // Reset to 2 minutes
            updateCountdownDisplay();
            
            // Clear existing interval if any
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            // Start new countdown
            countdownInterval = setInterval(() => {
                refreshCountdownSeconds--;
                
                if (refreshCountdownSeconds <= 0) {
                    refreshCountdownSeconds = 120; // Reset to 2 minutes
                }
                
                updateCountdownDisplay();
            }, 1000); // Update every second
        }
        
        function updateCountdownDisplay() {
            const minutes = Math.floor(refreshCountdownSeconds / 60);
            const seconds = refreshCountdownSeconds % 60;
            const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            const countdownEl = document.getElementById('refreshCountdown');
            if (countdownEl) {
                countdownEl.textContent = display;
                
                // Visual warning when close to refresh (last 10 seconds)
                if (refreshCountdownSeconds <= 10) {
                    countdownEl.classList.add('animate-pulse', 'text-yellow-300');
                } else {
                    countdownEl.classList.remove('animate-pulse', 'text-yellow-300');
                }
            }
        }
        
        function resetRefreshCountdown() {
            refreshCountdownSeconds = 120; // 2 minutes
            updateCountdownDisplay();
        }
        
        // Start countdown and auto-scroll on page load
        window.addEventListener('load', () => {
            startRefreshCountdown();
            updateLiveJpIndicator(); // Initial update
            startLiveJpTimer(); // Start real-time updates
            
            setTimeout(() => {
                requestAnimationFrame(autoScroll);
            }, 2000);
        });
        
        // ========================================
        // LIVE JP INDICATOR
        // ========================================
        
        let lastLoggedJp = null; // Track last logged JP to avoid duplicate logs
        
        function updateLiveJpIndicator() {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes(); // Minutes since midnight
            
            const indicator = document.getElementById('liveJpIndicator');
            const icon = document.getElementById('jpIcon');
            const label = document.getElementById('jpLabel');
            const name = document.getElementById('jpName');
            const time = document.getElementById('jpTime');
            const countdown = document.getElementById('jpCountdown');
            const container = indicator?.querySelector('div > div');
            
            if (!indicator || !timeSchedule || timeSchedule.length === 0) {
                return;
            }
            
            // Find current time slot
            let currentSlot = null;
            for (const slot of timeSchedule) {
                const [startHour, startMin] = slot.start.split(':').map(Number);
                const [endHour, endMin] = slot.end.split(':').map(Number);
                const startTime = startHour * 60 + startMin;
                const endTime = endHour * 60 + endMin;
                
                if (currentTime >= startTime && currentTime < endTime) {
                    currentSlot = slot;
                    break;
                }
            }
            
            if (currentSlot) {
                // Show indicator
                indicator.classList.remove('hidden');
                
                // Determine if it's break time or teaching time
                const isBreak = currentSlot.order === 1 || currentSlot.order === 5 || currentSlot.order === 10;
                
                if (isBreak) {
                    // Break time styling
                    container.className = 'bg-gradient-to-r from-orange-500 to-yellow-500 text-white px-6 py-4 rounded-2xl shadow-2xl border-4 border-orange-300 animate-pulse-slow';
                    icon.textContent = '☕';
                    label.textContent = 'ISTIRAHAT';
                } else {
                    // Teaching time styling
                    container.className = 'bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-2xl shadow-2xl border-4 border-red-300 animate-pulse-slow';
                    icon.textContent = '🔴';
                    label.textContent = 'SEDANG BERLANGSUNG';
                }
                
                // Extract JP number from name (e.g., "Jam ke-3 (08:40 - 09:20)" -> "JP 3")
                let jpNumber = '';
                const match = currentSlot.name.match(/Jam ke-(\d+)/);
                if (match) {
                    jpNumber = 'JP ' + match[1];
                } else {
                    jpNumber = currentSlot.name;
                }
                
                name.textContent = jpNumber;
                time.textContent = `${currentSlot.start} - ${currentSlot.end}`;
                
                // Calculate remaining time
                const [endHour, endMin] = currentSlot.end.split(':').map(Number);
                const endTime = endHour * 60 + endMin;
                const remaining = endTime - currentTime;
                
                if (remaining > 0) {
                    countdown.textContent = `Sisa ${remaining} menit`;
                } else {
                    countdown.textContent = 'Hampir selesai';
                }
                
                // Only log when JP changes (not every minute)
                if (lastLoggedJp !== jpNumber) {
                    console.log(`[LIVE JP] Currently: ${jpNumber} (${currentSlot.start}-${currentSlot.end}), remaining: ${remaining} min`);
                    lastLoggedJp = jpNumber;
                }
            } else {
                // No active slot, hide indicator
                indicator.classList.add('hidden');
                
                // Only log when status changes (not every minute)
                if (lastLoggedJp !== null) {
                    console.log('[LIVE JP] No active time slot at current time');
                    lastLoggedJp = null;
                }
            }
        }
        
        function startLiveJpTimer() {
            // Update every 60 seconds (needed for accurate countdown)
            setInterval(updateLiveJpIndicator, 60000);
        }
    </script>
@endpush

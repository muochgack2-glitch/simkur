<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kalender Kegiatan</h1>
            <p class="text-gray-600 mt-1">
                @if($activeYear)
                    Tahun Pelajaran: <span class="font-semibold">{{ $activeYear->year }}</span>
                @else
                    <span class="text-red-600">Belum ada tahun pelajaran aktif</span>
                @endif
            </p>
        </div>
        
        @if(auth()->user()->canManageActivities() && $activeYear)
            <div class="flex items-center space-x-3">
                <a href="{{ route('activities.export') }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export</span>
                </a>
                <a href="{{ route('activities.import') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>Import</span>
                </a>
                <a href="{{ route('activities.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Kegiatan</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filters & View Switcher -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 gap-4">
            <!-- Search -->
            <div class="flex-1">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Cari kegiatan..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2">
                <select 
                    wire:model.live="filterSemester"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="filterType"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Semua Jenis</option>
                    @foreach($activityTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- View Switcher -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button 
                    wire:click="$set('view', 'list')"
                    class="px-3 py-2 text-sm font-medium rounded-md {{ $view === 'list' ? 'bg-white text-blue-600 shadow' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
                <button 
                    wire:click="$set('view', 'month')"
                    class="px-3 py-2 text-sm font-medium rounded-md {{ $view === 'month' ? 'bg-white text-blue-600 shadow' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @if(!$activeYear)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
            <p>Belum ada tahun pelajaran aktif. Silakan aktifkan tahun pelajaran terlebih dahulu.</p>
        </div>
    @else
        <!-- List View -->
        @if($view === 'list')
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                            @if(auth()->user()->canManageActivities())
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $activity->color ?: ($activity->activityType->default_color ?? '#6B7280') }}"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $activity->name }}</div>
                                            @if($activity->description)
                                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($activity->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded" style="background-color: {{ $activity->activityType->default_color ?? '#6B7280' }}22; color: {{ $activity->activityType->default_color ?? '#6B7280' }}">
                                        {{ $activity->activityType->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeColor = $activity->getTargetGradesBadgeColor();
                                        $badgeClasses = match($badgeColor) {
                                            'green' => 'bg-green-100 text-green-800',
                                            'blue' => 'bg-blue-100 text-blue-800',
                                            'yellow' => 'bg-yellow-100 text-yellow-800',
                                            'purple' => 'bg-purple-100 text-purple-800',
                                            'orange' => 'bg-orange-100 text-orange-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        {{ $activity->getTargetGradesLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $activity->start_date->format('d M Y') }}
                                    @if($activity->start_date->format('Y-m-d') !== $activity->end_date->format('Y-m-d'))
                                        - {{ $activity->end_date->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $activity->semester->name }}
                                </td>
                                @if(auth()->user()->canManageActivities())
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('activities.edit', $activity->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            <button 
                                                wire:click="delete({{ $activity->id }})"
                                                wire:confirm="Hapus kegiatan {{ $activity->name }}?"
                                                class="text-red-600 hover:text-red-900"
                                                title="Hapus"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500">Belum ada kegiatan</p>
                                    @if(auth()->user()->canManageActivities())
                                        <a href="{{ route('activities.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                                            Tambah Kegiatan Pertama
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($activities->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Calendar View -->
        @if($view === 'month')
            <div class="bg-white rounded-lg shadow p-6">
                <div id="calendar" data-events="{{ json_encode($events) }}"></div>
            </div>
        @endif
    @endif
</div>

<!-- Calendar Scripts -->
<script>
    (function() {
        let calendarInstance = null;
        
        // Function to download current month PDF from calendar
        window.downloadCurrentMonthPdf = function() {
            let year = {{ now()->year }};
            let month = {{ now()->month }};
            
            // Try to get date from FullCalendar if available
            if (calendarInstance) {
                try {
                    const currentDate = calendarInstance.getDate();
                    year = currentDate.getFullYear();
                    month = currentDate.getMonth() + 1; // JavaScript months are 0-indexed
                } catch(e) {
                    console.log('Could not get calendar date, using current date:', e);
                }
            }
            
            // Redirect to download
            window.location.href = '/activities/export?type=monthly&format=pdf&year=' + year + '&month=' + month;
            
            // Update label
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const label = document.getElementById('download-month-label');
            if (label) {
                label.textContent = monthNames[month - 1] + ' ' + year;
            }
        };
        
        function initFullCalendar(eventsData) {
            const calendarEl = document.getElementById('calendar');
            
            if (!calendarEl) {
                console.log('Calendar element not found');
                return;
            }
            
            // Destroy existing calendar
            if (calendarInstance) {
                try {
                    calendarInstance.destroy();
                } catch(e) {
                    console.log('Destroy error:', e);
                }
                calendarInstance = null;
            }
            
            // Clear element
            calendarEl.innerHTML = '';
            
            // Check if initCalendar helper exists
            if (typeof window.initCalendar !== 'function') {
                console.error('window.initCalendar is not defined. Make sure calendar.js is loaded.');
                return;
            }
            
            console.log('Initializing calendar with', eventsData.length, 'events');
            
            // Initialize calendar
            try {
                calendarInstance = window.initCalendar('calendar', eventsData, {
                    initialView: 'dayGridMonth',
                    onEventClick: function(event) {
                        // Direct redirect to edit page
                        window.location.href = '/activities/' + event.id + '/edit';
                    },
                    onDateClick: function(date) {
                        @if(auth()->user()->canManageActivities())
                            window.location.href = '/activities/create?date=' + date.toISOString().split('T')[0];
                        @endif
                    }
                });
                
                // Navigate to specific month if provided
                @if($initialMonth ?? false)
                    const targetDate = new Date('{{ $initialMonth }}-01');
                    if (!isNaN(targetDate.getTime())) {
                        calendarInstance.gotoDate(targetDate);
                        console.log('Calendar navigated to:', '{{ $initialMonth }}');
                    }
                @endif
                
                console.log('Calendar initialized successfully with', eventsData.length, 'events');
            } catch(e) {
                console.error('Failed to initialize calendar:', e);
            }
        }
        
        // Check and init calendar on page load
        function checkAndInitCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                // Get events data from data attribute or embedded script
                const eventsJson = calendarEl.getAttribute('data-events');
                if (eventsJson) {
                    try {
                        const events = JSON.parse(eventsJson);
                        initFullCalendar(events);
                    } catch(e) {
                        console.error('Failed to parse events data:', e);
                    }
                }
            }
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkAndInitCalendar);
        } else {
            setTimeout(checkAndInitCalendar, 100);
        }
        
        // Listen for Livewire init-calendar event
        document.addEventListener('livewire:init', () => {
            Livewire.on('init-calendar', (eventData) => {
                console.log('Received init-calendar event from Livewire');
                // Get events from event data or check DOM
                setTimeout(checkAndInitCalendar, 150);
            });
        });
        
        // Also check after any Livewire update
        document.addEventListener('livewire:navigated', checkAndInitCalendar);
        
        // Expose function globally for manual trigger
        window.refreshCalendar = checkAndInitCalendar;
    })();
</script>

@if($view === 'month')
    <script>
        // Trigger calendar init after this script loads
        if (typeof window.refreshCalendar === 'function') {
            setTimeout(window.refreshCalendar, 200);
        }
    </script>
@endif

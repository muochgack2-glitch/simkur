<?php

namespace App\Livewire\Activity;

use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Semester;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSemester = '';
    public $filterType = '';
    public $view = 'month'; // list, month, year
    public $month = null; // For focusing on specific month after edit

    protected $queryString = [
        'search' => ['except' => ''],
        'filterSemester' => ['except' => ''],
        'filterType' => ['except' => ''],
        'view' => ['except' => 'month'],
        'month' => ['except' => null],
    ];

    public function updatedView()
    {
        if ($this->view === 'month') {
            $this->dispatch('init-calendar');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSemester()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (!auth()->user()->canManageActivities()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus kegiatan.');
            return;
        }

        $activity = Activity::findOrFail($id);
        $name = $activity->name;
        
        $activity->delete();

        ActivityLog::createLog(
            action: 'delete',
            modelType: 'Activity',
            modelId: $id,
            description: "Menghapus kegiatan: {$name}"
        );

        session()->flash('success', 'Kegiatan berhasil dihapus!');
        
        // Redirect back with current view preserved
        return redirect()->route('activities.index', ['view' => $this->view]);
    }

    #[Layout('components.layouts.app')]
    #[Title('Kalender Kegiatan - e-KALDIK')]
    public function render()
    {
        // Get active academic year
        $activeYear = AcademicYear::active()->first();
        
        $query = Activity::with(['activityType', 'semester.academicYear', 'creator']);

        // Filter by active academic year
        if ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by semester
        if ($this->filterSemester) {
            $query->where('semester_id', $this->filterSemester);
        }

        // Filter by type
        if ($this->filterType) {
            $query->where('activity_type_id', $this->filterType);
        }

        // Filter by grade for students
        if (auth()->user()->role === 'siswa' && auth()->user()->grade) {
            $query->forGrade(auth()->user()->grade);
        }

        $activities = $query->orderBy('start_date')->paginate(15);

        // Get semesters for filter
        $semesters = $activeYear ? $activeYear->semesters : collect();
        
        // Get activity types for filter
        $activityTypes = ActivityType::orderBy('name')->get();

        // Get events for calendar (if needed)
        $events = [];
        if ($this->view !== 'list') {
            $activitiesQuery = Activity::with('activityType')
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->when($this->filterSemester, fn($q) => $q->where('semester_id', $this->filterSemester))
                ->when($this->filterType, fn($q) => $q->where('activity_type_id', $this->filterType));
            
            // Filter by grade for students
            if (auth()->user()->role === 'siswa' && auth()->user()->grade) {
                $activitiesQuery->forGrade(auth()->user()->grade);
            }
            
            $activities = $activitiesQuery->get();
            
            foreach ($activities as $activity) {
                $color = $activity->color ?: ($activity->activityType->default_color ?? '#3B82F6');
                
                // Get icon emoji based on activity type code
                $iconMap = [
                    'LAP' => '🌙',      // Libur Awal Puasa
                    'PKL' => '💼',      // PKL
                    'MPLS' => '🎓',     // MPLS
                    'PTS' => '📝',      // PTS (Ujian)
                    'PAS' => '📋',      // PAS (Ujian)
                    'PAT' => '📄',      // PAT (Ujian)
                    'ANBK' => '💻',     // ANBK (Ujian)
                    'LIBNAS' => '🏖️',   // Libur Nasional
                    'LIBSEM' => '🏝️',   // Libur Semester
                    'RAPAT' => '👥',    // Rapat Guru
                    'KEGIATAN' => '🎯', // Kegiatan Sekolah
                    'UPACARA' => '🚩',  // Upacara
                    'TKA' => '✏️',      // TKA
                    'RAPOR' => '📜',    // Pembagian Rapor
                ];
                
                $icon = $iconMap[$activity->activityType->code] ?? '📅';
                $eventTitle = $icon . ' ' . $activity->name;
                
                $start = \Carbon\Carbon::parse($activity->start_date);
                $end = \Carbon\Carbon::parse($activity->end_date);
                
                // Skip weekends - split continuous events into weekday segments
                $currentStart = null;
                
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $isWeekend = $date->dayOfWeek === 0 || $date->dayOfWeek === 6;
                    
                    if (!$isWeekend) {
                        // Weekday - start or continue event segment
                        if ($currentStart === null) {
                            $currentStart = $date->copy();
                        }
                    } else {
                        // Weekend - end current segment if any
                        if ($currentStart !== null) {
                            $events[] = [
                                'id' => $activity->id,
                                'title' => $eventTitle,
                                'start' => $currentStart->format('Y-m-d'),
                                'end' => $date->format('Y-m-d'), // Exclusive end (before weekend)
                                'backgroundColor' => $color,
                                'borderColor' => $color,
                                'textColor' => '#ffffff',
                                'extendedProps' => [
                                    'type' => $activity->activityType->name,
                                    'description' => $activity->description,
                                    'target_grades' => $activity->target_grades,
                                    'target_grades_label' => $activity->getTargetGradesLabel(),
                                    'badge_color' => $activity->getTargetGradesBadgeColor(),
                                    'dateRange' => $activity->start_date->format('d M Y') . 
                                                   ($activity->start_date->format('Y-m-d') !== $activity->end_date->format('Y-m-d') 
                                                       ? ' - ' . $activity->end_date->format('d M Y') 
                                                       : ''),
                                ],
                            ];
                            $currentStart = null;
                        }
                    }
                }
                
                // Add final segment if exists
                if ($currentStart !== null) {
                    $events[] = [
                        'id' => $activity->id,
                        'title' => $eventTitle,
                        'start' => $currentStart->format('Y-m-d'),
                        'end' => $end->copy()->addDay()->format('Y-m-d'), // Exclusive end
                        'backgroundColor' => $color,
                        'borderColor' => $color,
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'type' => $activity->activityType->name,
                            'description' => $activity->description,
                            'target_grades' => $activity->target_grades,
                            'target_grades_label' => $activity->getTargetGradesLabel(),
                            'badge_color' => $activity->getTargetGradesBadgeColor(),
                            'dateRange' => $activity->start_date->format('d M Y') . 
                                           ($activity->start_date->format('Y-m-d') !== $activity->end_date->format('Y-m-d') 
                                               ? ' - ' . $activity->end_date->format('d M Y') 
                                               : ''),
                        ],
                    ];
                }
            }
        }

        return view('livewire.activity.index', [
            'activities' => $activities,
            'semesters' => $semesters,
            'activityTypes' => $activityTypes,
            'activeYear' => $activeYear,
            'events' => $events,
            'initialMonth' => $this->month,
        ]);
    }
}

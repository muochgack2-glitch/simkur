# STRUKTUR FOLDER PROJECT - SISTEM PKL

**Project:** e-KALDIK - Modul PKL  
**Version:** 1.0  
**Date:** 2026-06-23

---

## 📁 PROJECT STRUCTURE

```
e-KALDIK/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── PKL/                          # PKL Controllers
│   │   │       ├── PKLWaveController.php
│   │   │       ├── PKLPlacementController.php
│   │   │       ├── PKLStudentController.php
│   │   │       ├── PKLSupervisorController.php
│   │   │       ├── PKLMonitoringController.php
│   │   │       └── PKLReportController.php
│   │   │
│   │   └── Middleware/
│   │       └── CheckPKLAccess.php            # Authorization middleware
│   │
│   ├── Livewire/
│   │   └── PKL/                              # Livewire Components
│   │       ├── Wave/
│   │       │   ├── Index.php                 # List waves
│   │       │   ├── Create.php                # Create wave form
│   │       │   ├── Edit.php                  # Edit wave form
│   │       │   └── Show.php                  # Wave detail
│   │       │
│   │       ├── Placement/
│   │       │   ├── Index.php
│   │       │   ├── Create.php
│   │       │   ├── Edit.php
│   │       │   └── Show.php
│   │       │
│   │       ├── Student/
│   │       │   ├── Index.php
│   │       │   ├── Assign.php                # Batch assign wizard
│   │       │   ├── AssignSingle.php          # Single assign
│   │       │   ├── Move.php                  # Move student modal
│   │       │   ├── Import.php                # Import Excel
│   │       │   └── Show.php                  # Student PKL detail
│   │       │
│   │       ├── Supervisor/
│   │       │   ├── Index.php
│   │       │   ├── Assign.php                # Assign supervisor
│   │       │   └── LoadReport.php            # Supervisor load report
│   │       │
│   │       ├── Monitoring/
│   │       │   ├── Index.php                 # List all monitorings
│   │       │   ├── Create.php                # Input monitoring
│   │       │   ├── MyStudents.php            # Guru: my students
│   │       │   └── History.php               # Student: my history
│   │       │
│   │       └── Dashboard/
│   │           └── Index.php                 # PKL Dashboard
│   │
│   ├── Models/
│   │   ├── PKL/                              # PKL Models
│   │   │   ├── PKLWave.php
│   │   │   ├── PKLPlacement.php
│   │   │   ├── PKLStudent.php
│   │   │   ├── PKLSupervisor.php
│   │   │   ├── PKLMonitoring.php
│   │   │   ├── PKLStudentMove.php
│   │   │   ├── PKLCalendarLink.php
│   │   │   └── PKLSetting.php
│   │   │
│   │   └── (existing models...)
│   │
│   ├── Services/
│   │   └── PKL/                              # Business Logic Services
│   │       ├── PKLWaveService.php
│   │       ├── PKLAssignmentService.php      # Student assignment logic
│   │       ├── PKLCapacityService.php        # Capacity management
│   │       ├── PKLMonitoringService.php
│   │       ├── PKLCalendarService.php        # Calendar integration
│   │       └── PKLReportService.php          # Report generation
│   │
│   ├── Observers/
│   │   └── PKL/                              # Model Observers
│   │       ├── PKLWaveObserver.php           # Auto-create calendar
│   │       ├── PKLStudentObserver.php        # Update capacity
│   │       └── PKLMonitoringObserver.php     # Update supervisor stats
│   │
│   ├── Policies/
│   │   └── PKL/                              # Authorization Policies
│   │       ├── PKLWavePolicy.php
│   │       ├── PKLPlacementPolicy.php
│   │       ├── PKLStudentPolicy.php
│   │       └── PKLMonitoringPolicy.php
│   │
│   └── Exports/
│       └── PKL/                              # Excel Exports (Laravel Excel)
│           ├── PKLStudentsExport.php
│           ├── PKLSupervisorsExport.php
│           └── PKLMonitoringsExport.php
│
├── database/
│   ├── migrations/
│   │   ├── 2026_06_23_000001_create_pkl_waves_table.php
│   │   ├── 2026_06_23_000002_create_pkl_placements_table.php
│   │   ├── 2026_06_23_000003_create_pkl_students_table.php
│   │   ├── 2026_06_23_000004_create_pkl_supervisors_table.php
│   │   ├── 2026_06_23_000005_create_pkl_monitorings_table.php
│   │   ├── 2026_06_23_000006_create_pkl_student_moves_table.php
│   │   ├── 2026_06_23_000007_create_pkl_calendar_links_table.php
│   │   └── 2026_06_23_000008_create_pkl_settings_table.php
│   │
│   ├── seeders/
│   │   ├── PKLSeeder.php                     # Main seeder
│   │   ├── PKLWaveSeeder.php
│   │   ├── PKLPlacementSeeder.php
│   │   └── PKLSettingSeeder.php
│   │
│   └── factories/
│       └── PKL/
│           ├── PKLWaveFactory.php
│           ├── PKLPlacementFactory.php
│           ├── PKLStudentFactory.php
│           └── PKLMonitoringFactory.php
│
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   └── pkl/
│   │   │       ├── wave/
│   │   │       │   ├── index.blade.php
│   │   │       │   ├── create.blade.php
│   │   │       │   ├── edit.blade.php
│   │   │       │   └── show.blade.php
│   │   │       │
│   │   │       ├── placement/
│   │   │       │   ├── index.blade.php
│   │   │       │   ├── create.blade.php
│   │   │       │   ├── edit.blade.php
│   │   │       │   └── show.blade.php
│   │   │       │
│   │   │       ├── student/
│   │   │       │   ├── index.blade.php
│   │   │       │   ├── assign.blade.php
│   │   │       │   ├── assign-single.blade.php
│   │   │       │   ├── move.blade.php
│   │   │       │   ├── import.blade.php
│   │   │       │   └── show.blade.php
│   │   │       │
│   │   │       ├── supervisor/
│   │   │       │   ├── index.blade.php
│   │   │       │   ├── assign.blade.php
│   │   │       │   └── load-report.blade.php
│   │   │       │
│   │   │       ├── monitoring/
│   │   │       │   ├── index.blade.php
│   │   │       │   ├── create.blade.php
│   │   │       │   ├── my-students.blade.php
│   │   │       │   └── history.blade.php
│   │   │       │
│   │   │       └── dashboard/
│   │   │           └── index.blade.php
│   │   │
│   │   ├── pkl/
│   │   │   └── reports/                      # PDF Reports
│   │   │       ├── student-placements.blade.php
│   │   │       ├── supervisor-load.blade.php
│   │   │       └── monitoring-history.blade.php
│   │   │
│   │   └── components/
│   │       └── pkl/                          # Reusable Components
│   │           ├── wave-card.blade.php
│   │           ├── placement-card.blade.php
│   │           ├── student-row.blade.php
│   │           ├── capacity-badge.blade.php
│   │           └── monitoring-timeline.blade.php
│   │
│   └── js/
│       └── pkl/                              # Alpine.js Components
│           ├── wave-manager.js
│           ├── capacity-tracker.js
│           └── monitoring-form.js
│
├── routes/
│   └── web.php                               # PKL Routes
│       # Route::prefix('pkl')->group(...)
│
├── config/
│   └── pkl.php                               # PKL Configuration
│
├── tests/
│   ├── Feature/
│   │   └── PKL/
│   │       ├── PKLWaveTest.php
│   │       ├── PKLAssignmentTest.php
│   │       ├── PKLCapacityTest.php
│   │       ├── PKLMonitoringTest.php
│   │       └── PKLCalendarIntegrationTest.php
│   │
│   └── Unit/
│       └── PKL/
│           ├── PKLWaveModelTest.php
│           ├── PKLCapacityServiceTest.php
│           └── PKLSettingTest.php
│
├── storage/
│   └── app/
│       └── public/
│           └── pkl/                          # PKL File Storage
│               ├── photos/                   # Monitoring photos
│               ├── documents/                # Monitoring documents
│               ├── imports/                  # Import Excel files
│               └── exports/                  # Export files
│
└── PKL/                                      # Documentation (this folder)
    ├── 1-ANALISIS-KEBUTUHAN.md
    ├── 2-ERD-DATABASE.md
    ├── 3-STRUKTUR-TABEL.md
    ├── 4-USER-FLOW.md
    ├── 5-STRUKTUR-FOLDER.md                 # This file
    └── 6-ROADMAP-PENGEMBANGAN.md
```

---

## 📝 FILE NAMING CONVENTIONS

### **Models**
```php
// Namespace: App\Models\PKL
PKLWave.php           // Not: Wave.php
PKLPlacement.php      // Not: Placement.php
PKLStudent.php        // Not: Student.php
```
**Reason:** Prefix 'PKL' untuk avoid naming conflicts dengan existing models

### **Livewire Components**
```php
// Namespace: App\Livewire\PKL\Wave
Index.php             // List view
Create.php            // Create form
Edit.php              // Edit form
Show.php              // Detail view
```
**Reason:** Standard CRUD naming, folder by entity

### **Views**
```php
// Path: resources/views/livewire/pkl/wave/
index.blade.php
create.blade.php
edit.blade.php
show.blade.php
```
**Reason:** Kebab-case untuk views, match dengan Livewire component structure

### **Services**
```php
// Namespace: App\Services\PKL
PKLWaveService.php
PKLAssignmentService.php
PKLCapacityService.php
```
**Reason:** Service suffix untuk business logic layer

---

## 🔧 KEY FILES DETAIL

### **1. config/pkl.php**

```php
<?php

return [
    // Max students per supervisor
    'max_students_per_supervisor' => env('PKL_MAX_STUDENTS_PER_SUPERVISOR', 15),
    
    // Minimum PKL duration in days
    'minimum_duration_days' => 90,
    
    // Maximum moves per student per wave
    'max_moves_per_student' => 2,
    
    // Monitoring frequency (days)
    'monitoring_frequency_days' => 14,
    
    // Default calendar color for PKL
    'calendar_color' => '#9333EA',
    
    // Allow overlapping waves
    'allow_overlap_waves' => false,
    
    // Require approval for student moves
    'require_move_approval' => true,
    
    // Auto-create calendar events
    'auto_create_calendar_event' => true,
    
    // File upload settings
    'photo_max_size_mb' => 5,
    'photo_max_count' => 5,
    'allowed_photo_types' => ['jpg', 'jpeg', 'png'],
    'allowed_document_types' => ['pdf', 'doc', 'docx'],
    
    // Company types
    'company_types' => [
        'it' => 'IT & Software',
        'manufacturing' => 'Manufaktur',
        'finance' => 'Keuangan & Perbankan',
        'retail' => 'Retail & Perdagangan',
        'government' => 'Pemerintahan',
        'education' => 'Pendidikan',
        'healthcare' => 'Kesehatan',
        'hospitality' => 'Perhotelan & Pariwisata',
        'other' => 'Lainnya',
    ],
    
    // Status options
    'wave_statuses' => ['draft', 'active', 'completed', 'cancelled'],
    'student_statuses' => ['assigned', 'active', 'completed', 'cancelled', 'moved'],
    'performance_levels' => ['excellent', 'good', 'fair', 'poor'],
];
```

---

### **2. routes/web.php (PKL Section)**

```php
<?php

use App\Livewire\PKL;

// PKL Routes - Requires Authentication
Route::middleware(['auth'])->prefix('pkl')->name('pkl.')->group(function () {
    
    // Dashboard
    Route::get('/', PKL\Dashboard\Index::class)->name('dashboard');
    
    // Waves - Admin & Guru BK only
    Route::middleware(['role:admin,guru_bk'])->group(function () {
        Route::get('/waves', PKL\Wave\Index::class)->name('waves.index');
        Route::get('/waves/create', PKL\Wave\Create::class)->name('waves.create');
        Route::get('/waves/{wave}/edit', PKL\Wave\Edit::class)->name('waves.edit');
        Route::get('/waves/{wave}', PKL\Wave\Show::class)->name('waves.show');
    });
    
    // Placements - Admin & Guru BK only
    Route::middleware(['role:admin,guru_bk'])->group(function () {
        Route::get('/placements', PKL\Placement\Index::class)->name('placements.index');
        Route::get('/placements/create', PKL\Placement\Create::class)->name('placements.create');
        Route::get('/placements/{placement}/edit', PKL\Placement\Edit::class)->name('placements.edit');
        Route::get('/placements/{placement}', PKL\Placement\Show::class)->name('placements.show');
    });
    
    // Students - Admin & Guru BK only
    Route::middleware(['role:admin,guru_bk'])->group(function () {
        Route::get('/students', PKL\Student\Index::class)->name('students.index');
        Route::get('/students/assign', PKL\Student\Assign::class)->name('students.assign');
        Route::get('/students/import', PKL\Student\Import::class)->name('students.import');
    });
    
    // Student Detail - Accessible by student themselves, guru, admin
    Route::get('/students/{student}', PKL\Student\Show::class)
        ->name('students.show')
        ->middleware(['can:view,student']);
    
    // Supervisors - Admin & Guru BK only
    Route::middleware(['role:admin,guru_bk'])->group(function () {
        Route::get('/supervisors', PKL\Supervisor\Index::class)->name('supervisors.index');
        Route::get('/supervisors/assign', PKL\Supervisor\Assign::class)->name('supervisors.assign');
        Route::get('/supervisors/load', PKL\Supervisor\LoadReport::class)->name('supervisors.load');
    });
    
    // Monitoring - Guru Pembimbing
    Route::middleware(['role:admin,guru,guru_pembimbing'])->group(function () {
        Route::get('/monitoring', PKL\Monitoring\Index::class)->name('monitoring.index');
        Route::get('/monitoring/create', PKL\Monitoring\Create::class)->name('monitoring.create');
        Route::get('/monitoring/my-students', PKL\Monitoring\MyStudents::class)->name('monitoring.my-students');
    });
    
    // Monitoring History - Student view
    Route::get('/my-pkl', PKL\Monitoring\History::class)
        ->name('my-pkl')
        ->middleware(['role:siswa']);
    
    // Reports - Admin only
    Route::middleware(['role:admin'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/placements', [PKLReportController::class, 'placements'])->name('placements');
        Route::get('/supervisors', [PKLReportController::class, 'supervisors'])->name('supervisors');
        Route::get('/monitoring', [PKLReportController::class, 'monitoring'])->name('monitoring');
    });
});
```

---

### **3. app/Services/PKL/PKLCapacityService.php**

```php
<?php

namespace App\Services\PKL;

use App\Models\PKL\PKLPlacement;
use App\Models\PKL\PKLStudent;

class PKLCapacityService
{
    /**
     * Get current filled capacity for a placement
     */
    public function getFilledCapacity(PKLPlacement $placement, $waveId = null): int
    {
        $query = PKLStudent::where('placement_id', $placement->id)
            ->whereNotIn('status', ['cancelled', 'moved']);
        
        if ($waveId) {
            $query->where('wave_id', $waveId);
        }
        
        return $query->count();
    }
    
    /**
     * Get available capacity for a placement
     */
    public function getAvailableCapacity(PKLPlacement $placement, $waveId = null): int
    {
        $filled = $this->getFilledCapacity($placement, $waveId);
        return max(0, $placement->capacity - $filled);
    }
    
    /**
     * Check if placement has capacity for N students
     */
    public function hasCapacity(PKLPlacement $placement, int $count = 1, $waveId = null): bool
    {
        return $this->getAvailableCapacity($placement, $waveId) >= $count;
    }
    
    /**
     * Get capacity status: available, almost_full, full
     */
    public function getCapacityStatus(PKLPlacement $placement, $waveId = null): string
    {
        $filled = $this->getFilledCapacity($placement, $waveId);
        $percentage = ($filled / $placement->capacity) * 100;
        
        if ($percentage >= 100) return 'full';
        if ($percentage >= 90) return 'almost_full';
        if ($percentage >= 70) return 'filling';
        return 'available';
    }
    
    /**
     * Get capacity badge color
     */
    public function getCapacityBadgeColor(PKLPlacement $placement, $waveId = null): string
    {
        return match($this->getCapacityStatus($placement, $waveId)) {
            'full' => 'red',
            'almost_full' => 'yellow',
            'filling' => 'blue',
            'available' => 'green',
        };
    }
}
```

---

### **4. app/Observers/PKL/PKLWaveObserver.php**

```php
<?php

namespace App\Observers\PKL;

use App\Models\PKL\PKLWave;
use App\Services\PKL\PKLCalendarService;

class PKLWaveObserver
{
    protected $calendarService;
    
    public function __construct(PKLCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }
    
    /**
     * Handle the PKLWave "created" event.
     */
    public function created(PKLWave $wave): void
    {
        // Auto-create calendar event if enabled
        if (config('pkl.auto_create_calendar_event')) {
            $this->calendarService->createWaveEvent($wave);
        }
    }
    
    /**
     * Handle the PKLWave "updated" event.
     */
    public function updated(PKLWave $wave): void
    {
        // Update calendar event if dates changed
        if ($wave->wasChanged(['start_date', 'end_date', 'name'])) {
            $this->calendarService->updateWaveEvent($wave);
        }
    }
    
    /**
     * Handle the PKLWave "deleted" event.
     */
    public function deleted(PKLWave $wave): void
    {
        // Soft delete calendar event
        $this->calendarService->deleteWaveEvent($wave);
    }
}
```

---

### **5. app/Policies/PKL/PKLStudentPolicy.php**

```php
<?php

namespace App\Policies\PKL;

use App\Models\User;
use App\Models\PKL\PKLStudent;

class PKLStudentPolicy
{
    /**
     * Determine if user can view any students
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'guru_bk', 'guru', 'guru_pembimbing']);
    }
    
    /**
     * Determine if user can view specific student
     */
    public function view(User $user, PKLStudent $student): bool
    {
        // Admin & Guru BK can view all
        if (in_array($user->role, ['admin', 'guru_bk'])) {
            return true;
        }
        
        // Student can view their own
        if ($user->role === 'siswa' && $user->id === $student->user_id) {
            return true;
        }
        
        // Supervisor can view their students
        if (in_array($user->role, ['guru', 'guru_pembimbing'])) {
            return $student->supervisors()->where('user_id', $user->id)->exists();
        }
        
        return false;
    }
    
    /**
     * Determine if user can create students
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'guru_bk']);
    }
    
    /**
     * Determine if user can update student
     */
    public function update(User $user, PKLStudent $student): bool
    {
        return in_array($user->role, ['admin', 'guru_bk']);
    }
    
    /**
     * Determine if user can delete student
     */
    public function delete(User $user, PKLStudent $student): bool
    {
        return $user->role === 'admin';
    }
}
```

---

## 🎯 FOLDER PURPOSE SUMMARY

| Folder | Purpose | Key Files |
|--------|---------|-----------|
| **app/Http/Controllers/PKL** | HTTP Controllers untuk non-Livewire routes | ReportController |
| **app/Livewire/PKL** | Livewire Components (UI Logic) | Index, Create, Edit, Show |
| **app/Models/PKL** | Eloquent Models | PKLWave, PKLStudent, dll |
| **app/Services/PKL** | Business Logic Layer | CapacityService, CalendarService |
| **app/Observers/PKL** | Model Event Listeners | Auto-create calendar |
| **app/Policies/PKL** | Authorization Logic | Who can view/edit/delete |
| **app/Exports/PKL** | Excel Export Classes | Laravel Excel |
| **database/migrations** | Database Schema | Create tables |
| **database/seeders** | Sample/Test Data | Development data |
| **database/factories** | Model Factories | Testing & seeding |
| **resources/views/livewire/pkl** | Blade Views for Livewire | UI Templates |
| **resources/views/pkl/reports** | PDF Report Templates | Print views |
| **resources/views/components/pkl** | Reusable Blade Components | Cards, Badges |
| **resources/js/pkl** | Alpine.js Components | Frontend interactivity |
| **storage/app/public/pkl** | File Storage | Photos, Documents, Exports |
| **tests/Feature/PKL** | Feature Tests | Integration tests |
| **tests/Unit/PKL** | Unit Tests | Service & Model tests |

---

## 📦 DEPENDENCIES

**Required Packages:**

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "livewire/livewire": "^3.0",
        "maatwebsite/excel": "^3.1",
        "barryvdh/laravel-dompdf": "^2.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "pestphp/pest": "^2.0"
    }
}
```

**Install Commands:**
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

**STATUS:** ✅ STRUKTUR FOLDER COMPLETE  
**NEXT:** Roadmap Pengembangan

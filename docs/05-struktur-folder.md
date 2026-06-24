# Struktur Folder Project - e-KALDIK

## 1. Overview Struktur Laravel 12

```
e-KALDIK/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   ├── CalculateEffectiveDays.php
│   │   │   └── BackupDatabase.php
│   │   └── Kernel.php
│   │
│   ├── Events/
│   │   ├── ActivityCreated.php
│   │   ├── ActivityUpdated.php
│   │   └── ActivityDeleted.php
│   │
│   ├── Exceptions/
│   │   └── Handler.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── LogoutController.php
│   │   │   │   └── PasswordController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ExportController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   ├── LogActivity.php
│   │   │   └── EnsureActiveAcademicYear.php
│   │   │
│   │   └── Requests/
│   │       ├── ActivityRequest.php
│   │       ├── AcademicYearRequest.php
│   │       └── ImportRequest.php
│   │
│   ├── Livewire/
│   │   ├── Auth/
│   │   │   ├── Login.php
│   │   │   └── ChangePassword.php
│   │   │
│   │   ├── Dashboard/
│   │   │   ├── Index.php
│   │   │   └── QuickStats.php
│   │   │
│   │   ├── AcademicYear/
│   │   │   ├── Index.php
│   │   │   ├── Create.php
│   │   │   ├── Edit.php
│   │   │   └── Activate.php
│   │   │
│   │   ├── ActivityType/
│   │   │   ├── Index.php
│   │   │   ├── Create.php
│   │   │   └── Edit.php
│   │   │
│   │   ├── Calendar/
│   │   │   ├── Index.php
│   │   │   ├── MonthView.php
│   │   │   ├── YearView.php
│   │   │   ├── ListView.php
│   │   │   ├── CreateActivity.php
│   │   │   ├── EditActivity.php
│   │   │   └── ActivityDetail.php
│   │   │
│   │   ├── EffectiveDay/
│   │   │   ├── Index.php
│   │   │   └── Calculate.php
│   │   │
│   │   └── Import/
│   │       ├── ImportExcel.php
│   │       └── ImportHistory.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── AcademicYear.php
│   │   ├── Semester.php
│   │   ├── ActivityType.php
│   │   ├── Activity.php
│   │   ├── EffectiveDay.php
│   │   ├── ActivityLog.php
│   │   ├── ImportLog.php
│   │   └── Setting.php
│   │
│   ├── Observers/
│   │   └── ActivityObserver.php
│   │
│   ├── Policies/
│   │   ├── ActivityPolicy.php
│   │   └── AcademicYearPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   └── EventServiceProvider.php
│   │
│   ├── Services/
│   │   ├── EffectiveDayService.php
│   │   ├── ImportService.php
│   │   ├── ExportPdfService.php
│   │   ├── ExportExcelService.php
│   │   └── CalendarService.php
│   │
│   └── Traits/
│       ├── HasActivityLog.php
│       └── HasUuid.php
│
├── bootstrap/
│   ├── app.php
│   └── providers.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── livewire.php
│   └── ekaldik.php (custom config)
│
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── AcademicYearFactory.php
│   │   └── ActivityFactory.php
│   │
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_academic_years_table.php
│   │   ├── 2024_01_01_000002_create_semesters_table.php
│   │   ├── 2024_01_01_000003_create_activity_types_table.php
│   │   ├── 2024_01_01_000004_create_activities_table.php
│   │   ├── 2024_01_01_000005_create_effective_days_table.php
│   │   ├── 2024_01_01_000006_create_activity_logs_table.php
│   │   ├── 2024_01_01_000007_create_import_logs_table.php
│   │   └── 2024_01_01_000008_create_settings_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── ActivityTypeSeeder.php
│       └── SettingSeeder.php
│
├── docs/
│   ├── 01-analisis-kebutuhan.md
│   ├── 02-erd-database.md
│   ├── 03-struktur-tabel.md
│   ├── 04-user-flow.md
│   ├── 05-struktur-folder.md
│   ├── 06-roadmap.md
│   └── api-documentation.md
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   ├── storage/ (symlink)
│   └── index.php
│
├── resources/
│   ├── css/
│   │   └── app.css
│   │
│   ├── js/
│   │   ├── app.js
│   │   └── fullcalendar-config.js
│   │
│   ├── views/
│   │   ├── components/
│   │   │   ├── layouts/
│   │   │   │   ├── app.blade.php
│   │   │   │   ├── guest.blade.php
│   │   │   │   └── navigation.blade.php
│   │   │   │
│   │   │   ├── alert.blade.php
│   │   │   ├── button.blade.php
│   │   │   ├── card.blade.php
│   │   │   ├── modal.blade.php
│   │   │   ├── table.blade.php
│   │   │   └── badge.blade.php
│   │   │
│   │   ├── livewire/
│   │   │   ├── auth/
│   │   │   │   ├── login.blade.php
│   │   │   │   └── change-password.blade.php
│   │   │   │
│   │   │   ├── dashboard/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── quick-stats.blade.php
│   │   │   │
│   │   │   ├── academic-year/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   │
│   │   │   ├── activity-type/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   │
│   │   │   ├── calendar/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── month-view.blade.php
│   │   │   │   ├── year-view.blade.php
│   │   │   │   ├── list-view.blade.php
│   │   │   │   ├── create-activity.blade.php
│   │   │   │   ├── edit-activity.blade.php
│   │   │   │   └── activity-detail.blade.php
│   │   │   │
│   │   │   ├── effective-day/
│   │   │   │   └── index.blade.php
│   │   │   │
│   │   │   └── import/
│   │   │       ├── import-excel.blade.php
│   │   │       └── import-history.blade.php
│   │   │
│   │   ├── pdf/
│   │   │   ├── calendar-yearly.blade.php
│   │   │   ├── calendar-monthly.blade.php
│   │   │   └── activity-list.blade.php
│   │   │
│   │   └── errors/
│   │       ├── 403.blade.php
│   │       ├── 404.blade.php
│   │       └── 500.blade.php
│   │
│   └── lang/
│       └── id/
│           ├── auth.php
│           ├── pagination.php
│           └── validation.php
│
├── routes/
│   ├── web.php
│   ├── console.php
│   └── channels.php
│
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   ├── avatars/
│   │   │   ├── exports/
│   │   │   ├── imports/
│   │   │   └── logos/
│   │   └── private/
│   │
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   │
│   └── logs/
│       └── laravel.log
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   │   └── LoginTest.php
│   │   ├── AcademicYearTest.php
│   │   ├── ActivityTest.php
│   │   └── EffectiveDayTest.php
│   │
│   ├── Unit/
│   │   ├── Models/
│   │   │   └── ActivityTest.php
│   │   └── Services/
│   │       └── EffectiveDayServiceTest.php
│   │
│   ├── Pest.php
│   └── TestCase.php
│
├── .env.example
├── .env
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── phpunit.xml
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## 2. Penjelasan Struktur Folder Utama

### 2.1 app/

#### Console/Commands/
Berisi Artisan commands custom:
- `CalculateEffectiveDays.php`: Command untuk recalculate hari efektif (bisa dijadwalkan)
- `BackupDatabase.php`: Command untuk backup database otomatis

#### Events/
Event yang di-trigger saat ada perubahan:
- `ActivityCreated.php`: Saat kegiatan dibuat
- `ActivityUpdated.php`: Saat kegiatan diupdate
- `ActivityDeleted.php`: Saat kegiatan dihapus

#### Http/Controllers/
Controller tradisional untuk fitur non-Livewire:
- **Auth/**: Login, logout, change password
- **DashboardController**: Handle dashboard logic
- **ExportController**: Handle export PDF & Excel

#### Http/Middleware/
Custom middleware:
- `CheckRole.php`: Cek role user (admin/waka/guru)
- `LogActivity.php`: Log setiap aktivitas user
- `EnsureActiveAcademicYear.php`: Pastikan ada tahun pelajaran aktif

#### Http/Requests/
Form request validation:
- `ActivityRequest.php`: Validasi input kegiatan
- `AcademicYearRequest.php`: Validasi tahun pelajaran
- `ImportRequest.php`: Validasi file import

#### Livewire/
Component Livewire untuk interactivity:
- **Auth/**: Login & change password forms
- **Dashboard/**: Dashboard widgets
- **AcademicYear/**: CRUD tahun pelajaran
- **ActivityType/**: CRUD jenis kegiatan
- **Calendar/**: Kalender dengan multiple views
- **EffectiveDay/**: Perhitungan hari efektif
- **Import/**: Import Excel

#### Models/
Eloquent models dengan relationships:
- Semua model mengikuti PSR-12
- Include relationships, scopes, dan accessors

#### Observers/
- `ActivityObserver.php`: Auto-trigger recalculate saat activity berubah

#### Policies/
Authorization policies:
- `ActivityPolicy.php`: Policy untuk kegiatan
- `AcademicYearPolicy.php`: Policy untuk tahun pelajaran

#### Services/
Business logic layer:
- `EffectiveDayService.php`: Logika perhitungan hari efektif
- `ImportService.php`: Logika import Excel
- `ExportPdfService.php`: Generate PDF
- `ExportExcelService.php`: Generate Excel
- `CalendarService.php`: Logika kalender

---

### 2.2 database/

#### migrations/
Migration files dengan urutan eksekusi yang tepat:
1. users
2. academic_years
3. semesters
4. activity_types
5. activities
6. effective_days
7. activity_logs
8. import_logs
9. settings

#### seeders/
Seeder untuk data awal:
- `UserSeeder.php`: User default (admin, waka, guru)
- `ActivityTypeSeeder.php`: 9 jenis kegiatan standar
- `SettingSeeder.php`: Settings aplikasi

---

### 2.3 resources/

#### css/
- `app.css`: Tailwind CSS imports dan custom styles

#### js/
- `app.js`: Alpine.js, Livewire, dan JS utilities
- `fullcalendar-config.js`: Konfigurasi FullCalendar

#### views/
Blade templates dengan struktur yang rapi:
- **components/**: Reusable components (button, card, modal, dll)
- **livewire/**: Blade views untuk Livewire components
- **pdf/**: Template untuk PDF export
- **errors/**: Custom error pages

#### lang/id/
Localization Bahasa Indonesia

---

### 2.4 routes/

#### web.php
Routing aplikasi dengan grouping berdasarkan role:
```php
// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Auth routes
Route::middleware(['auth', 'log.activity'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    
    // Admin & Waka routes
    Route::middleware(['role:admin,waka_kurikulum'])->group(function () {
        // CRUD routes
    });
    
    // All authenticated users
    Route::get('/calendar', Calendar\Index::class)->name('calendar');
});
```

---

## 3. Konvensi Penamaan

### 3.1 File Naming
- **Controllers**: PascalCase + `Controller` suffix
  - ✅ `DashboardController.php`
  - ❌ `dashboard_controller.php`

- **Models**: PascalCase, singular
  - ✅ `Activity.php`
  - ❌ `Activities.php`

- **Livewire Components**: PascalCase
  - ✅ `CreateActivity.php`
  - ❌ `create_activity.php`

- **Migrations**: snake_case dengan timestamp
  - ✅ `2024_01_01_000004_create_activities_table.php`

### 3.2 Namespace
```php
// Models
namespace App\Models;

// Livewire
namespace App\Livewire\Calendar;

// Services
namespace App\Services;

// Requests
namespace App\Http\Requests;
```

### 3.3 Routes Naming
```php
// Pattern: resource.action
Route::get('/calendar', ...)->name('calendar.index');
Route::get('/calendar/create', ...)->name('calendar.create');
Route::post('/calendar', ...)->name('calendar.store');
```

---

## 4. Dependency Management

### 4.1 PHP Dependencies (composer.json)
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "livewire/livewire": "^4.0",
        "barryvdh/laravel-dompdf": "^2.0",
        "maatwebsite/excel": "^3.1",
        "spatie/laravel-permission": "^6.0"
    },
    "require-dev": {
        "pestphp/pest": "^3.0",
        "laravel/pint": "^1.0"
    }
}
```

### 4.2 JavaScript Dependencies (package.json)
```json
{
    "devDependencies": {
        "@tailwindcss/forms": "^0.5",
        "@tailwindcss/typography": "^0.5",
        "alpinejs": "^3.13",
        "autoprefixer": "^10.4",
        "postcss": "^8.4",
        "tailwindcss": "^3.4",
        "vite": "^5.0"
    },
    "dependencies": {
        "@fullcalendar/core": "^6.1",
        "@fullcalendar/daygrid": "^6.1",
        "@fullcalendar/interaction": "^6.1"
    }
}
```

---

## 5. Configuration Files

### 5.1 config/ekaldik.php (Custom Config)
```php
<?php

return [
    // School Info
    'school_name' => env('SCHOOL_NAME', 'SMK Negeri 1'),
    'school_logo' => env('SCHOOL_LOGO', '/images/logo.png'),
    
    // Academic Settings
    'default_start_month' => 7, // Juli
    'default_end_month' => 6,   // Juni
    'weekend_days' => ['saturday', 'sunday'],
    
    // Import Settings
    'max_import_rows' => 1000,
    'allowed_extensions' => ['xlsx', 'xls'],
    'max_file_size' => 2048, // KB
    
    // Export Settings
    'pdf_orientation' => 'landscape',
    'include_logo' => true,
    
    // System Settings
    'session_timeout' => 120, // minutes
    'items_per_page' => 15,
];
```

---

## 6. Storage Structure

```
storage/
├── app/
│   ├── public/
│   │   ├── avatars/          # User avatars
│   │   ├── exports/          # Generated PDFs & Excel
│   │   │   ├── pdf/
│   │   │   └── excel/
│   │   ├── imports/          # Uploaded import files
│   │   │   └── temp/
│   │   └── logos/            # School logos
│   │
│   └── private/
│       └── backups/          # Database backups
│
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
│
└── logs/
    └── laravel.log
```

---

## 7. Public Assets Structure

```
public/
├── assets/
│   ├── css/
│   │   └── custom.css        # Custom CSS jika diperlukan
│   │
│   ├── js/
│   │   └── custom.js         # Custom JS utilities
│   │
│   └── images/
│       ├── logo-default.png
│       ├── banner.jpg
│       └── icons/
│
└── storage/ → symlink ke storage/app/public
```

---

## 8. Testing Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── ChangePasswordTest.php
│   │
│   ├── AcademicYearTest.php
│   ├── ActivityTest.php
│   ├── EffectiveDayTest.php
│   ├── ImportTest.php
│   └── ExportTest.php
│
├── Unit/
│   ├── Models/
│   │   ├── ActivityTest.php
│   │   └── AcademicYearTest.php
│   │
│   └── Services/
│       ├── EffectiveDayServiceTest.php
│       └── CalendarServiceTest.php
│
├── Pest.php
└── TestCase.php
```

---

## 9. Best Practices

### 9.1 Code Organization
- ✅ Single Responsibility Principle
- ✅ Service layer untuk business logic
- ✅ Repository pattern untuk query kompleks (jika diperlukan)
- ✅ Observer untuk auto-actions
- ✅ Policy untuk authorization

### 9.2 File Size
- Controller max 200 lines
- Service max 300 lines
- Livewire component max 250 lines
- Jika melebihi, split menjadi beberapa class

### 9.3 Commenting
```php
/**
 * Calculate effective days for a semester
 * 
 * @param Semester $semester
 * @return EffectiveDay
 */
public function calculate(Semester $semester): EffectiveDay
{
    // Implementation
}
```

---

## 10. Git Structure

```
.gitignore content:
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
npm-debug.log
yarn-error.log
/.idea
/.vscode
```

---

Struktur folder ini dirancang untuk:
1. ✅ Mudah di-maintain
2. ✅ Scalable untuk fase berikutnya
3. ✅ Follow Laravel best practices
4. ✅ Clear separation of concerns
5. ✅ Easy testing

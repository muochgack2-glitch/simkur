# Roadmap Pengembangan - e-KALDIK Phase 1

## 1. Overview Timeline

**Total Estimasi**: 8-10 Minggu  
**Metodologi**: Agile/Iterative Development  
**Testing Strategy**: Continuous Testing per Sprint

```
Sprint 1: Setup & Auth (Week 1-2)
Sprint 2: Master Data (Week 2-3)
Sprint 3: Kalender Core (Week 3-5)
Sprint 4: Hari Efektif (Week 5-6)
Sprint 5: Import/Export (Week 6-7)
Sprint 6: Dashboard & Polish (Week 7-8)
Sprint 7: Testing & Deployment (Week 8-10)
```

---

## 2. Sprint 1: Project Setup & Authentication (Week 1-2)

### 2.1 Development Environment Setup
**Estimasi: 2 hari**

**Tasks**:
- [ ] Install Laravel 12
- [ ] Setup database MySQL
- [ ] Configure Livewire 4
- [ ] Install Tailwind CSS
- [ ] Setup Vite build tool
- [ ] Configure Laravel Pint (code style)
- [ ] Setup Pest (testing framework)
- [ ] Create .env.example
- [ ] Initialize Git repository

**Output**:
```bash
# Successful commands
composer create-project laravel/laravel e-kaldik
composer require livewire/livewire
npm install -D tailwindcss postcss autoprefixer
npm install @fullcalendar/core @fullcalendar/daygrid
```

**Deliverables**:
- ✅ Laravel 12 running
- ✅ Tailwind CSS configured
- ✅ Livewire working
- ✅ Git initialized

---

### 2.2 Database Schema & Migrations
**Estimasi: 3 hari**

**Tasks**:
- [ ] Create migration: users table
- [ ] Create migration: academic_years table
- [ ] Create migration: semesters table
- [ ] Create migration: activity_types table
- [ ] Create migration: activities table
- [ ] Create migration: effective_days table
- [ ] Create migration: activity_logs table
- [ ] Create migration: import_logs table
- [ ] Create migration: settings table
- [ ] Run migrations
- [ ] Test rollback migrations

**Output**:
```bash
php artisan make:migration create_users_table
php artisan make:migration create_academic_years_table
# ... etc
php artisan migrate
php artisan migrate:rollback
```

**Deliverables**:
- ✅ 9 migration files created
- ✅ Database schema created
- ✅ Relationships working

---

### 2.3 Models & Seeders
**Estimasi: 2 hari**

**Tasks**:
- [ ] Create all Eloquent models (9 models)
- [ ] Define relationships
- [ ] Add fillable & casts
- [ ] Create UserSeeder
- [ ] Create ActivityTypeSeeder
- [ ] Create SettingSeeder
- [ ] Create factories for testing
- [ ] Run seeders

**Output**:
```bash
php artisan make:model Activity -f
php artisan make:seeder UserSeeder
php artisan db:seed
```

**Sample Seeder**:
```php
// UserSeeder
User::create([
    'name' => 'Administrator',
    'username' => 'admin',
    'email' => 'admin@smk.sch.id',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);
```

**Deliverables**:
- ✅ 9 Models created with relationships
- ✅ 3 Seeders created
- ✅ Sample data populated

---

### 2.4 Authentication System
**Estimasi: 3 hari**

**Tasks**:
- [ ] Create Login Livewire component
- [ ] Create LoginController
- [ ] Create Logout functionality
- [ ] Create Change Password component
- [ ] Create auth middleware
- [ ] Create role middleware (CheckRole)
- [ ] Create guest layout
- [ ] Create auth layout
- [ ] Style login page with Tailwind
- [ ] Add remember me functionality
- [ ] Add validation
- [ ] Add activity logging for login/logout

**Files to Create**:
```
app/Livewire/Auth/Login.php
app/Livewire/Auth/ChangePassword.php
app/Http/Controllers/Auth/LogoutController.php
app/Http/Middleware/CheckRole.php
resources/views/livewire/auth/login.blade.php
resources/views/components/layouts/guest.blade.php
```

**Deliverables**:
- ✅ Login system working
- ✅ Logout working
- ✅ Change password working
- ✅ Role-based access working
- ✅ Session management working

---

## 3. Sprint 2: Master Data Management (Week 2-3)

### 3.1 Tahun Pelajaran Module
**Estimasi: 3 hari**

**Tasks**:
- [ ] Create AcademicYear Livewire components (Index, Create, Edit)
- [ ] Create AcademicYearRequest validation
- [ ] Create AcademicYearPolicy
- [ ] Implement CRUD operations
- [ ] Auto-generate 2 semesters on create
- [ ] Activate/Deactivate functionality (only 1 active)
- [ ] Archive functionality
- [ ] Add soft delete
- [ ] Create views with Tailwind
- [ ] Add data table with search & pagination

**Features**:
- ✅ List tahun pelajaran (table view)
- ✅ Create tahun pelajaran + auto generate semesters
- ✅ Edit tahun pelajaran
- ✅ Activate (set as active year)
- ✅ Archive (move to archive)
- ✅ Delete (soft delete)

**Deliverables**:
- ✅ Tahun pelajaran CRUD complete
- ✅ Only 1 active year enforced
- ✅ Auto semester generation working

---

### 3.2 Master Jenis Kegiatan Module
**Estimasi: 2 hari**

**Tasks**:
- [ ] Create ActivityType Livewire components
- [ ] Implement CRUD operations
- [ ] Add color picker for default_color
- [ ] Add category dropdown (akademik/non_akademik)
- [ ] Add is_holiday, is_exam checkboxes
- [ ] Prevent delete if type is used in activities
- [ ] Sort by sort_order
- [ ] Create views

**Features**:
- ✅ List jenis kegiatan (with color preview)
- ✅ Create custom jenis kegiatan
- ✅ Edit jenis kegiatan
- ✅ Delete (with validation)
- ✅ Drag & drop reorder (sort_order)

**Deliverables**:
- ✅ Master jenis kegiatan CRUD complete
- ✅ 9 default types seeded
- ✅ Custom types can be added

---

### 3.3 Settings Module (Bonus)
**Estimasi: 1 hari**

**Tasks**:
- [ ] Create Settings page (admin only)
- [ ] School name, logo, address
- [ ] Weekend days configuration
- [ ] Session timeout
- [ ] Items per page
- [ ] Logo upload functionality

**Deliverables**:
- ✅ Settings page working
- ✅ School info configurable
- ✅ Logo upload working

---

## 4. Sprint 3: Kalender Pendidikan Core (Week 3-5)

### 4.1 Calendar Infrastructure
**Estimasi: 2 hari**

**Tasks**:
- [ ] Install & configure FullCalendar
- [ ] Create CalendarService
- [ ] Create base Calendar Livewire component
- [ ] Setup FullCalendar config
- [ ] Create event color mapping
- [ ] Setup date range validation

**Deliverables**:
- ✅ FullCalendar integrated
- ✅ CalendarService created
- ✅ Base calendar rendering

---

### 4.2 Calendar Views
**Estimasi: 4 hari**

**Tasks**:
- [ ] **Month View**: Calendar grid with events
- [ ] **Year View**: 12-month overview
- [ ] **List View**: Table/list of all activities
- [ ] View switcher (tabs/dropdown)
- [ ] Filter by semester
- [ ] Filter by jenis kegiatan
- [ ] Search functionality
- [ ] Color coding by activity type

**Deliverables**:
- ✅ 3 calendar views working
- ✅ View switching seamless
- ✅ Filters working
- ✅ Responsive design

---

### 4.3 Activity CRUD
**Estimasi: 4 hari**

**Tasks**:
- [ ] Create Activity Livewire components
  - CreateActivity (modal)
  - EditActivity (modal)
  - ActivityDetail (modal)
- [ ] Create ActivityRequest validation
- [ ] Create ActivityPolicy
- [ ] Implement form with:
  - Nama kegiatan (text input)
  - Jenis kegiatan (dropdown)
  - Tanggal mulai/selesai (datepicker)
  - Semester (auto-detect or dropdown)
  - Warna (color picker with default)
  - Keterangan (textarea)
- [ ] Date range validation (must be within academic year)
- [ ] Conflict detection (overlap warning)
- [ ] Soft delete implementation
- [ ] Activity history (created_by tracking)

**Validations**:
```php
'name' => 'required|string|max:255',
'activity_type_id' => 'required|exists:activity_types,id',
'start_date' => 'required|date',
'end_date' => 'required|date|after_or_equal:start_date',
'semester_id' => 'required|exists:semesters,id',
```

**Deliverables**:
- ✅ Create activity from calendar
- ✅ Edit activity (modal)
- ✅ Delete activity (with confirmation)
- ✅ View activity detail
- ✅ Conflict detection working
- ✅ All validations working

---

### 4.4 Calendar Integration
**Estimasi: 2 hari**

**Tasks**:
- [ ] Click date to create activity
- [ ] Click event to view detail
- [ ] Drag & drop to reschedule (optional)
- [ ] Event tooltips
- [ ] Legend untuk color coding
- [ ] Refresh calendar after CRUD

**Deliverables**:
- ✅ Fully interactive calendar
- ✅ Smooth UX for CRUD operations

---

## 5. Sprint 4: Perhitungan Hari Efektif (Week 5-6)

### 5.1 Effective Day Service
**Estimasi: 3 hari**

**Tasks**:
- [ ] Create EffectiveDayService
- [ ] Implement calculation logic:
  ```php
  1. Get semester date range
  2. Exclude weekends (Sat, Sun)
  3. Subtract holiday days (is_holiday=true)
  4. Subtract exam days (is_exam=true)
  5. Calculate study_days
  6. Calculate effective_weeks = study_days / 5
  ```
- [ ] Create ActivityObserver to auto-trigger recalculation
- [ ] Create Artisan command: `php artisan ekaldik:calculate-days`
- [ ] Add caching for performance
- [ ] Handle edge cases (public holidays, custom weekends)

**Sample Calculation**:
```php
// Semester: 1 Juli - 31 Desember (184 days)
// Weekends: 52 days
// Holidays: 15 days
// Exam days: 10 days
// Study days: 184 - 52 - 15 - 10 = 107 days
// Effective weeks: 107 / 5 = 21.4 weeks
```

**Deliverables**:
- ✅ EffectiveDayService working
- ✅ Auto-recalculation on activity change
- ✅ Artisan command working

---

### 5.2 Effective Day UI
**Estimasi: 2 hari**

**Tasks**:
- [ ] Create EffectiveDay Livewire component
- [ ] Display per-semester breakdown
- [ ] Show calculation details
- [ ] Add charts (bar/pie chart)
- [ ] Manual recalculate button
- [ ] Export to Excel

**UI Design**:
```
┌─────────────────────────────────────┐
│ Hari Efektif Semester Ganjil 24/25 │
├─────────────────────────────────────┤
│ Total Hari       : 184 hari         │
│ Hari Belajar     : 107 hari         │
│ Hari Libur       : 15 hari          │
│ Hari Ujian       : 10 hari          │
│ Minggu Efektif   : 21.4 minggu      │
│ Terakhir Hitung  : 15 Sep 2024      │
│                                      │
│ [Recalculate] [Export Excel]        │
└─────────────────────────────────────┘
```

**Deliverables**:
- ✅ Hari efektif page working
- ✅ Per-semester view
- ✅ Visual representation
- ✅ Manual recalculate working

---

## 6. Sprint 5: Import & Export (Week 6-7)

### 6.1 Import Excel
**Estimasi: 3 hari**

**Tasks**:
- [ ] Install Maatwebsite/Laravel-Excel
- [ ] Create ImportService
- [ ] Create Excel template (download)
- [ ] Create import Livewire component
- [ ] Implement file upload validation
- [ ] Parse Excel file
- [ ] Validate each row
- [ ] Preview import data (table with status)
- [ ] Process import (insert to database)
- [ ] Log import to import_logs table
- [ ] Show result summary
- [ ] Download error log (Excel)

**Excel Template Columns**:
```
| Nama Kegiatan | Jenis Kegiatan | Tanggal Mulai | Tanggal Selesai | Semester | Keterangan |
|---------------|----------------|---------------|-----------------|----------|------------|
| MPLS 2024     | MPLS           | 2024-07-08    | 2024-07-10      | Ganjil   | ...        |
```

**Deliverables**:
- ✅ Template download working
- ✅ Upload & validation working
- ✅ Preview before import
- ✅ Bulk import working
- ✅ Error handling & logging

---

### 6.2 Export PDF
**Estimasi: 3 hari**

**Tasks**:
- [ ] Install Barryvdh/Laravel-DomPDF
- [ ] Create ExportPdfService
- [ ] Create PDF templates (Blade views):
  - Calendar Yearly (12 months grid)
  - Calendar Monthly (single month)
  - Activity List (table)
- [ ] Add school logo
- [ ] Add header/footer
- [ ] Implement orientation selection (landscape/portrait)
- [ ] Add page numbers
- [ ] Style PDF (CSS)
- [ ] Download functionality

**PDF Layout**:
```
┌──────────────────────────────────────┐
│ [LOGO]  SMK NEGERI 1 JAKARTA        │
│         Kalender Pendidikan 2024/25  │
├──────────────────────────────────────┤
│                                       │
│  [Calendar Grid / List]              │
│                                       │
├──────────────────────────────────────┤
│ Generated: 15 Sep 2024        Page 1 │
└──────────────────────────────────────┘
```

**Deliverables**:
- ✅ 3 PDF templates working
- ✅ Professional layout
- ✅ Download PDF working

---

### 6.3 Export Excel
**Estimasi: 2 hari**

**Tasks**:
- [ ] Create ExportExcelService
- [ ] Export activity list (with filters)
- [ ] Export effective days summary
- [ ] Add Excel styling (header, border, color)
- [ ] Add formulas if needed
- [ ] Download functionality

**Excel Output**:
```
Sheet 1: Daftar Kegiatan
| No | Nama | Jenis | Mulai | Selesai | Semester | Keterangan |

Sheet 2: Hari Efektif
| Semester | Total Hari | Hari Belajar | Hari Libur | Minggu Efektif |
```

**Deliverables**:
- ✅ Export to Excel working
- ✅ Multiple sheets
- ✅ Professional formatting

---

## 7. Sprint 6: Dashboard & Polish (Week 7-8)

### 7.1 Dashboard Development
**Estimasi: 3 hari**

**Tasks**:
- [ ] Create Dashboard controller
- [ ] Create dashboard Livewire components
- [ ] Stat cards:
  - Tahun Pelajaran Aktif (badge)
  - Jumlah Kegiatan (count dengan icon)
  - Hari Efektif Semester Aktif
  - Minggu Efektif
- [ ] Agenda terdekat (7 hari) - list with dates
- [ ] Chart: Kegiatan per bulan (Chart.js/ApexCharts)
- [ ] Quick action buttons
- [ ] Role-based dashboard content

**Deliverables**:
- ✅ Informative dashboard
- ✅ Real-time stats
- ✅ Upcoming events
- ✅ Visual charts

---

### 7.2 UI/UX Polish
**Estimasi: 3 hari**

**Tasks**:
- [ ] Review all pages for consistency
- [ ] Add loading states (wire:loading)
- [ ] Add success/error notifications (toast)
- [ ] Add confirmation dialogs
- [ ] Improve mobile responsiveness
- [ ] Add animations (smooth transitions)
- [ ] Fix any UI bugs
- [ ] Add breadcrumbs
- [ ] Add page titles & meta
- [ ] Accessibility improvements (ARIA labels)

**Deliverables**:
- ✅ Consistent UI across all pages
- ✅ Smooth UX
- ✅ Mobile-friendly
- ✅ Professional look & feel

---

### 7.3 Performance Optimization
**Estimasi: 2 hari**

**Tasks**:
- [ ] Optimize database queries (N+1 problem)
- [ ] Add database indexes
- [ ] Implement caching (Redis/File cache)
- [ ] Lazy loading for Livewire
- [ ] Optimize images
- [ ] Minify assets (Vite build)
- [ ] Add pagination where needed
- [ ] Profile slow queries

**Deliverables**:
- ✅ Page load < 2 seconds
- ✅ Smooth interactions
- ✅ Optimized queries

---

## 8. Sprint 7: Testing & Deployment (Week 8-10)

### 8.1 Testing
**Estimasi: 4 hari**

**Tasks**:
- [ ] Write Feature tests:
  - Auth tests (login, logout, change password)
  - AcademicYear CRUD tests
  - Activity CRUD tests
  - Import tests
  - Export tests
  - Effective day calculation tests
- [ ] Write Unit tests:
  - EffectiveDayService tests
  - CalendarService tests
  - Model relationship tests
- [ ] Manual testing:
  - Test all user flows
  - Test role-based access
  - Test validations
  - Test error handling
  - Cross-browser testing
  - Mobile testing

**Run Tests**:
```bash
php artisan test
# or
./vendor/bin/pest
```

**Target Coverage**: 80%+

**Deliverables**:
- ✅ All critical features tested
- ✅ No major bugs
- ✅ 80%+ code coverage

---

### 8.2 Documentation
**Estimasi: 2 hari**

**Tasks**:
- [ ] Update README.md
- [ ] Installation guide
- [ ] User manual (PDF)
  - Login
  - Mengelola tahun pelajaran
  - Membuat kalender
  - Import/Export
- [ ] Admin guide
- [ ] Developer documentation
- [ ] Database schema documentation
- [ ] API documentation (if any)

**Deliverables**:
- ✅ Complete documentation
- ✅ User manual ready

---

### 8.3 Deployment Preparation
**Estimasi: 2 hari**

**Tasks**:
- [ ] Setup production environment
- [ ] Configure .env for production
- [ ] Setup database backup
- [ ] Configure web server (Nginx/Apache)
- [ ] Setup SSL certificate
- [ ] Configure queue worker (if needed)
- [ ] Setup cron jobs:
  ```bash
  0 2 * * * php artisan ekaldik:calculate-days
  0 3 * * * php artisan backup:run
  ```
- [ ] Security hardening
- [ ] Performance tuning

**Deliverables**:
- ✅ Production environment ready
- ✅ SSL configured
- ✅ Backups automated

---

### 8.4 Deployment & Handover
**Estimasi: 2 hari**

**Tasks**:
- [ ] Deploy to production server
- [ ] Run migrations
- [ ] Seed initial data
- [ ] Test on production
- [ ] Train users (Waka Kurikulum, Admin)
- [ ] Handover documentation
- [ ] Setup monitoring (optional)
- [ ] Go live!

**Deliverables**:
- ✅ Application live in production
- ✅ Users trained
- ✅ Monitoring active

---

## 9. Post-Launch Support (Week 10+)

### 9.1 Bug Fixes
**Ongoing**

**Tasks**:
- [ ] Monitor error logs
- [ ] Fix reported bugs
- [ ] Optimize based on user feedback

---

### 9.2 Phase 2 Preparation
**Future**

**Modules to Add in Phase 2**:
1. Modul PKL (Praktek Kerja Lapangan)
2. Modul UKK (Uji Kompetensi Keahlian)
3. Modul TEFA (Teaching Factory)
4. Jadwal Pelajaran
5. WhatsApp Gateway / Notifikasi
6. AI Features (optional)
7. Supervisi KBM
8. Monitoring KBM

**Note**: Database schema Phase 1 sudah dirancang untuk support Phase 2 tanpa breaking changes.

---

## 10. Risk Management

### 10.1 Potential Risks

| Risk | Impact | Mitigation |
|------|--------|------------|
| Laravel 12 baru release, dokumentasi kurang | High | Gunakan Laravel 11 jika perlu, atau follow changelog closely |
| FullCalendar kompleks | Medium | Start early, baca dokumentasi lengkap, gunakan wrapper jika ada |
| Perhitungan hari efektif tidak akurat | High | Buat unit test lengkap, validasi dengan user |
| Import Excel gagal parsing | Medium | Validasi ketat, berikan template jelas, handle errors dengan baik |
| Performance issue di production | Medium | Optimize early, use caching, profile queries |
| User tidak terbiasa dengan sistem digital | Low | Training, buat UI intuitif, sediakan user manual |

---

## 11. Success Metrics

### Phase 1 dianggap sukses jika:

1. **Functional**:
   - ✅ Semua 10 fitur utama berjalan tanpa bug critical
   - ✅ Perhitungan hari efektif akurat 100%
   - ✅ Import/Export berjalan lancar

2. **Performance**:
   - ✅ Page load time < 2 detik
   - ✅ Import 100 rows < 5 detik
   - ✅ Export PDF < 3 detik

3. **Usability**:
   - ✅ Waka Kurikulum dapat membuat kalender lengkap dalam < 30 menit
   - ✅ Guru dapat dengan mudah melihat kalender (no training needed)
   - ✅ 90% user satisfaction rate

4. **Stability**:
   - ✅ Zero data loss
   - ✅ 99% uptime
   - ✅ Sistem berjalan stabil selama 1 tahun pelajaran penuh

5. **Scalability**:
   - ✅ Database schema support Phase 2 without major changes
   - ✅ Modular code structure untuk easy extension

---

## 12. Daily Development Checklist

**Setiap hari**:
- [ ] Pull latest code from Git
- [ ] Run migrations if any
- [ ] Write code for current task
- [ ] Write tests for new features
- [ ] Run Laravel Pint (code style)
- [ ] Run Pest tests
- [ ] Commit with clear message
- [ ] Push to repository
- [ ] Update task status

**Setiap akhir sprint**:
- [ ] Code review
- [ ] Merge to main branch
- [ ] Deploy to staging
- [ ] User acceptance testing
- [ ] Sprint retrospective
- [ ] Plan next sprint

---

## 13. Tools & Resources

### Development Tools:
- **IDE**: VS Code / PHPStorm
- **Database Tool**: TablePlus / phpMyAdmin
- **API Testing**: Postman / Insomnia
- **Git Client**: GitHub Desktop / Tower
- **Browser DevTools**: Chrome DevTools

### Monitoring Tools (Production):
- **Error Tracking**: Sentry / Flare
- **Performance**: Laravel Telescope
- **Server Monitoring**: Uptime Kuma / New Relic
- **Backup**: Laravel Backup package

---

Roadmap ini bersifat flexible dan dapat disesuaikan berdasarkan:
- Feedback user selama development
- Technical challenges yang muncul
- Perubahan requirement
- Resource availability

**Key Principle**: Ship features incrementally, test continuously, get feedback early! 🚀

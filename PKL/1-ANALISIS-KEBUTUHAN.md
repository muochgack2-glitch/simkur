# ANALISIS KEBUTUHAN SISTEM PKL

**Project:** e-KALDIK - Modul PKL (Praktik Kerja Lapangan)  
**Version:** 1.0  
**Date:** 2026-06-23  
**Author:** Development Team

---

## 📋 1. BUSINESS REQUIREMENTS

### **Latar Belakang**
Sistem PKL dirancang untuk mengelola seluruh proses Praktik Kerja Lapangan siswa kelas XII, mulai dari penempatan, pembimbingan, monitoring, hingga pelaporan. Sistem ini harus terintegrasi dengan Kalender Pendidikan yang sudah ada tanpa mengubah struktur tabel existing.

### **Tujuan Sistem**
1. Mempermudah pengelolaan gelombang PKL multiple per tahun
2. Otomasi assignment siswa ke tempat PKL
3. Tracking pembimbingan dan monitoring real-time
4. Integrasi seamless dengan kalender pendidikan
5. Reporting dan analytics untuk decision making

### **Stakeholders**
- **Administrator Sekolah** - Full access, manage all
- **Guru BK/Kaprog** - Manage assignment & placement
- **Guru Pembimbing** - Input monitoring, track students
- **Siswa** - View own PKL information
- **Industri/DU/DI** - (Future) View assigned students

---

## 🎯 2. FUNCTIONAL REQUIREMENTS

### **FR-1: Manajemen Gelombang PKL**

**FR-1.1 CRUD Gelombang**
```
Priority: HIGH
Actor: Administrator, Guru BK

Description:
- Admin dapat create gelombang PKL baru
- Setiap gelombang memiliki nama/nomor unik
- Gelombang terhubung ke semester & tahun pelajaran
- Set periode tanggal mulai dan selesai
- Dapat activate/deactivate gelombang

Acceptance Criteria:
✓ Form create gelombang dengan validasi
✓ Tanggal gelombang harus dalam range semester
✓ Tidak boleh overlap gelombang aktif (opsional)
✓ Auto-generate entry di kalender pendidikan
✓ Status: draft → active → completed → cancelled

Business Rules:
- Maksimal 3 gelombang per tahun pelajaran
- Gelombang harus linked ke semester valid
- Nama gelombang unique per tahun pelajaran
```

**FR-1.2 Monitor Status Gelombang**
```
Priority: MEDIUM
Actor: Administrator, Guru BK

Description:
- Dashboard overview semua gelombang
- Filter by tahun pelajaran, semester, status
- Quick stats: total siswa, penempatan, progress

Acceptance Criteria:
✓ Card view untuk setiap gelombang
✓ Progress bar penempatan siswa
✓ Status badge dengan color coding
✓ Quick action buttons (view, edit, report)
```

---

### **FR-2: Manajemen Tempat PKL (Industri/DU/DI)**

**FR-2.1 CRUD Tempat PKL**
```
Priority: HIGH
Actor: Administrator, Guru BK

Description:
- Create data tempat PKL (industri/perusahaan/instansi)
- Data: nama, alamat, kontak, kategori bisnis
- Set kapasitas maksimal siswa
- Upload logo/photo tempat (opsional)
- Status kemitraan (active, inactive, blacklist)

Acceptance Criteria:
✓ Form lengkap dengan Google Maps integration (future)
✓ Validasi kapasitas > 0
✓ Kategori bisnis (dropdown): IT, Manufaktur, Perbankan, dll
✓ Contact person dan nomor telepon
✓ Upload foto maks 2MB

Business Rules:
- Satu tempat dapat digunakan di multiple gelombang
- Kapasitas per gelombang (bisa berbeda)
- Tempat dengan status blacklist tidak bisa dipilih
- Soft delete (jangan hapus jika ada history)
```

**FR-2.2 Track Kapasitas Real-time**
```
Priority: HIGH
Actor: System

Description:
- Hitung otomatis kapasitas terisi vs available
- Update real-time saat siswa assigned/moved
- Warning jika mendekati full (>90%)
- Block assignment jika full

Acceptance Criteria:
✓ Counter displayed: "8/10" (filled/capacity)
✓ Visual indicator: green (<70%), yellow (70-90%), red (>90%)
✓ Validation error saat assign ke tempat full
✓ Suggest alternatif tempat dengan kapasitas available
```

---

### **FR-3: Penempatan Siswa**

**FR-3.1 Assign Siswa ke Tempat PKL**
```
Priority: HIGH
Actor: Administrator, Guru BK

Description:
- Pilih gelombang PKL
- Multi-select siswa kelas XII
- Assign ke tempat PKL (cek kapasitas)
- Set tanggal mulai/selesai per siswa
- Konfirmasi assignment

Acceptance Criteria:
✓ Filter siswa: by kelas, belum assigned
✓ List tempat PKL dengan kapasitas tersedia
✓ Batch assignment (multiple students to one place)
✓ Individual assignment (one student at a time)
✓ Preview before confirm
✓ Notification ke siswa via email/whatsapp (future)

Business Rules:
- Hanya siswa kelas XII yang eligible
- Siswa tidak boleh double-assign di gelombang sama
- Tanggal harus dalam range gelombang
- Validasi kapasitas sebelum assign
- Auto-create calendar event for student (opsional)

Validation:
- Check: user is student & grade XII
- Check: not already assigned in this wave
- Check: placement has capacity
- Check: dates within wave period
```

**FR-3.2 Pindah Siswa ke Tempat Lain**
```
Priority: MEDIUM
Actor: Administrator, Guru BK

Description:
- Pilih siswa yang sudah assigned
- Pilih tempat PKL baru
- Input alasan perpindahan
- Record history perpindahan
- Update kapasitas tempat lama & baru

Acceptance Criteria:
✓ Form move dengan reason (required)
✓ Show tempat lama vs baru side-by-side
✓ Confirm dengan warning
✓ History perpindahan tersimpan
✓ Old record status = 'moved'
✓ New record created dengan status = 'assigned'

Business Rules:
- Maksimal 2x pindah per siswa per gelombang
- Alasan wajib diisi (min 20 karakter)
- Approval required untuk pindah ke-2 (opsional)
- Kapasitas tempat lama +1, tempat baru -1
- History tidak boleh dihapus (audit trail)
```

**FR-3.3 Bulk Import Penempatan**
```
Priority: LOW
Actor: Administrator, Guru BK

Description:
- Download template Excel
- Fill data: NISN, Nama, Tempat, Tanggal
- Upload file Excel
- Validate & preview
- Import ke database

Acceptance Criteria:
✓ Template Excel dengan format fixed
✓ Validation saat upload
✓ Preview dengan error highlighting
✓ Skip error rows atau abort all
✓ Success summary report

Validation:
- NISN exists & is grade XII
- Placement exists & has capacity
- Dates valid
- No duplicate
```

---

### **FR-4: Pembimbingan**

**FR-4.1 Assign Guru Pembimbing**
```
Priority: HIGH
Actor: Administrator, Guru BK

Description:
- Pilih guru pembimbing
- Assign ke siswa PKL
- Set jadwal monitoring (opsional)
- Batas maksimal siswa per guru (configurable)
- Satu guru bisa bimbing banyak siswa

Acceptance Criteria:
✓ Select guru from dropdown (role: guru)
✓ Multi-select students to assign
✓ Show current load guru
✓ Warning jika exceed max load
✓ Set as primary/secondary supervisor

Business Rules:
- Max siswa per guru: 15 (default, configurable)
- Guru harus role 'guru' atau 'guru_pembimbing'
- Satu siswa bisa punya >1 pembimbing (primary + secondary)
- Pembimbing bisa replaced/reassigned
- History pembimbingan disimpan

Validation:
- User has role 'guru'
- Not exceed max load
- Student exists & assigned to placement
```

**FR-4.2 Input Monitoring/Kunjungan**
```
Priority: HIGH
Actor: Guru Pembimbing

Description:
- Input catatan kunjungan ke tempat PKL
- Record tanggal, waktu, jenis kunjungan
- Evaluasi: kehadiran, performa, attitude, skill
- Upload foto kunjungan
- Notes dan feedback untuk siswa
- Issue yang ditemui & solusi

Acceptance Criteria:
✓ Form monitoring lengkap
✓ Score: attitude (0-100), skill (0-100)
✓ Performance: excellent/good/fair/poor
✓ Attendance: present/absent/sick/permission
✓ Upload multiple photos (max 5, masing-masing 5MB)
✓ Next visit planning
✓ Auto-update monitoring count & last visit date

Business Rules:
- Guru hanya bisa input untuk siswa bimbingannya
- Visit date tidak boleh future date
- Notes required (min 10 karakter)
- Monitoring minimal 1x per 2 minggu (opsional enforcement)
- Photo upload opsional

Validation:
- Date <= today
- Student belongs to this supervisor
- Required fields filled
- File size & type validation
```

**FR-4.3 View Monitoring History**
```
Priority: MEDIUM
Actor: Guru Pembimbing, Siswa, Administrator

Description:
- List semua monitoring per siswa
- Filter by date range
- Show scores trend
- Export to PDF

Acceptance Criteria:
✓ Timeline view monitoring
✓ Chart: score progression
✓ Photo gallery
✓ Print-friendly layout
✓ Filter & search

Authorization:
- Guru: view own students only
- Siswa: view own history only
- Admin: view all
```

---

### **FR-5: Integrasi Kalender Pendidikan**

**FR-5.1 Auto-Create Calendar Entry**
```
Priority: HIGH
Actor: System

Description:
- Saat gelombang PKL created/updated
- Otomatis create/update entry di activities table
- Menggunakan polymorphic relationship
- Tidak mengubah struktur existing activities table
- Warna khas untuk PKL (purple)

Technical Approach:
- Tabel: pkl_calendar_links (polymorphic pivot)
- Event listener: WaveCreated, WaveUpdated
- Activity type: 'PKL' (new type di activity_types)
- Link: activity_id <-> pkl_calendar_links <-> pklable (wave/student)

Acceptance Criteria:
✓ PKL muncul di kalender existing
✓ Click event opens PKL detail
✓ Update wave → update calendar
✓ Delete wave → soft delete calendar entry
✓ Warna purple (#9333EA) untuk distinguish
```

**FR-5.2 Student-Level Calendar Event (Opsional)**
```
Priority: LOW
Actor: System

Description:
- Setiap siswa punya entry personal di kalender
- Show tanggal mulai/selesai PKL siswa
- Visible only for that student (private)

Acceptance Criteria:
✓ Personal calendar for students
✓ Different color dari wave event
✓ Shows placement name
✓ Shows supervisor info
```

---

### **FR-6: Reporting & Analytics**

**FR-6.1 Laporan Penempatan Siswa**
```
Priority: MEDIUM
Actor: Administrator, Guru BK

Description:
- List semua siswa per gelombang
- Group by tempat PKL
- Status penempatan
- Export ke Excel/PDF

Data Fields:
- No, NISN, Nama, Kelas
- Tempat PKL, Alamat
- Tanggal Mulai/Selesai
- Status, Guru Pembimbing

Format:
- Table view dengan filter
- PDF: landscape A4
- Excel: multiple sheets per gelombang
```

**FR-6.2 Laporan Beban Pembimbingan**
```
Priority: MEDIUM
Actor: Administrator

Description:
- List guru pembimbing
- Jumlah siswa yang dibimbing
- Status monitoring (up-to-date/overdue)
- Last visit date

Acceptance Criteria:
✓ Table dengan sorting
✓ Color indicator: green (normal), red (overload)
✓ Filter by status
✓ Export to Excel

Business Insight:
- Identify overloaded teachers
- Rebalance assignment
- Monitoring compliance check
```

**FR-6.3 Dashboard Monitoring Real-time**
```
Priority: MEDIUM
Actor: Administrator, Guru BK

Description:
- Quick stats cards
- Charts: placement distribution, status breakdown
- Recent activities feed
- Alerts: capacity full, overdue monitoring

Widgets:
1. Total Students Assigned
2. Total Placements
3. Supervisors Count
4. Monitoring Compliance %
5. Chart: Students per Placement
6. Chart: Monitoring Trend
7. Recent Moves
8. Alerts & Notifications
```

**FR-6.4 Export Data PKL**
```
Priority: MEDIUM
Actor: Administrator

Description:
- Export semua data PKL
- Format: Excel (multiple sheets), PDF
- Include: waves, placements, students, monitorings

Sheets (Excel):
1. Waves Overview
2. Placements List
3. Student Assignments
4. Supervisor Assignments
5. Monitoring Records
6. Move History
7. Summary Statistics

Security:
- Only admin can export full data
- Log export activity (audit)
```

---

## 🔐 3. NON-FUNCTIONAL REQUIREMENTS

### **NFR-1: Performance**
```
Requirement: Response time < 2 detik untuk 95% requests
Scope: Dashboard, list views, assignment forms

Metrics:
- Dashboard load: < 1.5s
- Search/filter: < 1s
- Bulk assignment (50 students): < 5s
- Report generation: < 10s

Strategies:
- Database indexing
- Query optimization (eager loading)
- Caching (Redis)
- CDN untuk assets
- Lazy loading untuk tables
```

### **NFR-2: Scalability**
```
Requirement: Support 500 siswa PKL per gelombang

Capacity Planning:
- 500 students × 2 waves/year = 1,000 records/year
- 5 years data = 5,000 student records
- Monitoring: 500 students × 5 visits = 2,500 records/wave

Database:
- Proper indexing for fast queries
- Archival strategy untuk data lama
- Pagination (50 records per page)
- Chunk processing untuk bulk operations
```

### **NFR-3: Availability**
```
Requirement: Uptime 99% selama jam kerja (07:00-17:00)

Measures:
- Regular database backup (daily)
- Error logging & monitoring
- Graceful degradation (fallback)
- Maintenance window: Minggu 00:00-04:00
```

### **NFR-4: Security**
```
Authentication:
- Laravel Sanctum/Passport
- Session-based auth
- Password hash (bcrypt)

Authorization:
- Role-based access control (RBAC)
- Policy gates untuk setiap action
- Middleware untuk route protection

Data Protection:
- Input validation & sanitization
- SQL injection prevention (Eloquent)
- XSS prevention
- CSRF protection
- File upload validation

Audit:
- Log semua sensitive actions
- Track created_by, updated_by
- Soft deletes untuk data recovery
```

### **NFR-5: Usability**
```
User Experience:
- Intuitive navigation (max 3 clicks)
- Responsive design (mobile-friendly)
- Consistent UI/UX dengan existing e-KALDIK
- Loading indicators
- Success/error messages yang jelas

Accessibility:
- Keyboard navigation
- Color contrast WCAG AA
- Alt text untuk images
- Readable font size (min 14px)

Browser Support:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers
```

### **NFR-6: Maintainability**
```
Code Quality:
- Follow Laravel best practices
- PSR-12 coding standard
- Meaningful variable/function names
- Comments untuk complex logic
- DRY principle

Documentation:
- README dengan setup instructions
- API documentation (if applicable)
- Database schema documentation
- User manual (PDF/Wiki)

Testing:
- Unit tests untuk business logic
- Feature tests untuk critical flows
- Minimum 70% code coverage
```

### **NFR-7: Flexibility**
```
Extensibility:
- JSON metadata fields untuk custom data
- Configurable settings (via pkl_settings table)
- Plugin architecture untuk future features
- API-ready (RESTful endpoints)

Configuration:
- Max students per supervisor (editable)
- Minimum PKL duration (editable)
- Monitoring frequency (editable)
- Status options (extendable enum)
```

---

## 👥 4. USER STORIES

### **Administrator**

**US-1:** Sebagai Administrator, saya ingin membuat gelombang PKL baru agar siswa dapat ditempatkan sesuai periode yang ditentukan.
```
Acceptance Criteria:
- Dapat input nama gelombang, tanggal mulai/selesai
- Dapat pilih semester
- Otomatis muncul di kalender
- Status draft hingga diaktifkan
```

**US-2:** Sebagai Administrator, saya ingin melihat dashboard overview PKL agar dapat monitoring progress penempatan siswa.
```
Acceptance Criteria:
- Quick stats (total siswa, tempat, dll)
- Charts distribusi
- Recent activities
- Alerts untuk anomali
```

---

### **Guru BK/Kaprog**

**US-3:** Sebagai Guru BK, saya ingin assign siswa ke tempat PKL agar mereka dapat melaksanakan praktik sesuai jurusan.
```
Acceptance Criteria:
- Filter siswa kelas XII
- Lihat kapasitas tempat PKL
- Multi-select untuk batch assignment
- Validasi tidak double-assign
```

**US-4:** Sebagai Guru BK, saya ingin memindahkan siswa jika ada masalah di tempat PKL agar siswa dapat melanjutkan di tempat yang sesuai.
```
Acceptance Criteria:
- Pilih siswa aktif
- Pilih tempat baru
- Input alasan perpindahan
- History tersimpan
```

---

### **Guru Pembimbing**

**US-5:** Sebagai Guru Pembimbing, saya ingin input catatan monitoring agar progress siswa terdokumentasi dengan baik.
```
Acceptance Criteria:
- Form dengan score, notes, photo
- Hanya untuk siswa bimbingan saya
- History monitoring tersimpan
- Notifikasi ke siswa (opsional)
```

**US-6:** Sebagai Guru Pembimbing, saya ingin melihat jadwal kunjungan saya agar tidak melewatkan monitoring siswa.
```
Acceptance Criteria:
- Calendar view monitoring plan
- Reminder untuk overdue visit
- List siswa yang belum dikunjungi
```

---

### **Siswa**

**US-7:** Sebagai Siswa, saya ingin melihat info tempat PKL saya agar tahu lokasi dan kontak yang bisa dihubungi.
```
Acceptance Criteria:
- Detail tempat: nama, alamat, kontak
- Map location (future)
- Tanggal mulai/selesai
- Info guru pembimbing
```

**US-8:** Sebagai Siswa, saya ingin melihat history monitoring agar tahu feedback dari guru pembimbing.
```
Acceptance Criteria:
- Timeline monitoring
- Notes dan score
- Photo dari kunjungan
- Progress chart
```

---

## 🎯 5. SUCCESS CRITERIA

### **MVP (Minimum Viable Product)**
```
✓ Gelombang PKL dapat dibuat dan dikelola
✓ Tempat PKL dapat ditambahkan dengan kapasitas
✓ Siswa dapat di-assign ke tempat PKL
✓ Guru dapat di-assign sebagai pembimbing
✓ Monitoring dapat diinput dan diview
✓ Integrasi dengan kalender berfungsi
✓ Dashboard menampilkan summary
```

### **Business Goals**
```
✓ Reduce admin workload by 50%
✓ 100% siswa kelas XII ter-assign PKL
✓ 90% compliance monitoring schedule
✓ Zero data loss
✓ User satisfaction score > 4/5
```

### **Technical Goals**
```
✓ Page load < 2s
✓ API response < 500ms
✓ Zero critical bugs in production
✓ 70% test coverage
✓ Documentation complete
```

---

## 📅 6. TIMELINE & MILESTONES

**Phase 1: Foundation (Week 1-2)**
- Database schema & migrations
- Models & relationships
- Seeders & factories

**Phase 2: Core Features (Week 3-4)**
- Gelombang & Tempat PKL CRUD
- Student assignment
- Supervisor assignment

**Phase 3: Monitoring (Week 5)**
- Monitoring input form
- History view
- Photo upload

**Phase 4: Integration (Week 6)**
- Calendar integration
- Dashboard & reports
- Export features

**Phase 5: Testing & Launch (Week 7-8)**
- Testing (unit, feature, E2E)
- Bug fixes
- User training
- Production deployment

---

## 🚨 7. RISKS & MITIGATION

**Risk 1: Capacity Overbooking**
- Mitigation: Database transaction with locking

**Risk 2: Data Inconsistency**
- Mitigation: Computed properties, validation, tests

**Risk 3: Performance Issues**
- Mitigation: Proper indexing, caching, optimization

**Risk 4: User Adoption**
- Mitigation: Training, documentation, support

**Risk 5: Integration Conflicts**
- Mitigation: Polymorphic relationship, non-intrusive design

---

**STATUS:** ✅ APPROVED FOR DEVELOPMENT  
**NEXT:** ERD & Database Design


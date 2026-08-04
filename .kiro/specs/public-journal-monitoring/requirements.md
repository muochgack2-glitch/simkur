# Requirements: Public Journal Monitoring

## 📋 Feature Overview

**Feature Name:** Halaman Monitoring Publik Jurnal Guru Hari Ini  
**Request Date:** 2026-08-04  
**Requested By:** User/Stakeholder  
**Priority:** High  
**Target Release:** MVP Phase 1

---

## 🎯 Business Requirements

### BR-1: Real-Time Monitoring
**As a** kepala sekolah atau waka kurikulum  
**I want to** melihat status pengisian jurnal guru secara real-time  
**So that** saya dapat monitor kedisiplinan guru dan segera follow-up yang belum isi

**Acceptance Criteria:**
- Data selalu menampilkan status terkini (hari ini)
- Auto-refresh setiap 5 menit
- Manual refresh tersedia

---

### BR-2: Transparansi & Accountability
**As a** stakeholder sekolah  
**I want** halaman ini dapat diakses publik tanpa login  
**So that** transparansi meningkat dan guru merasa accountable

**Acceptance Criteria:**
- URL dapat diakses tanpa autentikasi
- Hanya menampilkan data non-sensitif (nama guru, statistik)
- Tidak menampilkan detail materi/kehadiran siswa

---

### BR-3: Kategorisasi Sederhana (2 Kategori)
**As a** user halaman monitoring  
**I want to** melihat guru dikategorikan berdasarkan status pengisian  
**So that** mudah identifikasi yang perlu ditindaklanjuti

**Categories:**
1. ❌ **Belum Isi** - 0% completion (belum isi sama sekali)
2. ✅ **Sudah Isi** - 1-100% completion (termasuk partial)

**Rationale:**
- Simplifikasi: Yang penting tahu siapa yang BELUM ISI sama sekali
- Guru yang sudah isi (walau partial) masuk kategori "Sudah Isi"
- Fokus monitoring: Follow-up yang 0%

**Acceptance Criteria:**
- Kategorisasi akurat berdasarkan completion %
- 2 section terpisah dengan warna berbeda
- Counter untuk tiap kategori
- Sorting alphabetical (A-Z) dalam tiap kategori

---

### BR-4: Summary Statistics
**As a** decision maker  
**I want to** melihat statistik ringkasan di atas  
**So that** dapat evaluasi secara keseluruhan

**Data yang Ditampilkan:**
- Total guru yang ada jadwal hari ini
- Jumlah dan persentase tiap kategori
- Tanggal dan hari dalam bahasa Indonesia

**Acceptance Criteria:**
- Stats update real-time
- Percentage calculated correctly
- Format tampilan jelas dan mudah dibaca

---

## 🔧 Functional Requirements

### FR-1: Auto-Detect Hari Ini
**Description:** Sistem otomatis detect tanggal dan hari saat ini

**Specifications:**
- Gunakan server time (bukan client time)
- Convert ke format Indonesia: "Senin, 4 Agustus 2026"
- Day name dalam bahasa Indonesia untuk query database

**Test Cases:**
- Senin s/d Jumat (hari sekolah)
- Sabtu/Minggu (tidak ada jadwal)
- Hari libur nasional

---

### FR-2: Load Jadwal Guru Hari Ini
**Description:** Query semua guru yang punya jadwal mengajar di hari ini

**Data Source:** `teaching_schedules` table

**Filter Criteria:**
- `day_of_week` = hari ini (dalam bahasa Indonesia)
- `is_active` = true
- `academic_year_id` = active academic year
- `teacher.role` = 'guru'
- `teacher.is_active` = true

**Output:** Collection of schedules with teacher, class, subject data

---

### FR-3: Load Jurnal Terisi Hari Ini
**Description:** Query semua jurnal yang sudah diisi untuk hari ini

**Data Source:** `teaching_journals` table

**Filter Criteria:**
- `date` = hari ini
- `academic_year_id` = active academic year

**Output:** Collection of journals with time_slot data

---

### FR-4: Match JP Schedule vs Journal
**Description:** Matching antara JP di schedule dengan JP di journal

**Logic:**
1. Schedule memiliki `time_slot_id` (array of time slot IDs)
2. Journal memiliki `time_slot` (array of time slot display names)
3. Count total JP dari schedule
4. Count filled JP dari journal
5. Calculate percentage = (filled / total) * 100

**Edge Cases:**
- Schedule dengan single time_slot_id (legacy format)
- Journal dengan single time_slot (legacy format)
- Schedule tanpa time_slot_id (invalid data)
- Multiple journals untuk sama teacher di hari yang sama

---

### FR-5: Categorization Logic
**Description:** Kategorisasi guru berdasarkan completion percentage

**Rules:**
- percentage == 100 → **Sudah Isi Lengkap**
- percentage > 0 && percentage < 100 → **Isi Sebagian**
- percentage == 0 → **Belum Isi**

**Sort Order:** Alphabetically by teacher name dalam tiap kategori

---

### FR-6: Display Teacher Cards
**Description:** Tampilkan setiap guru dalam card format

**Data per Card:**
- Nama guru
- Progress text: "X/Y JP" (filled/total)
- Percentage: "XX%"
- Progress bar visual (optional)

**Color Scheme:**
- Sudah: Green background (#10B981)
- Sebagian: Yellow background (#F59E0B)
- Belum: Red background (#EF4444)

---

### FR-7: Auto-Refresh Mechanism
**Description:** Halaman auto-refresh setiap 5 menit

**Implementation:**
- Use Livewire `wire:poll.300s`
- Show countdown timer: "Auto-refresh: ⟳ 4 menit 32 detik"
- Update countdown setiap detik (JavaScript)

**Behavior:**
- Tidak mengganggu user saat sedang membaca
- Smooth transition (no flickering)
- Reset countdown setelah refresh

---

### FR-8: Manual Refresh Button
**Description:** Tombol untuk refresh data secara manual

**Specifications:**
- Icon: 🔄 Refresh Sekarang
- Location: Top right near countdown
- Action: Trigger Livewire refresh
- Feedback: Loading spinner during refresh

---

### FR-9: Responsive Layout
**Description:** Layout responsive di berbagai ukuran layar

**Breakpoints:**
- **Desktop (≥1024px):** 3 columns side-by-side
- **Tablet (768px-1023px):** 2 columns + 1 row below
- **Mobile (<768px):** 1 column stack

**Considerations:**
- Touch-friendly buttons
- Readable font sizes
- No horizontal scroll

---

### FR-10: Empty State Handling
**Description:** Handle case ketika tidak ada data

**Scenarios:**
1. **Tidak ada jadwal hari ini** (weekend/libur)
   - Message: "Tidak ada jadwal mengajar hari ini"
   
2. **Tidak ada guru aktif**
   - Message: "Tidak ada guru aktif di sistem"

3. **Academic year tidak aktif**
   - Message: "Tidak ada tahun ajaran aktif"

---

## 🔒 Non-Functional Requirements

### NFR-1: Performance
**Target:**
- Page load: < 1 second
- Refresh: < 500ms
- Max database queries: 5 per request

**Strategy:**
- Eager loading relationships
- Efficient collection operations
- Optional caching (5 min TTL)

---

### NFR-2: Scalability
**Expected Load:**
- Concurrent users: 1-10
- Teachers: 20-50
- Schedules per day: 100-200

**Design for:**
- Up to 100 teachers
- Up to 500 schedules per day

---

### NFR-3: Security
**Access Control:**
- Public access (no authentication)
- Rate limiting: 60 requests/minute (optional)

**Data Protection:**
- Only display non-sensitive data
- No student personal information
- No detailed teaching content

---

### NFR-4: Usability
**Standards:**
- Intuitive UI (no training needed)
- Clear visual hierarchy
- Accessible (WCAG AA recommended)
- Fast interaction response

---

### NFR-5: Maintainability
**Code Quality:**
- Well-documented code
- Unit tests coverage > 80%
- Follow Laravel best practices
- Modular and reusable components

---

### NFR-6: Reliability
**Uptime:**
- Target: 99% uptime
- Graceful error handling
- Fallback for missing data

**Error Scenarios:**
- Database connection failed
- No active academic year
- Invalid date format

---

## 📊 Data Requirements

### Input Data
1. **Current Date & Time** (server time)
2. **Active Academic Year** (from `academic_years` table)
3. **Teaching Schedules** (from `teaching_schedules` table)
4. **Teaching Journals** (from `teaching_journals` table)
5. **Time Slots** (from `time_slots` table)
6. **Teachers** (from `users` table where role='guru')

### Output Data
1. **Summary Stats**
   - total_teachers: integer
   - completed_count: integer
   - completed_percentage: float
   - partial_count: integer
   - partial_percentage: float
   - not_started_count: integer
   - not_started_percentage: float

2. **Teacher Data per Category**
   - id: integer
   - name: string
   - total_jp: integer
   - filled_jp: integer
   - remaining_jp: integer
   - percentage: float
   - schedules: array (optional for detail view)

---

## 🧪 Testing Requirements

### Unit Tests Required
- [ ] Test day name conversion (English → Indonesian)
- [ ] Test JP counting from time_slot array
- [ ] Test JP counting from single time_slot
- [ ] Test categorization with 100% completion
- [ ] Test categorization with partial completion
- [ ] Test categorization with 0% completion
- [ ] Test percentage calculation
- [ ] Test edge case: 0 total JP

### Integration Tests Required
- [ ] Test page load without authentication
- [ ] Test data loads correctly
- [ ] Test auto-refresh mechanism
- [ ] Test manual refresh button
- [ ] Test responsive layout (mobile, tablet, desktop)
- [ ] Test with no schedule data
- [ ] Test with large dataset (50+ teachers)

### Manual Tests Required
- [ ] Visual inspection on different devices
- [ ] Performance testing with realistic data
- [ ] Usability testing with end users
- [ ] Cross-browser compatibility

---

## 🚫 Out of Scope (Phase 1)

The following features are **NOT** included in MVP:
- ❌ Export to Excel
- ❌ Filter by subject/class
- ❌ Detail modal (schedule breakdown)
- ❌ History view (select past dates)
- ❌ WhatsApp/Email notifications
- ❌ User authentication/authorization
- ❌ Edit jurnal dari halaman ini
- ❌ Analytics dashboard

These may be considered for **Phase 2** enhancements.

---

## 🎯 Success Criteria

### MVP Launch Criteria
1. ✅ All FR-1 to FR-10 implemented
2. ✅ All NFR requirements met
3. ✅ Unit tests pass (coverage > 80%)
4. ✅ Integration tests pass
5. ✅ Manual testing completed
6. ✅ Performance targets achieved
7. ✅ Stakeholder approval

### Success Metrics (Post-Launch)
- **Usage:** 5+ unique visitors per day
- **Performance:** < 1s page load maintained
- **Uptime:** > 99% availability
- **User Feedback:** Positive feedback from 80%+ users

---

## 📅 Timeline Estimate

| Phase | Duration | Notes |
|-------|----------|-------|
| Design & Planning | ✅ Complete | This document |
| Implementation (Phase 1) | 2-3 days | MVP features |
| Testing & QA | 1 day | Unit + Integration |
| Deployment | 0.5 day | Production deploy |
| **Total** | **3-4 days** | For Phase 1 MVP |

---

## 📝 Assumptions & Dependencies

### Assumptions
1. Database already has:
   - Active academic year
   - Teaching schedules data
   - Time slots data
   - Active teachers
2. Server timezone configured correctly
3. Livewire already installed and configured
4. Tailwind CSS available for styling

### Dependencies
- Laravel 10+
- Livewire 3+
- PHP 8.1+
- MySQL 8.0+
- Existing models: TeachingSchedule, TeachingJournal, User, TimeSlot

---

## 🔄 Future Enhancements (Phase 2)

Priority order for Phase 2:
1. **High Priority:**
   - Export to Excel (for reporting)
   - Filter by subject/class (for focused monitoring)

2. **Medium Priority:**
   - Detail modal (expand schedule info)
   - History view (see past days)

3. **Low Priority:**
   - WhatsApp notifications (automated reminders)
   - Email notifications
   - Analytics dashboard

---

**Requirements Version:** 1.0  
**Last Updated:** 2026-08-04  
**Status:** ✅ Approved

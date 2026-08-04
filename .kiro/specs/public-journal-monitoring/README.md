# Public Journal Monitoring - Feature Spec

> **Halaman Monitoring Publik Jurnal Guru Hari Ini**  
> Real-time monitoring untuk transparansi dan accountability pengisian jurnal mengajar

---

## 📁 Spec Files

| File | Description |
|------|-------------|
| **requirements.md** | Business & functional requirements |
| **design.md** | Technical design (high-level + low-level) |
| **tasks.md** | Implementation tasks breakdown |

---

## 🎯 Quick Overview

### What is this?
Halaman publik yang menampilkan status real-time pengisian jurnal mengajar untuk semua guru yang ada jadwal hari ini.

### Who is it for?
- **Primary:** Kepala Sekolah, Waka Kurikulum
- **Secondary:** Guru (self-monitoring), Stakeholder lain

### Key Features
✅ Auto-detect hari ini  
✅ Kategorisasi otomatis (Sudah/Sebagian/Belum)  
✅ Summary statistics  
✅ Auto-refresh 5 menit  
✅ Public access (no login)  
✅ Responsive design  

---

## 🚀 Implementation Status

**Current Phase:** 🔵 Design Complete → Ready for Implementation

| Phase | Status | Progress |
|-------|--------|----------|
| Requirements | ✅ Complete | 100% |
| Design | ✅ Complete | 100% |
| Implementation | ⏳ Not Started | 0% |
| Testing | ⏳ Not Started | 0% |
| Deployment | ⏳ Not Started | 0% |

---

## 📊 Categorization Logic

```
GURU + JADWAL HARI INI
        ↓
   CARI JURNAL
        ↓
  HITUNG JP TERISI
        ↓
┌───────────────────────┐
│  JP Terisi / Total JP │
└───────────────────────┘
        ↓
   ┌────┴────┐
   │         │
 100%     0-99%      0%
   ↓         ↓        ↓
✅ SUDAH  ⚠️ SEBAGIAN ❌ BELUM
```

---

## 🎨 UI Preview

```
┌─────────────────────────────────────────────────┐
│  🗓️ Monitoring Jurnal Hari Ini: Senin, 4 Agt   │
│  Auto-refresh: ⟳ 4m 32s  [🔄 Refresh Sekarang] │
├─────────────────────────────────────────────────┤
│  Total: 23 | ✅ 15 (65%) | ⚠️ 5 (22%) | ❌ 3   │
├───────────────┬───────────────┬─────────────────┤
│ ✅ SUDAH (15) │ ⚠️ SEBAGIAN   │ ❌ BELUM (3)    │
├───────────────┼───────────────┼─────────────────┤
│ Dewi          │ Budi          │ Ari             │
│ 4/4 JP (100%) │ 2/3 JP (67%)  │ 0/2 JP (0%)     │
│               │               │                 │
│ Ilham         │ Yully         │ Dhani           │
│ 3/3 JP (100%) │ 1/2 JP (50%)  │ 0/3 JP (0%)     │
└───────────────┴───────────────┴─────────────────┘
```

---

## 🔧 Technical Stack

**Backend:**
- Laravel 10+
- Livewire 3+ (for real-time updates)
- Eloquent ORM

**Frontend:**
- Blade templates
- Tailwind CSS
- Alpine.js (via Livewire)

**Database:**
- MySQL 8.0+
- Tables: `teaching_schedules`, `teaching_journals`, `users`, `time_slots`

---

## 📈 Data Flow

```
┌──────────┐
│ Browser  │
└────┬─────┘
     │ GET /monitoring/jurnal-hari-ini
     ↓
┌─────────────────────────────────────┐
│  JournalMonitoring\Index (Livewire) │
│                                     │
│  1. Detect today (date + day)      │
│  2. Get active academic year        │
│  3. Query schedules for today       │
│  4. Query journals for today        │
│  5. Match & categorize teachers     │
│  6. Calculate stats                 │
└────┬────────────────────────────────┘
     │
     ↓
┌─────────────────────────────────────┐
│  Blade View                         │
│  - Display stats                    │
│  - Show 3 columns (categorized)     │
│  - Auto-refresh every 5 min         │
└─────────────────────────────────────┘
```

---

## 🧪 Testing Strategy

### Unit Tests
- Day name conversion
- JP counting logic
- Categorization algorithm
- Percentage calculation

### Integration Tests
- Full page load flow
- Auto-refresh mechanism
- Manual refresh
- Responsive layout

### Manual Tests
- Visual QA on devices
- Performance testing
- Usability testing

**Target Coverage:** > 80%

---

## 📦 Deliverables

### Phase 1: MVP (Est. 3-4 days)
- [x] Requirements document ✅
- [x] Design document ✅
- [x] Task breakdown ✅
- [ ] Livewire component
- [ ] Blade view
- [ ] Route registration
- [ ] Unit tests
- [ ] Integration tests
- [ ] Documentation

### Phase 2: Enhancements (Future)
- [ ] Export to Excel
- [ ] Filter by subject/class
- [ ] Detail modal
- [ ] History view
- [ ] Notifications (WA/Email)

---

## 🎯 Success Metrics

**Launch Criteria:**
- ✅ All MVP features complete
- ✅ Tests pass (coverage > 80%)
- ✅ Performance < 1s load time
- ✅ Responsive on all devices
- ✅ Stakeholder approval

**Post-Launch:**
- Usage: 5+ visitors/day
- Uptime: > 99%
- User satisfaction: 80%+

---

## 🔗 Related Features

- **Teaching Journal** (`/teaching-journal`) - Input jurnal mengajar
- **Public Calendar** (`/kaldik`) - Kalender akademik publik
- **Dashboard Guru** (`/dashboard`) - Dashboard guru internal

---

## 📚 References

### Models
- `app/Models/TeachingSchedule.php`
- `app/Models/TeachingJournal.php`
- `app/Models/User.php`
- `app/Models/TimeSlot.php`
- `app/Models/AcademicYear.php`

### Similar Features
- Public Calendar implementation
- Dashboard stats components

---

## 🤝 Stakeholders

**Product Owner:** User/Requester  
**Developer:** To be assigned  
**Reviewer:** To be assigned  
**End Users:** Kepala Sekolah, Waka Kurikulum, Guru

---

## 📅 Timeline

| Milestone | Target Date | Status |
|-----------|-------------|--------|
| Requirements | 2026-08-04 | ✅ Done |
| Design | 2026-08-04 | ✅ Done |
| Implementation Start | TBD | ⏳ Pending |
| MVP Complete | TBD | ⏳ Pending |
| Testing | TBD | ⏳ Pending |
| Launch | TBD | ⏳ Pending |

---

## ❓ FAQ

**Q: Apakah perlu login?**  
A: Tidak, halaman ini public access untuk transparansi.

**Q: Data apa saja yang ditampilkan?**  
A: Hanya nama guru, jumlah JP, dan progress. Tidak ada data sensitif seperti materi atau kehadiran siswa.

**Q: Bagaimana kalau weekend atau libur?**  
A: Akan muncul message "Tidak ada jadwal mengajar hari ini".

**Q: Apakah bisa lihat history hari sebelumnya?**  
A: Tidak di MVP. Fitur ini masuk Phase 2 enhancements.

**Q: Bagaimana cara export data?**  
A: Export Excel akan tersedia di Phase 2.

---

## 📞 Contact & Support

**Questions?** Contact development team or create issue.

**Found a bug?** Report via issue tracker.

**Feature request?** Add to Phase 2 backlog.

---

**Spec Version:** 1.0  
**Created:** 2026-08-04  
**Last Updated:** 2026-08-04  
**Status:** ✅ Ready for Implementation

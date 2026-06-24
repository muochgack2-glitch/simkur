# 🗺️ ROADMAP PENGEMBANGAN SIM KURIKULUM

**Base System:** E-KALDIK (Kalender Pendidikan)  
**Target:** Sistem Informasi Manajemen Kurikulum Terpadu  
**Waktu:** Tahap 1-5 (Bertahap)

---

## 📊 CURRENT STATE (E-KALDIK v1.0)

### ✅ **Fitur yang Sudah Ada:**
- ✅ Manajemen Tahun Pelajaran
- ✅ Manajemen Semester
- ✅ Manajemen Kegiatan Sekolah
- ✅ Jenis Kegiatan (Akademik/Non-Akademik)
- ✅ Kalender Pendidikan (Admin + Publik)
- ✅ Perhitungan Hari Efektif
- ✅ Export/Import Kegiatan
- ✅ Export PDF Kalender
- ✅ Icon Emoji di Kalender
- ✅ Multi-role (Admin, Kurikulum, Guru, Siswa)
- ✅ Activity Log
- ✅ Settings Management
- ✅ Logo Upload

---

## 🎯 TAHAP 1: PENINGKATAN E-KALDIK (1-2 Bulan)

### **Prioritas: Sempurnakan Foundation**

#### 1.1 **PDF Enhancement** ⭐
- [ ] Update PDF view dengan icon emoji
- [ ] Update PDF view dengan gradient background
- [ ] Improve PDF layout consistency
- [ ] Add watermark option

#### 1.2 **Calendar Enhancement**
- [ ] Multiple calendar views (Yearly, Weekly)
- [ ] Calendar export to Google Calendar/iCal
- [ ] Recurring events support
- [ ] Drag & drop improvements

#### 1.3 **Notification System** ⭐⭐
- [ ] Email notifications untuk kegiatan penting
- [ ] WhatsApp integration (optional)
- [ ] Push notifications (web push)
- [ ] Reminder untuk kegiatan mendatang

#### 1.4 **Reporting**
- [ ] Laporan kehadiran kegiatan
- [ ] Laporan realisasi hari efektif
- [ ] Dashboard analytics
- [ ] Custom report generator

#### 1.5 **User Management Enhancement**
- [ ] Bulk user import
- [ ] User groups/departments
- [ ] Permission granular control
- [ ] Activity log per user

---

## 📚 TAHAP 2: KURIKULUM FOUNDATION (2-3 Bulan)

### **Prioritas: Struktur Kurikulum Dasar**

#### 2.1 **Mata Pelajaran (Mapel)** ⭐⭐⭐
```
Tabel: subjects
- id
- code (Kode Mapel: MAT, BIN, IPA, dll)
- name (Nama Mapel)
- category (Wajib, Peminatan, Muatan Lokal)
- grade_applicable (JSON: [10,11,12])
- kkm (Kriteria Ketuntasan Minimal)
- description
```

**Fitur:**
- [ ] CRUD Mata Pelajaran
- [ ] Import/Export Mapel
- [ ] Mapel per tingkat
- [ ] Mapel per jurusan (IPA/IPS)

#### 2.2 **Struktur Kurikulum** ⭐⭐⭐
```
Tabel: curriculum_structures
- id
- academic_year_id
- grade (10, 11, 12)
- major (IPA, IPS, Umum)
- subject_id
- hours_per_week (Jam per minggu)
- semester (ganjil/genap/both)
```

**Fitur:**
- [ ] Template kurikulum (K13, Merdeka)
- [ ] Beban jam per mapel
- [ ] Beban jam total per tingkat
- [ ] Validation jam sesuai regulasi

#### 2.3 **Kompetensi Dasar (KD)** ⭐⭐
```
Tabel: basic_competencies
- id
- subject_id
- semester_id
- code (3.1, 4.1, dll)
- description
- indicator
```

**Fitur:**
- [ ] CRUD KD per mapel
- [ ] Import KD dari template
- [ ] Mapping KD ke materi

---

## 👥 TAHAP 3: MANAJEMEN KELAS & PENGAJARAN (2-3 Bulan)

### **Prioritas: Jadwal & Kelas**

#### 3.1 **Manajemen Kelas** ⭐⭐⭐
```
Tabel: classes
- id
- academic_year_id
- grade (10, 11, 12)
- major (IPA, IPS)
- class_number (1, 2, 3)
- homeroom_teacher_id
- student_count
```

**Fitur:**
- [ ] CRUD Kelas
- [ ] Assign wali kelas
- [ ] Daftar siswa per kelas
- [ ] Import siswa bulk

#### 3.2 **Jadwal Pelajaran** ⭐⭐⭐
```
Tabel: schedules
- id
- class_id
- subject_id
- teacher_id
- day (Senin-Jumat)
- start_time
- end_time
- room
```

**Fitur:**
- [ ] Generator jadwal otomatis
- [ ] Drag & drop jadwal
- [ ] Conflict detection (bentrok)
- [ ] Print jadwal per kelas/guru
- [ ] Jadwal pengganti

#### 3.3 **Pembagian Mengajar** ⭐⭐
```
Tabel: teaching_assignments
- id
- academic_year_id
- teacher_id
- subject_id
- class_id
- total_hours
```

**Fitur:**
- [ ] Assign guru ke mapel+kelas
- [ ] Beban mengajar per guru
- [ ] Laporan beban kerja
- [ ] SK pembagian tugas (PDF)

---

## 📖 TAHAP 4: PEMBELAJARAN & PENILAIAN (3-4 Bulan)

### **Prioritas: RPP & Nilai**

#### 4.1 **Rencana Pelaksanaan Pembelajaran (RPP)** ⭐⭐
```
Tabel: lesson_plans
- id
- teacher_id
- subject_id
- class_id
- semester_id
- basic_competency_id
- topic
- sub_topic
- learning_objectives
- materials
- methods
- media
- assessment
- references
- file_attachment
```

**Fitur:**
- [ ] CRUD RPP
- [ ] Template RPP (format baru/lama)
- [ ] Upload file RPP
- [ ] Approval workflow
- [ ] Sharing RPP antar guru

#### 4.2 **Modul Ajar / Materi** ⭐
```
Tabel: learning_materials
- id
- subject_id
- topic
- content (HTML/Markdown)
- file_attachments (JSON)
- video_url
```

**Fitur:**
- [ ] Upload materi per topik
- [ ] Video embed (YouTube, dll)
- [ ] Download center
- [ ] Share ke siswa

#### 4.3 **Penilaian / Rapor** ⭐⭐⭐
```
Tabel: assessments
- id
- student_id
- subject_id
- semester_id
- type (tugas, uh, uts, uas, praktik, sikap)
- score
- date
- description
```

**Fitur:**
- [ ] Input nilai per siswa
- [ ] Nilai pengetahuan (KI-3)
- [ ] Nilai keterampilan (KI-4)
- [ ] Nilai sikap (KI-1, KI-2)
- [ ] Generate rapor (PDF)
- [ ] Statistik nilai per kelas
- [ ] Analisis ketuntasan

---

## 📊 TAHAP 5: MONITORING & EVALUASI (2-3 Bulan)

### **Prioritas: Pelaporan & Analytics**

#### 5.1 **Monitoring Pembelajaran** ⭐⭐
- [ ] Realisasi pembelajaran vs RPP
- [ ] Progres KD per mapel
- [ ] Kehadiran siswa per mapel
- [ ] Kehadiran guru mengajar
- [ ] Catatan supervisi

#### 5.2 **Evaluasi Kurikulum** ⭐⭐
- [ ] Analisis ketuntasan per KD
- [ ] Daya serap siswa
- [ ] Tingkat kesulitan soal
- [ ] Rekomendasi perbaikan

#### 5.3 **Dashboard Analytics** ⭐⭐⭐
- [ ] Overview statistik sekolah
- [ ] Chart nilai per mapel
- [ ] Chart kehadiran
- [ ] Trend ketuntasan
- [ ] Export data untuk akreditasi

#### 5.4 **Pelaporan Dapodik/EMIS** ⭐
- [ ] Export format Dapodik
- [ ] Export format EMIS
- [ ] Export format Akreditasi
- [ ] Custom report builder

---

## 🚀 TAHAP 6: ADVANCED FEATURES (Optional)

### **Bonus Features (Sesuai Kebutuhan)**

#### 6.1 **E-Learning Integration**
- [ ] Quiz online
- [ ] Assignment submission
- [ ] Forum diskusi
- [ ] Live class integration (Zoom/GMeet)

#### 6.2 **Parent Portal**
- [ ] Akses nilai siswa
- [ ] Jadwal pelajaran
- [ ] Kalender kegiatan
- [ ] Komunikasi guru-ortu

#### 6.3 **Library Management**
- [ ] Katalog buku
- [ ] Peminjaman
- [ ] E-book repository

#### 6.4 **Inventory & Asset**
- [ ] Sarana prasarana
- [ ] Inventaris lab
- [ ] Peminjaman alat

---

## 📋 TEKNOLOGI STACK

### **Current Stack (E-KALDIK):**
- ✅ Laravel 11
- ✅ Livewire 3
- ✅ Tailwind CSS
- ✅ FullCalendar.js
- ✅ DomPDF
- ✅ MySQL

### **Recommended Additions:**
- [ ] **Laravel Excel** - Import/Export advanced
- [ ] **Spatie Media Library** - File management
- [ ] **Laravel Backup** - Auto backup
- [ ] **Laravel Queue** - Background jobs (email, report generation)
- [ ] **Chart.js / ApexCharts** - Dashboard analytics
- [ ] **Laravel Sanctum** - API authentication (untuk mobile app nantinya)

---

## 💡 SARAN PENGEMBANGAN

### **Fase Development:**
1. **Tahap 1-2** → Fokus di foundation yang kuat
2. **Tahap 3** → Multi-tenant (jika akan di-deploy ke banyak sekolah)
3. **Tahap 4-5** → Fitur core kurikulum
4. **Tahap 6** → Advanced features sesuai feedback user

### **Best Practices:**
- ✅ Gunakan Git branching (main, development, feature/*)
- ✅ Buat seeder lengkap untuk testing
- ✅ Unit testing untuk fitur critical
- ✅ API documentation (jika akan buat mobile app)
- ✅ User manual/documentation
- ✅ Regular backup database
- ✅ Monitoring server & error tracking

### **Scalability:**
- Jika akan untuk banyak sekolah → Multi-tenant architecture
- Jika hanya 1 sekolah → Single tenant lebih simple
- Database optimization (indexing)
- Caching strategy (Redis untuk production)
- CDN untuk assets

---

## 📅 ESTIMASI TIMELINE

| Tahap | Durasi | Target Completion |
|-------|--------|-------------------|
| Tahap 1 | 1-2 bulan | Agustus 2026 |
| Tahap 2 | 2-3 bulan | November 2026 |
| Tahap 3 | 2-3 bulan | Februari 2027 |
| Tahap 4 | 3-4 bulan | Juni 2027 |
| Tahap 5 | 2-3 bulan | September 2027 |
| Tahap 6 | Ongoing | 2028+ |

**Total:** ~12-15 bulan untuk SIM Kurikulum lengkap

---

## 🎯 PRIORITAS AWAL (Quick Wins)

Setelah deploy E-KALDIK, fokus ke:
1. ⭐⭐⭐ **Mata Pelajaran & Struktur Kurikulum** (Tahap 2.1 & 2.2)
2. ⭐⭐⭐ **Manajemen Kelas** (Tahap 3.1)
3. ⭐⭐⭐ **Jadwal Pelajaran** (Tahap 3.2)

Ketiga fitur ini adalah backbone SIM Kurikulum dan akan memberikan value langsung ke sekolah.

---

## 📞 NEXT STEPS

1. ✅ Deploy E-KALDIK ke production (aaPanel)
2. ✅ Gathering feedback dari user
3. ✅ Prioritize features untuk Tahap 1
4. ⏳ Start development Tahap 1
5. ⏳ Iterative development & testing

---

**Prepared by:** AI Assistant  
**Date:** 24 Juni 2026  
**Version:** 1.0  
**Status:** Planning Phase 🗺️

**Good luck with your SIM Kurikulum project! 🚀📚**

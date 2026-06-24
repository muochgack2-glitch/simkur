# USER FLOW & UI WIREFRAMES - SISTEM PKL

**Project:** e-KALDIK - Modul PKL  
**Version:** 1.0  
**Date:** 2026-06-23

---

## 🎯 USER ROLES

1. **Administrator** - Full access, manage all
2. **Guru BK/Kaprog** - Manage assignment & placement
3. **Guru Pembimbing** - Input monitoring, track students
4. **Siswa** - View own PKL information

---

## 🗺️ NAVIGATION STRUCTURE

```
Dashboard
│
├─ PKL Management
│  ├─ Gelombang PKL (Waves)
│  │  ├─ List Gelombang
│  │  ├─ Create Gelombang
│  │  ├─ Edit Gelombang
│  │  └─ Detail Gelombang
│  │
│  ├─ Tempat PKL (Placements)
│  │  ├─ List Tempat
│  │  ├─ Create Tempat
│  │  ├─ Edit Tempat
│  │  └─ Detail Tempat (+ List Siswa)
│  │
│  ├─ Penempatan Siswa (Students)
│  │  ├─ List Penempatan
│  │  ├─ Assign Siswa (Single/Batch)
│  │  ├─ Move Siswa
│  │  └─ Detail Siswa PKL
│  │
│  ├─ Pembimbingan (Supervisors)
│  │  ├─ Assign Pembimbing
│  │  ├─ List Pembimbingan
│  │  └─ Detail Beban Guru
│  │
│  └─ Monitoring
│     ├─ Input Monitoring (Guru)
│     ├─ List Monitoring (All)
│     └─ History per Siswa
│
├─ Reports
│  ├─ Laporan Penempatan
│  ├─ Laporan Pembimbingan
│  ├─ Laporan Monitoring
│  └─ Dashboard PKL
│
└─ Settings
   └─ Konfigurasi PKL
```

---

## 📱 USER FLOW DIAGRAMS

### 1️⃣ ADMINISTRATOR: Create Gelombang PKL

```
[Dashboard] 
    ↓
[Menu PKL > Gelombang PKL]
    ↓
[List Gelombang] ──► [Button: + Buat Gelombang Baru]
    ↓
[Form Create Gelombang]
    ├─ Pilih Tahun Ajaran (dropdown)
    ├─ Pilih Semester (dropdown)
    ├─ Input Nama Gelombang
    ├─ Input Nomor Batch
    ├─ Set Tanggal Mulai (datepicker)
    ├─ Set Tanggal Selesai (datepicker)
    ├─ Deskripsi (textarea, optional)
    ├─ Max Siswa (input number, optional)
    └─ [Button: Simpan]
    ↓
[Validation]
    ├─ Valid? ──YES─► [Save to DB]
    │                      ↓
    │              [Auto Create Calendar Event]
    │                      ↓
    │              [Success Message + Redirect to List]
    │
    └─ Invalid? ──NO──► [Show Error Messages] ──► [Form tetap terbuka]

[List Gelombang] 
    ├─ Card view per gelombang
    ├─ Show: Nama, Periode, Status, Progress
    ├─ Actions: View Detail, Edit, Activate/Deactivate, Report
    └─ Filter: Tahun Ajaran, Status, Semester
```

**Wireframe Konsep:**
```
┌─────────────────────────────────────────────────────────────┐
│  GELOMBANG PKL                          [+ Buat Gelombang]  │
├─────────────────────────────────────────────────────────────┤
│  Filter: [Tahun Ajaran ▼] [Semester ▼] [Status ▼] [Reset]  │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Gelombang 1 - RPL & TKJ         [Status: ACTIVE 🟢]  │   │
│  │ Periode: 01 Jul 2026 - 30 Sep 2026                   │   │
│  │ Siswa: 85/150  [████████░░░░] 57%                    │   │
│  │ Tempat: 12 industri                                   │   │
│  │ [Detail] [Edit] [Laporan] [Non-aktifkan]             │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Gelombang 2 - MM & AKL          [Status: DRAFT 🟡]   │   │
│  │ Periode: 15 Jan 2027 - 15 Apr 2027                   │   │
│  │ Siswa: 0/120  [░░░░░░░░░░░░] 0%                      │   │
│  │ Tempat: 0 industri                                    │   │
│  │ [Detail] [Edit] [Aktifkan] [Hapus]                   │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

### 2️⃣ GURU BK: Assign Siswa ke Tempat PKL (Batch)

```
[Dashboard]
    ↓
[Menu PKL > Penempatan Siswa]
    ↓
[List Penempatan] ──► [Button: + Assign Siswa]
    ↓
[Form Assign - Step 1: Pilih Gelombang]
    ├─ Dropdown: Pilih Gelombang (hanya yang active)
    └─ [Button: Lanjut]
    ↓
[Form Assign - Step 2: Pilih Siswa]
    ├─ Tabel siswa kelas XII (checkbox multi-select)
    ├─ Filter: Kelas, Jurusan, Status (belum assigned)
    ├─ Search: Nama/NISN
    ├─ [Select All] checkbox
    └─ [Button: Lanjut] (min 1 siswa selected)
    ↓
[Form Assign - Step 3: Pilih Tempat PKL]
    ├─ List tempat PKL dengan kapasitas
    ├─ Show: Nama, Alamat, Kapasitas (Terisi/Total)
    ├─ Filter: Kota, Tipe Perusahaan, Kapasitas Tersedia
    ├─ Visual indicator: 🟢 Tersedia | 🟡 Hampir Penuh | 🔴 Penuh
    └─ [Button: Pilih Tempat] (radio select)
    ↓
[Form Assign - Step 4: Set Tanggal]
    ├─ Tanggal Mulai (default dari gelombang, bisa diubah)
    ├─ Tanggal Selesai (default dari gelombang, bisa diubah)
    └─ [Button: Lanjut]
    ↓
[Preview Assignment]
    ├─ Tabel preview: Siswa → Tempat → Tanggal
    ├─ [Button: Kembali] atau [Button: Simpan]
    └─ Konfirmasi: "Yakin assign X siswa ke tempat ini?"
    ↓
[Validation & Processing]
    ├─ Check kapasitas masih cukup?
    ├─ Check siswa belum assigned?
    ├─ Check tanggal dalam range gelombang?
    ├─ Valid? ──YES─► [Batch Insert ke pkl_students]
    │                      ↓
    │              [Update kapasitas placement]
    │                      ↓
    │              [Success: "X siswa berhasil di-assign"]
    │                      ↓
    │              [Redirect to List Penempatan]
    │
    └─ Invalid? ──NO──► [Show Errors] ──► [Kembali ke Preview]
```

**Wireframe Konsep (Step 3):**
```
┌─────────────────────────────────────────────────────────────┐
│  ASSIGN SISWA - PILIH TEMPAT PKL       Step 3 of 4          │
├─────────────────────────────────────────────────────────────┤
│  85 siswa dipilih                             [← Kembali]    │
│  Filter: [Kota ▼] [Tipe ▼] [Tersedia Saja ☑]              │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🟢 PT Telkom Indonesia                               │    │
│  │    IT Company • Jakarta Selatan                      │    │
│  │    Kapasitas: 5/10 [█████░░░░░] Tersedia 5          │    │
│  │    Kontak: Budi Santoso (0812-3456-789)             │    │
│  │    [● Pilih Tempat Ini]                              │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🟡 PT Bank Mandiri                                   │    │
│  │    Finance • Jakarta Pusat                           │    │
│  │    Kapasitas: 18/20 [█████████░] Tersedia 2         │    │
│  │    Kontak: Siti Nur (0813-7654-321)                 │    │
│  │    [○ Pilih Tempat Ini]                              │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🔴 PT Unilever Indonesia                             │    │
│  │    Manufacturing • Tangerang                         │    │
│  │    Kapasitas: 15/15 [██████████] PENUH              │    │
│  │    [TIDAK TERSEDIA]                                  │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  [Kembali]                                [Lanjut →]         │
└─────────────────────────────────────────────────────────────┘
```

---

### 3️⃣ GURU BK: Pindahkan Siswa ke Tempat Lain

```
[List Penempatan Siswa]
    ↓
[Action pada Row Siswa: "Pindahkan"]
    ↓
[Modal: Pindahkan Siswa]
    ├─ Info Siswa: Nama, NISN, Kelas
    ├─ Tempat Sekarang: [Disabled field, read-only]
    ├─ Pilih Tempat Baru: [Dropdown tempat dengan kapasitas]
    ├─ Alasan Perpindahan: [Textarea, required, min 20 char]
    ├─ Tanggal Pindah: [Datepicker, default today]
    └─ [Button: Batal] [Button: Pindahkan]
    ↓
[Validation]
    ├─ Alasan >= 20 karakter?
    ├─ Tempat baru != tempat lama?
    ├─ Kapasitas tempat baru tersedia?
    ├─ Jumlah perpindahan siswa ini < max_moves?
    ├─ Valid? ──YES─► [Confirmation Dialog]
    │                      ↓
    │              "Yakin pindahkan [Nama] dari [Tempat A] ke [Tempat B]?"
    │                      ↓
    │              [Proses Move]
    │                 ├─ Create record di pkl_student_moves
    │                 ├─ Update pkl_students.status = 'moved'
    │                 ├─ Create pkl_students baru (status 'assigned')
    │                 ├─ Update kapasitas: placement A +1, placement B -1
    │                 └─ Success: "Siswa berhasil dipindahkan"
    │
    └─ Invalid? ──NO──► [Show Error] ──► [Modal tetap terbuka]
```

**Wireframe Modal:**
```
┌────────────────────────────────────────────────┐
│  PINDAHKAN SISWA                         [✕]   │
├────────────────────────────────────────────────┤
│  Siswa: Ahmad Fauzi (123456789)                │
│  Kelas: XII RPL 1                              │
│                                                 │
│  Tempat Sekarang:                              │
│  [PT Telkom Indonesia - Jakarta] [disabled]    │
│                                                 │
│  Tempat Baru: *                                │
│  [Pilih Tempat PKL ▼                        ]  │
│                                                 │
│  Alasan Perpindahan: * (min 20 karakter)       │
│  ┌──────────────────────────────────────────┐  │
│  │ Siswa mengalami masalah adaptasi dengan  │  │
│  │ lingkungan kerja di tempat sebelumnya.   │  │
│  │                                           │  │
│  └──────────────────────────────────────────┘  │
│  128 karakter                                   │
│                                                 │
│  Tanggal Pindah:                               │
│  [23/06/2026 📅]                               │
│                                                 │
│  ⚠ Ini adalah perpindahan ke-1 untuk siswa ini│
│                                                 │
│  [Batal]                      [Pindahkan]      │
└────────────────────────────────────────────────┘
```

---

### 4️⃣ GURU PEMBIMBING: Input Monitoring

```
[Dashboard Guru]
    ↓
[Menu PKL > Monitoring Saya]
    ↓
[List Siswa Bimbingan]
    ├─ Tabel: Nama, NISN, Tempat, Last Visit, Status
    ├─ Sort: Last visit (oldest first) - priority overdue
    ├─ Badge: 🔴 Overdue (>14 hari) | 🟡 Soon (7-14 hari) | 🟢 OK
    └─ [Action: + Input Monitoring]
    ↓
[Form Input Monitoring]
    ├─ Siswa: [Auto-filled, read-only]
    ├─ Tanggal Kunjungan: [Datepicker, max today]
    ├─ Waktu Kunjungan: [Time picker]
    ├─ Tipe Kunjungan: [Radio: Onsite | Online | Phone]
    ├─ Status Kehadiran: [Radio: Hadir | Tidak Hadir | Sakit | Izin]
    ├─ Performa Kerja: [Select: Excellent | Good | Fair | Poor]
    ├─ Skor Sikap: [Number input 0-100]
    ├─ Skor Keterampilan: [Number input 0-100]
    ├─ Catatan: [Textarea, required, min 10 char]
    ├─ Masalah Ditemui: [Textarea, optional]
    ├─ Solusi Diberikan: [Textarea, optional]
    ├─ Upload Foto: [File input, multiple, max 5 files]
    ├─ Rencana Kunjungan Berikutnya: [Datepicker, optional]
    └─ [Button: Simpan]
    ↓
[Validation]
    ├─ Tanggal <= today?
    ├─ Catatan >= 10 karakter?
    ├─ Skor 0-100 atau kosong?
    ├─ File size & type valid?
    ├─ Valid? ──YES─► [Upload Files]
    │                      ↓
    │              [Save Monitoring Record]
    │                      ↓
    │              [Update pkl_supervisors: monitoring_count++, last_visit_date]
    │                      ↓
    │              [Success: "Monitoring berhasil disimpan"]
    │                      ↓
    │              [Redirect to List Siswa]
    │
    └─ Invalid? ──NO──► [Show Errors] ──► [Form tetap terbuka]
```

**Wireframe Form:**
```
┌─────────────────────────────────────────────────────────────┐
│  INPUT MONITORING                                 [Simpan]   │
├─────────────────────────────────────────────────────────────┤
│  Siswa: Ahmad Fauzi (XII RPL 1)                             │
│  Tempat PKL: PT Telkom Indonesia                            │
│                                                               │
│  ┌── Informasi Kunjungan ────────────────────────────────┐  │
│  │ Tanggal: [23/06/2026 📅]  Waktu: [10:30 🕐]         │  │
│  │ Tipe: ● Onsite  ○ Online  ○ Phone                   │  │
│  │ Kehadiran: ● Hadir  ○ Tidak Hadir  ○ Sakit  ○ Izin  │  │
│  └────────────────────────────────────────────────────────┘  │
│                                                               │
│  ┌── Evaluasi ────────────────────────────────────────────┐  │
│  │ Performa Kerja: [Excellent ▼]                          │  │
│  │ Skor Sikap: [85]  (0-100)                              │  │
│  │ Skor Keterampilan: [80]  (0-100)                       │  │
│  └────────────────────────────────────────────────────────┘  │
│                                                               │
│  ┌── Catatan * ───────────────────────────────────────────┐  │
│  │ Siswa menunjukkan progress yang baik dalam             │  │
│  │ mengerjakan project website company profile.           │  │
│  │ Komunikasi dengan team juga sangat baik.               │  │
│  └────────────────────────────────────────────────────────┘  │
│  120 karakter (min 10)                                       │
│                                                               │
│  Masalah Ditemui:                                            │
│  [Textarea kosong, optional]                                 │
│                                                               │
│  Solusi Diberikan:                                           │
│  [Textarea kosong, optional]                                 │
│                                                               │
│  Upload Foto: (Max 5 files, masing-masing 5MB)              │
│  [📎 Pilih File] atau Drag & Drop                           │
│  • monitoring_1.jpg (1.2 MB) [✕]                            │
│  • diskusi_team.jpg (850 KB) [✕]                            │
│                                                               │
│  Rencana Kunjungan Berikutnya:                              │
│  [07/07/2026 📅] (optional)                                 │
│                                                               │
│  [Batal]                                        [Simpan]     │
└─────────────────────────────────────────────────────────────┘
```

---

### 5️⃣ SISWA: Lihat Info PKL Saya

```
[Dashboard Siswa]
    ↓
[Menu: PKL Saya]
    ↓
[Detail PKL Siswa]
    ├─ Card: Info Gelombang (Nama, Periode)
    ├─ Card: Info Tempat PKL
    │   ├─ Nama Perusahaan
    │   ├─ Alamat Lengkap
    │   ├─ Kontak Person & Telepon
    │   ├─ Maps (future)
    │   └─ Tanggal Mulai/Selesai PKL
    │
    ├─ Card: Info Guru Pembimbing
    │   ├─ Nama Guru
    │   ├─ NIP
    │   ├─ Kontak (future)
    │   └─ Last Visit Date
    │
    └─ Tab: History Monitoring
        ├─ Timeline view (newest first)
        ├─ Show: Date, Visit Type, Performance, Scores
        ├─ Expandable untuk baca catatan lengkap
        └─ Photo gallery (jika ada)

[Tab History Monitoring - Detail View]
    ├─ [Button: Filter by Date Range]
    ├─ [Button: Print/Export PDF]
    └─ Timeline Items:
        ├─ 23 Jun 2026 | Onsite | Excellent
        │   Catatan: "Progress sangat baik..."
        │   Skor Sikap: 85 | Skor Skill: 80
        │   Photos: [🖼️ 2 foto]
        │
        ├─ 09 Jun 2026 | Online | Good
        │   Catatan: "Siswa aktif bertanya..."
        │   Skor Sikap: 80 | Skor Skill: 75
        │
        └─ 26 May 2026 | Onsite | Good
            Catatan: "Awal yang bagus..."
```

**Wireframe Konsep:**
```
┌─────────────────────────────────────────────────────────────┐
│  PKL SAYA - AHMAD FAUZI                                      │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 📅 GELOMBANG 1 - RPL & TKJ                          │    │
│  │ Periode: 01 Juli 2026 - 30 September 2026          │    │
│  │ Status: AKTIF 🟢                                    │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🏢 TEMPAT PKL                                        │    │
│  │ PT Telkom Indonesia                                  │    │
│  │ 📍 Jl. Gatot Subroto No. 52, Jakarta Selatan        │    │
│  │ 📞 Kontak: Budi Santoso (0812-3456-789)             │    │
│  │ 📆 Periode: 01 Jul - 30 Sep 2026 (90 hari)          │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 👨‍🏫 GURU PEMBIMBING                                   │    │
│  │ Pak Agus Santoso, S.Kom                              │    │
│  │ NIP: 197505152006041003                              │    │
│  │ Last Visit: 23 Juni 2026                             │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 📊 HISTORY MONITORING          [Export PDF ▼]       │    │
│  ├─────────────────────────────────────────────────────┤    │
│  │ ● 23 Jun 2026 | 10:30 | Onsite | Excellent         │    │
│  │   Skor: Sikap 85 | Skill 80                         │    │
│  │   "Siswa menunjukkan progress yang baik..."  [+]    │    │
│  │   📷 2 foto                                          │    │
│  │                                                       │    │
│  │ ● 09 Jun 2026 | 14:00 | Online | Good              │    │
│  │   Skor: Sikap 80 | Skill 75                         │    │
│  │   "Siswa aktif bertanya saat meeting..."  [+]       │    │
│  │                                                       │    │
│  │ ● 26 May 2026 | 09:00 | Onsite | Good              │    │
│  │   Skor: Sikap 78 | Skill 72                         │    │
│  │   "Awal yang bagus untuk minggu pertama"  [+]       │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

---

### 6️⃣ DASHBOARD PKL (Administrator View)

```
[Login as Admin]
    ↓
[Dashboard PKL]
    ├─ Quick Stats Cards (Row 1)
    │   ├─ Total Siswa Assigned
    │   ├─ Total Tempat PKL
    │   ├─ Total Guru Pembimbing
    │   └─ Monitoring Compliance %
    │
    ├─ Charts (Row 2)
    │   ├─ Pie Chart: Siswa per Tempat (Top 5)
    │   └─ Bar Chart: Monitoring Trend (Last 30 days)
    │
    ├─ Alerts & Notifications (Row 3)
    │   ├─ 🔴 3 tempat PKL mencapai kapasitas penuh
    │   ├─ 🟡 5 siswa belum dikunjungi > 14 hari
    │   └─ 🟢 2 guru pembimbing mencapai max load
    │
    └─ Recent Activities (Row 4)
        ├─ Table: Last 10 activities
        │   ├─ User, Action, Entity, Timestamp
        │   └─ Examples: "Admin created Gelombang 2"
        │                "Guru BK assigned 5 students"
        │                "Guru Agus input monitoring for Ahmad"
        └─ [View All Activities]
```

**Wireframe Dashboard:**
```
┌─────────────────────────────────────────────────────────────┐
│  DASHBOARD PKL                                   👤 Admin    │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ 📚 SISWA │ │ 🏢 TEMPAT│ │ 👨‍🏫 GURU │ │ ✅ COMPLY│       │
│  │   150    │ │    25    │ │    12    │ │   85%    │       │
│  │ Assigned │ │   PKL    │ │ Pembimb. │ │ Monitoring│      │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                               │
│  ┌─── Siswa per Tempat ───┐ ┌─── Monitoring Trend ──────┐  │
│  │                          │ │                            │  │
│  │    [PIE CHART]           │ │    [BAR CHART]             │  │
│  │  PT Telkom: 25           │ │  Jun 1-7:  12 visit        │  │
│  │  PT Mandiri: 20          │ │  Jun 8-14: 15 visit        │  │
│  │  PT Unilever: 15         │ │  Jun 15-21: 18 visit       │  │
│  │  Lainnya: 90             │ │  Jun 22-28: 20 visit       │  │
│  │                          │ │                            │  │
│  └──────────────────────────┘ └────────────────────────────┘  │
│                                                               │
│  ⚠️ ALERTS & NOTIFICATIONS                                   │
│  ├─ 🔴 3 tempat PKL mencapai kapasitas penuh                 │
│  ├─ 🟡 5 siswa belum dikunjungi lebih dari 14 hari           │
│  └─ 🟢 2 guru pembimbing mencapai max load (15 siswa)        │
│                                                               │
│  📋 RECENT ACTIVITIES                      [View All →]      │
│  ├─ 23 Jun 14:30 | Admin | Created Gelombang 3              │
│  ├─ 23 Jun 10:45 | Guru BK | Assigned 8 students            │
│  ├─ 23 Jun 09:15 | Guru Agus | Input monitoring for Ahmad   │
│  ├─ 22 Jun 16:20 | Guru BK | Moved student Budi             │
│  └─ 22 Jun 11:00 | Admin | Updated placement PT Telkom      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎨 UI/UX DESIGN PRINCIPLES

### **1. Consistency**
- Use Tailwind CSS components consistent dengan existing e-KALDIK
- Color scheme: Purple (#9333EA) untuk PKL-related features
- Icons: Heroicons (same as existing)
- Layout: Livewire + Alpine.js (same stack)

### **2. Responsive Design**
- Mobile-first approach
- Breakpoints: sm (640px), md (768px), lg (1024px), xl (1280px)
- Tabel besar → horizontal scroll di mobile
- Cards stack vertical di mobile, grid di desktop

### **3. Performance**
- Lazy loading untuk tables (pagination 50 items)
- Image lazy loading untuk photos
- Debounce untuk search/filter (300ms)
- Skeleton loaders untuk loading states

### **4. Accessibility**
- Color contrast WCAG AA compliant
- Keyboard navigation support
- Screen reader friendly (aria labels)
- Focus indicators visible

### **5. Error Handling**
- Inline validation messages (real-time)
- Toast notifications untuk success/error
- Confirmation dialogs untuk destructive actions
- Helpful error messages (bukan technical jargon)

---

## 📱 RESPONSIVE BREAKPOINTS

```css
/* Mobile First */
.container {
  @apply px-4;                    /* Mobile (< 640px) */
}

@screen sm {
  .container {
    @apply px-6;                  /* Small (640px+) */
  }
}

@screen md {
  .container {
    @apply px-8;                  /* Medium (768px+) */
  }
  .stats-grid {
    @apply grid-cols-2;           /* 2 columns */
  }
}

@screen lg {
  .container {
    @apply px-10;                 /* Large (1024px+) */
  }
  .stats-grid {
    @apply grid-cols-4;           /* 4 columns */
  }
}
```

---

**STATUS:** ✅ USER FLOW COMPLETE  
**NEXT:** Struktur Folder Project

# User Flow - e-KALDIK

## 1. Authentication Flow

```
┌─────────────┐
│ Login Page  │
└──────┬──────┘
       │
       ▼
┌─────────────────────┐
│ Input Username &    │
│ Password            │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐     NO     ┌──────────────┐
│ Validate Credentials├──────────►│ Show Error   │
└──────┬──────────────┘            └──────┬───────┘
       │ YES                               │
       ▼                                   │
┌─────────────────────┐                   │
│ Check is_active     │◄──────────────────┘
└──────┬──────────────┘
       │ Active
       ▼
┌─────────────────────┐
│ Create Session      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Log Activity        │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Redirect to         │
│ Dashboard           │
└─────────────────────┘
```

### Flow Description:
1. User mengakses halaman login
2. Input username dan password
3. Sistem validasi credentials
4. Jika valid dan user active, create session
5. Log aktivitas login
6. Redirect ke dashboard sesuai role

---

## 2. Admin Flow - Setup Tahun Pelajaran Baru

```
┌─────────────────────┐
│ Admin Dashboard     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Menu Tahun Pelajaran│
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Klik "Tambah Baru"  │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Form Input:         │
│ - Tahun (2025/2026) │
│ - Tanggal Mulai     │
│ - Tanggal Selesai   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐     NO     ┌──────────────┐
│ Validasi Input      ├──────────►│ Show Error   │
└──────┬──────────────┘            └──────┬───────┘
       │ YES                               │
       ▼                                   │
┌─────────────────────┐                   │
│ Check Duplikasi     │◄──────────────────┘
└──────┬──────────────┘
       │ Tidak Ada
       ▼
┌─────────────────────┐
│ Simpan Tahun Ajar   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Auto Generate       │
│ 2 Semester:         │
│ - Semester Ganjil   │
│ - Semester Genap    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Tanya: Aktifkan?    │
└──┬────────────────┬─┘
   │ YA             │ TIDAK
   ▼                ▼
┌──────────────┐   ┌──────────────┐
│ Set Active   │   │ Simpan Draft │
│ Non-aktifkan │   └──────────────┘
│ Tahun Lain   │
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Success Message     │
│ Redirect to List    │
└─────────────────────┘
```

---

## 3. Waka Kurikulum Flow - Membuat Kalender Kegiatan

```
┌─────────────────────┐
│ Dashboard           │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Menu Kalender       │
│ Pendidikan          │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Pilih View:         │
│ - Bulanan           │
│ - Tahunan           │
│ - Daftar Agenda     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Klik "Tambah        │
│ Kegiatan"           │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Form Input:         │
│ - Nama Kegiatan     │
│ - Jenis Kegiatan    │
│ - Tanggal Mulai     │
│ - Tanggal Selesai   │
│ - Semester          │
│ - Warna (optional)  │
│ - Keterangan        │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐     NO     ┌──────────────┐
│ Validasi:           ├──────────►│ Show Error   │
│ - Date range valid  │            └──────┬───────┘
│ - Dalam range       │                   │
│   tahun pelajaran   │                   │
└──────┬──────────────┘                   │
       │ YES                               │
       ▼                                   │
┌─────────────────────┐                   │
│ Check Bentrok?      │◄──────────────────┘
└──┬────────────────┬─┘
   │ Ya             │ Tidak
   ▼                ▼
┌──────────────┐   ┌──────────────┐
│ Show Warning │   │ Simpan Data  │
│ Tetap Lanjut?│   └──────┬───────┘
└──┬───────────┘          │
   │ Ya                   │
   ▼                      │
┌──────────────┐          │
│ Simpan Data  │◄─────────┘
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Auto Recalculate    │
│ Hari Efektif        │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Show Success        │
│ Update Calendar View│
└─────────────────────┘
```

---

## 4. Waka Kurikulum Flow - Import Excel

```
┌─────────────────────┐
│ Kalender Page       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Klik "Import Excel" │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Download Template   │
│ (Optional)          │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Upload File Excel   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐     NO     ┌──────────────┐
│ Validasi File:      ├──────────►│ Show Error   │
│ - Format (.xlsx)    │            └──────┬───────┘
│ - Max size (2MB)    │                   │
└──────┬──────────────┘                   │
       │ YES                               │
       ▼                                   │
┌─────────────────────┐                   │
│ Parse Excel         │◄──────────────────┘
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Validasi Data:      │
│ - Required fields   │
│ - Date format       │
│ - Jenis kegiatan    │
│ - Semester valid    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Show Preview Table: │
│ - Valid rows (hijau)│
│ - Error rows (merah)│
│ - Total summary     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Konfirmasi Import?  │
└──┬────────────────┬─┘
   │ Ya             │ Batal
   ▼                ▼
┌──────────────┐   ┌──────────────┐
│ Process      │   │ Cancel       │
│ Import       │   └──────────────┘
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Insert Valid Rows   │
│ to Database         │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Log Import Activity │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Recalculate         │
│ Hari Efektif        │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Show Result:        │
│ - Success: 23       │
│ - Failed: 2         │
│ - Download log      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Redirect to         │
│ Calendar View       │
└─────────────────────┘
```

---

## 5. Waka Kurikulum Flow - Export PDF

```
┌─────────────────────┐
│ Kalender Page       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Klik "Export PDF"   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Modal Export:       │
│ - Pilih Jenis:      │
│   • Tahunan         │
│   • Bulanan         │
│   • Daftar Agenda   │
│ - Pilih Orientasi:  │
│   • Landscape       │
│   • Portrait        │
│ - Include Logo: ✓   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Klik "Generate PDF" │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Show Loading        │
│ "Generating..."     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Server Generate:    │
│ - Get data from DB  │
│ - Load template     │
│ - Insert logo       │
│ - Format content    │
│ - Generate PDF      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Download PDF File   │
│ kalender-2024.pdf   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Show Success Toast  │
└─────────────────────┘
```

---

## 6. Guru Flow - View Kalender (Read Only)

```
┌─────────────────────┐
│ Login as Guru       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Dashboard           │
│ - Tahun Aktif       │
│ - Agenda Terdekat   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Menu Kalender       │
│ (Read Only)         │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Pilih View:         │
│ - Bulanan           │
│ - Tahunan           │
│ - Daftar Agenda     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Lihat Kalender      │
│ (No Edit/Delete)    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Filter:             │
│ - Bulan             │
│ - Semester          │
│ - Jenis Kegiatan    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Klik Detail Kegiatan│
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Modal Detail:       │
│ - Nama Kegiatan     │
│ - Jenis             │
│ - Tanggal           │
│ - Semester          │
│ - Keterangan        │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Export untuk Pribadi│
│ - PDF               │
│ - Excel             │
└─────────────────────┘
```

---

## 7. Dashboard Flow (All Roles)

```
┌─────────────────────┐
│ Login Success       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Load Dashboard      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Show Cards:         │
│                     │
│ ┌────────┐┌────────┐│
│ │Tahun   ││Jumlah  ││
│ │Aktif   ││Kegiatan││
│ └────────┘└────────┘│
│                     │
│ ┌────────┐┌────────┐│
│ │Hari    ││Minggu  ││
│ │Efektif ││Efektif ││
│ └────────┘└────────┘│
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Agenda Terdekat     │
│ (7 hari ke depan)   │
│                     │
│ • 15 Sep - PTS      │
│ • 17 Aug - Libur    │
│ • 20 Sep - Rapat    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Chart Kegiatan      │
│ Per Bulan (Bar)     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Quick Actions:      │
│ [+ Kegiatan Baru]   │
│ [📅 Lihat Kalender] │
│ [📊 Hari Efektif]   │
└─────────────────────┘
        │
        │ (Based on Role)
        │
   ┌────┴────┐
   │         │
   ▼         ▼
┌────────┐ ┌────────┐
│ Admin/ │ │ Guru   │
│ Waka   │ │ (View) │
│ (Full) │ └────────┘
└────────┘
```

---

## 8. Perhitungan Hari Efektif Flow (Automated)

```
┌─────────────────────┐
│ Trigger Events:     │
│ - Kegiatan Created  │
│ - Kegiatan Updated  │
│ - Kegiatan Deleted  │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Get Semester ID     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Calculate:          │
│                     │
│ 1. Total Days       │
│    (Date range)     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ 2. Exclude Weekend  │
│    (Sat & Sun)      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ 3. Count Holiday    │
│    (is_holiday=TRUE)│
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ 4. Count Exam Days  │
│    (is_exam=TRUE)   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ 5. Calculate Study  │
│    Days:            │
│    Total - Weekend  │
│    - Holiday        │
│    - Exam           │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ 6. Calculate Weeks: │
│    Study Days / 5   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Update/Insert       │
│ effective_days      │
│ table               │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Set calculated_at   │
│ timestamp           │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Refresh Dashboard   │
│ Statistics          │
└─────────────────────┘
```

---

## 9. Role-Based Access Matrix

| Fitur | Admin | Waka Kurikulum | Guru |
|-------|-------|----------------|------|
| Dashboard | ✅ Full | ✅ Full | ✅ View Only |
| Tahun Pelajaran - View | ✅ | ✅ | ✅ |
| Tahun Pelajaran - Create | ✅ | ✅ | ❌ |
| Tahun Pelajaran - Update | ✅ | ✅ | ❌ |
| Tahun Pelajaran - Activate | ✅ | ✅ | ❌ |
| Tahun Pelajaran - Archive | ✅ | ✅ | ❌ |
| Master Jenis Kegiatan - View | ✅ | ✅ | ✅ |
| Master Jenis Kegiatan - Manage | ✅ | ✅ | ❌ |
| Kalender - View | ✅ | ✅ | ✅ |
| Kalender - Create | ✅ | ✅ | ❌ |
| Kalender - Update | ✅ | ✅ | ❌ |
| Kalender - Delete | ✅ | ✅ | ❌ |
| Hari Efektif - View | ✅ | ✅ | ✅ |
| Import Excel | ✅ | ✅ | ❌ |
| Export PDF | ✅ | ✅ | ✅ |
| Export Excel | ✅ | ✅ | ✅ |
| User Management | ✅ | ❌ | ❌ |
| Settings | ✅ | ❌ | ❌ |

---

## 10. Error Handling Flow

```
┌─────────────────────┐
│ User Action         │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Try Process         │
└──────┬──────────────┘
       │
       ▼
    ┌──┴──┐
    │Error?│
    └──┬──┘
   Yes │ No
       │ │
   ┌───┘ └───┐
   ▼         ▼
┌────────┐ ┌────────┐
│Catch   │ │Success │
│Error   │ │Process │
└───┬────┘ └───┬────┘
    │          │
    ▼          │
┌────────────┐ │
│Classify:   │ │
│- Validation│ │
│- Database  │ │
│- System    │ │
└───┬────────┘ │
    │          │
    ▼          │
┌────────────┐ │
│Log Error   │ │
└───┬────────┘ │
    │          │
    ▼          │
┌────────────┐ │
│Show User-  │ │
│Friendly    │ │
│Message     │ │
└───┬────────┘ │
    │          │
    ▼          ▼
┌────────────────┐
│Stay on Page /  │
│Redirect        │
└────────────────┘
```

**Error Message Examples**:
- Validation: "Tanggal selesai harus lebih besar dari tanggal mulai"
- Database: "Gagal menyimpan data. Silakan coba lagi"
- System: "Terjadi kesalahan sistem. Hubungi administrator"
- Permission: "Anda tidak memiliki akses untuk fitur ini"

# 📊 Monitoring Kelengkapan Perangkat Ajar

## 🎯 Konsep

Sistem monitoring ini melacak kelengkapan **7 dokumen perencanaan wajib** yang harus diupload oleh setiap guru untuk setiap mata pelajaran yang mereka ajar.

### 7 Dokumen Wajib:
1. ✅ **CP** - Capaian Pembelajaran
2. ✅ **ATP** - Alur Tujuan Pembelajaran  
3. ✅ **KKTP** - Kriteria Ketercapaian Tujuan Pembelajaran
4. ✅ **PROTA** - Program Tahunan
5. ✅ **PROSEM** - Program Semester
6. ✅ **Modul Ajar**
7. ✅ **Modul Projek**

## 🔄 Cara Kerja

### 1. **Auto-Generate dari Jadwal Mengajar**
- Sistem membaca tabel `teaching_schedules`
- Untuk setiap kombinasi **Guru + Mata Pelajaran** yang unik, sistem membuat record di `teacher_subject_requirements`
- Data otomatis di-generate berdasarkan jadwal yang aktif

### 2. **Auto-Update saat Upload**
- Ketika guru upload perangkat ajar, sistem otomatis update status kelengkapan
- Event listener di model `TeachingMaterial` akan trigger update
- Percentage kelengkapan dihitung otomatis: (jumlah dokumen lengkap / 7) × 100%

### 3. **Status Kelengkapan**
- 🔴 **Belum Lengkap** (0-39%): Merah
- 🟠 **Sebagian** (40-69%): Orange
- 🟡 **Hampir Lengkap** (70-99%): Yellow
- 🟢 **Lengkap** (100%): Green

## 📱 Akses Menu

### Admin & Waka Kurikulum:
```
Perangkat Ajar (Dropdown)
├── 📖 Lihat Semua
├── ⏳ Approval
└── 📊 Monitoring Kelengkapan  ← BARU!
```

### Kepala Sekolah:
```
Perangkat Ajar (Dropdown)
├── 📖 Lihat Semua
└── 📊 Monitoring Kelengkapan  ← BARU!
```

### Guru:
```
📚 Perangkat Ajar (Link langsung)
```

## 🚀 Cara Penggunaan

### Langkah 1: Sync Requirements dari Jadwal
1. Buka menu **Perangkat Ajar → Monitoring Kelengkapan**
2. Klik tombol **"Sync dari Jadwal"** (biru)
3. Sistem akan generate data requirements berdasarkan `teaching_schedules`

### Langkah 2: Refresh Data Upload
1. Klik tombol **"Refresh Data"** (hijau)
2. Sistem akan scan semua perangkat ajar yang sudah diupload
3. Status kelengkapan akan di-update otomatis

### Langkah 3: Monitoring
- Lihat progress bar untuk setiap guru per mapel
- Cek dokumen mana yang sudah/belum diupload (badge dengan ✓ atau ✗)
- Filter berdasarkan:
  - **Tahun Akademik**
  - **Status** (Semua / Lengkap / Belum Lengkap)
  - **Search** (nama guru atau mapel)

## 📊 Fitur Dashboard

### 1. Statistics Cards
- **Total Tugas**: Total assignments (guru × mapel)
- **Lengkap**: Jumlah yang sudah 100%
- **Belum Lengkap**: Jumlah yang belum 100%
- **Rata-rata**: Average completion percentage

### 2. Data Table
Setiap baris menampilkan:
- **Guru**: Nama & email
- **Mata Pelajaran**: Nama & kode mapel
- **Kelengkapan**: Progress bar dengan percentage
- **Dokumen**: Badge untuk 7 dokumen (hijau = ada, abu = belum)
- **Upload Terakhir**: Timestamp relative (e.g., "2 hari lalu")
- **Status**: Label dengan warna (Lengkap/Hampir/Sebagian/Belum)

### 3. Filter & Search
- Dropdown tahun akademik
- Dropdown status kelengkapan
- Search box untuk nama guru/mapel
- Pagination untuk data besar

## 🗄️ Database Structure

### Tabel: `teacher_subject_requirements`

```sql
CREATE TABLE teacher_subject_requirements (
    id BIGINT PRIMARY KEY,
    teacher_id BIGINT,           -- Foreign key ke users
    subject_id BIGINT,            -- Foreign key ke subjects
    academic_year_id BIGINT,      -- Foreign key ke academic_years
    
    -- Status per dokumen
    has_cp BOOLEAN,
    has_atp BOOLEAN,
    has_kktp BOOLEAN,
    has_prota BOOLEAN,
    has_prosem BOOLEAN,
    has_modul_ajar BOOLEAN,
    has_modul_projek BOOLEAN,
    
    -- Metadata
    completion_percentage INT,    -- 0-100
    last_upload_at TIMESTAMP,
    completed_at TIMESTAMP,
    
    -- Unique constraint
    UNIQUE(teacher_id, subject_id, academic_year_id)
);
```

## 🔧 Technical Details

### Model: `TeacherSubjectRequirement`

**Methods:**
- `calculateCompletion()`: Hitung percentage kelengkapan
- `updateCompletion()`: Update percentage + timestamp
- `getMissingDocuments()`: Array dokumen yang belum ada
- `getCompletedDocuments()`: Array dokumen yang sudah ada
- `isComplete()`: Boolean apakah sudah 100%

**Static Methods:**
- `syncFromSchedules($academicYearId)`: Generate requirements dari jadwal
- `updateFromMaterials($teacherId, $subjectId, $academicYearId)`: Update dari upload

**Scopes:**
- `forTeacher($teacherId)`: Filter by teacher
- `forAcademicYear($academicYearId)`: Filter by tahun akademik
- `complete()`: Hanya yang 100%
- `incomplete()`: Hanya yang < 100%

### Auto-Update Mechanism

Di model `TeachingMaterial`, ada event listener:

```php
protected static function booted()
{
    // Auto-update setelah save
    static::saved(function ($material) {
        TeacherSubjectRequirement::updateFromMaterials(
            $material->created_by,
            $material->subject_id,
            $material->academic_year_id
        );
    });

    // Auto-update setelah delete
    static::deleted(function ($material) {
        TeacherSubjectRequirement::updateFromMaterials(
            $material->created_by,
            $material->subject_id,
            $material->academic_year_id
        );
    });
}
```

## 📝 Use Cases

### Use Case 1: Admin/Waka Monitoring Kelengkapan
**Aktor:** Admin / Waka Kurikulum  
**Flow:**
1. Login sebagai admin/waka
2. Buka menu **Perangkat Ajar → Monitoring Kelengkapan**
3. Klik **"Sync dari Jadwal"** untuk generate data
4. Lihat daftar guru dan progress kelengkapan
5. Identifikasi guru yang belum lengkap
6. Beri reminder atau tindak lanjut

### Use Case 2: Kepsek Lihat Laporan
**Aktor:** Kepala Sekolah  
**Flow:**
1. Login sebagai kepsek
2. Buka menu **Perangkat Ajar → Monitoring Kelengkapan**
3. Lihat statistik global (berapa % yang sudah lengkap)
4. Filter berdasarkan status "Belum Lengkap"
5. Export/print laporan untuk rapat

### Use Case 3: Guru Upload Dokumen
**Aktor:** Guru  
**Flow:**
1. Login sebagai guru
2. Buka menu **Perangkat Ajar**
3. Klik **"Upload Baru"**
4. Pilih kategori dokumen (misal: CP)
5. Upload file
6. **Sistem otomatis update status kelengkapan** di backend
7. Admin/Waka bisa langsung lihat progress di monitoring

## 🎨 UI/UX Features

### Visual Indicators
- **Progress Bar**: Visual representation of completion
- **Color Coding**: 
  - 🔴 Red: Urgent (< 40%)
  - 🟠 Orange: Needs attention (40-69%)
  - 🟡 Yellow: Almost done (70-99%)
  - 🟢 Green: Complete (100%)
- **Badge Icons**: ✓ untuk dokumen ada, ✗ untuk belum
- **Relative Time**: "2 hari lalu", "1 minggu lalu"

### Responsive Design
- Desktop: Full table view
- Tablet: Scrollable table
- Mobile: Stacked card view (otomatis dari Tailwind)

## 🔐 Permissions

| Role | Akses |
|------|-------|
| **Admin** | ✅ Full access (monitoring, sync, refresh) |
| **Waka Kurikulum** | ✅ Full access (monitoring, sync, refresh) |
| **Kepala Sekolah** | ✅ View only (monitoring) |
| **Guru** | ❌ No access (mereka hanya upload) |
| **Siswa** | ❌ No access |

## 🚦 Routes

```php
// Monitoring (Admin, Waka, Kepsek)
Route::get('/teaching-materials/monitoring', Monitoring::class)
    ->name('teaching-materials.monitoring')
    ->middleware('check.role:admin,waka_kurikulum,kepala_sekolah');
```

## 📦 Files Created

### Migration
- `database/migrations/2026_07_27_create_teacher_subject_requirements_table.php`

### Model
- `app/Models/TeacherSubjectRequirement.php`

### Livewire Component
- `app/Livewire/TeachingMaterial/Monitoring.php`

### View
- `resources/views/livewire/teaching-material/monitoring.blade.php`

### Updated Files
- `routes/web.php` - Added monitoring route
- `resources/views/components/layouts/app.blade.php` - Updated navigation menu
- `app/Models/TeachingMaterial.php` - Added auto-update event listeners

## 🎯 Next Steps (Optional Enhancements)

1. **Export to Excel**: Download laporan kelengkapan
2. **Email Reminder**: Kirim email otomatis ke guru yang belum lengkap
3. **Deadline Tracking**: Set deadline per semester, beri warning jika mendekati deadline
4. **Detail View**: Klik nama guru → lihat detail per dokumen
5. **Notification Bell**: Real-time notification untuk admin
6. **History Log**: Track kapan dokumen diupload/dihapus
7. **Bulk Actions**: Approve/remind multiple teachers at once

## 🐛 Troubleshooting

### Q: Data tidak muncul di monitoring?
**A:** Klik tombol **"Sync dari Jadwal"** untuk generate data dari `teaching_schedules`

### Q: Progress tidak update setelah upload?
**A:** Klik tombol **"Refresh Data"** atau tunggu beberapa detik (auto-update via event)

### Q: Kenapa percentage tidak 100% padahal sudah upload semua?
**A:** Pastikan:
- Status perangkat ajar adalah **approved** atau **pending_approval** (bukan draft/rejected)
- Kategori dokumen sesuai dengan 7 kategori wajib
- `subject_id` dan `academic_year_id` cocok dengan jadwal

### Q: Guru tidak bisa lihat monitoring?
**A:** Memang by design. Guru hanya fokus upload, admin/waka/kepsek yang monitoring

## 📚 Related Documentation

- [Perangkat Ajar - User Guide](./TEACHING-MATERIALS.md) (jika ada)
- [Teaching Schedule Setup](./TEACHING-SCHEDULE.md) (jika ada)

---

**Dibuat:** 27 Juli 2026  
**Versi:** 1.0.0  
**Status:** ✅ Ready for Production

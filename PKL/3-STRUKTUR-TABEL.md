# STRUKTUR TABEL DETAIL - SISTEM PKL

**Project:** e-KALDIK - Modul PKL  
**Version:** 1.0  
**Date:** 2026-06-23

---

## 📋 DAFTAR TABEL

1. **pkl_waves** - Gelombang PKL
2. **pkl_placements** - Tempat PKL (Industri/DU/DI)
3. **pkl_students** - Penempatan Siswa
4. **pkl_supervisors** - Pembimbingan
5. **pkl_monitorings** - Catatan Monitoring
6. **pkl_student_moves** - History Perpindahan
7. **pkl_calendar_links** - Link ke Kalender
8. **pkl_settings** - Konfigurasi Sistem

---

## 1️⃣ TABLE: pkl_waves

**Deskripsi:** Menyimpan data gelombang PKL per semester/tahun ajaran

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `academic_year_id` | BIGINT UNSIGNED | NO | - | FK ke academic_years |
| `semester_id` | BIGINT UNSIGNED | NO | - | FK ke semesters |
| `name` | VARCHAR(255) | NO | - | Nama gelombang: "Gelombang 1 RPL" |
| `batch_number` | INT | NO | - | Nomor urut: 1, 2, 3 |
| `start_date` | DATE | NO | - | Tanggal mulai PKL |
| `end_date` | DATE | NO | - | Tanggal selesai PKL |
| `description` | TEXT | YES | NULL | Deskripsi tambahan |
| `status` | ENUM | NO | 'draft' | draft, active, completed, cancelled |
| `max_students` | INT | YES | NULL | Maks siswa (NULL = unlimited) |
| `is_active` | BOOLEAN | NO | TRUE | Status aktif |
| `metadata` | JSON | YES | NULL | Data tambahan (config, custom fields) |
| `created_by` | BIGINT UNSIGNED | NO | - | FK user yang create |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |

### Indexes

```sql
PRIMARY KEY (id)
UNIQUE KEY unique_wave (academic_year_id, batch_number)
INDEX idx_wave_status (status, is_active)
INDEX idx_wave_dates (start_date, end_date)
FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT
FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE RESTRICT
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
```

### Constraints

```sql
CONSTRAINT chk_wave_dates CHECK (end_date > start_date)
CONSTRAINT chk_max_students CHECK (max_students IS NULL OR max_students > 0)
```

### Business Rules

- **Unique Constraint:** Kombinasi `academic_year_id` + `batch_number` harus unique
- **Date Validation:** `end_date` harus lebih besar dari `start_date`
- **Status Flow:** draft → active → completed/cancelled
- **Max Students:** Jika NULL = tidak ada batasan, jika ada nilai harus > 0
- **Soft Delete:** Tidak menggunakan soft delete, gunakan status 'cancelled'

### Example Data

```sql
INSERT INTO pkl_waves VALUES
(1, 1, 1, 'Gelombang 1 - RPL & TKJ', 1, '2026-07-01', '2026-09-30', 
 'PKL Gelombang pertama semester ganjil', 'active', 150, TRUE, 
 '{"allow_remote": false, "require_uniform": true}', 1, NOW(), NOW()),
 
(2, 1, 2, 'Gelombang 2 - MM & AKL', 2, '2027-01-15', '2027-04-15', 
 'PKL Gelombang kedua semester genap', 'draft', 120, TRUE, NULL, 1, NOW(), NOW());
```

---

## 2️⃣ TABLE: pkl_placements

**Deskripsi:** Menyimpan data tempat PKL (industri, perusahaan, instansi)

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `wave_id` | BIGINT UNSIGNED | YES | NULL | FK ke pkl_waves (NULL = reusable) |
| `company_name` | VARCHAR(255) | NO | - | Nama perusahaan/instansi |
| `company_type` | ENUM | NO | 'other' | it, manufacturing, finance, retail, government, education, healthcare, hospitality, other |
| `address` | TEXT | NO | - | Alamat lengkap |
| `city` | VARCHAR(100) | NO | - | Kota |
| `province` | VARCHAR(100) | NO | - | Provinsi |
| `postal_code` | VARCHAR(10) | YES | NULL | Kode pos |
| `phone` | VARCHAR(20) | YES | NULL | Telepon kantor |
| `email` | VARCHAR(100) | YES | NULL | Email perusahaan |
| `contact_person` | VARCHAR(255) | YES | NULL | Nama kontak person |
| `contact_phone` | VARCHAR(20) | YES | NULL | HP kontak person |
| `capacity` | INT | NO | 1 | Kapasitas maksimal siswa |
| `latitude` | DECIMAL(10,8) | YES | NULL | Koordinat GPS (future map) |
| `longitude` | DECIMAL(11,8) | YES | NULL | Koordinat GPS (future map) |
| `partnership_status` | ENUM | NO | 'active' | active, inactive, blacklist |
| `partnership_since` | YEAR | YES | NULL | Tahun mulai kerjasama |
| `notes` | TEXT | YES | NULL | Catatan tambahan |
| `facilities` | JSON | YES | NULL | Fasilitas: {wifi, mess, transport, meal} |
| `is_active` | BOOLEAN | NO | TRUE | Status aktif |
| `metadata` | JSON | YES | NULL | Data tambahan |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete timestamp |

### Indexes

```sql
PRIMARY KEY (id)
INDEX idx_placement_wave (wave_id)
INDEX idx_placement_status (partnership_status, is_active)
INDEX idx_placement_location (city, province)
FULLTEXT INDEX idx_placement_search (company_name, city)
FOREIGN KEY (wave_id) REFERENCES pkl_waves(id) ON DELETE SET NULL
```

### Constraints

```sql
CONSTRAINT chk_capacity CHECK (capacity > 0)
```

### Business Rules

- **Reusable Placement:** Jika `wave_id` NULL, tempat bisa digunakan di semua gelombang
- **Capacity Management:** Harus selalu > 0, track real-time via computed property
- **Soft Delete:** Jangan hapus permanent jika ada history penempatan
- **Blacklist:** Tempat dengan status 'blacklist' tidak bisa dipilih untuk assignment baru
- **Facilities JSON Example:** `{"wifi": true, "mess": false, "transport": true, "meal": true}`

### Example Data

```sql
INSERT INTO pkl_placements VALUES
(1, NULL, 'PT Telkom Indonesia', 'it', 'Jl. Gatot Subroto No. 52', 
 'Jakarta Selatan', 'DKI Jakarta', '12710', '021-5215555', 'info@telkom.co.id',
 'Budi Santoso', '08123456789', 10, -6.2297, 106.8202, 
 'active', 2020, 'Perusahaan BUMN Telekomunikasi', 
 '{"wifi": true, "mess": true, "transport": true, "meal": true}',
 TRUE, NULL, NOW(), NOW(), NULL);
```

---

## 3️⃣ TABLE: pkl_students

**Deskripsi:** Menyimpan data penempatan siswa ke tempat PKL

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `wave_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_waves |
| `placement_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_placements |
| `user_id` | BIGINT UNSIGNED | NO | - | FK ke users (siswa) |
| `student_name` | VARCHAR(255) | NO | - | Denormalized nama siswa |
| `student_nisn` | VARCHAR(20) | NO | - | Denormalized NISN |
| `class` | VARCHAR(10) | NO | - | Kelas: "XII RPL 1" |
| `start_date` | DATE | NO | - | Tanggal mulai PKL (planned) |
| `end_date` | DATE | NO | - | Tanggal selesai PKL (planned) |
| `actual_start_date` | DATE | YES | NULL | Realisasi mulai |
| `actual_end_date` | DATE | YES | NULL | Realisasi selesai |
| `status` | ENUM | NO | 'assigned' | assigned, active, completed, cancelled, moved |
| `assignment_date` | DATE | NO | - | Tanggal di-assign |
| `completion_date` | DATE | YES | NULL | Tanggal selesai aktual |
| `final_score` | DECIMAL(5,2) | YES | NULL | Nilai akhir PKL (0-100) |
| `certificate_number` | VARCHAR(50) | YES | NULL | Nomor sertifikat |
| `notes` | TEXT | YES | NULL | Catatan tambahan |
| `metadata` | JSON | YES | NULL | Data tambahan |
| `created_by` | BIGINT UNSIGNED | NO | - | FK user yang assign |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete timestamp |

### Indexes

```sql
PRIMARY KEY (id)
UNIQUE KEY unique_student_wave (user_id, wave_id, status) WHERE status NOT IN ('cancelled', 'moved')
INDEX idx_student_wave_placement (wave_id, placement_id, status)
INDEX idx_student_user (user_id)
INDEX idx_student_dates (start_date, end_date)
INDEX idx_student_status (status)
FOREIGN KEY (wave_id) REFERENCES pkl_waves(id) ON DELETE RESTRICT
FOREIGN KEY (placement_id) REFERENCES pkl_placements(id) ON DELETE RESTRICT
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
```

### Constraints

```sql
CONSTRAINT chk_student_dates CHECK (end_date >= start_date)
CONSTRAINT chk_final_score CHECK (final_score IS NULL OR (final_score >= 0 AND final_score <= 100))
```

### Business Rules

- **No Double Assignment:** Satu siswa tidak boleh di-assign 2x di gelombang yang sama (kecuali status cancelled/moved)
- **Denormalization:** Simpan nama & NISN untuk performance (avoid JOIN)
- **Status Flow:** assigned → active → completed/cancelled/moved
- **Capacity Check:** Sebelum assign, cek kapasitas tempat PKL
- **Score Range:** Nilai akhir 0-100

### Status Definitions

- **assigned:** Sudah di-assign tapi belum mulai
- **active:** Sedang menjalankan PKL
- **completed:** Sudah selesai PKL
- **cancelled:** Dibatalkan (sakit, DO, dll)
- **moved:** Dipindahkan ke tempat lain

---

## 4️⃣ TABLE: pkl_supervisors

**Deskripsi:** Menyimpan data pembimbingan (guru → siswa)

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `wave_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_waves |
| `user_id` | BIGINT UNSIGNED | NO | - | FK ke users (guru) |
| `pkl_student_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_students |
| `supervisor_name` | VARCHAR(255) | NO | - | Denormalized nama guru |
| `supervisor_nip` | VARCHAR(20) | YES | NULL | NIP guru |
| `assigned_date` | DATE | NO | - | Tanggal di-assign |
| `is_primary` | BOOLEAN | NO | TRUE | Pembimbing utama/cadangan |
| `monitoring_count` | INT | NO | 0 | Cache jumlah monitoring |
| `last_visit_date` | DATE | YES | NULL | Tanggal kunjungan terakhir |
| `status` | ENUM | NO | 'active' | active, completed, replaced |
| `notes` | TEXT | YES | NULL | Catatan |
| `created_by` | BIGINT UNSIGNED | NO | - | FK user yang assign |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |

### Indexes

```sql
PRIMARY KEY (id)
INDEX idx_supervisor_user_wave (user_id, wave_id, status)
INDEX idx_supervisor_student (pkl_student_id)
INDEX idx_supervisor_status (status)
FOREIGN KEY (wave_id) REFERENCES pkl_waves(id) ON DELETE RESTRICT
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
FOREIGN KEY (pkl_student_id) REFERENCES pkl_students(id) ON DELETE CASCADE
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
```

### Constraints

```sql
CONSTRAINT chk_monitoring_count CHECK (monitoring_count >= 0)
```

### Business Rules

- **Multiple Supervisors:** Satu siswa bisa punya >1 pembimbing (primary + secondary)
- **Max Load:** Check jumlah siswa per guru <= `max_students_per_supervisor` (from settings)
- **Auto Update:** `monitoring_count` dan `last_visit_date` update otomatis saat monitoring dibuat
- **Denormalization:** Simpan nama guru untuk performa

---

## 5️⃣ TABLE: pkl_monitorings

**Deskripsi:** Menyimpan catatan kunjungan/monitoring guru pembimbing

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `supervisor_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_supervisors |
| `pkl_student_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_students |
| `visit_date` | DATE | NO | - | Tanggal kunjungan |
| `visit_time` | TIME | NO | - | Jam kunjungan |
| `visit_type` | ENUM | NO | 'onsite' | onsite, online, phone |
| `attendance_status` | ENUM | NO | 'present' | present, absent, sick, permission |
| `work_performance` | ENUM | YES | NULL | excellent, good, fair, poor |
| `attitude_score` | INT | YES | NULL | Skor sikap (0-100) |
| `skill_score` | INT | YES | NULL | Skor keterampilan (0-100) |
| `notes` | TEXT | NO | - | Catatan (required, min 10 char) |
| `issues` | TEXT | YES | NULL | Masalah yang ditemui |
| `solutions` | TEXT | YES | NULL | Solusi yang diberikan |
| `photos` | JSON | YES | NULL | Array path foto |
| `documents` | JSON | YES | NULL | Array path dokumen |
| `next_visit_plan` | DATE | YES | NULL | Rencana kunjungan berikutnya |
| `created_by` | BIGINT UNSIGNED | NO | - | FK user (guru) |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |

### Indexes

```sql
PRIMARY KEY (id)
INDEX idx_monitoring_supervisor (supervisor_id)
INDEX idx_monitoring_student_date (pkl_student_id, visit_date DESC)
INDEX idx_monitoring_date (visit_date)
FOREIGN KEY (supervisor_id) REFERENCES pkl_supervisors(id) ON DELETE CASCADE
FOREIGN KEY (pkl_student_id) REFERENCES pkl_students(id) ON DELETE CASCADE
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
```

### Constraints

```sql
CONSTRAINT chk_attitude_score CHECK (attitude_score IS NULL OR (attitude_score >= 0 AND attitude_score <= 100))
CONSTRAINT chk_skill_score CHECK (skill_score IS NULL OR (skill_score >= 0 AND skill_score <= 100))
CONSTRAINT chk_visit_date CHECK (visit_date <= CURDATE())
```

### Business Rules

- **Required Notes:** Catatan wajib diisi minimal 10 karakter
- **Past Date Only:** Tanggal kunjungan tidak boleh di masa depan
- **Score Validation:** Skor 0-100 atau NULL
- **Photo Upload:** Max 5 files, masing-masing max 5MB (validation di application layer)
- **Auto Update Parent:** Setelah create, update `pkl_supervisors.monitoring_count++` dan `last_visit_date`

### Photos/Documents JSON Format

```json
{
  "photos": [
    {"path": "storage/pkl/photos/abc123.jpg", "name": "Kunjungan 1.jpg", "size": 1024000},
    {"path": "storage/pkl/photos/def456.jpg", "name": "Diskusi dengan mentor.jpg", "size": 850000}
  ],
  "documents": [
    {"path": "storage/pkl/docs/report.pdf", "name": "Laporan Progres.pdf", "size": 500000}
  ]
}
```

---

## 6️⃣ TABLE: pkl_student_moves

**Deskripsi:** History perpindahan siswa ke tempat PKL lain

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `pkl_student_id` | BIGINT UNSIGNED | NO | - | FK ke pkl_students |
| `from_placement_id` | BIGINT UNSIGNED | NO | - | FK tempat lama |
| `to_placement_id` | BIGINT UNSIGNED | NO | - | FK tempat baru |
| `move_date` | DATE | NO | - | Tanggal pindah |
| `reason` | TEXT | NO | - | Alasan (required, min 20 char) |
| `approved_by` | BIGINT UNSIGNED | NO | - | FK user yang approve |
| `notes` | TEXT | YES | NULL | Catatan tambahan |
| `created_by` | BIGINT UNSIGNED | NO | - | FK user yang input |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |

### Indexes

```sql
PRIMARY KEY (id)
INDEX idx_move_student (pkl_student_id)
INDEX idx_move_date (move_date)
FOREIGN KEY (pkl_student_id) REFERENCES pkl_students(id) ON DELETE CASCADE
FOREIGN KEY (from_placement_id) REFERENCES pkl_placements(id) ON DELETE RESTRICT
FOREIGN KEY (to_placement_id) REFERENCES pkl_placements(id) ON DELETE RESTRICT
FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE RESTRICT
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
```

### Constraints

```sql
CONSTRAINT chk_different_placement CHECK (from_placement_id != to_placement_id)
CONSTRAINT chk_reason_length CHECK (CHAR_LENGTH(reason) >= 20)
```

### Business Rules

- **Max Moves:** Maksimal 2x perpindahan per siswa per gelombang (validation di application)
- **Reason Required:** Alasan wajib minimal 20 karakter
- **Audit Trail:** Tidak boleh dihapus (permanent record)
- **Capacity Update:** Saat move, kapasitas tempat lama +1, tempat baru -1
- **Status Update:** `pkl_students.status` berubah menjadi 'moved', create record baru dengan status 'assigned'

---

## 7️⃣ TABLE: pkl_calendar_links

**Deskripsi:** Polymorphic link antara activities table dan PKL entities

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `activity_id` | BIGINT UNSIGNED | NO | - | FK ke activities |
| `pklable_type` | VARCHAR(255) | NO | - | Model class name |
| `pklable_id` | BIGINT UNSIGNED | NO | - | ID di model tersebut |
| `display_type` | ENUM | NO | 'wave' | wave, student, monitoring |
| `color` | VARCHAR(7) | NO | '#9333EA' | Warna hex (purple) |
| `created_by` | BIGINT UNSIGNED | NO | - | FK user yang create |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |

### Indexes

```sql
PRIMARY KEY (id)
UNIQUE KEY unique_activity (activity_id)
INDEX idx_pklable (pklable_type, pklable_id)
INDEX idx_display_type (display_type)
FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
```

### Polymorphic Relationship

```php
// pklable_type examples:
'App\Models\PKLWave'     → pklable_id = pkl_waves.id
'App\Models\PKLStudent'  → pklable_id = pkl_students.id
```

### Business Rules

- **One-to-One:** Satu activity hanya bisa link ke 1 PKL entity
- **Auto Create:** Saat wave dibuat, otomatis create activity + link
- **Auto Update:** Saat wave/student updated, sync ke activities
- **Color Code:** Purple (#9333EA) untuk distinguish dari activity lain

---

## 8️⃣ TABLE: pkl_settings

**Deskripsi:** Konfigurasi sistem PKL yang bisa diubah tanpa code change

### Columns

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `key` | VARCHAR(100) | NO | - | Setting key (unique) |
| `value` | TEXT | NO | - | Setting value |
| `type` | ENUM | NO | 'string' | string, integer, boolean, json |
| `description` | TEXT | YES | NULL | Penjelasan setting |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu update |

### Indexes

```sql
PRIMARY KEY (id)
UNIQUE KEY unique_key (key)
INDEX idx_setting_key (key)
```

### Default Settings

```sql
INSERT INTO pkl_settings (key, value, type, description) VALUES
('max_students_per_supervisor', '15', 'integer', 'Maksimal siswa per guru pembimbing'),
('minimum_duration_days', '90', 'integer', 'Durasi minimal PKL dalam hari'),
('maximum_moves_per_student', '2', 'integer', 'Maksimal perpindahan tempat per siswa'),
('require_monitoring_frequency_days', '14', 'integer', 'Frekuensi monitoring minimal (hari)'),
('default_pkl_color', '#9333EA', 'string', 'Warna default untuk PKL di kalender'),
('allow_overlap_waves', 'false', 'boolean', 'Izinkan gelombang overlap tanggal'),
('require_move_approval', 'true', 'boolean', 'Perpindahan butuh approval'),
('auto_create_calendar_event', 'true', 'boolean', 'Otomatis create event di kalender'),
('photo_max_size_mb', '5', 'integer', 'Maksimal ukuran foto (MB)'),
('photo_max_count', '5', 'integer', 'Maksimal jumlah foto per monitoring'),
('company_types', '["it","manufacturing","finance","retail","government","education","healthcare","hospitality","other"]', 'json', 'Tipe perusahaan yang tersedia');
```

### Usage in Code

```php
// Helper function
function pklSetting($key, $default = null) {
    $setting = PKLSetting::where('key', $key)->first();
    if (!$setting) return $default;
    
    return match($setting->type) {
        'integer' => (int) $setting->value,
        'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
        'json' => json_decode($setting->value, true),
        default => $setting->value,
    };
}

// Example usage
$maxStudents = pklSetting('max_students_per_supervisor', 15);
$requireApproval = pklSetting('require_move_approval', true);
```

---

## 📊 DATA RELATIONSHIPS SUMMARY

```
academic_years (1) ─┬─► (N) pkl_waves
semesters (1) ──────┘

pkl_waves (1) ───────► (N) pkl_students
pkl_placements (1) ──► (N) pkl_students

users[siswa] (1) ────► (N) pkl_students
users[guru] (1) ─────► (N) pkl_supervisors

pkl_students (1) ────► (N) pkl_supervisors
pkl_students (1) ────► (N) pkl_monitorings
pkl_students (1) ────► (N) pkl_student_moves

pkl_supervisors (1) ─► (N) pkl_monitorings

activities (1) ──────► (1) pkl_calendar_links ◄── (polymorphic) ── pkl_waves/pkl_students
```

---

**STATUS:** ✅ STRUKTUR TABEL COMPLETE  
**NEXT:** User Flow & UI Wireframes

# Struktur Tabel Database - e-KALDIK

## 1. Tabel: users

**Deskripsi**: Menyimpan data pengguna sistem (Admin, Waka Kurikulum, Guru)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'waka_kurikulum', 'guru') NOT NULL DEFAULT 'guru',
    avatar VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_role (role),
    INDEX idx_users_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `name`: Nama lengkap pengguna
- `username`: Username untuk login (unique)
- `email`: Email pengguna (unique)
- `password`: Password terenkripsi (bcrypt)
- `role`: Peran pengguna dalam sistem
- `avatar`: Path ke file foto profil (optional)
- `is_active`: Status aktif pengguna
- `last_login_at`: Waktu login terakhir
- `remember_token`: Token untuk "remember me" feature

**Sample Data**:
```sql
INSERT INTO users (name, username, email, password, role) VALUES
('Administrator', 'admin', 'admin@smk.sch.id', '$2y$12$...', 'admin'),
('Budi Santoso', 'waka', 'waka@smk.sch.id', '$2y$12$...', 'waka_kurikulum'),
('Siti Nurhaliza', 'guru1', 'siti@smk.sch.id', '$2y$12$...', 'guru');
```

---

## 2. Tabel: academic_years

**Deskripsi**: Menyimpan data tahun pelajaran

```sql
CREATE TABLE academic_years (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(9) NOT NULL UNIQUE COMMENT 'Format: 2024/2025',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_academic_years_active (is_active, is_archived),
    CHECK (end_date >= start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `year`: Tahun pelajaran (format: YYYY/YYYY)
- `start_date`: Tanggal mulai tahun pelajaran (biasanya 1 Juli)
- `end_date`: Tanggal selesai tahun pelajaran (biasanya 30 Juni)
- `is_active`: Status aktif (hanya 1 yang boleh TRUE)
- `is_archived`: Status arsip (untuk tahun lama)

**Business Rules**:
- Hanya boleh ada 1 tahun pelajaran dengan `is_active = TRUE`
- Tahun pelajaran biasanya Juli - Juni tahun berikutnya

**Sample Data**:
```sql
INSERT INTO academic_years (year, start_date, end_date, is_active) VALUES
('2023/2024', '2023-07-10', '2024-06-29', FALSE),
('2024/2025', '2024-07-08', '2025-06-28', TRUE),
('2025/2026', '2025-07-14', '2026-06-27', FALSE);
```

---

## 3. Tabel: semesters

**Deskripsi**: Menyimpan data semester dalam tahun pelajaran

```sql
CREATE TABLE semesters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL COMMENT 'Contoh: Semester Ganjil 2024/2025',
    type ENUM('ganjil', 'genap') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    INDEX idx_semesters_academic_year (academic_year_id),
    INDEX idx_semesters_type (type),
    CHECK (end_date >= start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `academic_year_id`: Foreign key ke tabel academic_years
- `name`: Nama semester (untuk display)
- `type`: Jenis semester (ganjil/genap)
- `start_date`: Tanggal mulai semester
- `end_date`: Tanggal selesai semester

**Business Rules**:
- Setiap tahun pelajaran harus memiliki 2 semester
- Semester Ganjil: Juli - Desember
- Semester Genap: Januari - Juni

**Sample Data**:
```sql
INSERT INTO semesters (academic_year_id, name, type, start_date, end_date) VALUES
(2, 'Semester Ganjil 2024/2025', 'ganjil', '2024-07-08', '2024-12-21'),
(2, 'Semester Genap 2024/2025', 'genap', '2025-01-06', '2025-06-28');
```

---

## 4. Tabel: activity_types

**Deskripsi**: Menyimpan master jenis kegiatan

```sql
CREATE TABLE activity_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE COMMENT 'MPLS, PTS, PAS, PAT, etc',
    category ENUM('akademik', 'non_akademik') NOT NULL DEFAULT 'akademik',
    default_color VARCHAR(7) NOT NULL DEFAULT '#3B82F6' COMMENT 'Hex color',
    is_holiday BOOLEAN DEFAULT FALSE COMMENT 'Apakah termasuk hari libur',
    is_exam BOOLEAN DEFAULT FALSE COMMENT 'Apakah termasuk hari ujian',
    is_system BOOLEAN DEFAULT TRUE COMMENT 'System default atau custom',
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_activity_types_category (category),
    INDEX idx_activity_types_system (is_system)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `name`: Nama jenis kegiatan
- `code`: Kode unik (untuk identifier)
- `category`: Kategori kegiatan
- `default_color`: Warna default (hex code)
- `is_holiday`: Apakah kegiatan ini termasuk libur (untuk perhitungan hari efektif)
- `is_exam`: Apakah kegiatan ini ujian (untuk perhitungan hari efektif)
- `is_system`: TRUE = bawaan sistem, FALSE = custom admin
- `description`: Deskripsi jenis kegiatan
- `sort_order`: Urutan tampilan

**Sample Data**:
```sql
INSERT INTO activity_types (name, code, category, default_color, is_holiday, is_exam, is_system, sort_order) VALUES
('MPLS', 'MPLS', 'non_akademik', '#10B981', FALSE, FALSE, TRUE, 1),
('PTS', 'PTS', 'akademik', '#F59E0B', FALSE, TRUE, TRUE, 2),
('PAS', 'PAS', 'akademik', '#EF4444', FALSE, TRUE, TRUE, 3),
('PAT', 'PAT', 'akademik', '#DC2626', FALSE, TRUE, TRUE, 4),
('ANBK', 'ANBK', 'akademik', '#8B5CF6', FALSE, TRUE, TRUE, 5),
('Libur Nasional', 'LIBNAS', 'non_akademik', '#6B7280', TRUE, FALSE, TRUE, 6),
('Libur Semester', 'LIBSEM', 'non_akademik', '#3B82F6', TRUE, FALSE, TRUE, 7),
('Rapat Guru', 'RAPAT', 'non_akademik', '#14B8A6', FALSE, FALSE, TRUE, 8),
('Kegiatan Sekolah', 'KEGIATAN', 'non_akademik', '#EC4899', FALSE, FALSE, TRUE, 9);
```

---

## 5. Tabel: activities

**Deskripsi**: Menyimpan data kegiatan kalender pendidikan

```sql
CREATE TABLE activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    activity_type_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#3B82F6' COMMENT 'Override color dari activity_type',
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete',
    
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE RESTRICT,
    FOREIGN KEY (activity_type_id) REFERENCES activity_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_activities_academic_year (academic_year_id),
    INDEX idx_activities_semester (semester_id),
    INDEX idx_activities_activity_type (activity_type_id),
    INDEX idx_activities_created_by (created_by),
    INDEX idx_activities_dates (start_date, end_date),
    INDEX idx_activities_year_date (academic_year_id, start_date, end_date),
    INDEX idx_activities_deleted (deleted_at),
    
    CHECK (end_date >= start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `academic_year_id`: Foreign key ke tabel academic_years
- `semester_id`: Foreign key ke tabel semesters
- `activity_type_id`: Foreign key ke tabel activity_types
- `name`: Nama kegiatan
- `start_date`: Tanggal mulai
- `end_date`: Tanggal selesai
- `color`: Warna khusus untuk kegiatan ini (override default)
- `description`: Keterangan detail kegiatan
- `is_active`: Status aktif kegiatan
- `created_by`: User yang membuat kegiatan
- `deleted_at`: Soft delete timestamp

**Sample Data**:
```sql
INSERT INTO activities (academic_year_id, semester_id, activity_type_id, name, start_date, end_date, color, created_by) VALUES
(2, 1, 1, 'MPLS Tahun Pelajaran 2024/2025', '2024-07-08', '2024-07-10', '#10B981', 1),
(2, 1, 2, 'PTS Ganjil 2024/2025', '2024-09-23', '2024-09-28', '#F59E0B', 2),
(2, 1, 3, 'PAS Ganjil 2024/2025', '2024-12-09', '2024-12-14', '#EF4444', 2),
(2, 1, 6, 'Libur Hari Kemerdekaan RI', '2024-08-17', '2024-08-17', '#6B7280', 1),
(2, 2, 4, 'PAT 2024/2025', '2025-06-02', '2025-06-07', '#DC2626', 2);
```

---

## 6. Tabel: effective_days

**Deskripsi**: Menyimpan perhitungan hari efektif per semester

```sql
CREATE TABLE effective_days (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    semester_id BIGINT UNSIGNED NOT NULL UNIQUE,
    total_days INT NOT NULL DEFAULT 0 COMMENT 'Total hari dalam semester',
    study_days INT NOT NULL DEFAULT 0 COMMENT 'Hari belajar efektif',
    holiday_days INT NOT NULL DEFAULT 0 COMMENT 'Hari libur',
    exam_days INT NOT NULL DEFAULT 0 COMMENT 'Hari ujian',
    effective_weeks DECIMAL(5,2) NOT NULL DEFAULT 0 COMMENT 'Minggu efektif',
    calculated_at TIMESTAMP NULL COMMENT 'Terakhir dihitung',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    INDEX idx_effective_days_semester (semester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `semester_id`: Foreign key ke tabel semesters (unique)
- `total_days`: Total hari kalender dalam semester
- `study_days`: Jumlah hari belajar efektif
- `holiday_days`: Jumlah hari libur
- `exam_days`: Jumlah hari ujian
- `effective_weeks`: Jumlah minggu efektif (study_days / 5)
- `calculated_at`: Timestamp perhitungan terakhir

**Business Rules**:
- Otomatis recalculate saat ada perubahan kegiatan di semester terkait
- Sabtu & Minggu tidak dihitung (kecuali ada kegiatan khusus)
- Hari libur (is_holiday = TRUE) tidak dihitung
- Hari ujian (is_exam = TRUE) dihitung terpisah

**Sample Data**:
```sql
INSERT INTO effective_days (semester_id, total_days, study_days, holiday_days, exam_days, effective_weeks, calculated_at) VALUES
(1, 166, 112, 42, 12, 22.40, NOW()),
(2, 173, 118, 38, 17, 23.60, NOW());
```

---

## 7. Tabel: activity_logs

**Deskripsi**: Menyimpan log aktivitas user untuk audit trail

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(50) NOT NULL COMMENT 'create, update, delete, login, logout',
    model_type VARCHAR(100) NULL COMMENT 'Activity, AcademicYear, User, etc',
    model_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_activity_logs_user (user_id),
    INDEX idx_activity_logs_action (action),
    INDEX idx_activity_logs_model (model_type, model_id),
    INDEX idx_activity_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `user_id`: Foreign key ke users
- `action`: Jenis aksi (create, update, delete, login, logout)
- `model_type`: Nama model yang diakses
- `model_id`: ID record yang diakses
- `description`: Deskripsi detail aktivitas
- `ip_address`: IP address user
- `user_agent`: Browser/device info

**Sample Data**:
```sql
INSERT INTO activity_logs (user_id, action, model_type, model_id, description, ip_address) VALUES
(1, 'login', NULL, NULL, 'Admin login ke sistem', '192.168.1.100'),
(2, 'create', 'Activity', 1, 'Membuat kegiatan: MPLS Tahun Pelajaran 2024/2025', '192.168.1.101'),
(2, 'update', 'Activity', 1, 'Mengubah tanggal kegiatan MPLS', '192.168.1.101');
```

---

## 8. Tabel: import_logs

**Deskripsi**: Menyimpan log import Excel untuk tracking

```sql
CREATE TABLE import_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    filename VARCHAR(255) NOT NULL,
    total_rows INT NOT NULL DEFAULT 0,
    success_rows INT NOT NULL DEFAULT 0,
    failed_rows INT NOT NULL DEFAULT 0,
    error_details JSON NULL COMMENT 'Detail error per row',
    status ENUM('processing', 'completed', 'failed') NOT NULL DEFAULT 'processing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_import_logs_user (user_id),
    INDEX idx_import_logs_status (status),
    INDEX idx_import_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `user_id`: Foreign key ke users
- `filename`: Nama file yang diimport
- `total_rows`: Total baris dalam file
- `success_rows`: Jumlah baris berhasil diimport
- `failed_rows`: Jumlah baris gagal
- `error_details`: Detail error dalam format JSON
- `status`: Status import

**Sample Data**:
```sql
INSERT INTO import_logs (user_id, filename, total_rows, success_rows, failed_rows, status) VALUES
(2, 'kegiatan_semester_ganjil.xlsx', 25, 23, 2, 'completed');
```

---

## 9. Tabel: settings

**Deskripsi**: Menyimpan konfigurasi aplikasi

```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NULL,
    type ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
    group VARCHAR(50) NOT NULL DEFAULT 'general' COMMENT 'general, school, calendar, system',
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_settings_group (group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kolom**:
- `id`: Primary key
- `key`: Key setting (unique)
- `value`: Nilai setting
- `type`: Tipe data setting
- `group`: Grup/kategori setting
- `description`: Deskripsi setting

**Sample Data**:
```sql
INSERT INTO settings (`key`, value, type, `group`, description) VALUES
('school_name', 'SMK Negeri 1 Jakarta', 'string', 'school', 'Nama sekolah'),
('school_logo', '/storage/logo.png', 'string', 'school', 'Path logo sekolah'),
('school_address', 'Jl. Pendidikan No. 123', 'string', 'school', 'Alamat sekolah'),
('weekend_days', '["saturday","sunday"]', 'json', 'calendar', 'Hari libur akhir pekan'),
('session_timeout', '120', 'number', 'system', 'Session timeout (menit)'),
('enable_email_notification', 'false', 'boolean', 'system', 'Aktifkan notifikasi email');
```

---

## 10. Migration Order

Urutan pembuatan tabel (untuk Laravel migration):

1. `users`
2. `academic_years`
3. `semesters`
4. `activity_types`
5. `activities`
6. `effective_days`
7. `activity_logs`
8. `import_logs`
9. `settings`

---

## 11. Storage Estimation

Estimasi ukuran database untuk 1 tahun pelajaran:

| Tabel | Est. Rows | Avg. Row Size | Total Size |
|-------|-----------|---------------|------------|
| users | 50 | 1 KB | 50 KB |
| academic_years | 5 | 0.5 KB | 2.5 KB |
| semesters | 10 | 0.5 KB | 5 KB |
| activity_types | 15 | 1 KB | 15 KB |
| activities | 200 | 1 KB | 200 KB |
| effective_days | 10 | 0.5 KB | 5 KB |
| activity_logs | 5000 | 2 KB | 10 MB |
| import_logs | 50 | 5 KB | 250 KB |
| settings | 20 | 1 KB | 20 KB |
| **TOTAL** | | | **~11 MB** |

**Note**: Estimasi untuk 1 tahun operasional. Database akan grow seiring waktu, terutama tabel logs.

# ERD & DATABASE DESIGN - SISTEM PKL

**Project:** e-KALDIK - Modul PKL  
**Version:** 1.0  
**Date:** 2026-06-23

---

## 📊 ENTITY RELATIONSHIP DIAGRAM

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           PKL DATABASE SCHEMA                                   │
└─────────────────────────────────────────────────────────────────────────────────┘


        ┌──────────────────┐
        │ academic_years   │ (EXISTING TABLE)
        ├──────────────────┤
        │ • id             │
        │ • year           │
        │ • start_date     │
        │ • end_date       │
        │ • is_active      │
        └─────────┬────────┘
                  │
                  │ 1:N
                  │
        ┌─────────▼────────┐
        │  semesters       │ (EXISTING TABLE)
        ├──────────────────┤
        │ • id             │
        │ • academic_year_id│
        │ • name           │
        │ • start_date     │
        │ • end_date       │
        └─────────┬────────┘
                  │
                  │ 1:N
                  │
        ┌─────────▼────────────────┐
        │  pkl_waves               │ ★ GELOMBANG PKL
        ├──────────────────────────┤
        │ • id (PK)                │
        │ • academic_year_id (FK)  │ ───► academic_years.id
        │ • semester_id (FK)       │ ───► semesters.id
        │ • name VARCHAR(255)      │ "Gelombang 1", "Gel. 2 - RPL"
        │ • batch_number INT       │ 1, 2, 3, ...
        │ • start_date DATE        │
        │ • end_date DATE          │
        │ • description TEXT       │
        │ • status ENUM            │ draft, active, completed, cancelled
        │ • max_students INT       │ NULL = unlimited
        │ • is_active BOOLEAN      │
        │ • metadata JSON          │ {config: {...}}
        │ • created_by (FK)        │ ───► users.id
        │ • created_at             │
        │ • updated_at             │
        └────────┬─────────────────┘
                 │
                 │ 1:N
                 │
        ┌────────▼──────────────────┐
        │  pkl_placements           │ ★ TEMPAT PKL (INDUSTRI)
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • wave_id (FK) NULLABLE   │ ───► pkl_waves.id (NULL = reusable)
        │ • company_name VARCHAR    │ "PT Maju Bersama"
        │ • company_type ENUM       │ it, manufacturing, finance, retail, etc
        │ • address TEXT            │
        │ • city VARCHAR(100)       │
        │ • province VARCHAR(100)   │
        │ • postal_code VARCHAR(10) │
        │ • phone VARCHAR(20)       │
        │ • email VARCHAR(100)      │
        │ • contact_person VARCHAR  │
        │ • contact_phone VARCHAR   │
        │ • capacity INT            │ Maks siswa
        │ • latitude DECIMAL(10,8)  │ Opsional, untuk map
        │ • longitude DECIMAL(11,8) │
        │ • partnership_status ENUM │ active, inactive, blacklist
        │ • partnership_since YEAR  │ 2020, 2021, ...
        │ • notes TEXT              │
        │ • facilities JSON         │ {wifi:true, mess:true, ...}
        │ • is_active BOOLEAN       │
        │ • metadata JSON           │
        │ • created_at              │
        │ • updated_at              │
        │ • deleted_at              │ Soft delete
        └────────┬──────────────────┘
                 │
                 │ 1:N
                 │
        ┌────────▼──────────────────┐
        │  pkl_students             │ ★ PENEMPATAN SISWA
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • wave_id (FK)            │ ───► pkl_waves.id
        │ • placement_id (FK)       │ ───► pkl_placements.id
        │ • user_id (FK)            │ ───► users.id (siswa)
        │ • student_name VARCHAR    │ Denormalized
        │ • student_nisn VARCHAR    │
        │ • class VARCHAR(10)       │ "XII RPL 1"
        │ • start_date DATE         │ Bisa beda per siswa
        │ • end_date DATE           │
        │ • actual_start_date DATE  │ Realisasi
        │ • actual_end_date DATE    │
        │ • status ENUM             │ assigned, active, completed, cancelled, moved
        │ • assignment_date DATE    │
        │ • completion_date DATE    │
        │ • final_score DECIMAL     │ 0-100
        │ • certificate_number VAR  │
        │ • notes TEXT              │
        │ • metadata JSON           │
        │ • created_by (FK)         │ ───► users.id
        │ • created_at              │
        │ • updated_at              │
        │ • deleted_at              │ Soft delete
        └────┬──────────────────────┘
             │
             ├───► 1:N
             │
        ┌────▼──────────────────────┐
        │  pkl_supervisors          │ ★ PEMBIMBINGAN (GURU → SISWA)
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • wave_id (FK)            │ ───► pkl_waves.id
        │ • user_id (FK)            │ ───► users.id (guru)
        │ • pkl_student_id (FK)     │ ───► pkl_students.id
        │ • supervisor_name VARCHAR │ Denormalized
        │ • supervisor_nip VARCHAR  │
        │ • assigned_date DATE      │
        │ • is_primary BOOLEAN      │ Pembimbing utama?
        │ • monitoring_count INT    │ Cache counter
        │ • last_visit_date DATE    │
        │ • status ENUM             │ active, completed, replaced
        │ • notes TEXT              │
        │ • created_by (FK)         │ ───► users.id
        │ • created_at              │
        │ • updated_at              │
        └────────┬──────────────────┘
                 │
                 │ 1:N
                 │
        ┌────────▼──────────────────┐
        │  pkl_monitorings          │ ★ CATATAN KUNJUNGAN
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • supervisor_id (FK)      │ ───► pkl_supervisors.id
        │ • pkl_student_id (FK)     │ ───► pkl_students.id
        │ • visit_date DATE         │
        │ • visit_time TIME         │
        │ • visit_type ENUM         │ onsite, online, phone
        │ • attendance_status ENUM  │ present, absent, sick, permission
        │ • work_performance ENUM   │ excellent, good, fair, poor
        │ • attitude_score INT      │ 0-100
        │ • skill_score INT         │ 0-100
        │ • notes TEXT (required)   │
        │ • issues TEXT             │ Masalah yang ditemui
        │ • solutions TEXT          │ Solusi yang diberikan
        │ • photos JSON             │ [{path:"", name:""}, ...]
        │ • documents JSON          │ [{path:"", name:""}, ...]
        │ • next_visit_plan DATE    │
        │ • created_by (FK)         │ ───► users.id
        │ • created_at              │
        │ • updated_at              │
        └───────────────────────────┘


        ┌───────────────────────────┐
        │  pkl_student_moves        │ ★ HISTORI PERPINDAHAN
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • pkl_student_id (FK)     │ ───► pkl_students.id
        │ • from_placement_id (FK)  │ ───► pkl_placements.id
        │ • to_placement_id (FK)    │ ───► pkl_placements.id
        │ • move_date DATE          │
        │ • reason TEXT (required)  │ Min 20 char
        │ • approved_by (FK)        │ ───► users.id
        │ • notes TEXT              │
        │ • created_by (FK)         │ ───► users.id
        │ • created_at              │
        │ • updated_at              │
        └───────────────────────────┘


        ┌───────────────────────────┐
        │  activities               │ (EXISTING TABLE - KALENDER)
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • name                    │
        │ • start_date              │
        │ • end_date                │
        │ • activity_type_id        │
        │ • color                   │
        │ • ...                     │
        └────────┬──────────────────┘
                 │
                 │ 1:1 (polymorphic)
                 │
        ┌────────▼──────────────────┐
        │  pkl_calendar_links       │ ★ POLYMORPHIC LINK KE KALENDER
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • activity_id (FK)        │ ───► activities.id
        │ • pklable_type VARCHAR    │ "App\Models\PKLWave"
        │ • pklable_id INT          │ ID dari pkl_waves
        │ • display_type ENUM       │ wave, student, monitoring
        │ • color VARCHAR(7)        │ #9333EA
        │ • created_by (FK)         │ ───► users.id
        │ • created_at              │
        │ • updated_at              │
        └───────────────────────────┘
        
        Note: pklable polymorphic relationship
        - pklable_type = 'App\Models\PKLWave' → pklable_id = pkl_waves.id
        - pklable_type = 'App\Models\PKLStudent' → pklable_id = pkl_students.id


        ┌───────────────────────────┐
        │  pkl_settings             │ ★ KONFIGURASI SISTEM
        ├───────────────────────────┤
        │ • id (PK)                 │
        │ • key VARCHAR(100) UNIQUE │ "max_students_per_supervisor"
        │ • value TEXT/JSON         │ "15" atau {"min":5,"max":20}
        │ • type ENUM               │ string, integer, boolean, json
        │ • description TEXT        │ Human-readable explanation
        │ • created_at              │
        │ • updated_at              │
        └───────────────────────────┘

        Examples:
        - max_students_per_supervisor: 15
        - minimum_duration_days: 90
        - allow_replacement: true
        - require_monitoring_frequency: 14
        - default_pkl_color: "#9333EA"


        ┌───────────────────────────┐
        │  users                    │ (EXISTING TABLE)
        ├───────────────────────────┤
        │ • id                      │
        │ • name                    │
        │ • email                   │
        │ • role                    │ admin, guru, siswa
        │ • ...                     │
        └───────────────────────────┘
```

---

## 🔗 RELATIONSHIP DETAILS

### **1. academic_years → pkl_waves**
```
Type: One-to-Many
Foreign Key: pkl_waves.academic_year_id
On Delete: RESTRICT (jangan hapus tahun jika ada PKL)
Business Rule: Satu tahun pelajaran bisa punya banyak gelombang PKL
```

### **2. semesters → pkl_waves**
```
Type: One-to-Many
Foreign Key: pkl_waves.semester_id
On Delete: RESTRICT
Business Rule: Gelombang PKL harus dalam semester tertentu
```

### **3. pkl_waves → pkl_placements**
```
Type: One-to-Many (Optional/Nullable)
Foreign Key: pkl_placements.wave_id (NULLABLE)
On Delete: SET NULL
Business Rule: Tempat PKL bisa reusable di banyak gelombang
Note: Jika wave_id NULL, tempat bisa dipakai di semua gelombang
```

### **4. pkl_waves → pkl_students**
```
Type: One-to-Many
Foreign Key: pkl_students.wave_id
On Delete: RESTRICT
Business Rule: Satu gelombang punya banyak siswa
```

### **5. pkl_placements → pkl_students**
```
Type: One-to-Many
Foreign Key: pkl_students.placement_id
On Delete: RESTRICT (jangan hapus tempat jika ada siswa aktif)
Business Rule: Satu tempat bisa tampung banyak siswa (sesuai kapasitas)
```

### **6. users (siswa) → pkl_students**
```
Type: One-to-Many
Foreign Key: pkl_students.user_id
On Delete: RESTRICT
Business Rule: Satu siswa bisa punya banyak record PKL (beda gelombang/tahun)
Constraint: UNIQUE (user_id, wave_id) WHERE status NOT IN ('cancelled', 'moved')
```

### **7. pkl_students → pkl_supervisors**
```
Type: One-to-Many
Foreign Key: pkl_supervisors.pkl_student_id
On Delete: CASCADE
Business Rule: Satu siswa bisa punya >1 pembimbing (primary + secondary)
```

### **8. users (guru) → pkl_supervisors**
```
Type: One-to-Many
Foreign Key: pkl_supervisors.user_id
On Delete: RESTRICT
Business Rule: Satu guru bisa bimbing banyak siswa
Constraint: COUNT per guru <= max_students_per_supervisor (from settings)
```

### **9. pkl_supervisors → pkl_monitorings**
```
Type: One-to-Many
Foreign Key: pkl_monitorings.supervisor_id
On Delete: CASCADE
Business Rule: Satu supervisor record bisa punya banyak monitoring
```

### **10. pkl_students → pkl_monitorings**
```
Type: One-to-Many
Foreign Key: pkl_monitorings.pkl_student_id
On Delete: CASCADE
Business Rule: Satu siswa bisa punya banyak catatan monitoring
```

### **11. pkl_students → pkl_student_moves**
```
Type: One-to-Many
Foreign Key: pkl_student_moves.pkl_student_id
On Delete: CASCADE
Business Rule: Track histori perpindahan siswa
```

### **12. activities → pkl_calendar_links (Polymorphic)**
```
Type: One-to-One (Polymorphic)
Foreign Key: pkl_calendar_links.activity_id
Polymorphic: pklable_type + pklable_id
On Delete: CASCADE
Business Rule: Setiap activity bisa link ke PKL entity (wave/student)
```

---

## 📋 TABLE SPECIFICATIONS

### **Table: pkl_waves**
```sql
CREATE TABLE pkl_waves (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    batch_number INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description TEXT NULL,
    status ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft',
    max_students INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    metadata JSON NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_wave (academic_year_id, batch_number),
    INDEX idx_wave_status (status, is_active),
    INDEX idx_wave_dates (start_date, end_date),
    
    CONSTRAINT chk_wave_dates CHECK (end_date > start_date),
    CONSTRAINT chk_max_students CHECK (max_students IS NULL OR max_students > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_placements**
```sql
CREATE TABLE pkl_placements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wave_id BIGINT UNSIGNED NULL,
    company_name VARCHAR(255) NOT NULL,
    company_type ENUM('it', 'manufacturing', 'finance', 'retail', 'government', 'education', 'healthcare', 'hospitality', 'other') DEFAULT 'other',
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    contact_person VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
    capacity INT NOT NULL DEFAULT 1,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    partnership_status ENUM('active', 'inactive', 'blacklist') DEFAULT 'active',
    partnership_since YEAR NULL,
    notes TEXT NULL,
    facilities JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (wave_id) REFERENCES pkl_waves(id) ON DELETE SET NULL,
    
    INDEX idx_placement_wave (wave_id),
    INDEX idx_placement_status (partnership_status, is_active),
    INDEX idx_placement_location (city, province),
    FULLTEXT INDEX idx_placement_search (company_name, city),
    
    CONSTRAINT chk_capacity CHECK (capacity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_students**
```sql
CREATE TABLE pkl_students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wave_id BIGINT UNSIGNED NOT NULL,
    placement_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_nisn VARCHAR(20) NOT NULL,
    class VARCHAR(10) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    actual_start_date DATE NULL,
    actual_end_date DATE NULL,
    status ENUM('assigned', 'active', 'completed', 'cancelled', 'moved') DEFAULT 'assigned',
    assignment_date DATE NOT NULL,
    completion_date DATE NULL,
    final_score DECIMAL(5,2) NULL,
    certificate_number VARCHAR(50) NULL,
    notes TEXT NULL,
    metadata JSON NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (wave_id) REFERENCES pkl_waves(id) ON DELETE RESTRICT,
    FOREIGN KEY (placement_id) REFERENCES pkl_placements(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_student_wave (user_id, wave_id, status) WHERE status NOT IN ('cancelled', 'moved'),
    INDEX idx_student_wave_placement (wave_id, placement_id, status),
    INDEX idx_student_user (user_id),
    INDEX idx_student_dates (start_date, end_date),
    INDEX idx_student_status (status),
    
    CONSTRAINT chk_student_dates CHECK (end_date >= start_date),
    CONSTRAINT chk_final_score CHECK (final_score IS NULL OR (final_score >= 0 AND final_score <= 100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_supervisors**
```sql
CREATE TABLE pkl_supervisors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wave_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    pkl_student_id BIGINT UNSIGNED NOT NULL,
    supervisor_name VARCHAR(255) NOT NULL,
    supervisor_nip VARCHAR(20) NULL,
    assigned_date DATE NOT NULL,
    is_primary BOOLEAN DEFAULT TRUE,
    monitoring_count INT DEFAULT 0,
    last_visit_date DATE NULL,
    status ENUM('active', 'completed', 'replaced') DEFAULT 'active',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (wave_id) REFERENCES pkl_waves(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (pkl_student_id) REFERENCES pkl_students(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_supervisor_user_wave (user_id, wave_id, status),
    INDEX idx_supervisor_student (pkl_student_id),
    INDEX idx_supervisor_status (status),
    
    CONSTRAINT chk_monitoring_count CHECK (monitoring_count >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_monitorings**
```sql
CREATE TABLE pkl_monitorings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supervisor_id BIGINT UNSIGNED NOT NULL,
    pkl_student_id BIGINT UNSIGNED NOT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    visit_type ENUM('onsite', 'online', 'phone') DEFAULT 'onsite',
    attendance_status ENUM('present', 'absent', 'sick', 'permission') DEFAULT 'present',
    work_performance ENUM('excellent', 'good', 'fair', 'poor') NULL,
    attitude_score INT NULL,
    skill_score INT NULL,
    notes TEXT NOT NULL,
    issues TEXT NULL,
    solutions TEXT NULL,
    photos JSON NULL,
    documents JSON NULL,
    next_visit_plan DATE NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (supervisor_id) REFERENCES pkl_supervisors(id) ON DELETE CASCADE,
    FOREIGN KEY (pkl_student_id) REFERENCES pkl_students(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_monitoring_supervisor (supervisor_id),
    INDEX idx_monitoring_student_date (pkl_student_id, visit_date DESC),
    INDEX idx_monitoring_date (visit_date),
    
    CONSTRAINT chk_attitude_score CHECK (attitude_score IS NULL OR (attitude_score >= 0 AND attitude_score <= 100)),
    CONSTRAINT chk_skill_score CHECK (skill_score IS NULL OR (skill_score >= 0 AND skill_score <= 100)),
    CONSTRAINT chk_visit_date CHECK (visit_date <= CURDATE())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_student_moves**
```sql
CREATE TABLE pkl_student_moves (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pkl_student_id BIGINT UNSIGNED NOT NULL,
    from_placement_id BIGINT UNSIGNED NOT NULL,
    to_placement_id BIGINT UNSIGNED NOT NULL,
    move_date DATE NOT NULL,
    reason TEXT NOT NULL,
    approved_by BIGINT UNSIGNED NOT NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (pkl_student_id) REFERENCES pkl_students(id) ON DELETE CASCADE,
    FOREIGN KEY (from_placement_id) REFERENCES pkl_placements(id) ON DELETE RESTRICT,
    FOREIGN KEY (to_placement_id) REFERENCES pkl_placements(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_move_student (pkl_student_id),
    INDEX idx_move_date (move_date),
    
    CONSTRAINT chk_different_placement CHECK (from_placement_id != to_placement_id),
    CONSTRAINT chk_reason_length CHECK (CHAR_LENGTH(reason) >= 20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_calendar_links**
```sql
CREATE TABLE pkl_calendar_links (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id BIGINT UNSIGNED NOT NULL,
    pklable_type VARCHAR(255) NOT NULL,
    pklable_id BIGINT UNSIGNED NOT NULL,
    display_type ENUM('wave', 'student', 'monitoring') DEFAULT 'wave',
    color VARCHAR(7) DEFAULT '#9333EA',
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_activity (activity_id),
    INDEX idx_pklable (pklable_type, pklable_id),
    INDEX idx_display_type (display_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Table: pkl_settings**
```sql
CREATE TABLE pkl_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NOT NULL,
    type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_setting_key (key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔢 ESTIMATED DATA VOLUME

**Assumptions:** 300 siswa kelas XII, 2 gelombang/tahun

```
pkl_waves:              10 records (5 tahun)
pkl_placements:         50 records (tempat unique)
pkl_students:        3,000 records (300 × 2 × 5)
pkl_supervisors:     3,000 records (assignment)
pkl_monitorings:     9,000 records (3 visit/student)
pkl_student_moves:     300 records (10% move)
pkl_calendar_links:    100 records
pkl_settings:           20 records

TOTAL:              ~15,480 records
Storage:            ~30-50 MB (tanpa photos)
```

---

**STATUS:** ✅ ERD COMPLETE  
**NEXT:** Struktur Tabel Detail


# Entity Relationship Diagram (ERD) - e-KALDIK

## 1. Diagram ERD

```
┌─────────────────────┐
│       users         │
├─────────────────────┤
│ id (PK)            │
│ name               │
│ username           │
│ email              │
│ password           │
│ role               │
│ avatar             │
│ is_active          │
│ last_login_at      │
│ created_at         │
│ updated_at         │
└─────────────────────┘
         │
         │ 1
         │
         │ N
         ▼
┌─────────────────────┐
│   activity_logs     │
├─────────────────────┤
│ id (PK)            │
│ user_id (FK)       │
│ action             │
│ model_type         │
│ model_id           │
│ description        │
│ ip_address         │
│ user_agent         │
│ created_at         │
└─────────────────────┘


┌─────────────────────┐         1        ┌─────────────────────┐
│  academic_years     │◄─────────────────│     semesters       │
├─────────────────────┤         N        ├─────────────────────┤
│ id (PK)            │                   │ id (PK)            │
│ year               │                   │ academic_year_id(FK)│
│ start_date         │                   │ name               │
│ end_date           │                   │ type               │
│ is_active          │                   │ start_date         │
│ is_archived        │                   │ end_date           │
│ created_at         │                   │ created_at         │
│ updated_at         │                   │ updated_at         │
└─────────────────────┘                   └─────────────────────┘
         │                                         │
         │ 1                                       │ 1
         │                                         │
         │ N                                       │ N
         ▼                                         ▼
┌─────────────────────┐         N        ┌─────────────────────┐
│  activity_types     │◄─────────────────│   activities        │
├─────────────────────┤         1        ├─────────────────────┤
│ id (PK)            │                   │ id (PK)            │
│ name               │                   │ academic_year_id(FK)│
│ code               │                   │ semester_id (FK)   │
│ category           │                   │ activity_type_id(FK)│
│ default_color      │                   │ name               │
│ is_holiday         │                   │ start_date         │
│ is_exam            │                   │ end_date           │
│ is_system          │                   │ color              │
│ description        │                   │ description        │
│ sort_order         │                   │ is_active          │
│ created_at         │                   │ created_by (FK)    │
│ updated_at         │                   │ created_at         │
└─────────────────────┘                   │ updated_at         │
                                          │ deleted_at         │
                                          └─────────────────────┘
                                                   │
                                                   │ 1
                                                   │
                                                   │ N
                                                   ▼
                                          ┌─────────────────────┐
                                          │  effective_days     │
                                          ├─────────────────────┤
                                          │ id (PK)            │
                                          │ semester_id (FK)   │
                                          │ total_days         │
                                          │ study_days         │
                                          │ holiday_days       │
                                          │ exam_days          │
                                          │ effective_weeks    │
                                          │ calculated_at      │
                                          │ created_at         │
                                          │ updated_at         │
                                          └─────────────────────┘


┌─────────────────────┐
│     settings        │
├─────────────────────┤
│ id (PK)            │
│ key                │
│ value              │
│ type               │
│ group              │
│ description        │
│ created_at         │
│ updated_at         │
└─────────────────────┘


┌─────────────────────┐
│   import_logs       │
├─────────────────────┤
│ id (PK)            │
│ user_id (FK)       │
│ filename           │
│ total_rows         │
│ success_rows       │
│ failed_rows        │
│ error_details      │
│ status             │
│ created_at         │
│ updated_at         │
└─────────────────────┘
```

## 2. Relationship Descriptions

### Primary Relationships

1. **users → activity_logs** (One to Many)
   - Satu user dapat memiliki banyak log aktivitas
   - Untuk audit trail sistem

2. **academic_years → semesters** (One to Many)
   - Satu tahun pelajaran memiliki 2 semester (Ganjil dan Genap)
   - Cascade delete: jika tahun pelajaran dihapus, semester ikut terhapus

3. **academic_years → activities** (One to Many)
   - Satu tahun pelajaran memiliki banyak kegiatan
   - Kegiatan terikat ke tahun pelajaran

4. **semesters → activities** (One to Many)
   - Satu semester memiliki banyak kegiatan
   - Setiap kegiatan harus berada di salah satu semester

5. **activity_types → activities** (One to Many)
   - Satu jenis kegiatan dapat digunakan untuk banyak kegiatan
   - Contoh: Jenis "PTS" digunakan untuk "PTS Ganjil", "PTS Genap"

6. **semesters → effective_days** (One to One)
   - Setiap semester memiliki satu record perhitungan hari efektif
   - Update otomatis saat ada perubahan kegiatan

7. **users → activities** (One to Many - created_by)
   - Tracking siapa yang membuat kegiatan
   - Untuk audit dan accountability

8. **users → import_logs** (One to Many)
   - Tracking import yang dilakukan user
   - Untuk audit dan troubleshooting

## 3. Cardinality Summary

| Relationship | Type | Description |
|--------------|------|-------------|
| users → activity_logs | 1:N | User has many activity logs |
| users → activities | 1:N | User creates many activities |
| users → import_logs | 1:N | User performs many imports |
| academic_years → semesters | 1:N | Year has 2 semesters |
| academic_years → activities | 1:N | Year has many activities |
| semesters → activities | 1:N | Semester has many activities |
| semesters → effective_days | 1:1 | Semester has one calculation |
| activity_types → activities | 1:N | Type is used by many activities |

## 4. Indexes Strategy

### Primary Indexes (PK)
Semua tabel menggunakan `id` sebagai Primary Key dengan auto-increment.

### Foreign Key Indexes
```sql
-- activities table
INDEX idx_activities_academic_year (academic_year_id)
INDEX idx_activities_semester (semester_id)
INDEX idx_activities_activity_type (activity_type_id)
INDEX idx_activities_created_by (created_by)

-- semesters table
INDEX idx_semesters_academic_year (academic_year_id)

-- activity_logs table
INDEX idx_activity_logs_user (user_id)

-- import_logs table
INDEX idx_import_logs_user (user_id)

-- effective_days table
INDEX idx_effective_days_semester (semester_id)
```

### Composite Indexes
```sql
-- Untuk query filter berdasarkan tahun dan tanggal
INDEX idx_activities_year_date (academic_year_id, start_date, end_date)

-- Untuk query filter berdasarkan semester dan tanggal
INDEX idx_activities_semester_date (semester_id, start_date, end_date)

-- Untuk query active year
INDEX idx_academic_years_active (is_active, is_archived)

-- Untuk performance soft delete query
INDEX idx_activities_deleted (deleted_at)
```

### Unique Indexes
```sql
-- users table
UNIQUE INDEX idx_users_username (username)
UNIQUE INDEX idx_users_email (email)

-- academic_years table
UNIQUE INDEX idx_academic_years_year (year)

-- activity_types table
UNIQUE INDEX idx_activity_types_code (code)

-- settings table
UNIQUE INDEX idx_settings_key (key)
```

## 5. Database Constraints

### Check Constraints
```sql
-- Pastikan tanggal selesai >= tanggal mulai
CHECK (end_date >= start_date) ON activities
CHECK (end_date >= start_date) ON academic_years
CHECK (end_date >= start_date) ON semesters

-- Pastikan role valid
CHECK (role IN ('admin', 'waka_kurikulum', 'guru')) ON users

-- Pastikan semester type valid
CHECK (type IN ('ganjil', 'genap')) ON semesters

-- Pastikan hanya 1 tahun aktif
-- Implementasi via application logic atau trigger
```

### Cascade Rules
```sql
-- Jika tahun pelajaran dihapus
academic_years → semesters: CASCADE
academic_years → activities: RESTRICT (harus hapus kegiatan dulu)

-- Jika semester dihapus
semesters → activities: RESTRICT
semesters → effective_days: CASCADE

-- Jika activity_type dihapus
activity_types → activities: RESTRICT (tidak boleh hapus jika masih digunakan)

-- Jika user dihapus
users → activities (created_by): SET NULL
users → activity_logs: CASCADE
users → import_logs: SET NULL
```

## 6. Data Integrity Rules

1. **Referential Integrity**: Semua foreign key harus valid
2. **Domain Integrity**: Data harus sesuai tipe dan constraint
3. **Entity Integrity**: Setiap record harus memiliki PK unique
4. **Business Logic Integrity**:
   - Hanya 1 tahun pelajaran yang `is_active = true`
   - Tanggal kegiatan harus dalam range tahun pelajaran
   - Semester harus dalam range tahun pelajaran
   - Effective days harus recalculate saat ada perubahan kegiatan

## 7. Normalization Level

Database dirancang dalam **3rd Normal Form (3NF)**:
- ✅ 1NF: Tidak ada repeating groups, setiap cell atomic
- ✅ 2NF: Tidak ada partial dependency
- ✅ 3NF: Tidak ada transitive dependency

**Denormalization yang disengaja**:
- `color` di tabel `activities` (meskipun ada di `activity_types`) untuk flexibility
- `created_by` di tabel `activities` untuk performance audit query

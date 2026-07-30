# Dynamic End Period Implementation

## 📌 Overview

Sistem sekarang mendukung **penentuan akhir periode KBM per jenjang secara dinamis** melalui kegiatan (activities). Admin tidak perlu hardcode tanggal, cukup buat kegiatan khusus yang menandai "akhir periode".

---

## 🆕 Fitur Baru

### 1. **Kolom Baru di `activity_types`**

| Kolom | Type | Description |
|-------|------|-------------|
| `marks_end_of_period` | boolean | Flag penanda: kegiatan ini menandai akhir periode KBM |
| `affects_grades` | json | Array jenjang yang terpengaruh: `["X"]`, `["XI"]`, `["XII"]`, atau kombinasi |

### 2. **Logic Penentuan End Date**

System menggunakan **3 prioritas**:

#### **PRIORITAS 1: Activity dengan `marks_end_of_period = true`**
- Sistem mencari activity yang:
  - `marks_end_of_period = true`
  - `affects_grades` contains grade yang sedang dihitung
  - Dalam semester yang sama
- Gunakan `end_date` dari activity tersebut

#### **PRIORITAS 2: Fallback ke Logic Lama**
- Jika tidak ada activity penanda
- Kelas XII semester genap: 3 bulan lebih cepat atau 31 Maret
- Backward compatibility terjaga

#### **PRIORITAS 3: Default**
- Semua kelas sampai `semester.end_date`

---

## 🛠️ Cara Penggunaan

### **Skenario 1: Akhir KBM Kelas XII**

#### Step 1: Buat/Update Activity Type
```sql
INSERT INTO activity_types (
    name, code, category, default_color,
    is_holiday, is_exam,
    marks_end_of_period, affects_grades,
    description, sort_order
) VALUES (
    'Akhir KBM Kelas XII',
    'AKHIR_KBM_XII',
    'akademik',
    '#DC2626',
    0, 0,
    1, '["XII"]',
    'Penanda akhir KBM untuk kelas XII',
    100
);
```

#### Step 2: Buat Activity untuk Semester Genap
```sql
INSERT INTO activities (
    semester_id, activity_type_id,
    name, start_date, end_date,
    color, target_grades
) VALUES (
    2, -- Semester Genap 2026/2027
    [id_activity_type_akhir_kbm],
    'Akhir KBM Kelas XII',
    '2027-03-31',
    '2027-03-31',
    '#DC2626',
    '["XII"]'
);
```

#### Step 3: Recalculate
```bash
php artisan ekaldik:calculate-days
```

**Hasil:**
- Kelas XII: Periode 1 Jan - **31 Mar 2027** (sesuai activity)
- Kelas X/XI: Periode 1 Jan - 30 Jun 2027 (full semester)

---

### **Skenario 2: Multiple Grades Selesai Lebih Cepat**

Misal ada program khusus dimana Kelas XI juga selesai cepat:

#### Activity Type:
```php
[
    'name' => 'Akhir KBM Kelas XI & XII',
    'code' => 'AKHIR_KBM_XI_XII',
    'marks_end_of_period' => true,
    'affects_grades' => ['XI', 'XII'], // Kedua jenjang
]
```

#### Activity:
```php
[
    'name' => 'Akhir KBM Kelas XI & XII',
    'start_date' => '2027-04-15',
    'end_date' => '2027-04-15',
    'target_grades' => ['XI', 'XII'],
]
```

**Hasil:**
- Kelas XII: Sampai 15 Apr 2027
- Kelas XI: Sampai 15 Apr 2027
- Kelas X: Sampai 30 Jun 2027 (full)

---

### **Skenario 3: Beda Tanggal per Grade**

Misal XII selesai 31 Maret, XI selesai 30 April:

#### Create 2 Activity Types:
```php
// Activity Type 1: Untuk XII
[
    'code' => 'AKHIR_KBM_XII',
    'marks_end_of_period' => true,
    'affects_grades' => ['XII'],
]

// Activity Type 2: Untuk XI
[
    'code' => 'AKHIR_KBM_XI',
    'marks_end_of_period' => true,
    'affects_grades' => ['XI'],
]
```

#### Create 2 Activities:
```php
// Activity 1: XII ends 31 March
[
    'activity_type_id' => [id_akhir_kbm_xii],
    'end_date' => '2027-03-31',
    'target_grades' => ['XII'],
]

// Activity 2: XI ends 30 April
[
    'activity_type_id' => [id_akhir_kbm_xi],
    'end_date' => '2027-04-30',
    'target_grades' => ['XI'],
]
```

**Hasil:**
- Kelas XII: Sampai 31 Mar 2027
- Kelas XI: Sampai 30 Apr 2027
- Kelas X: Sampai 30 Jun 2027

---

## 🎯 Keuntungan Sistem Ini

### ✅ **Fleksibilitas Tinggi**
- Admin bisa ubah tanggal akhir periode tanpa coding
- Cukup edit tanggal activity

### ✅ **Per-Grade Customization**
- Setiap jenjang bisa punya akhir periode berbeda
- Tidak terbatas hanya kelas XII

### ✅ **Multi-Scenario Support**
- Semester genap: XII selesai cepat
- Semester ganjil: Semua sama (tidak perlu buat activity)
- Special case: Program khusus, PKL, dll

### ✅ **Transparent & Visible**
- Activity muncul di kalender
- Stakeholder bisa lihat "kapan kelas XII selesai?"
- Tidak tersembunyi di kode

### ✅ **Backward Compatible**
- Jika tidak ada activity penanda → gunakan logic lama
- Tidak break existing implementation

---

## 📊 Contoh Real Implementation

### Semester Genap 2026/2027

**Setup:**
```
Semester: 1 Jan - 30 Jun 2027

Activity 1: "Ujian Sekolah Kelas XII"
- Type: UJIANSEKOLAH (marks_end_of_period = true, affects_grades = ["XII"])
- Date: 24-28 Maret 2027
- Target: Kelas XII

Activity 2: "Akhir KBM Kelas XII"  
- Type: AKHIR_KBM_XII (marks_end_of_period = true)
- Date: 31 Maret 2027
- Target: Kelas XII
```

**Perhitungan:**

System akan:
1. Cari activity dengan `marks_end_of_period = true` untuk grade XII
2. Ketemu: "Akhir KBM Kelas XII" (31 Mar 2027)
3. Set `end_date` untuk XII = **31 Mar 2027**

**Hasil:**
```
Kelas X:
  Periode: 1 Jan - 30 Jun (181 hari)
  Hari Efektif: 102 hari

Kelas XI:
  Periode: 1 Jan - 30 Jun (181 hari)
  Hari Efektif: 102 hari

Kelas XII:
  Periode: 1 Jan - 31 Mar (90 hari) ⚡
  Hari Efektif: 44 hari
```

---

## 🔧 Technical Implementation

### Code Flow

```php
// EffectiveDayService::getGradeEndDate()

// 1. Query activity penanda
$endActivity = Activity::where('semester_id', $semester->id)
    ->whereHas('activityType', function ($q) use ($grade) {
        $q->where('marks_end_of_period', true)
          ->whereJsonContains('affects_grades', $grade);
    })
    ->orderBy('end_date', 'desc')
    ->first();

// 2. Jika ada, gunakan tanggal activity
if ($endActivity) {
    return Carbon::parse($endActivity->end_date);
}

// 3. Fallback ke logic lama
if ($grade === 'XII' && $semester->type === 'genap') {
    return $semesterEnd->copy()->subMonths(3);
}

// 4. Default: full semester
return $semesterEnd;
```

### Database Schema

**activity_types:**
```
| id | name               | marks_end_of_period | affects_grades |
|----|--------------------|---------------------|----------------|
| 15 | Akhir KBM Kelas XII| 1                   | ["XII"]        |
| 16 | Akhir KBM Kelas XI | 1                   | ["XI"]         |
| 17 | Ujian Sekolah      | 1                   | ["XII"]        |
```

**activities:**
```
| id | activity_type_id | name                | end_date   | target_grades |
|----|------------------|---------------------|------------|---------------|
| 50 | 15               | Akhir KBM Kelas XII | 2027-03-31 | ["XII"]       |
| 51 | 16               | Akhir KBM Kelas XI  | 2027-04-30 | ["XI"]        |
```

---

## 📝 Migration & Deployment

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Run Seeder
```bash
php artisan db:seed --class=EndPeriodActivitySeeder
```

### Step 3: Buat Activity (via Admin Panel atau Database)
```sql
-- Contoh: Akhir KBM XII untuk Semester Genap 2026/2027
INSERT INTO activities (...) VALUES (...);
```

### Step 4: Recalculate
```bash
php artisan ekaldik:calculate-days
```

---

## 🎓 Use Cases

### 1. **Sekolah Reguler**
- Kelas XII selesai Maret (US + UTBK)
- Kelas X & XI full semester

### 2. **Program Akselerasi**
- Semua kelas selesai lebih cepat
- Buat activity untuk ["X", "XI", "XII"]

### 3. **PKL (Praktek Kerja Lapangan)**
- Kelas XI PKL 2 bulan (Maret-April)
- Set end period untuk XI di April

### 4. **Sekolah International**
- System bisa adaptasi berbagai academic calendar
- Tidak terikat pola Indonesia

---

## ⚠️ Important Notes

### 1. **Validasi Data**
- Pastikan `end_date` activity tidak melewati `semester.end_date`
- Validasi di form: "End date must be within semester period"

### 2. **Multiple Activities**
- Jika ada multiple activities dengan `marks_end_of_period = true` untuk grade yang sama
- System ambil yang **paling akhir** (ORDER BY end_date DESC)

### 3. **Recalculation Required**
- Setiap kali edit/tambah activity penanda
- **Harus run**: `php artisan ekaldik:calculate-days`

### 4. **Display di UI**
- Activity penanda akan muncul di kalender
- Bisa diberi icon khusus (⚡, 🏁, atau 🎓)

---

## 🚀 Future Enhancements

### Possible Improvements:
1. **Auto-recalculate**: Trigger calculation saat activity created/updated
2. **UI Management**: Form khusus untuk manage end period per grade
3. **Validation**: Prevent overlap, ensure logical dates
4. **Notification**: Alert admin jika ada konflik
5. **History**: Track perubahan end date per semester

---

## ✅ Conclusion

Dengan implementasi ini:
- ✅ Admin punya **full control** atas akhir periode per jenjang
- ✅ **Tidak perlu** edit code untuk ubah tanggal
- ✅ **Fleksibel** untuk berbagai skenario
- ✅ **Transparent** - visible di kalender
- ✅ **Backward compatible** - tidak break existing data

**Perfect balance antara fleksibilitas dan kemudahan!** 🎯

# 📊 Perhitungan Hari Efektif Per Jenjang Kelas

## 🎯 Konsep

Sistem perhitungan hari efektif sekarang mendukung **breakdown per jenjang kelas** (X, XI, XII) karena setiap jenjang memiliki:
- **Durasi pembelajaran berbeda**
- **Jadwal ujian berbeda**
- **Tanggal selesai KBM berbeda**

---

## 🔄 Perbedaan Per Jenjang

### **Kelas X & XI:**
- ✅ KBM penuh 1 semester (6 bulan)
- ✅ Ujian: UTS + UAS
- ✅ Selesai: Akhir semester (Juni untuk Genap, Desember untuk Ganjil)

### **Kelas XII:**
- ⚡ KBM lebih pendek (3-4 bulan)
- ⚡ Ujian: UTS + UAS + **Ujian Sekolah** + **UTBK/Ujian Kelulusan**
- ⚡ Selesai: **Lebih cepat** (Maret/April untuk Genap)

---

## 📐 Formula Perhitungan

### **1. Tanggal Akhir KBM Per Jenjang**

```php
Kelas X & XI:
- End Date = Semester End Date (Juni/Desember)

Kelas XII (Semester Genap):
- End Date = MIN(
    Semester End Date - 3 bulan,
    31 Maret
  )
- Contoh: Semester s/d 30 Juni → Kelas XII selesai 31 Maret

Kelas XII (Semester Ganjil):
- End Date = Semester End Date (Desember)
```

### **2. Perhitungan Study Days Per Jenjang**

```
Study Days = Total Days - Weekend Days - Holiday Days - Exam Days

Dimana:
- Total Days = berbeda per jenjang (karena end date berbeda)
- Weekend Days = dihitung per jenjang
- Holiday Days = dihitung per jenjang (hanya yang jatuh dalam periode)
- Exam Days = berbeda per jenjang (Kelas XII lebih banyak)
```

### **3. Exam Days Per Jenjang**

**Kelas X & XI:**
```
- UTS: 5 hari
- UAS: 5 hari
Total: 10 hari
```

**Kelas XII (Semester Genap):**
```
- UTS: 5 hari
- Ujian Sekolah: 10 hari
- UTBK/Ujian Kelulusan: 5 hari
- UAS: 5 hari (atau tidak ada, karena sudah ada Ujian Sekolah)
Total: 20 hari (default) + hari ujian dari Activity
```

**Catatan:** Hari ujian juga bisa diambil dari tabel `activities` yang memiliki `is_exam = true`

---

## 🗄️ Database Structure

### **Tabel Baru: `effective_days_by_grade`**

```sql
CREATE TABLE effective_days_by_grade (
    id BIGINT PRIMARY KEY,
    effective_day_id BIGINT FOREIGN KEY,
    grade ENUM('X', 'XI', 'XII'),
    
    -- Date range
    start_date DATE,
    end_date DATE, -- Beda per grade!
    
    -- Calculation
    total_days INT,
    weekend_days INT,
    holiday_days INT,
    exam_days INT, -- Beda per grade!
    study_days INT,
    effective_weeks DECIMAL(5,2),
    percentage DECIMAL(5,2),
    
    -- Notes
    exam_notes TEXT, -- "UTS, UAS, Ujian Sekolah, UTBK"
    
    -- Metadata
    calculated_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(effective_day_id, grade)
);
```

### **Relasi:**

```
effective_days (1) ---→ (many) effective_days_by_grade
```

---

## 📊 Contoh Data

### **Semester Genap 2024/2025 (Januari - Juni 2025)**

#### **Keseluruhan Semester:**
- **Periode:** 6 Januari - 30 Juni 2025 (176 hari)
- **Weekend:** 50 hari
- **Libur:** 10 hari (Ramadan, Lebaran)
- **Ujian:** Varies per grade
- **Study Days:** Varies per grade

---

#### **Kelas X:**
```
Periode: 6 Jan - 30 Jun 2025 (176 hari)
Weekend: 50 hari
Libur: 10 hari
Ujian: 10 hari (UTS + UAS)
Study Days: 176 - 50 - 10 - 10 = 106 hari
Effective Weeks: 106 / 5 = 21.2 minggu
Percentage: 106 / (176-50) = 84.1%
Status: Sangat Baik ✅
```

---

#### **Kelas XI:**
```
Periode: 6 Jan - 30 Jun 2025 (176 hari)
Weekend: 50 hari
Libur: 10 hari
Ujian: 10 hari (UTS + UAS)
Study Days: 176 - 50 - 10 - 10 = 106 hari
Effective Weeks: 106 / 5 = 21.2 minggu
Percentage: 106 / (176-50) = 84.1%
Status: Sangat Baik ✅
```

---

#### **Kelas XII:**
```
Periode: 6 Jan - 31 Mar 2025 (85 hari) ⚡ LEBIH PENDEK!
Weekend: 24 hari
Libur: 5 hari (sebagian libur, karena periode lebih pendek)
Ujian: 20 hari (UTS + Ujian Sekolah + UTBK + UAS)
Study Days: 85 - 24 - 5 - 20 = 36 hari
Effective Weeks: 36 / 5 = 7.2 minggu
Percentage: 36 / (85-24) = 59.0%
Status: Cukup ⚠️

⚠️ Catatan: Percentage lebih rendah karena:
1. Periode lebih pendek (3 bulan vs 6 bulan)
2. Lebih banyak hari ujian (20 vs 10)
3. Fokus ke persiapan kelulusan
```

---

## 📱 UI/UX - Comparison Table

### **Tampilan:**

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│ Perhitungan Per Jenjang Kelas                                                    │
├────────┬───────────────┬──────────────┬────────┬────────────┬──────────────────┤
│ Kelas  │ Periode       │ Hari Belajar │ Minggu │ Persentase │ Status           │
├────────┼───────────────┼──────────────┼────────┼────────────┼──────────────────┤
│   X    │ 06/01-30/06   │     106      │  21.2  │   84.1%    │ Sangat Baik ✅   │
│        │ 5.8 bulan     │              │        │            │                  │
├────────┼───────────────┼──────────────┼────────┼────────────┼──────────────────┤
│   XI   │ 06/01-30/06   │     106      │  21.2  │   84.1%    │ Sangat Baik ✅   │
│        │ 5.8 bulan     │              │        │            │                  │
├────────┼───────────────┼──────────────┼────────┼────────────┼──────────────────┤
│  XII   │ 06/01-31/03   │      36      │  7.2   │   59.0%    │ Cukup ⚠️         │
│        │ 2.8 bulan     │              │        │            │ 📝 UTS, UAS,     │
│        │ ⚡ Lebih Cepat │              │        │            │ Ujian Sekolah... │
└────────┴───────────────┴──────────────┴────────┴────────────┴──────────────────┘

ℹ️ Catatan Penting:
- Kelas XII biasanya selesai KBM lebih cepat (Maret/April) karena Ujian Sekolah
- Kelas X & XI menjalani KBM hingga akhir semester (Juni)
- Hari ujian untuk Kelas XII termasuk: UTS, UAS, Ujian Sekolah, UTBK
```

---

## 🔧 Technical Implementation

### **1. Model: `EffectiveDayByGrade`**

**Location:** `app/Models/EffectiveDayByGrade.php`

**Key Methods:**
- `getGradeLabelAttribute()` - Get "Kelas X", "Kelas XI", dll
- `getStatusColorAttribute()` - Get color: green/yellow/orange/red
- `getStatusLabelAttribute()` - Get "Sangat Baik", "Baik", "Cukup", "Kurang"
- `getDurationInMonths()` - Get duration in months
- `isGradeXII()` - Check if this is grade XII
- `hasEarlyEnd()` - Check if end date is earlier than semester end

**Scopes:**
- `grade($grade)` - Filter by specific grade
- `gradeX()`, `gradeXI()`, `gradeXII()` - Shortcuts
- `orderByGrade()` - Order by X → XI → XII

---

### **2. Service: `EffectiveDayService`**

**New Methods:**

```php
calculateByGrades(EffectiveDay $effectiveDay, Semester $semester)
// Calculate untuk semua grade (X, XI, XII)

calculateForGrade(Semester $semester, Carbon $startDate, Carbon $endDate, string $grade)
// Calculate untuk 1 grade specific

getGradeEndDate(Semester $semester, string $grade)
// Tentukan end date per grade (Kelas XII lebih cepat)

countExamDaysForGrade(Semester $semester, string $grade, Carbon $startDate, Carbon $endDate)
// Count exam days (Kelas XII lebih banyak)

countActivityDaysInRange(Semester $semester, string $type, Carbon $startDate, Carbon $endDate)
// Count holiday/exam days dalam date range specific

generateExamNotes(string $grade)
// Generate exam notes ("UTS, UAS" vs "UTS, UAS, Ujian Sekolah, UTBK")
```

---

### **3. Livewire Component: `EffectiveDay\Index`**

**Updates:**
- Load `byGrades` relation
- Auto-calculate per grade jika belum ada
- Recalculate juga update per grade

---

### **4. View:**

**Location:** `resources/views/livewire/effective-day/index.blade.php`

**New Section:**
- Comparison table per grade
- Progress bar per grade
- Badge untuk grade XII yang selesai lebih cepat
- Exam notes tooltip

---

## 🚀 Cara Menggunakan

### **1. Recalculate (Artisan Command):**

```bash
php artisan ekaldik:calculate-days
```

Output:
```
Calculating effective days...
✓ Successfully calculated effective days for 2 semester(s)
```

### **2. Recalculate (Via UI):**

1. Buka menu **Kalender Akademik → Hari Efektif**
2. Klik tombol **"Hitung Ulang Semua"** (untuk semua semester)
   ATAU
3. Klik tombol **"Hitung Ulang"** di card semester tertentu

### **3. Lihat Hasil:**

Scroll ke bawah di card semester, akan muncul tabel **"Perhitungan Per Jenjang Kelas"** dengan breakdown untuk Kelas X, XI, dan XII.

---

## 📝 Customization

### **1. Ubah Tanggal Selesai Kelas XII:**

Edit di `EffectiveDayService::getGradeEndDate()`:

```php
if ($grade === 'XII' && $semester->type === 'genap') {
    // Option 1: Fixed date (31 Maret)
    return Carbon::create($semesterEnd->year, 3, 31);
    
    // Option 2: Relative (3 bulan sebelum end)
    return $semesterEnd->copy()->subMonths(3);
    
    // Option 3: Custom per semester (via database)
    // return Carbon::parse($semester->grade_xii_end_date);
}
```

### **2. Ubah Jumlah Hari Ujian Kelas XII:**

Edit di `EffectiveDayService::countExamDaysForGrade()`:

```php
if ($grade === 'XII' && $semester->type === 'genap') {
    $examDays += 10; // Ubah angka ini
}
```

### **3. Tambah Exam Notes Custom:**

Edit di `EffectiveDayService::generateExamNotes()`:

```php
if ($grade === 'XII') {
    return 'UTS, UAS, Ujian Sekolah, UTBK, Ujian Praktik';
}
```

---

## ⚠️ Important Notes

### **1. Libur yang Jatuh di Luar Periode Kelas XII:**

Jika ada libur di bulan April-Juni, itu **TIDAK** dihitung untuk Kelas XII karena mereka sudah selesai KBM di Maret.

Contoh:
- Libur Lebaran: 20-25 April
- Kelas XII selesai: 31 Maret
- Result: Libur Lebaran **tidak** dikurangi untuk Kelas XII ✅

### **2. Ujian dari Activity Table:**

Sistem akan otomatis ambil hari ujian dari `activities` table yang memiliki `is_exam = true`.

Untuk Kelas XII, ada **tambahan** 10 hari default untuk Ujian Sekolah + UTBK (bisa di-adjust).

### **3. Percentage Calculation:**

```
Percentage = (Study Days / Total Weekdays) × 100%
```

**BUKAN** dibagi Total Days!

Contoh:
- Kelas XII: 36 study days / 61 weekdays = 59.0% ✅
- **BUKAN:** 36 / 85 total days = 42.4% ❌

---

## 🎨 Status Colors

| Percentage | Color  | Status        |
|------------|--------|---------------|
| >= 85%     | Green  | Sangat Baik   |
| 70-84%     | Yellow | Baik          |
| 50-69%     | Orange | Cukup         |
| < 50%      | Red    | Kurang        |

---

## 🐛 Troubleshooting

### **Q: Data per grade tidak muncul?**
**A:** Klik "Hitung Ulang" untuk generate data.

### **Q: Kelas XII percentage terlalu rendah?**
**A:** Normal! Karena periode lebih pendek dan banyak ujian. Fokus ke "Study Days" bukan percentage.

### **Q: Tanggal selesai Kelas XII tidak sesuai?**
**A:** Edit di `EffectiveDayService::getGradeEndDate()` untuk custom logic.

### **Q: Exam days Kelas XII tidak akurat?**
**A:** 
1. Pastikan activities dengan `is_exam = true` sudah benar
2. Atau adjust default di `countExamDaysForGrade()`

---

## 📚 References

**Files:**
- Migration: `database/migrations/2026_07_30_create_effective_days_by_grade_table.php`
- Model: `app/Models/EffectiveDayByGrade.php`
- Service: `app/Services/EffectiveDayService.php`
- Livewire: `app/Livewire/EffectiveDay/Index.php`
- View: `resources/views/livewire/effective-day/index.blade.php`

**Related Docs:**
- [EFFECTIVE-DAYS-CALCULATION.md](./EFFECTIVE-DAYS-CALCULATION.md) - Formula perhitungan
- [FIX-EFFECTIVE-DAYS-SUMMARY.md](./FIX-EFFECTIVE-DAYS-SUMMARY.md) - Summary fix percentage

---

**Last Updated:** 30 Juli 2026  
**Version:** 1.0.0  
**Status:** ✅ Production Ready

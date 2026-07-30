# 📊 Perhitungan Hari Efektif - Dokumentasi

## 🎯 Tujuan
Menghitung jumlah hari efektif belajar dalam satu semester, dengan mempertimbangkan:
- Hari weekend (Sabtu & Minggu)
- Hari libur nasional/sekolah
- Hari ujian

---

## 📐 Formula Perhitungan

### **1. Total Days**
```
Total Days = End Date - Start Date + 1
```
Contoh: 1 Juli - 31 Desember 2024 = 184 hari

### **2. Weekend Days**
```
Weekend Days = Jumlah Sabtu + Jumlah Minggu dalam periode
```
Contoh: 26 Sabtu + 26 Minggu = 52 hari

### **3. Holiday Days** (Hari Libur)
```
Holiday Days = Jumlah hari libur (weekdays only, exclude Sabtu/Minggu)
```
**Penting:** Hanya hitung **weekdays** (Senin-Jumat)
- Jika libur jatuh di weekend, **tidak dihitung**
- Jika libur Senin-Jumat, **dihitung**

Contoh:
- Libur Idul Fitri: 2-4 Juli 2024 (Selasa-Kamis) = 3 hari ✅
- Libur Tahun Baru: 31 Des 2024 (Minggu) = 0 hari ❌ (sudah weekend)

### **4. Exam Days** (Hari Ujian)
```
Exam Days = Jumlah hari ujian (weekdays only, exclude Sabtu/Minggu)
```
**Penting:** Sama seperti holiday, hanya hitung **weekdays**
- Ujian di weekend = tidak dihitung (kan tidak ada KBM normal)
- Ujian di weekdays = dihitung

Contoh:
- UTS: 1-5 September 2024 (Senin-Jumat) = 5 hari ✅
- UAS: 18-22 Desember 2024 (Senin-Jumat) = 5 hari ✅

### **5. Study Days** (Hari Belajar Efektif)
```
Study Days = Total Days - Weekend Days - Holiday Days - Exam Days
```

**Breakdown:**
1. Total Days: Semua hari dalam semester
2. Kurangi Weekend Days: Sisa = Total Weekdays
3. Kurangi Holiday Days: Sisa = Weekdays tanpa libur
4. Kurangi Exam Days: Sisa = **Hari Belajar Efektif**

**Catatan:**
- Holiday Days dan Exam Days sudah exclude weekend
- Jadi tidak ada double counting

### **6. Effective Weeks** (Minggu Efektif)
```
Effective Weeks = Study Days / 5
```
Asumsi: 1 minggu = 5 hari kerja (Senin-Jumat)

Contoh:
- 117 hari belajar = 117 / 5 = **23.4 minggu efektif**

### **7. Percentage** (Persentase Hari Efektif)
```
Percentage = (Study Days / Total Weekdays) × 100%

Total Weekdays = Total Days - Weekend Days
```

**Catatan:**
- **Bukan** dibagi Total Days (karena termasuk weekend)
- **Dibagi** Total Weekdays (hanya Senin-Jumat)

Contoh:
- Total Days: 184 hari
- Weekend Days: 52 hari
- Total Weekdays: 184 - 52 = **132 hari** (weekdays only)
- Study Days: 117 hari
- Percentage: (117 / 132) × 100% = **88.6%**

---

## 🧮 Contoh Perhitungan Lengkap

### **Semester Ganjil 2024/2025**

**Periode:** 1 Juli 2024 - 31 Desember 2024

#### **Step 1: Total Days**
```
Start: 1 Juli 2024
End: 31 Desember 2024
Total Days = 184 hari
```

#### **Step 2: Weekend Days**
```
Sabtu: 26 hari
Minggu: 26 hari
Total Weekend = 52 hari
```

#### **Step 3: Holiday Days (Weekdays Only)**
```
Libur Idul Adha: 17-18 Juni 2024 (sebelum semester, skip)
Libur Awal Ramadan: 11-12 Maret 2024 (sebelum semester, skip)
Libur Hari Kemerdekaan: 17 Agustus 2024 (Sabtu) = 0 hari (weekend)
Libur Isra Mi'raj: 27 Januari 2024 (sebelum semester, skip)
... (hitung semua libur yang jatuh di weekdays dalam periode)
Total Holiday Days = 10 hari (contoh)
```

#### **Step 4: Exam Days (Weekdays Only)**
```
UTS: 1-5 September 2024 (Senin-Jumat) = 5 hari
UAS: 18-22 Desember 2024 (Senin-Jumat) = 5 hari
Total Exam Days = 10 hari
```

#### **Step 5: Study Days**
```
Study Days = Total - Weekend - Holiday - Exam
Study Days = 184 - 52 - 10 - 10
Study Days = 112 hari
```

#### **Step 6: Effective Weeks**
```
Effective Weeks = 112 / 5
Effective Weeks = 22.4 minggu
```

#### **Step 7: Percentage**
```
Total Weekdays = 184 - 52 = 132 hari
Percentage = (112 / 132) × 100%
Percentage = 84.85%
```

---

## 📋 Summary Hasil

| Item | Jumlah |
|------|--------|
| **Total Days** | 184 hari |
| **Weekend Days** | 52 hari |
| **Holiday Days** | 10 hari |
| **Exam Days** | 10 hari |
| **Study Days** | 112 hari |
| **Effective Weeks** | 22.4 minggu |
| **Percentage** | 84.85% |

---

## 🔍 Validasi Perhitungan

### **Checklist:**
- [ ] Total Days = End Date - Start Date + 1 ✅
- [ ] Weekend Days = Sabtu + Minggu ✅
- [ ] Holiday Days hanya weekdays (exclude weekend) ✅
- [ ] Exam Days hanya weekdays (exclude weekend) ✅
- [ ] Study Days = Total - Weekend - Holiday - Exam ✅
- [ ] Study Days >= 0 (tidak boleh negatif) ✅
- [ ] Effective Weeks = Study Days / 5 ✅
- [ ] Percentage = Study Days / (Total - Weekend) × 100% ✅

### **Common Errors:**

❌ **Error 1: Double Counting Weekend**
```php
// SALAH
$percentage = ($studyDays / $totalDays) * 100;
```
**Alasan:** Total Days termasuk weekend, harusnya dibagi Total Weekdays

✅ **Benar:**
```php
$totalWeekdays = $totalDays - $weekendCount;
$percentage = ($studyDays / $totalWeekdays) * 100;
```

---

❌ **Error 2: Tidak Exclude Weekend dari Holiday**
```php
// SALAH
$holidayDays = Activity::where('is_holiday', true)->count();
```
**Alasan:** Libur yang jatuh di weekend dihitung 2 kali

✅ **Benar:**
```php
foreach ($holidays as $holiday) {
    if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
        $count++; // Only weekdays
    }
}
```

---

❌ **Error 3: Study Days Negatif**
```php
// SALAH
$studyDays = $totalDays - $weekendCount - $holidayDays - $examDays;
// Hasil bisa negatif jika holiday + exam > weekdays
```

✅ **Benar:**
```php
$studyDays = max(0, $totalDays - $weekendCount - $holidayDays - $examDays);
```

---

## 🎯 Interpretasi Hasil

### **Study Days (Hari Belajar Efektif):**
- **> 20 minggu (100 hari):** ✅ Ideal untuk 1 semester
- **15-20 minggu (75-100 hari):** ⚠️ Cukup, tapi padat
- **< 15 minggu (< 75 hari):** ❌ Kurang, susah capai target

### **Percentage (Persentase Hari Efektif):**
- **> 85%:** 🌟 Sangat baik! Mayoritas weekdays dipakai belajar
- **70-85%:** ✅ Baik, cukup efektif
- **50-70%:** ⚠️ Kurang efektif, banyak libur/ujian
- **< 50%:** ❌ Sangat kurang efektif

---

## 🛠️ Cara Recalculate

### **Via Artisan Command:**
```bash
php artisan ekaldik:calculate-days
```

### **Via Livewire:**
1. Buka menu **Kalender Akademik → Hari Efektif**
2. Klik tombol **"Hitung Ulang"**
3. Sistem akan recalculate semua semester di tahun akademik aktif

### **Kapan Harus Recalculate:**
- ✅ Setiap kali ada perubahan tanggal semester
- ✅ Setiap kali menambah/edit/hapus kegiatan libur
- ✅ Setiap kali menambah/edit/hapus kegiatan ujian
- ✅ Setiap awal semester baru
- ✅ Sebelum cetak kalender akademik

---

## 🐛 Troubleshooting

### **Q: Percentage lebih dari 100%?**
**A:** Bug! Study Days tidak boleh lebih dari Total Weekdays. Cek:
- Apakah Holiday/Exam Days sudah exclude weekend?
- Apakah ada overlap kegiatan?

### **Q: Study Days negatif?**
**A:** Bug! Kemungkinan:
- Holiday Days + Exam Days > Total Weekdays
- Ada double counting
- Sudah diperbaiki dengan `max(0, $studyDays)`

### **Q: Weekend Days tidak sesuai kalender?**
**A:** Cek setting `weekend_days` di tabel `settings`:
- Default: `['saturday', 'sunday']`
- Bisa custom, misal `['friday', 'saturday']` untuk sekolah Jumat libur

### **Q: Holiday Days termasuk weekend?**
**A:** Seharusnya tidak. Cek method `countActivityDays()`:
```php
if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
    $count++; // Only Monday-Friday
}
```

---

## 📚 Referensi

**Files Terkait:**
- `app/Services/EffectiveDayService.php` - Logic perhitungan
- `app/Models/EffectiveDay.php` - Model database
- `app/Console/Commands/CalculateEffectiveDays.php` - Artisan command
- `app/Livewire/EffectiveDay/Index.php` - UI component

**Database Tables:**
- `effective_days` - Hasil perhitungan
- `semesters` - Data semester
- `activities` - Data kegiatan (libur, ujian)
- `activity_types` - Jenis kegiatan (`is_holiday`, `is_exam`)

---

**Last Updated:** 30 Juli 2026  
**Version:** 2.0 (Fixed percentage calculation)

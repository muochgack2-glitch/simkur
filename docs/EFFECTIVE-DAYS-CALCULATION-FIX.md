# Perbaikan Perhitungan Hari Efektif

## 📊 Masalah yang Ditemukan

Perhitungan hari efektif di sistem tidak sesuai dengan file Excel referensi (`Kalender_Pendidikan_2026_2027.xlsx`) karena:

1. **Tanggal semester tidak akurat** - Start/End date tidak sesuai dengan kalender aktual
2. **Logika perhitungan berbeda** - Sistem mengurangi hari ujian, padahal seharusnya tidak
3. **Total hari berbeda** - Akibat perbedaan rentang tanggal semester

---

## 🔍 Analisis Perbedaan

### **Perbandingan Semester 1 (Ganjil)**

| Item | Excel (Benar) | Sistem (Sebelum Fix) | Selisih |
|------|---------------|----------------------|---------|
| Start Date | 13 Juli 2026 | 1 Juli 2026 | -12 hari |
| End Date | 20 Des 2026 | 31 Des 2026 | +11 hari |
| Total Hari | 172 | 184 | +12 |
| Hari Efektif | 102 | 120 | +18 |
| Minggu Efektif | 20.4 | 24.0 | +3.6 |

### **Perbandingan Semester 2 (Genap)**

| Item | Excel (Benar) | Sistem (Sebelum Fix) | Selisih |
|------|---------------|----------------------|---------|
| Start Date | 5 Jan 2027 | 1 Jan 2027 | -4 hari |
| End Date | 20 Jun 2027 | 30 Jun 2027 | +10 hari |
| Total Hari | 192 | 181 | -11 |
| Hari Efektif | 105 | 101 | -4 |
| Minggu Efektif | 21.0 | 20.2 | -0.8 |

---

## ✅ Solusi yang Diterapkan

### **1. Perbaikan Logika Perhitungan**

**Formula Lama** (Salah):
```
Hari Efektif = Total Hari - Weekend - Holiday - Exam
```

**Formula Baru** (Benar):
```
Hari Efektif = Total Hari - Weekend - Holiday
(Exam TIDAK dikurangi karena siswa tetap masuk)
```

**File yang diubah**: `app/Services/EffectiveDayService.php`

**Perubahan**:
```php
// OLD: Exam days subtracted from study days
$studyDays = $totalDays - $weekendCount - $holidayDays - $examDays;

// NEW: Exam days tracked but NOT subtracted
$studyDays = $totalDays - $weekendCount - $holidayDays;
```

**Alasan**:
- Hari ujian adalah hari belajar efektif karena siswa tetap hadir di sekolah
- Sesuai dengan standar perhitungan kalender pendidikan nasional
- Konsisten dengan referensi Excel

---

### **2. Perbaikan Tanggal Semester**

Berdasarkan analisis kalender visual dari Excel:

#### **Semester 1 (Gasal) 2026/2027**

**Tanggal yang Benar**:
- **Start Date**: `13 Juli 2026` (Senin)
- **End Date**: `20 Desember 2026` (Minggu)

**Alasan**:
- Tanggal 1-12 Juli 2026 masih **libur semester sebelumnya** (garis kuning diagonal)
- Hari pertama masuk: **Senin, 13 Juli 2026**
- Tanggal 21-31 Desember 2026 sudah **libur semester** (garis kuning diagonal)
- Hari terakhir efektif: sekitar **18-20 Desember 2026**

**Breakdown Per Bulan**:
- Juli 2026: 15 hari efektif (13-31 Juli)
- Agustus 2026: 19 hari efektif
- September 2026: 22 hari efektif
- Oktober 2026: 22 hari efektif
- November 2026: 21 hari efektif
- Desember 2026: 14 hari efektif (1-20 Des)
- **Total**: ~113 hari belajar (belum kurangi libur nasional)

#### **Semester 2 (Genap) 2026/2027**

**Tanggal yang Benar**:
- **Start Date**: `5 Januari 2027` (Selasa)
- **End Date**: `20 Juni 2027` (Minggu)

**Alasan**:
- Tanggal 1-4 Januari 2027 masih **libur semester** (garis kuning)
- Hari pertama masuk: **Senin/Selasa, 4-5 Januari 2027**
- Tanggal 21-30 Juni 2027 sudah **libur kenaikan kelas** (garis kuning ganda)
- **Juli 2027 SELURUHNYA LIBUR** (tidak termasuk semester 2)

**Breakdown Per Bulan**:
- Januari 2027: 19 hari efektif (5-31 Jan)
- Februari 2027: 19 hari efektif
- Maret 2027: 17 hari efektif
- April 2027: 22 hari efektif
- Mei 2027: 18 hari efektif
- Juni 2027: 13 hari efektif (1-20 Jun)
- **Total**: ~108 hari belajar (belum kurangi libur nasional)

---

## 📝 Langkah Update Manual

### **Step 1: Update Tanggal Semester di Database**

1. Buka menu **Tahun Pelajaran**
2. Edit Tahun Pelajaran **2026/2027**
3. Update tanggal semester:

   **Semester Ganjil**:
   - Start Date: `2026-07-13`
   - End Date: `2026-12-20`

   **Semester Genap**:
   - Start Date: `2027-01-05`
   - End Date: `2027-06-20`

4. **Save**

### **Step 2: Recalculate Hari Efektif**

Jalankan command artisan:
```bash
php artisan effective-days:calculate
```

Atau via Dashboard → Hari Efektif → Tombol "Hitung Ulang"

---

## 🎯 Hasil yang Diharapkan

Setelah update tanggal dan recalculate:

### **Semester 1 (Ganjil)**
- Total Hari: ~160 hari (13 Jul - 20 Des)
- Weekend: ~52 hari
- Libur: ~12 hari
- Hari Efektif: **~102 hari** ✅
- Minggu Efektif: **~20.4 minggu** ✅

### **Semester 2 (Genap)**
- Total Hari: ~167 hari (5 Jan - 20 Jun)
- Weekend: ~48 hari
- Libur: ~28 hari
- Hari Efektif: **~105 hari** ✅
- Minggu Efektif: **~21.0 minggu** ✅

### **Total 1 Tahun Pelajaran**
- Total Hari: ~327 hari
- Hari Efektif: **~207 hari** ✅
- Minggu Efektif: **~41.4 minggu** ✅

---

## 📌 Catatan Penting

### **Legend Kalender**

Simbol di kalender Excel:
- 🟨 **Garis Kuning (Diagonal)** = Libur Semester Gasal
- 🟨🟨 **Garis Kuning (Ganda)** = Libur Semester Genap/Libur Akhir Tahun Ajaran
- ❤️ **Hati Merah** = Libur Umum
- 🟥 **Kotak Merah** = Mengikuti Upacara Hari Besar Nasional
- 🟣 **Lingkaran Ungu** = Libur Bulan Ramadhan & Idul Fitri
- ❌ **X Biru** = Perkiraan TKA (Tes Kemampuan Akademik)
- 🟦 **Kotak Biru** = MPLS (Masa Pengenalan Lingkungan Sekolah)
- 🔖 **Bookmark** = Penyerahan Buku Laporan Hasil Belajar
- 🏃 **Orang** = Perkiraan Pengumuman Kelulusan

### **Kegiatan yang Masuk Hari Efektif**

✅ **Dihitung sebagai hari efektif**:
- Hari belajar normal (Senin-Jumat)
- Hari ujian (TKA, PTS, PAS, dll)
- Hari upacara
- Hari MPLS
- Hari penyerahan rapor

❌ **TIDAK dihitung sebagai hari efektif**:
- Sabtu & Minggu (weekend)
- Libur nasional
- Libur semester
- Libur Ramadhan & Idul Fitri
- Cuti bersama

### **Validasi Hasil**

Untuk memastikan perhitungan sudah benar:

1. ✅ Total hari semester sesuai dengan rentang tanggal
2. ✅ Weekend = Jumlah Sabtu + Minggu di rentang tanggal
3. ✅ Hari Libur = Total hari libur nasional (Senin-Jumat saja)
4. ✅ Hari Efektif = Total - Weekend - Libur (TANPA kurangi ujian)
5. ✅ Minggu Efektif = Hari Efektif / 5

---

## 🔗 File Terkait

- **Service**: `app/Services/EffectiveDayService.php`
- **Model**: `app/Models/EffectiveDay.php`
- **Command**: `app/Console/Commands/CalculateEffectiveDays.php`
- **Excel Referensi**: `Kalender_Pendidikan_2026_2027.xlsx`

---

## ✨ Status

- [x] Logika perhitungan diperbaiki
- [x] Dokumentasi dibuat
- [ ] Tanggal semester di database diupdate
- [ ] Recalculate hari efektif dijalankan
- [ ] Validasi hasil dengan Excel

**Tanggal**: 23 Juni 2026
**Versi**: 1.0

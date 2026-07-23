# 📋 Template untuk Data Guru & Mata Pelajaran dari Jadwal

## ✅ Status Database
- **Guru saat ini:** 0 guru
- **Siswa saat ini:** 0 siswa
- **Mata Pelajaran:** 0 mapel (DIKOSONGKAN - siap diisi dari jadwal)
- **Admin:** 2 (admin + waka - tetap ada)
- **Database:** SIAP menerima data baru

---

## 📝 Format Data yang Dibutuhkan dari Jadwal

Ketika Anda share file jadwal, saya akan extract informasi berikut:

### **1. Data Mata Pelajaran (dari jadwal):**
- ✅ **Nama Mata Pelajaran** - Nama lengkap
- 🔄 **Kode Mapel** (optional) - Singkatan (misal: MTK, BING, PKN)
- 🔄 **Deskripsi** (optional) - Auto-generate jika tidak ada

### **2. Data Guru yang Diperlukan:**
1. **Nama Lengkap** - Contoh: "Drs. Suseno"
2. **Mata Pelajaran** - Yang diampu (bisa lebih dari 1)
3. **Jurusan yang Diajar** - MPLB, AKL, BUSANA, atau Semua
4. **NIP/NUPTK** (optional) - Jika ada di jadwal

### **Data yang Auto-Generate:**
- Username: dari nama (lowercase, tanpa gelar)
- Email: dari username@smkpgriblora.sch.id
- Password: `password` (default untuk semua)
- Role: `guru`

---

## 🎯 Contoh Data dari Jadwal Sebelumnya

Dari `JAWAL GURU_001.jpg` yang lalu, saya extract:

```
Nama: Drs. Suseno
Mapel: Pendidikan Pancasila dan Kewarganegaraan (PKN)
Jurusan: Semua (MPLB, AKL, BUSANA)
```

Dikonversi jadi:
```php
[
    'name' => 'Drs. Suseno',
    'username' => 'suseno',
    'email' => 'suseno@smkpgriblora.sch.id',
    'nip_nuptk' => null,
    'subjects' => ['Pendidikan Pancasila dan Kewarganegaraan'],
    'taught_majors' => ['MPLB', 'AKL', 'BUSANA'],
    'beban_mengajar' => 24, // jam/minggu (optional)
]
```

---

## 📊 Data Mata Pelajaran

**DATABASE DIKOSONGKAN** - Semua mata pelajaran akan diisi ulang dari jadwal Anda.

Saya akan extract mata pelajaran yang ada di jadwal dan buatkan seeder lengkap dengan:
- Nama mata pelajaran (dari jadwal)
- Kode mapel (auto-generate dari singkatan)
- Deskripsi (auto-generate atau dari jadwal)
- Kategori (Umum/Produktif MPLB/AKL/BUSANA)

**Keuntungan:** Data mapel 100% sesuai dengan jadwal aktual sekolah Anda!

---

## 🚀 Proses Setelah Anda Share Jadwal

1. **Anda upload file jadwal** (gambar/excel/pdf)
2. **Saya analisis** - Extract semua mata pelajaran + guru
3. **Saya buat 2 seeder:**
   - SubjectSeeder.php - Daftar semua mata pelajaran
   - TeacherSeeder.php - Daftar semua guru + relasi ke mapel
4. **Execute seeder** - Data masuk ke database
5. **Verifikasi** - Cek data & login test

---

## ⚠️ Catatan Penting

### **Nama Mata Pelajaran:**
- Saya akan menggunakan nama **PERSIS** seperti di jadwal
- Jika ada singkatan, saya akan tanyakan nama lengkapnya
- Jika perlu normalisasi (misal: B.Indonesia → Bahasa Indonesia), saya akan konfirmasi dulu

### **Guru yang Mengajar Multiple Mapel:**
Akan di-attach ke semua mapel yang diampu.

### **Guru Produktif:**
`taught_majors` akan di-set sesuai jurusan:
- Guru MPLB: `['MPLB']`
- Guru AKL: `['AKL']`
- Guru BUSANA: `['BUSANA']`
- Guru Umum: `['MPLB', 'AKL', 'BUSANA']`

---

## ✅ Siap Terima Data!

Silakan share file jadwal guru Anda. Format yang bisa diterima:
- 📸 **Gambar** (JPG, PNG) - Jadwal dalam bentuk foto/scan
- 📊 **Excel** (XLS, XLSX) - Jadwal dalam tabel
- 📄 **PDF** - Jadwal dalam dokumen
- 📝 **Text** - List manual jika perlu

Saya akan extract dan buatkan seeder lengkap! 🚀

---

**Status:** ✅ READY - Database dikosongkan, siap terima data baru
**Timestamp:** 2026-07-20

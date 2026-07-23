# 📊 LAPORAN JURNAL MENGAJAR - DOCUMENTATION

## SIM Kurikulum SMK PGRI Blora
**Feature:** Export & Report Jurnal Mengajar  
**Date:** July 20, 2026  
**Status:** ✅ PRODUCTION READY

---

## 🎯 OVERVIEW

Fitur Laporan Jurnal Mengajar menyediakan 5 jenis report dalam format PDF untuk monitoring dan evaluasi pembelajaran:

1. **👨‍🏫 Rekap Jurnal Per Guru** - Monitoring produktivitas guru
2. **📊 Rekap Kehadiran Siswa** - Statistik kehadiran siswa
3. **📚 Rekap Materi Ajar** - Tracking materi yang sudah diajarkan
4. **⚠️ Monitoring Jurnal Kosong** - Alert untuk guru yang belum mengisi jurnal
5. **📄 Export Jurnal Saya** - Export jurnal pribadi guru

---

## 🔐 ACCESS CONTROL

### Admin / Kepsek / Waka Kurikulum:
✅ Semua 5 laporan
- Dapat melihat data semua guru
- Dapat filter per guru/kelas
- Full monitoring access

### Guru:
✅ Export Jurnal Saya (report #5 saja)
- Hanya bisa export jurnal pribadi
- Tidak bisa lihat laporan monitoring

---

## 📍 LOKASI FITUR

### Di Menu:
`📓 Jurnal Mengajar` → Index Page

### Tampilan Button:
- Button hijau "📊 Laporan" di pojok kanan atas
- Dropdown menu dengan 5 opsi report (role-based)
- Modal konfigurasi periode sebelum generate PDF

---

## 📋 DETAIL SETIAP LAPORAN

### 1. 👨‍🏫 Rekap Jurnal Per Guru

**Tujuan:** Monitoring produktivitas guru dalam mengisi jurnal

**Konten:**
- Daftar semua guru dengan total jurnal yang dibuat
- Total Jam Pelajaran (JP) - asumsi 2 JP per jurnal
- Kelas yang diampu
- Mata pelajaran yang diajarkan
- Summary: Total guru, total jurnal, rata-rata per guru

**Filter:**
- Periode tanggal (wajib)
- Guru tertentu (opsional - default: semua)

**Use Case:**
- Kepala Sekolah monitoring guru mana yang rajin/jarang mengisi
- Waka Kurikulum evaluasi beban mengajar vs jurnal yang dibuat

**Access:** Admin, Kepsek, Waka Kurikulum

---

### 2. 📊 Rekap Kehadiran Siswa

**Tujuan:** Tracking kehadiran siswa untuk BK dan wali kelas

**Konten:**
- Daftar siswa dengan breakdown kehadiran: Hadir, Sakit, Izin, Alpha
- Persentase kehadiran per siswa
- Data: NISN, Nama, Kelas
- Color coding: Hijau (Hadir), Kuning (Sakit), Biru (Izin), Merah (Alpha)

**Filter:**
- Periode tanggal (wajib)
- Kelas tertentu (opsional - default: semua)

**Use Case:**
- Wali kelas monitoring kehadiran siswa di kelasnya
- BK identifikasi siswa dengan absensi rendah
- Orang tua siswa (untuk laporan bulanan)

**Access:** Admin, Kepsek, Waka Kurikulum

---

### 3. 📚 Rekap Materi Ajar

**Tujuan:** Tracking progress pembelajaran per kelas/mapel

**Konten:**
- Grouped by: Kelas - Mata Pelajaran
- Setiap entry: Tanggal, Jam, Guru, KD, Materi Pokok
- Chronological order (per tanggal)

**Filter:**
- Periode tanggal (wajib)
- Kelas tertentu (opsional - default: semua)

**Use Case:**
- Waka Kurikulum cek apakah materi sesuai silabus
- Koordinator mapel monitoring coverage materi
- Persiapan akreditasi (bukti pembelajaran)

**Access:** Admin, Kepsek, Waka Kurikulum

---

### 4. ⚠️ Monitoring Jurnal Kosong

**Tujuan:** Alert untuk guru yang belum/jarang mengisi jurnal

**Konten:**
- Daftar guru sorted by total jurnal (ascending - paling sedikit di atas)
- Status color coded:
  - 🔴 Belum Isi (0 jurnal)
  - 🟡 Kurang (1-4 jurnal)
  - 🟢 Baik (≥5 jurnal)
- Summary: Jumlah guru per kategori

**Filter:**
- Periode tanggal (wajib)
- No filter guru/kelas (semua guru ditampilkan)

**Use Case:**
- Kepala Sekolah tindak lanjut guru yang tidak disiplin
- Waka Kurikulum reminder ke guru
- Evaluasi kinerja guru

**Access:** Admin, Kepsek, Waka Kurikulum

---

### 5. 📄 Export Jurnal Saya

**Tujuan:** Guru export jurnal pribadi untuk dokumentasi

**Konten:**
- Detail lengkap setiap jurnal:
  - Tanggal, Jam, Kelas, Mata Pelajaran
  - KD, Materi Pokok, Metode Mengajar, Catatan
  - Daftar hadir siswa per jurnal (NISN, Nama, Status, Keterangan)
  - Ringkasan kehadiran per jurnal
- Header: Info guru (Nama, NIP/NUPTK)

**Filter:**
- Periode tanggal (wajib)
- Auto-filter: hanya jurnal milik guru yang login

**Use Case:**
- Guru submit jurnal untuk penilaian kinerja
- Dokumentasi pribadi guru
- Bukti untuk sertifikasi/kenaikan pangkat

**Access:** Semua Guru

---

## 🎛️ CARA MENGGUNAKAN

### Langkah-langkah:

1. **Buka Halaman Jurnal Mengajar**
   - Menu: `📓 Jurnal Mengajar`

2. **Klik Button "📊 Laporan"**
   - Pojok kanan atas (button hijau)
   - Dropdown menu akan muncul

3. **Pilih Jenis Laporan**
   - 5 opsi report (sesuai role)
   - Klik salah satu

4. **Konfigurasi Modal**
   - **Tanggal Mulai:** Wajib
   - **Tanggal Selesai:** Wajib
   - **Filter tambahan:** Guru/Kelas (opsional, tergantung jenis report)
   - **Quick Select:** Button "Bulan Ini" & "Bulan Lalu"

5. **Generate PDF**
   - Klik button "📄 Generate PDF"
   - PDF akan auto-download
   - Filename format: `Laporan_[Jenis]_[Tanggal].pdf`

---

## 🔧 TECHNICAL DETAILS

### Technology Stack:
- **PDF Generator:** DomPDF (via `barryvdh/laravel-dompdf`)
- **Framework:** Livewire (for modal & AJAX)
- **Styling:** Inline CSS (for PDF compatibility)

### File Structure:
```
app/Livewire/TeachingJournal/
├── Index.php (updated with report methods)

resources/views/
├── livewire/teaching-journal/
│   └── index.blade.php (updated with modal & dropdown)
└── reports/teaching-journal/
    ├── teacher-summary.blade.php
    ├── attendance-recap.blade.php
    ├── material-recap.blade.php
    ├── missing-journals.blade.php
    └── my-journals.blade.php
```

### Key Methods (Index.php):
```php
openReportModal($type)         // Open modal dengan report type
closeReportModal()             // Close modal
generateReport()               // Dispatch ke method yang sesuai
generateTeacherSummaryReport() // Report #1
generateAttendanceRecapReport() // Report #2
generateMaterialRecapReport()  // Report #3
generateMissingJournalsReport() // Report #4
generateMyJournalsReport()     // Report #5
```

### Database Queries:
- **Efficient eager loading:** `with(['teacher', 'schoolClass', 'subject', 'attendances.student'])`
- **Date filtering:** `whereBetween('date', [$start, $end])`
- **Grouping:** `groupBy('teacher_id')`, `groupBy(function)` for complex grouping
- **Aggregation:** `count()`, `sum()`, custom calculations

---

## 📊 SAMPLE DATA

### Testing Data Seeded:
- **Students:** 45 siswa (5 per kelas × 9 kelas)
- **Teachers:** 23 guru (dari jadwal)
- **Journals:** 20 jurnal sample (2 minggu terakhir)
- **Attendance:** ~100 records (5 siswa × 20 jurnal)

### Seeders Created:
1. `DummyStudentSeeder` - 45 dummy students
2. `SampleTeachingJournalSeeder` - 20 sample journals

### To Seed:
```bash
php artisan db:seed --class=DummyStudentSeeder
php artisan db:seed --class=SampleTeachingJournalSeeder
```

---

## ⚙️ CONFIGURATION

### PDF Settings:
Default paper size: **A4**  
Default orientation: **Portrait**  
Font: **Arial, 10-11px**

### To Change PDF Settings:
Edit the PDF generator call in `Index.php`:
```php
$pdf = Pdf::loadView('reports...', [...])
    ->setPaper('a4', 'landscape')  // Change to landscape
    ->setOption('margin-top', 10); // Adjust margins
```

---

## 🐛 TROUBLESHOOTING

### 1. "No journals found"
**Cause:** Tidak ada data jurnal dalam periode yang dipilih  
**Solution:** Pilih periode yang lebih luas atau seed sample data

### 2. "Class 'Barryvdh\DomPDF\Facade\Pdf' not found"
**Cause:** DomPDF tidak terinstall  
**Solution:** 
```bash
composer require barryvdh/laravel-dompdf
```

### 3. PDF tidak tampil / blank
**Cause:** Error di view atau data kosong  
**Solution:** 
- Check browser console untuk error
- Verify data dengan `dd($variable)` di controller
- Check file view untuk syntax error

### 4. Dropdown tidak muncul
**Cause:** Alpine.js conflict atau tidak load  
**Solution:**
- Clear cache: `php artisan view:clear`
- Check browser console
- Verify Alpine.js loaded di layout

### 5. Modal tidak close setelah generate
**Cause:** Normal behavior - user perlu manual close  
**Solution:** This is by design. User dapat generate ulang dengan konfigurasi berbeda tanpa reopen modal.

---

## 🚀 FUTURE ENHANCEMENTS

### Possible Improvements:

1. **Export Excel** (selain PDF)
   - Untuk data analysis
   - Import ke aplikasi lain

2. **Email Report**
   - Schedule auto-send laporan bulanan
   - Email ke Kepsek & Waka

3. **Chart & Visualization**
   - Grafik trend kehadiran
   - Bar chart jurnal per guru

4. **Filter Advanced**
   - Per semester
   - Per tahun pelajaran
   - Multiple kelas selection

5. **Signature Field**
   - Tanda tangan digital di PDF
   - QR Code untuk verifikasi

---

## 📝 CHANGELOG

### Version 1.0.0 (July 20, 2026)
- ✅ Initial release
- ✅ 5 report types implemented
- ✅ Role-based access control
- ✅ PDF generation with DomPDF
- ✅ Modal configuration UI
- ✅ Sample data seeders

---

## 👥 CREDITS

**Developer:** Kiro AI Assistant  
**System:** SIM Kurikulum SMK PGRI Blora  
**Client:** SMK PGRI Blora  
**Tech Stack:** Laravel 11, Livewire 3, DomPDF, TailwindCSS

---

## 📞 SUPPORT

For questions or issues:
- Check this documentation first
- Review code comments in `Index.php` and view files
- Test with sample data seeders

---

**🎉 Feature Status: PRODUCTION READY**

All 5 reports tested and working. Ready for production use!

---

*Documentation generated: July 20, 2026*  
*Last updated: July 20, 2026*

# 🚀 Quick Start - Modul Perangkat Ajar

## ✅ STATUS: READY TO USE!

Migration dan Seeder berhasil dijalankan. Modul siap digunakan!

---

## 📊 Database Status

```
✅ Migration completed (3 tables created)
✅ Seeder completed (5 sample materials created)
✅ Routes registered (4 routes)
✅ Menu added to navbar
```

---

## 🧪 Quick Test

### **1. Login sebagai Guru**
```
URL: http://localhost:8000/login
Username: suseno (atau guru lain)
Password: password
```

### **2. Akses Menu Perangkat Ajar**
- Cek navbar → Menu "📚 Perangkat Ajar" harus muncul
- Klik menu tersebut
- URL: `http://localhost:8000/teaching-materials`

### **3. Lihat Sample Data**
Anda akan melihat 5 sample materials:
1. **ATP Matematika Fase F** (Approved, Public)
2. **Modul Ajar: Sistem Persamaan Linear** (Pending Approval)
3. **Video: Pengenalan Akuntansi Keuangan** (Approved, Public)
4. **Bank Soal UTS Bahasa Indonesia** (Draft, Private)
5. **Job Sheet: Praktikum Komputer Akuntansi MYOB** (Approved, Public)

### **4. Test Upload**
1. Klik "Upload Perangkat Ajar"
2. Isi form minimal:
   - Judul: "Test Upload"
   - Kategori: Pilih salah satu
   - Tahun Ajaran: Pilih tahun aktif
   - Upload Type: Pilih "Link Eksternal"
   - Link: Masukkan URL apapun (contoh: https://youtube.com/test)
3. Klik "💾 Simpan sebagai Draft"

### **5. Test Filter**
- Filter by Category → "Modul Ajar"
- Filter by Subject → "Matematika"
- Filter by Status → "Approved"
- Check Dimensi → "Bernalar Kritis"

### **6. Test Detail & Comment**
1. Klik "👁️ Lihat" pada salah satu material
2. Scroll ke bawah → Section "Komentar"
3. Tulis komentar: "Test komentar"
4. Klik "💬 Kirim Komentar"

---

## 📂 Struktur Menu

```
Navbar SIMKUR
├── 📊 Dashboard
├── 📅 Kalender Akademik (dropdown)
├── 📂 Master Data (dropdown)
├── 📓 Jurnal Mengajar
├── 📚 Perangkat Ajar ← NEW! 
└── 📝 Asesmen (dropdown)
```

---

## 🎯 12 Kategori yang Tersedia

### **Perencanaan Pembelajaran**
1. ATP (Alur Tujuan Pembelajaran)
2. CP (Capaian Pembelajaran)
3. Modul Ajar ⭐ (Lengkap: LKPD, Asesmen, Rubrik)

### **Media & Bahan Ajar Tambahan**
4. Buku Teks / E-Book
5. Video Pembelajaran
6. Presentasi / Infografis
7. Bahan Bacaan / Artikel

### **Asesmen Mandiri**
8. Bank Soal / Paket Soal
9. Rubrik Penilaian Umum

### **Kokurikuler SMK**
10. Job Sheet / Panduan Praktikum
11. Teaching Factory
12. PKL (Praktik Kerja Lapangan)

---

## 🔐 Authorization

| Role | Access | Upload | Edit | Delete | Approve |
|------|--------|--------|------|--------|---------|
| **Admin** | ✅ All | ✅ | ✅ (Draft) | ✅ (Draft) | ⏳ Planned |
| **Waka Kurikulum** | ✅ All | ✅ | ✅ (Draft) | ✅ (Draft) | ⏳ Planned |
| **Kepala Sekolah** | ✅ Approved + Own | ✅ | ✅ (Own Draft) | ✅ (Own Draft) | ❌ |
| **Guru** | ✅ Approved + Own | ✅ | ✅ (Own Draft) | ✅ (Own Draft) | ❌ |
| **Siswa** | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 🎯 8 Dimensi Profil Lulusan (Kurikulum Merdeka 2025/2026)

1. ✅ Beriman, Bertakwa kepada Tuhan YME, Berakhlak Mulia
2. ✅ Berkebinekaan Global
3. ✅ Bergotong Royong
4. ✅ Mandiri
5. ✅ Bernalar Kritis
6. ✅ Kreatif
7. ✅ Literasi Numerasi
8. ✅ Literasi (Baca-Tulis)

---

## 📈 What's Working

✅ **Database:** 3 tables created successfully  
✅ **Models:** 3 models with relationships  
✅ **Components:** 4 Livewire components  
✅ **Views:** 4 Blade files  
✅ **Routes:** 4 routes registered  
✅ **Menu:** Added to navbar  
✅ **Seeder:** 5 sample materials  
✅ **Filter:** Category, Subject, Grade, Status, Dimensi  
✅ **Search:** Title, Description, Tags  
✅ **Upload:** File or Link  
✅ **Comments:** Working  
✅ **Tracking:** View & download count  
✅ **Authorization:** Role-based access  

---

## ⏳ What's NOT Yet (FASE 2)

❌ **Approval UI:** untuk Waka Kurikulum (data bisa di-approve manual via DB dulu)  
❌ **Dashboard Analytics:** Statistics & coverage  
❌ **Sharing UI:** Share to specific users/classes  
❌ **File Preview:** PDF viewer in browser  
❌ **Export:** Excel/PDF  
❌ **Notifications:** Email/in-app  
❌ **Bulk Operations:** Bulk approve, bulk delete  

---

## 🐛 Troubleshooting

### **Problem: Menu tidak muncul**
**Solution:**
```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
```
Refresh browser (Ctrl+Shift+R)

### **Problem: Error 404 Not Found**
**Solution:**
Pastikan routes sudah terdaftar:
```bash
php artisan route:list --name=teaching-materials
```

### **Problem: File upload error**
**Solution:**
Pastikan storage sudah di-link:
```bash
php artisan storage:link
```

### **Problem: Tidak bisa upload file besar**
**Solution:**
Edit `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
```
Restart server: `php artisan serve`

---

## 📞 Next Steps

1. ✅ **Test semua fitur** dengan user guru
2. ✅ **Upload real data** (ATP, CP, Modul Ajar, dll)
3. ⏳ **Buat Approval UI** (untuk Waka Kurikulum)
4. ⏳ **Buat Dashboard Analytics**
5. ⏳ **Add File Preview** (PDF viewer)
6. ⏳ **Add Notifications**

---

## 📚 Dokumentasi Lengkap

Lihat file: **`PERANGKAT_AJAR_README.md`**

---

**Date:** 2026-07-24  
**Status:** ✅ READY TO USE  
**Version:** FASE 1 MVP  
**Branding:** SIMKUR SMK PGRI Blora

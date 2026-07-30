# 🚀 Cara Pakai: Monitoring Kelengkapan Perangkat Ajar

## 📖 Penjelasan Singkat

Sistem ini melacak **kelengkapan 7 dokumen perencanaan wajib** yang harus diupload setiap guru untuk setiap mata pelajaran yang mereka ajar.

### 7 Dokumen Wajib:
1. CP (Capaian Pembelajaran)
2. ATP (Alur Tujuan Pembelajaran)
3. KKTP (Kriteria Ketercapaian)
4. PROTA (Program Tahunan)
5. PROSEM (Program Semester)
6. Modul Ajar
7. Modul Projek

---

## 🎬 Tutorial Step-by-Step

### **STEP 1: Setup Awal (Admin/Waka)**

1. **Login** sebagai **Admin** atau **Waka Kurikulum**

2. **Buka menu:**
   ```
   Perangkat Ajar (dropdown) → Monitoring Kelengkapan
   ```
   
   Atau langsung akses:
   ```
   http://127.0.0.1:8000/teaching-materials/monitoring
   ```

3. **Klik tombol "Sync dari Jadwal"** (tombol biru)
   - Sistem akan membaca `teaching_schedules`
   - Generate requirements untuk setiap kombinasi Guru + Mapel
   - Misalnya:
     - Pak Budi mengajar Matematika → 1 requirement
     - Pak Budi mengajar Fisika → 1 requirement
     - Bu Ani mengajar Bahasa Indonesia → 1 requirement
     - **Total: 3 requirements**

4. **Klik tombol "Refresh Data"** (tombol hijau)
   - Sistem akan scan semua perangkat ajar yang sudah diupload
   - Update status kelengkapan (mana yang sudah, mana yang belum)

5. **Selesai!** Sekarang Anda bisa monitoring kelengkapan 📊

---

### **STEP 2: Guru Upload Perangkat Ajar**

1. **Login** sebagai **Guru**

2. **Buka menu:**
   ```
   Perangkat Ajar → Upload Baru
   ```

3. **Isi form:**
   - **Judul:** "CP Matematika Kelas 10"
   - **Kategori:** Pilih **"CP (Capaian Pembelajaran)"**
   - **Mata Pelajaran:** Pilih **"Matematika"**
   - **Tahun Akademik:** Pilih tahun aktif
   - **File:** Upload PDF/DOCX
   - **Status:** Simpan sebagai **"Pending Approval"**

4. **Klik "Simpan"**

5. **Otomatis:** Sistem akan update status kelengkapan di monitoring
   - `has_cp` jadi `true`
   - Completion percentage naik dari 0% → 14% (1/7)

6. **Ulangi** untuk dokumen lainnya (ATP, KKTP, PROTA, dst)

---

### **STEP 3: Admin/Waka Monitoring**

1. **Buka halaman monitoring**

2. **Lihat statistik di atas:**
   - Total Tugas: 15 (misalnya)
   - Lengkap: 3 (20%)
   - Belum Lengkap: 12 (80%)
   - Rata-rata: 45%

3. **Filter data:**
   - **Filter Status:** Pilih "Belum Lengkap"
   - Lihat guru-guru yang belum lengkap

4. **Identifikasi dokumen yang kurang:**
   - Lihat badge dokumen (hijau = ada, abu = belum)
   - Misalnya: Pak Budi sudah upload CP, ATP, PROTA (3/7)
   - Kurang: KKTP, PROSEM, Modul Ajar, Modul Projek

5. **Tindak lanjut:**
   - Hubungi guru yang belum lengkap
   - Beri reminder via WA/email
   - Set deadline (misal: sebelum semester dimulai)

---

### **STEP 4: Kepala Sekolah Lihat Laporan**

1. **Login** sebagai **Kepala Sekolah**

2. **Buka menu:**
   ```
   Perangkat Ajar (dropdown) → Monitoring Kelengkapan
   ```

3. **Lihat overview:**
   - Berapa persen kelengkapan keseluruhan?
   - Guru mana yang sudah lengkap?
   - Guru mana yang masih kurang?

4. **Export/Print:**
   - Screenshot statistik untuk laporan
   - Presentasi di rapat
   - Evaluasi kinerja guru

---

## 🎯 Contoh Kasus Nyata

### **Kasus 1: Awal Semester**

**Situasi:**
- Semester baru dimulai
- Belum ada guru yang upload perangkat ajar

**Langkah:**
1. Admin klik "Sync dari Jadwal"
2. Sistem generate 50 requirements (misalnya)
3. Semua status: 0% (belum ada yang upload)
4. Admin broadcast reminder ke semua guru
5. Set deadline: 1 minggu sebelum KBM

**Monitoring:**
- Week 1: 10% sudah lengkap
- Week 2: 35% sudah lengkap
- Week 3: 70% sudah lengkap
- Week 4: 100% sudah lengkap ✅

---

### **Kasus 2: Pertengahan Semester**

**Situasi:**
- Ada guru baru (Pak Doni)
- Pak Doni mengajar Bahasa Inggris

**Langkah:**
1. Admin input jadwal Pak Doni di `teaching_schedules`
2. Admin buka monitoring
3. Klik "Sync dari Jadwal"
4. Pak Doni muncul di list dengan status 0%
5. Admin reminder Pak Doni untuk upload

**Pak Doni mulai upload:**
- Upload CP → 14%
- Upload ATP → 29%
- Upload KKTP → 43%
- Upload PROTA → 57%
- Upload PROSEM → 71%
- Upload Modul Ajar → 86%
- Upload Modul Projek → 100% ✅

---

### **Kasus 3: Evaluasi Akhir Semester**

**Situasi:**
- Kepsek perlu laporan kelengkapan untuk rapat

**Langkah:**
1. Kepsek buka monitoring
2. Lihat statistik:
   - Total: 50 assignments
   - Lengkap: 45 (90%)
   - Belum Lengkap: 5 (10%)
3. Filter "Belum Lengkap"
4. Terlihat 5 guru yang belum lengkap:
   - Bu Ani (Matematika) → 86% (kurang Modul Projek)
   - Pak Budi (Fisika) → 71% (kurang 2 dokumen)
   - Bu Cici (Kimia) → 57% (kurang 3 dokumen)
   - Pak Dedi (Biologi) → 43% (kurang 4 dokumen)
   - Bu Eni (Sejarah) → 29% (kurang 5 dokumen)
5. Kepsek panggil 5 guru tersebut
6. Beri teguran/bimbingan

---

## 📊 Interpretasi Dashboard

### **Statistics Cards:**

**1. Total Tugas (50)**
- Artinya: Ada 50 kombinasi Guru × Mapel
- Contoh:
  - 10 guru × 5 mapel = 50 assignments

**2. Lengkap (30) → 60%**
- Artinya: 30 dari 50 assignments sudah 100%
- Good! Mayoritas sudah lengkap

**3. Belum Lengkap (20) → 40%**
- Artinya: 20 dari 50 assignments masih < 100%
- Need attention: 20 guru masih kurang dokumen

**4. Rata-rata (75%)**
- Artinya: Rata-rata completion semua guru = 75%
- Interpretation:
  - < 50%: ❌ Buruk, banyak yang kurang
  - 50-70%: ⚠️ Cukup, perlu improvement
  - 70-85%: ✅ Baik, hampir lengkap
  - > 85%: 🌟 Sangat baik!

---

### **Progress Bar:**

- **0-39% (Merah):** Urgent! Belum lengkap
- **40-69% (Orange):** Perlu perhatian
- **70-99% (Kuning):** Hampir selesai
- **100% (Hijau):** Lengkap! ✅

---

### **Badge Dokumen:**

**Contoh:**
```
✅ CP   ✅ ATP   ❌ KKTP   ✅ PROTA   ❌ PROSEM   ❌ MODUL AJAR   ❌ MODUL PROJEK
```

**Artinya:**
- Sudah upload: CP, ATP, PROTA (3/7)
- Belum upload: KKTP, PROSEM, Modul Ajar, Modul Projek (4/7)
- Completion: 43%

---

## 🔍 Tips & Tricks

### **Tip 1: Filter Strategis**
- Filter "Belum Lengkap" → fokus ke yang perlu dikejar
- Filter "Lengkap" → apresiasi guru yang sudah selesai

### **Tip 2: Search Cepat**
- Cari nama guru: "Budi"
- Cari nama mapel: "Matematika"
- Gabungan: Cari semua yang terkait Matematika

### **Tip 3: Sort by Completion**
- Default sort: ascending (dari yang paling rendah)
- Prioritas reminder: yang < 50%

### **Tip 4: Refresh Berkala**
- Klik "Refresh Data" setiap hari
- Pastikan data selalu update
- Auto-update via event juga jalan

### **Tip 5: Export Screenshot**
- Screenshot statistics cards
- Paste di PowerPoint
- Presentasi di rapat

---

## ❓ FAQ

### **Q: Kenapa data tidak muncul?**
**A:** Klik tombol "Sync dari Jadwal" dulu. Data requirements diambil dari `teaching_schedules`.

### **Q: Progress tidak update setelah guru upload?**
**A:** Harusnya otomatis update via event. Kalau belum, klik "Refresh Data" manual.

### **Q: Guru bisa lihat monitoring?**
**A:** Tidak. Guru hanya fokus upload. Monitoring hanya untuk Admin/Waka/Kepsek.

### **Q: Bagaimana cara reminder guru?**
**A:** Manual via WA/email. (Future: bisa auto-email reminder)

### **Q: Apakah ada deadline?**
**A:** Belum ada fitur deadline otomatis. Sementara manual tracking.

### **Q: Dokumen draft/rejected dihitung?**
**A:** Tidak. Hanya status "approved" dan "pending_approval" yang dihitung.

### **Q: Bisa monitoring per kelas?**
**A:** Saat ini per Guru × Mapel. Kelas tidak di-track (karena 1 guru bisa mengajar banyak kelas untuk mapel yang sama).

### **Q: Bagaimana kalau guru pindah mapel?**
**A:** Update jadwal → klik "Sync dari Jadwal" → requirements akan update sesuai jadwal baru.

### **Q: Apakah bisa export Excel?**
**A:** Belum ada. (Future enhancement)

### **Q: Dark mode?**
**A:** ✅ Support! Toggle dari user profile.

---

## 🎓 Training Checklist

### **Untuk Admin/Waka:**
- [ ] Cara sync dari jadwal
- [ ] Cara refresh data
- [ ] Cara filter & search
- [ ] Cara interpretasi dashboard
- [ ] Cara identifikasi guru yang kurang
- [ ] Cara reminder guru

### **Untuk Kepsek:**
- [ ] Cara akses monitoring
- [ ] Cara baca statistik
- [ ] Cara filter data
- [ ] Cara screenshot untuk laporan
- [ ] Cara evaluasi kelengkapan

### **Untuk Guru:**
- [ ] Cara upload perangkat ajar
- [ ] Cara pilih kategori yang benar
- [ ] Cara pilih mapel & tahun akademik
- [ ] Cara submit untuk approval
- [ ] Pemahaman 7 dokumen wajib

---

## ✅ Done!

Sekarang Anda sudah paham cara pakai sistem monitoring kelengkapan perangkat ajar! 🚀

**Mulai dari:**
1. Sync dari jadwal
2. Guru upload dokumen
3. Monitor progress
4. Reminder yang kurang
5. Evaluasi kelengkapan

**Happy monitoring!** 📊✨

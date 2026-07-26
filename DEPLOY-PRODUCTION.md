# Deploy ke Production - simkur.smkpgriblora.sch.id

## 🚀 Step-by-Step Deployment

### 1. Login ke Server

```bash
ssh root@aapanel-lxc
# atau login via aaPanel terminal
```

### 2. Masuk ke Direktori Project

```bash
cd /www/wwwroot/simkur
```

### 3. Pull Perubahan dari GitHub

```bash
git pull origin main
```

### 4. Jalankan Migrasi (Buat Tabel Baru)

```bash
php artisan migrate --force
```

**Output yang diharapkan:**
```
Running migrations.
2026_07_26_074418_create_teaching_schedules_table .... DONE
```

### 5. Jalankan Master Data Seeder (OPTIONAL - Jika Fresh Install)

Seeder ini akan membuat:
- 21 guru
- 43 mata pelajaran  
- 10 kelas

```bash
php artisan db:seed --class=MasterDataFromScheduleSeeder --force
```

**Output yang diharapkan:**
```
Creating master data from schedule...
Creating teachers...
  + Created teacher: [nama guru]
Creating subjects...
  + Created subject: [nama mapel]
Creating classes...
  + Created class: [nama kelas]
✓ Master data seeding completed!
```

### 6. Jalankan Teaching Schedule Seeder

Seeder ini akan membuat 154 jadwal mengajar untuk semua guru.

```bash
php artisan db:seed --class=TeachingScheduleSeeder --force
```

**Output yang diharapkan:**
```
Seeding teaching schedules...
✓ Created: 154 schedules
⊘ Skipped: 0 schedules (missing data)
```

### 7. Clear All Caches

```bash
php artisan optimize:clear
php artisan view:clear
php artisan config:cache
```

### 8. Set Permissions (Jika Perlu)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ✅ Verification Checklist

Setelah deploy, test hal-hal berikut:

### A. Test Login Guru
1. Login sebagai salah satu guru yang ada di jadwal
2. Buka Dashboard Guru
3. Harus muncul notifikasi:
   - **Jika hari ada jadwal**: "Anda ada X jam mengajar hari ini"
   - **Jika hari tidak ada jadwal**: Tidak ada notifikasi/atau sudah isi
4. Cek card merah "Belum Isi (1 Bulan)"
5. Angka harus sesuai dengan hari ada jadwal tapi belum isi

### B. Test Kelola Jadwal
1. Login sebagai Admin/Waka Kurikulum
2. Buka **Master Data → Jadwal Mengajar**
3. Harus tampil 154 jadwal
4. Test filter: Guru, Kelas, Hari
5. Test tambah jadwal baru
6. Test edit dan hapus jadwal

### C. Test Isi Jurnal
1. Login sebagai guru yang punya jadwal hari ini
2. Buka **Jurnal Mengajar → Tambah**
3. Pilih tanggal hari ini
4. Pilih kelas
5. **Jam pelajaran harus muncul** sesuai jadwal hari ini
6. Isi jurnal dan simpan
7. Kembali ke Dashboard
8. Notifikasi harus update

---

## 🔧 Troubleshooting

### Problem: Tabel `teaching_schedules` tidak ada
**Solution:**
```bash
php artisan migrate --force
```

### Problem: Jadwal tidak muncul di dashboard
**Solution:**
```bash
# Clear cache
php artisan optimize:clear

# Verify schedule data
php artisan tinker
>>> App\Models\TeachingSchedule::count();
>>> exit
```

### Problem: Jam tidak muncul saat isi jurnal
**Solution:**
- Pastikan guru punya jadwal untuk hari itu
- Cek di Master Data → Jadwal Mengajar
- Filter by guru dan hari

### Problem: Data guru/kelas/mapel tidak ada
**Solution:**
```bash
php artisan db:seed --class=MasterDataFromScheduleSeeder --force
```

### Problem: "Skipped: X schedules"
**Solution:**
- Cek nama guru/kelas/mapel di database
- Sesuaikan dengan yang ada di jadwal
- Jalankan ulang seeder

---

## 📊 Summary Perubahan

### Database Changes:
- ✅ Tabel baru: `teaching_schedules`
- ✅ 154 jadwal mengajar (21 guru, 10 kelas, 43 mata pelajaran)

### Feature Changes:
- ✅ Dashboard Guru: Reminder spesifik berdasarkan jadwal tetap
- ✅ Dashboard Guru: Missing journal days (1 minggu & 1 bulan)
- ✅ Halaman baru: Kelola Jadwal Mengajar (Admin, Kepsek, Waka)
- ✅ Jurnal Mengajar: Jam otomatis sesuai jadwal guru

### Access Rights:
- **Admin**: Full access
- **Kepala Sekolah**: Full access
- **Waka Kurikulum**: Full access (kelola jadwal)
- **Guru**: Lihat jadwal sendiri di dashboard

---

## 📝 Notes

- **Password default guru baru**: `password123`
- **Username**: otomatis dari nama (lowercase, no space/punctuation)
- **Tahun ajaran**: Seeder menggunakan tahun ajaran aktif
- **Time slots**: Pastikan sudah diatur di Settings → Time Slots

---

## ⚠️ PENTING

Sebelum deploy ke production:
1. ✅ Backup database dulu
2. ✅ Test di local environment
3. ✅ Verifikasi data setelah deploy
4. ✅ Inform user jika ada downtime

---

Selamat deploy! 🚀

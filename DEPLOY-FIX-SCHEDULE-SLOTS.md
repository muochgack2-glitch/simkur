# Deployment: Fix Teaching Schedule Time Slots

## Masalah yang Diperbaiki
Jadwal mengajar guru menampilkan waktu yang tidak seharusnya:
1. **Kegiatan Jumat/Upacara/Kegiatan Pagi** (07:00-07:30) - bukan jam mengajar
2. **Waktu Istirahat** (11:50-12:50, dll) - bukan jam mengajar

Sekarang jadwal hanya menampilkan **jam mengajar aktual** (Jam ke-1 sampai Jam ke-10).

---

## Perubahan yang Dilakukan

### 1. Command Cleanup (Hapus Schedule Invalid)
- File: `app/Console/Commands/CleanScheduleSlotZero.php`
- Fungsi: Menghapus jadwal dengan time slot yang tidak valid:
  * Order <= 1 (Upacara, Kegiatan Pagi, Kegiatan Jumat)
  * Waktu Istirahat

### 2. Seeder Update (Skip Invalid Slots)
- File: `database/seeders/TeachingScheduleSeeder.php`
- Perubahan: Mapping slot number disesuaikan + skip break times
- Slot 2-11 sekarang mapping ke jam mengajar yang benar (tanpa istirahat)

### 3. Command Verifikasi (Opsional)
- `app/Console/Commands/CheckTimeSlots.php` - Cek slot order=1
- `app/Console/Commands/CheckBreakTimeSlots.php` - Cek slot istirahat
- `app/Console/Commands/TestTeacherSchedule.php` - Test schedule guru

---

## Langkah Deploy ke Production

### STEP 1: Backup Database
```bash
# Backup database sebelum perubahan
mysqldump -u username -p database_name > backup_before_schedule_fix_$(date +%Y%m%d).sql
```

### STEP 2: Pull Kode Terbaru
```bash
cd /www/wwwroot/simkur.smkpgriblora.sch.id
git pull origin main
```

### STEP 3: Jalankan Cleanup Command
```bash
# Hapus schedule dengan slot invalid
php artisan schedule:clean-slot-zero
```

Output yang diharapkan:
```
Cleaning schedules with invalid time slots...
Found 15 invalid time slot(s)
✓ Deleted 95 schedule(s) with invalid slots
✓ Cleanup completed!
```

### STEP 4: Re-seed Teaching Schedules
```bash
# Buat ulang schedule dengan slot yang benar
php artisan db:seed --class=TeachingScheduleSeeder
```

Output yang diharapkan:
```
✓ Created: 154 schedules
⊘ Skipped: 0 schedules (missing data)
```

### STEP 5: Verifikasi (Opsional)
```bash
# Cek tidak ada lagi schedule dengan slot istirahat
php artisan check:breaks
```

Output yang diharapkan:
```
Total schedules using break slots: 0
```

```bash
# Cek tidak ada lagi schedule dengan slot order=1
php artisan check:timeslots
```

Output yang diharapkan:
```
Total schedules using order=1 slots: 0
```

### STEP 6: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Verifikasi di Browser

1. Login sebagai **Guru**
2. Buka **Dashboard Guru**
3. Periksa section **"Jadwal Mengajar Hari Ini"**
4. **HARUS TIDAK ADA**:
   - ❌ Kegiatan Jumat (07:00-07:30)
   - ❌ Upacara/Apel (07:00-07:30)
   - ❌ Kegiatan Pagi (07:00-07:20)
   - ❌ Istirahat (11:50-12:50, 09:15-09:30, dll)
5. **HARUS ADA HANYA**:
   - ✅ Jam ke-1, Jam ke-2, ..., Jam ke-10
   - ✅ Dengan waktu mengajar yang sesuai

---

## Rollback (Jika Ada Masalah)

```bash
# Restore dari backup
mysql -u username -p database_name < backup_before_schedule_fix_YYYYMMDD.sql

# Atau reset schedule saja
php artisan migrate:refresh --path=database/migrations/*_create_teaching_schedules_table.php
php artisan db:seed --class=MasterDataFromScheduleSeeder
php artisan db:seed --class=TeachingScheduleSeeder
```

---

## Summary Perubahan Database

- **Dihapus**: 95 schedule records dengan time slot invalid
- **Dibuat ulang**: 381 schedule records (154 schedule entries x multiple time slots per day)
- **Tidak berubah**: Data master (guru, kelas, mata pelajaran, time slots tetap sama)

---

## Catatan Penting

⚠️ **JANGAN** jalankan seeder tanpa cleanup dulu, karena akan duplicate!

✅ **URUTAN YANG BENAR**:
1. Cleanup (hapus yang lama)
2. Seed (buat yang baru)

✅ **Time slots yang VALID untuk teaching schedules**:
- Order >= 2 (Jam ke-1 dan seterusnya)
- BUKAN "Kegiatan" atau "Upacara"
- BUKAN "Istirahat" atau "Break"

---

## Testing Selesai

✅ Lokal: Sudah ditest dan berjalan dengan baik
✅ Commands: Semua command berjalan tanpa error
✅ Verifikasi: 0 schedule dengan slot invalid
✅ Git: Sudah commit dan push ke repository

**Ready for production deployment!**

---

Deploy Date: _____________
Deployed By: _____________
Status: [ ] Success  [ ] Rollback  [ ] Issues
Notes: _______________________________________________

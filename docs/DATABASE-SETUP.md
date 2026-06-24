# Database Setup Guide - e-KALDIK

## 🗄️ Create Database

Sebelum menjalankan migrations, buat database terlebih dahulu.

### Option 1: Via MySQL Command Line

```bash
mysql -u root -p
```

Kemudian jalankan:

```sql
CREATE DATABASE ekaldik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Option 2: Via phpMyAdmin

1. Buka phpMyAdmin di browser
2. Klik tab "Databases" atau "Basis Data"
3. Isi nama database: `ekaldik`
4. Pilih Collation: `utf8mb4_unicode_ci`
5. Klik "Create"

### Option 3: Via HeidiSQL / TablePlus / DBeaver

1. Connect ke MySQL server
2. Right-click → Create new database
3. Name: `ekaldik`
4. Charset: `utf8mb4`
5. Collation: `utf8mb4_unicode_ci`

---

## ⚙️ Configure Database Connection

File `.env` sudah dikonfigurasi dengan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ekaldik
DB_USERNAME=root
DB_PASSWORD=
```

**⚠️ IMPORTANT**: 
- Jika MySQL Anda menggunakan password, update `DB_PASSWORD=your_password`
- Jika port berbeda, update `DB_PORT=`
- Jika username berbeda, update `DB_USERNAME=`

---

## 🚀 Run Migrations

Setelah database dibuat dan .env dikonfigurasi:

```bash
php artisan migrate
```

### Expected Output:

```
INFO  Preparing database.

Creating migration table .................................... DONE

INFO  Running migrations.

0001_01_01_000000_create_users_table ........................ DONE
0001_01_01_000001_create_cache_table ........................ DONE
0001_01_01_000002_create_jobs_table ......................... DONE
2026_06_23_032721_create_academic_years_table ............... DONE
2026_06_23_032741_create_semesters_table .................... DONE
2026_06_23_032742_create_activity_types_table ............... DONE
2026_06_23_032743_create_activities_table ................... DONE
2026_06_23_032744_create_effective_days_table ............... DONE
2026_06_23_032745_create_activity_logs_table ................ DONE
2026_06_23_032746_create_import_logs_table .................. DONE
2026_06_23_032747_create_settings_table ..................... DONE
```

---

## 🌱 Seed Initial Data

Populate database dengan data awal:

```bash
php artisan db:seed
```

### What gets seeded:

1. **Users** (4 users):
   - 1 Admin
   - 1 Waka Kurikulum
   - 2 Guru

2. **Activity Types** (9 types):
   - MPLS
   - PTS
   - PAS
   - PAT
   - ANBK
   - Libur Nasional
   - Libur Semester
   - Rapat Guru
   - Kegiatan Sekolah

3. **Settings** (17 settings):
   - School information
   - Calendar settings
   - System settings
   - Import/Export settings

### Default Credentials:

```
Admin:
  Username: admin
  Password: password

Waka Kurikulum:
  Username: waka
  Password: password

Guru:
  Username: guru1
  Password: password
```

---

## 🔄 Reset Database (Optional)

Jika ingin reset database dari awal:

```bash
php artisan migrate:fresh --seed
```

**⚠️ WARNING**: Ini akan **menghapus semua data** dan membuat ulang dari awal!

---

## 🔍 Verify Database

Check migration status:

```bash
php artisan migrate:status
```

Check tables in database:

```bash
php artisan tinker
```

Kemudian di Tinker:

```php
DB::select('SHOW TABLES');
\App\Models\User::count();
\App\Models\ActivityType::count();
\App\Models\Setting::count();
exit
```

Expected output:
- Users: 4
- Activity Types: 9
- Settings: 17

---

## 📊 Database Structure

Setelah migrations dijalankan, database akan memiliki **12 tables**:

### Core Tables:
1. **users** - User accounts (admin, waka, guru)
2. **academic_years** - Tahun pelajaran
3. **semesters** - Semester (Ganjil & Genap)
4. **activity_types** - Master jenis kegiatan
5. **activities** - Kegiatan kalender pendidikan
6. **effective_days** - Perhitungan hari efektif per semester

### Log & Support Tables:
7. **activity_logs** - Audit trail sistem
8. **import_logs** - Log import Excel
9. **settings** - Konfigurasi aplikasi

### Laravel Default Tables:
10. **cache** - Cache storage
11. **jobs** - Queue jobs
12. **sessions** - User sessions
13. **password_reset_tokens** - Password reset tokens

---

## 🐛 Troubleshooting

### Error: SQLSTATE[HY000] [1049] Unknown database 'ekaldik'

**Solusi:** Database belum dibuat. Ikuti langkah "Create Database" di atas.

---

### Error: SQLSTATE[HY000] [2002] Connection refused

**Solusi:** MySQL server tidak berjalan. Start MySQL service:

**Windows:**
```cmd
net start MySQL80
```

**Linux:**
```bash
sudo systemctl start mysql
```

**macOS:**
```bash
brew services start mysql
```

---

### Error: Access denied for user 'root'@'localhost'

**Solusi:** Password MySQL salah atau username tidak sesuai.

1. Cek password MySQL Anda
2. Update `.env`:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=your_actual_password
   ```

---

### Error: Syntax error or access violation: 1071 Specified key was too long

**Solusi:** Versi MySQL terlalu lama. Minimal MySQL 8.0 atau MariaDB 10.3+

Atau tambahkan di `AppServiceProvider.php`:
```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

---

### Migration already run, want to re-run

**Rollback specific migration:**
```bash
php artisan migrate:rollback --step=1
```

**Rollback all:**
```bash
php artisan migrate:rollback
```

**Fresh migration (WARNING: deletes all data):**
```bash
php artisan migrate:fresh --seed
```

---

## ✅ Success Checklist

Setelah setup database, pastikan:

- [x] Database `ekaldik` sudah dibuat
- [x] `.env` sudah dikonfigurasi dengan benar
- [x] Migrations berhasil dijalankan (12 tables)
- [x] Seeders berhasil dijalankan (4 users, 9 activity types, 17 settings)
- [x] Bisa login dengan credentials default

---

## 🎯 Next Steps

Setelah database setup berhasil:

1. ✅ Build assets: `npm run dev`
2. ✅ Start server: `php artisan serve`
3. ✅ Open browser: http://localhost:8000
4. ✅ Login dengan kredensial default
5. ✅ Mulai development!

---

**Database is ready! 🚀**

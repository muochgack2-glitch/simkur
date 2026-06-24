# Setup Instructions - e-KALDIK

## Prerequisites

Pastikan sudah terinstall:
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL 8.0+
- Web Server (Apache/Nginx) atau PHP built-in server

---

## Step 1: Database Setup

### Buat Database MySQL

1. **Via MySQL Command Line:**
```sql
CREATE DATABASE ekaldik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Via phpMyAdmin:**
- Buka phpMyAdmin
- Klik "New" / "Baru"
- Database name: `ekaldik`
- Collation: `utf8mb4_unicode_ci`
- Klik "Create"

3. **Via HeidiSQL / TablePlus / DBeaver:**
- Connect ke MySQL server
- Create new database: `ekaldik`
- Charset: `utf8mb4`

### Update .env File

File `.env` sudah dikonfigurasi dengan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ekaldik
DB_USERNAME=root
DB_PASSWORD=
```

**⚠️ PENTING:** Jika MySQL Anda menggunakan password, update baris `DB_PASSWORD=`

---

## Step 2: Run Migrations

Setelah database dibuat, jalankan migrations:

```bash
php artisan migrate
```

Output yang diharapkan:
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

## Step 3: Build Assets

Compile CSS dan JavaScript:

```bash
npm run build
```

Atau untuk development dengan auto-reload:

```bash
npm run dev
```

---

## Step 4: Run Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

---

## Step 5: Seed Data (Setelah Seeders Dibuat)

```bash
php artisan db:seed
```

Ini akan membuat:
- User default (admin, waka, guru)
- Activity types (MPLS, PTS, PAS, dll)
- Settings default

---

## Troubleshooting

### Error: SQLSTATE[HY000] [1049] Unknown database 'ekaldik'
**Solusi:** Database belum dibuat. Ikuti Step 1 untuk membuat database.

### Error: SQLSTATE[HY000] [2002] Connection refused
**Solusi:** MySQL server tidak berjalan. Start MySQL service:
- Windows: `net start MySQL80` (atau `MySQL`)
- Linux: `sudo systemctl start mysql`
- macOS: `brew services start mysql`

### Error: Access denied for user 'root'@'localhost'
**Solusi:** Password MySQL salah. Update `DB_PASSWORD` di file `.env`

### Error: npm command not found
**Solusi:** Install Node.js dari https://nodejs.org

### Tailwind CSS tidak muncul
**Solusi:** Jalankan `npm run build` atau `npm run dev`

---

## Quick Start Commands

```bash
# 1. Install dependencies (jika belum)
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Create database (manual di MySQL)
# CREATE DATABASE ekaldik;

# 4. Run migrations
php artisan migrate

# 5. Build assets
npm run dev

# 6. Start server
php artisan serve
```

---

## Development Workflow

### Daily Development:

1. **Start development server:**
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (auto-reload CSS/JS)
npm run dev
```

2. **Make changes to code**

3. **Test in browser:** http://localhost:8000

### Before Committing:

```bash
# Format code (Laravel Pint)
./vendor/bin/pint

# Run tests
php artisan test

# Check migrations
php artisan migrate:status
```

---

## Directory Permissions

Pastikan folder berikut writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Windows: Biasanya tidak perlu setting permission manual.

---

## Next Steps

Setelah setup berhasil:
1. ✅ Migrations sudah dijalankan
2. ⏳ Buat Models & Seeders
3. ⏳ Buat Authentication system
4. ⏳ Buat fitur-fitur utama

---

## Support

Jika ada masalah saat setup, cek:
- Laravel Log: `storage/logs/laravel.log`
- PHP Version: `php -v` (harus >= 8.2)
- MySQL Version: `mysql --version` (harus >= 8.0)
- Composer: `composer --version`
- Node: `node -v` & `npm -v`

---

**Happy Coding! 🚀**

# 🔧 PANDUAN LENGKAP FIX SESSION LOGIN

## 🎯 MASALAH
Login tidak berfungsi - setelah klik tombol login, form kembali kosong dan user tidak masuk ke dashboard. Session tidak persist antara request.

## 🔍 ROOT CAUSE
**Config cache tidak me-refresh ketika `.env` diubah**, sehingga Laravel tetap menggunakan session driver lama (biasanya `array` yang tidak persistent).

## ✅ SOLUSI DEFINITIF

### STEP 1: Upload Files ke Hosting

Upload 3 file baru ke `/www/wwwroot/simkur/`:
- `fix-session-ultimate.php`
- `fix-session-hosting.sh`
- `.env.production-template`

### STEP 2: Jalankan Script Fix

```bash
cd /www/wwwroot/simkur
chmod +x fix-session-hosting.sh
./fix-session-hosting.sh
```

Script ini akan:
1. ✅ Stop PHP-FPM
2. ✅ Hapus SEMUA cache files (config, route, view)
3. ✅ Hapus SEMUA session files
4. ✅ Hapus cache data
5. ✅ Set permissions yang benar
6. ✅ Tanya apakah mau update .env ke FILE driver
7. ✅ Clear Laravel cache
8. ✅ Rebuild config cache
9. ✅ Verify session configuration
10. ✅ Test session persistence
11. ✅ Restart PHP-FPM
12. ✅ Restart Nginx

### STEP 3: Verifikasi .env

Pastikan `.env` memiliki setting ini:

```env
# SESSION - PRODUCTION SETTINGS
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=

# HTTPS SETTINGS
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

**PENTING**: 
- Gunakan `SESSION_DRIVER=file` bukan `database` untuk production
- `SESSION_SECURE_COOKIE=true` karena site menggunakan HTTPS
- `SESSION_DOMAIN=` kosong (atau hapus baris ini)

### STEP 4: Clear Browser

1. Buka browser settings
2. Clear ALL cookies dan site data untuk `simkur.smkpgriblora.sch.id`
3. Tutup SEMUA tab browser
4. Buka NEW incognito/private window

### STEP 5: Test Login

1. Buka: https://simkur.smkpgriblora.sch.id
2. Login dengan:
   - Username: `admin`
   - Password: `password`
3. Seharusnya redirect ke dashboard

---

## 🐛 TROUBLESHOOTING

### Cek 1: Session Files Dibuat?

```bash
# Hapus session lama
rm -rf /www/wwwroot/simkur/storage/framework/sessions/*

# Coba login dari browser

# Cek apakah ada session baru
ls -lah /www/wwwroot/simkur/storage/framework/sessions/
```

**Expected**: Harus ada file baru setiap kali login

**Jika TIDAK ada file**: Permission problem atau session driver salah

### Cek 2: Config Actual vs .env

```bash
cd /www/wwwroot/simkur

# Cek .env
grep SESSION_DRIVER .env

# Cek config actual
php artisan tinker --execute="echo config('session.driver');"
```

**Expected**: Harus SAMA (misalnya `file`)

**Jika BEDA**: Config cache belum di-clear dengan benar

### Cek 3: Permission Storage

```bash
ls -ld /www/wwwroot/simkur/storage/framework/sessions
```

**Expected**: `drwxrwxr-x www www`

**Jika bukan www:www**: Run:
```bash
chown -R www:www /www/wwwroot/simkur/storage
chmod -R 775 /www/wwwroot/simkur/storage
```

### Cek 4: Session Persistence Test

```bash
cd /www/wwwroot/simkur

php artisan tinker --execute="
session()->put('test', 'value');
echo 'Put: ' . session()->get('test') . PHP_EOL;
session()->save();
echo 'ID: ' . session()->getId() . PHP_EOL;
"
```

**Expected**: Output `Put: value` dan `ID: xxxx`

**Jika NULL**: Session driver tidak berfungsi

### Cek 5: Laravel Log

```bash
tail -f /www/wwwroot/simkur/storage/logs/laravel.log
```

Coba login dan lihat log. Seharusnya muncul:
```
=== LOGIN ATTEMPT START ===
Validation passed
Auth attempt SUCCESS
Last login updated
Activity log created
Session regenerated
Redirecting to dashboard
```

**Jika stuck di "Auth attempt SUCCESS"**: Session tidak regenerate

### Cek 6: Nginx Session Cookie

Buka browser DevTools → Network → klik request login → cek Response Headers:

```
Set-Cookie: e_kaldik_session=xxx; path=/; secure; HttpOnly; SameSite=lax
```

**Harus ada**: `secure`, `HttpOnly`, `SameSite=lax`

---

## 🔄 MANUAL FIX (Jika Script Gagal)

### Cara 1: Manual Clear Cache

```bash
cd /www/wwwroot/simkur

# Stop PHP
systemctl stop php-fpm-83

# Delete cache manually
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/events.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# Fix permissions
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Clear artisan cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild
php artisan config:cache

# Start PHP
systemctl start php-fpm-83
systemctl restart nginx
```

### Cara 2: Update .env Manual

```bash
nano /www/wwwroot/simkur/.env
```

Ubah/tambah baris ini:
```env
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=
```

Simpan (Ctrl+O, Enter, Ctrl+X), lalu:
```bash
php artisan config:clear
php artisan config:cache
systemctl restart php-fpm-83
```

### Cara 3: Alternative - Cookie Driver

Jika `file` driver tetap gagal, coba `cookie`:

```bash
nano /www/wwwroot/simkur/.env
```

Ubah:
```env
SESSION_DRIVER=cookie
```

```bash
php artisan config:clear
php artisan config:cache
systemctl restart php-fpm-83
```

**Note**: Cookie driver menyimpan session di browser cookie, lebih reliable untuk HTTPS.

---

## 📊 CHECKLIST LENGKAP

Sebelum declare "sudah fix", pastikan:

- [ ] Script `fix-session-hosting.sh` dijalankan tanpa error
- [ ] Output script menunjukkan `SUCCESS: Session is working!`
- [ ] `.env` memiliki `SESSION_DRIVER=file` (atau `cookie`)
- [ ] `.env` memiliki `SESSION_SECURE_COOKIE=true`
- [ ] `php artisan tinker --execute="echo config('session.driver');"` = `file`
- [ ] Permission storage: `drwxrwxr-x www www`
- [ ] PHP-FPM dan Nginx sudah di-restart
- [ ] Browser cookies sudah di-clear SEMUA
- [ ] Test login di incognito window baru
- [ ] Setelah login, redirect ke `/dashboard`
- [ ] Tidak ada error di `storage/logs/laravel.log`
- [ ] Session files muncul di `storage/framework/sessions/`

---

## 🚀 ALTERNATIVE: Fresh Deploy

Jika semua cara di atas gagal, lakukan fresh deploy:

```bash
# Backup database
mysqldump -u simkur_user -p simkur_ekaldik > backup.sql

# Delete old installation
rm -rf /www/wwwroot/simkur/*

# Clone fresh
cd /www/wwwroot/simkur
git clone https://github.com/YOUR_REPO/E-KALDIK.git .

# Setup
cp .env.production-template .env
nano .env  # isi DB credentials dan APP_KEY

# Install
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Permissions
chown -R www:www .
chmod -R 775 storage bootstrap/cache

# Migrate & Seed
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart
systemctl restart php-fpm-83
systemctl restart nginx
```

---

## 📞 BANTUAN

Jika masih gagal, kirim output dari:

```bash
cd /www/wwwroot/simkur

# Session config
php artisan tinker --execute="
echo 'Driver: ' . config('session.driver') . PHP_EOL;
echo 'Secure: ' . (config('session.secure') ? 'true' : 'false') . PHP_EOL;
echo 'SameSite: ' . config('session.same_site') . PHP_EOL;
"

# Permission check
ls -ld storage/framework/sessions

# Session test
php artisan tinker --execute="
session()->put('test', time());
echo session()->get('test') . PHP_EOL;
"

# Laravel log (last 50 lines)
tail -50 storage/logs/laravel.log
```

---

## ✨ KENAPA INI TERJADI?

1. **Laravel config cache**: Laravel meng-cache konfigurasi di `bootstrap/cache/config.php` untuk performa
2. **Ketika `.env` diubah**: Config cache TIDAK otomatis update
3. **Hasil**: Laravel tetap pakai config lama (`SESSION_DRIVER=array`)
4. **Array driver**: Session disimpan di memory, hilang setelah request selesai
5. **Solusi**: Hapus cache file, reload .env, rebuild cache

**Best Practice**: Setiap kali ubah `.env` di production, WAJIB run:
```bash
php artisan config:clear && php artisan config:cache
```

---

**Last Updated**: 2026-06-24
**Status**: Definitive Solution
**Author**: Kiro AI Assistant

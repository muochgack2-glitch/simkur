# 🚀 LANGKAH DEPLOYMENT DI HOSTING (aaPanel)

**Date:** 24 Juni 2026  
**Repository:** https://github.com/muochgack2-glitch/simkur.git  
**Status:** ✅ SIAP DEPLOYMENT

---

## 📋 PERSIAPAN SEBELUM DEPLOYMENT

### ✅ **Checklist:**
- [x] Repository sudah di-push ke GitHub
- [x] ProductionSeeder sudah diperbaiki (role & settings type)
- [x] Migration sudah lengkap
- [x] .env.example sudah ada
- [x] composer.json sudah siap

---

## 🔧 STEP-BY-STEP DEPLOYMENT

### **STEP 1: SSH ke Server**

```bash
# Login via SSH (dari aaPanel Terminal atau PuTTY)
ssh root@your-server-ip
```

---

### **STEP 2: Hapus Deployment Lama (Jika Ada)**

```bash
# Backup database dulu jika ada data penting
cd /www/wwwroot
rm -rf simkur  # Atau nama folder yang sudah ada

# Atau rename jika mau backup
mv simkur simkur.backup
```

---

### **STEP 3: Clone Repository Fresh**

```bash
cd /www/wwwroot
git clone https://github.com/muochgack2-glitch/simkur.git simkur
cd simkur

# Verifikasi file
ls -la
```

---

### **STEP 4: Install Dependencies**

```bash
# Install Composer dependencies (production mode)
composer install --optimize-autoloader --no-dev

# Tunggu sampai selesai...
```

---

### **STEP 5: Setup Environment File**

```bash
# Copy .env.example ke .env
cp .env.example .env

# Edit .env dengan nano atau vi
nano .env
```

**Edit bagian ini di .env:**

```env
APP_NAME="E-KALDIK"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simkur
DB_USERNAME=simkur
DB_PASSWORD=your_secure_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

**Tekan CTRL+O untuk save, CTRL+X untuk exit**

---

### **STEP 6: Generate Application Key**

```bash
php artisan key:generate
```

Output:
```
Application key set successfully.
```

---

### **STEP 7: Set Permissions**

```bash
# Set ownership
chown -R www:www /www/wwwroot/simkur

# Set permissions untuk storage dan cache
chmod -R 775 storage bootstrap/cache
chmod -R 777 storage/app/public storage/logs
```

---

### **STEP 8: Buat Database**

**Via aaPanel:**
1. Buka aaPanel → Database → Add Database
2. Database Name: `simkur`
3. Username: `simkur`
4. Password: (generate atau custom)
5. Click Create

**ATAU via Command Line:**

```bash
mysql -u root -p
```

```sql
CREATE DATABASE simkur CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'simkur'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON simkur.* TO 'simkur'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

### **STEP 9: Run Migrations**

```bash
# Run migrations
php artisan migrate --force
```

Output yang diharapkan:
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table (XX ms)
Migrating: 2026_06_23_032740_create_academic_years_table
Migrated:  2026_06_23_032740_create_academic_years_table (XX ms)
...
```

---

### **STEP 10: Run Production Seeder**

```bash
# Run seeder dengan force flag (production mode)
php artisan db:seed --force
```

**Expected Output:**

```
🚀 Starting Production Seeder...
Creating users...
✓ Users created
Creating activity types...
✓ Activity types created
Creating settings...
✓ Settings created
Creating academic year and semesters...
✓ Academic year and semesters created
✅ Production Seeder completed successfully!
```

---

### **STEP 11: Create Storage Link**

```bash
php artisan storage:link
```

---

### **STEP 12: Cache Configuration (Opsional untuk Performance)**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### **STEP 13: Setup Website di aaPanel**

1. **Buka aaPanel → Website → Add Site**
2. **Domain:** your-domain.com
3. **Root Directory:** `/www/wwwroot/simkur/public`
4. **PHP Version:** 8.2 atau 8.3
5. **Create**

---

### **STEP 14: Setup SSL (Opsional tapi Recommended)**

1. **Di aaPanel → Website → your-domain.com → SSL**
2. **Let's Encrypt** (Free)
3. **Apply Certificate**
4. **Enable Force HTTPS**

---

### **STEP 15: Test Aplikasi**

```bash
# Test di browser
https://your-domain.com

# Atau test akses via curl
curl -I https://your-domain.com
```

---

## 🔐 LOGIN PERTAMA KALI

### **Kredensial Default:**

⚠️ **GUNAKAN USERNAME, BUKAN EMAIL!**

```
Admin:
  Username: admin
  Password: password

Waka Kurikulum:
  Username: kurikulum
  Password: password

Guru:
  Username: guru
  Password: password
```

---

## ⚙️ POST-DEPLOYMENT CHECKLIST

### **Langkah Setelah Deploy:**

1. **Login sebagai Admin**
   - URL: https://your-domain.com
   - Email: admin@ekaldik.local
   - Password: password

2. **Ganti Password Semua Akun**
   - Pergi ke: Profile → Change Password
   - Ganti password admin
   - Login dengan akun lain dan ganti password mereka

3. **Update Settings**
   - Pergi ke: Settings
   - Update `school_name` (nama sekolah)
   - Update `school_address`
   - Update `principal_name` (nama kepala sekolah)
   - Update `principal_niy` (NIY/NIP kepala sekolah)
   - Upload logo sekolah

4. **Test Fitur-fitur:**
   - ✅ Buat kegiatan baru
   - ✅ Lihat kalender admin
   - ✅ Lihat kalender publik: https://your-domain.com/calendar
   - ✅ Test export PDF
   - ✅ Test import Excel

---

## 🐛 TROUBLESHOOTING

### **Error: "500 Internal Server Error"**

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check permissions
chmod -R 775 storage bootstrap/cache
chown -R www:www /www/wwwroot/simkur
```

---

### **Error: "Database Connection Failed"**

```bash
# Test koneksi database
php artisan tinker
DB::connection()->getPdo();

# Jika error, cek .env
nano .env

# Verifikasi:
# - DB_DATABASE
# - DB_USERNAME
# - DB_PASSWORD benar
```

---

### **Error: "SQLSTATE[01000]: Warning: 1265 Data truncated"**

**Penyebab:** Seeder lama masih ada di server

**Solusi:**

```bash
# Pull perubahan terbaru
cd /www/wwwroot/simkur
git pull origin main

# Clear cache
composer dump-autoload
php artisan config:clear

# Reset database dan seed ulang
php artisan migrate:fresh --seed --force
```

---

### **Seeder Error tapi Migration OK?**

```bash
# Run migration dulu
php artisan migrate --force

# Baru run seeder
php artisan db:seed --force

# Atau spesifik seeder
php artisan db:seed --class=ProductionSeeder --force
```

---

### **Git Pull Error: "Cannot pull with rebase: You have unstaged changes"**

```bash
# Stash changes
git stash

# Pull
git pull origin main

# Atau reset hard (HATI-HATI: akan hapus perubahan lokal)
git reset --hard origin/main
git pull origin main
```

---

## 🔄 UPDATE APLIKASI (FUTURE)

### **Cara Update Jika Ada Perubahan Code:**

```bash
# SSH ke server
cd /www/wwwroot/simkur

# Backup database dulu (PENTING!)
php artisan backup:run  # Jika sudah install Laravel Backup
# ATAU manual:
mysqldump -u simkur -p simkur > backup_$(date +%Y%m%d).sql

# Pull perubahan terbaru
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations baru (jika ada)
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Cache ulang (optional)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📊 MONITORING & MAINTENANCE

### **Daily/Weekly Tasks:**

```bash
# 1. Monitor disk space
df -h

# 2. Monitor database size
du -sh /www/wwwroot/simkur/database/

# 3. Check logs
tail -100 storage/logs/laravel.log

# 4. Clear old logs (jika terlalu besar)
rm storage/logs/laravel-*.log

# 5. Backup database (automated recommended)
mysqldump -u simkur -p simkur > /backup/simkur_$(date +%Y%m%d).sql
```

---

## 🎯 PERFORMANCE TIPS

### **Optimize untuk Production:**

```bash
# 1. Enable OPcache (via aaPanel)
# aaPanel → PHP → Configuration File → opcache.enable=1

# 2. Use cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Enable Gzip (via aaPanel)
# aaPanel → Website → your-domain → Configuration → Enable Gzip

# 4. Setup Cron untuk Queue (jika pakai)
# aaPanel → Cron → Add:
# */1 * * * * cd /www/wwwroot/simkur && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📚 NEXT STEPS

### **Setelah Deploy Sukses:**

1. ✅ Ganti semua password default
2. ✅ Upload logo sekolah
3. ✅ Update settings sekolah
4. ✅ Buat tahun pelajaran aktif
5. ✅ Input kegiatan kalender pendidikan
6. ✅ Share URL kalender publik ke guru & siswa
7. ✅ Monitor error logs
8. ✅ Setup automated backup

---

## 🎊 SUMMARY CHECKLIST

```
[ ] Clone repository dari GitHub
[ ] Install composer dependencies
[ ] Setup .env file
[ ] Generate application key
[ ] Set permissions (775 storage, www:www owner)
[ ] Buat database MySQL
[ ] Run migrations
[ ] Run production seeder
[ ] Create storage link
[ ] Cache config (optional)
[ ] Setup website di aaPanel (public folder)
[ ] Setup SSL (Let's Encrypt)
[ ] Test login
[ ] Ganti password default
[ ] Update settings sekolah
[ ] Upload logo
[ ] Test semua fitur
```

---

**🚀 DEPLOYMENT COMPLETE!**

Aplikasi E-KALDIK sudah siap digunakan di production!

**URL Akses:**
- Admin Dashboard: https://your-domain.com
- Public Calendar: https://your-domain.com/calendar

**Support:**
- Documentation: Check DEPLOYMENT-GUIDE.md
- Roadmap: Check ROADMAP-SIM-KURIKULUM.md
- Seeder Docs: Check SEEDER-DOCUMENTATION.md

---

**Last Updated:** 24 Juni 2026  
**Version:** 1.0 Production Ready  
**Status:** ✅ SIAP DEPLOY

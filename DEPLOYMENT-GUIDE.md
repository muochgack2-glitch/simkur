# 🚀 DEPLOYMENT GUIDE - E-KALDIK ke aaPanel

**Tanggal:** 24 Juni 2026  
**Target Server:** aaPanel  
**Method:** Git Pull dari GitHub

---

## 📋 CHECKLIST SEBELUM DEPLOYMENT

### 1. **Persiapan Repository GitHub**
- [ ] Push semua changes ke GitHub
- [ ] Pastikan `.gitignore` sudah benar
- [ ] Hapus file temporary/test PHP (*.php di root)
- [ ] Pastikan `.env` tidak ter-commit
- [ ] Buat `.env.example` yang lengkap

### 2. **File yang Harus Dihapus/Ignore**
File-file temporary ini sebaiknya dihapus atau di-gitignore:
```
add_cuti_bersama.php
add_final_day.php
add_libur_semester_ganjil.php
add_more_cutibersama_sem1.php
add_semester_ganjil_holidays.php
analyze_excel_data.php
check_dates.php
check_final_result.php
check_holidays.php
check_libur_semester.php
check_new_calculation.php
check_activity_types.php
check_semester_ganjil.php
```

### 3. **Database**
- [ ] Export database production-ready
- [ ] Buat seeder untuk data default (activity types, settings, dll)
- [ ] Dokumentasi struktur database

---

## 🔧 LANGKAH DEPLOYMENT DI AAPANEL

### **STEP 1: Setup Website di aaPanel**

1. **Login aaPanel**
2. **Buat Website Baru:**
   - Website → Add site
   - Domain: `ekaldik.example.com` (ganti dengan domain Anda)
   - Root directory: `/www/wwwroot/ekaldik.example.com`
   - PHP Version: **8.2** (minimal 8.1)
   - Database: Buat database baru (misal: `ekaldik_db`)

3. **Setup SSL (Opsional tapi Recommended):**
   - Website → SSL → Let's Encrypt
   - Apply SSL

---

### **STEP 2: Install Dependencies di Server**

SSH ke server dan install:

```bash
# Install Composer (jika belum ada)
cd /www/wwwroot/ekaldik.example.com

# Install Git (jika belum ada)
# Biasanya sudah ada di aaPanel

# Install PHP Extensions (via aaPanel)
# - php-mbstring
# - php-xml
# - php-bcmath
# - php-zip
# - php-gd (untuk PDF dengan logo)
```

Di aaPanel:
- App Store → PHP → Select PHP 8.2 → Install Extensions:
  - mbstring
  - xml
  - bcmath
  - zip
  - gd
  - pdo_mysql
  - fileinfo

---

### **STEP 3: Clone Repository dari GitHub**

```bash
cd /www/wwwroot
rm -rf ekaldik.example.com/*  # Hapus file default aaPanel

# Clone repository
git clone https://github.com/USERNAME/ekaldik.git ekaldik.example.com
cd ekaldik.example.com
```

---

### **STEP 4: Setup Laravel**

```bash
# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Copy .env
cp .env.example .env
nano .env  # atau vi .env

# Generate App Key
php artisan key:generate

# Setup Storage Link
php artisan storage:link

# Create images directory
mkdir -p public/images
chmod -R 775 public/images
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (ganti www dengan user aaPanel)
chown -R www:www /www/wwwroot/ekaldik.example.com
```

---

### **STEP 5: Konfigurasi .env untuk Production**

Edit `/www/wwwroot/ekaldik.example.com/.env`:

```env
APP_NAME="E-KALDIK SIM Kurikulum"
APP_ENV=production
APP_KEY=base64:xxxxx  # Sudah auto-generate
APP_DEBUG=false  # PENTING: false di production!
APP_URL=https://ekaldik.example.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ekaldik_db
DB_USERNAME=ekaldik_user
DB_PASSWORD=password_yang_kuat

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Timezone
APP_TIMEZONE=Asia/Jakarta
```

---

### **STEP 6: Setup Database**

```bash
# Run migrations
php artisan migrate --force

# Run seeders (untuk data default)
php artisan db:seed --force

# Atau seed specific seeder
php artisan db:seed --class=ActivityTypeSeeder --force
php artisan db:seed --class=DefaultUserSeeder --force
```

---

### **STEP 7: Setup Web Server (Nginx)**

Di aaPanel:
- Website → Domain Management → Select site
- **Root Directory:** Ubah ke `/www/wwwroot/ekaldik.example.com/public`
- **Rewrite Rules:** Pilih Laravel

Atau manual edit Nginx config:
```nginx
root /www/wwwroot/ekaldik.example.com/public;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/tmp/php-cgi-82.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

Reload Nginx:
```bash
nginx -t
systemctl reload nginx
```

---

### **STEP 8: Optimasi Production**

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

### **STEP 9: Setup Cron Job (Opsional)**

Jika ada task scheduling, tambahkan di crontab:

```bash
crontab -e
```

Tambahkan:
```
* * * * * cd /www/wwwroot/ekaldik.example.com && php artisan schedule:run >> /dev/null 2>&1
```

---

### **STEP 10: Testing**

1. Buka browser: `https://ekaldik.example.com`
2. Test login dengan user default
3. Test create kegiatan
4. Test kalender publik
5. Test export PDF
6. Test effective days calculation

---

## 🔄 UPDATE DEPLOYMENT (Pull dari GitHub)

Untuk update aplikasi di production:

```bash
cd /www/wwwroot/ekaldik.example.com

# Pull latest code
git pull origin main  # atau branch production

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations (jika ada)
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www:www /www/wwwroot/ekaldik.example.com
chmod -R 775 storage bootstrap/cache public/images
```

**Script Otomatis (deploy.sh):**

```bash
#!/bin/bash
cd /www/wwwroot/ekaldik.example.com

echo "🚀 Starting deployment..."

# Git pull
git pull origin main

# Composer
composer install --optimize-autoloader --no-dev

# Migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chown -R www:www /www/wwwroot/ekaldik.example.com
chmod -R 775 storage bootstrap/cache public/images

echo "✅ Deployment complete!"
```

Jalankan dengan: `bash deploy.sh`

---

## 🔒 SECURITY CHECKLIST

- [ ] `APP_DEBUG=false` di production
- [ ] Strong `APP_KEY` (auto-generated)
- [ ] Strong database password
- [ ] `.env` tidak ter-commit ke Git
- [ ] SSL/HTTPS aktif
- [ ] File permissions benar (775 untuk storage, 644 untuk files)
- [ ] Disable directory listing di Nginx
- [ ] Rate limiting aktif (Laravel default)
- [ ] CSRF protection aktif (Laravel default)

---

## 📝 DEFAULT USER

Setelah deployment, login dengan:
- **URL:** `https://ekaldik.example.com`
- **Username:** (sesuai seeder)
- **Password:** (sesuai seeder)

**PENTING:** Ganti password default setelah login pertama!

---

## 🐛 TROUBLESHOOTING

### **Error 500 - Internal Server Error**
```bash
# Cek log Laravel
tail -f storage/logs/laravel.log

# Cek log Nginx
tail -f /www/wwwlogs/ekaldik.example.com.error.log
```

### **Permission Denied**
```bash
chown -R www:www /www/wwwroot/ekaldik.example.com
chmod -R 775 storage bootstrap/cache
```

### **Database Connection Error**
- Cek kredensial di `.env`
- Pastikan database exist
- Pastikan user punya privilege

### **Missing PHP Extensions**
Di aaPanel: App Store → PHP → Install Extensions

---

## 📞 SUPPORT

Jika ada masalah saat deployment:
1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek log Nginx/Apache
3. Cek PHP error log
4. Test dengan `php artisan tinker`

---

**Last Updated:** 24 Juni 2026  
**Version:** 1.0  
**Status:** Ready for Production 🚀

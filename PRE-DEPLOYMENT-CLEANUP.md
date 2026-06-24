# 🧹 PRE-DEPLOYMENT CLEANUP CHECKLIST

**Target:** Clean repository sebelum push ke GitHub  
**Tanggal:** 24 Juni 2026

---

## ❌ FILE YANG HARUS DIHAPUS

### **1. Test/Debug PHP Scripts (Root Directory)**
File-file temporary untuk testing, **HAPUS SEMUA:**

```bash
# Di root project, hapus file ini:
rm add_cuti_bersama.php
rm add_final_day.php
rm add_libur_semester_ganjil.php
rm add_more_cutibersama_sem1.php
rm add_semester_ganjil_holidays.php
rm analyze_excel_data.php
rm check_dates.php
rm check_final_result.php
rm check_holidays.php
rm check_libur_semester.php
rm check_semester_ganjil.php
rm check_new_calculation.php
rm check_activity_types.php
```

**Atau satu command:**
```bash
rm add_*.php check_*.php analyze_*.php
```

---

### **2. Markdown Documentation (Opsional - Pilih)**

Documentation files - **PILIH:** Hapus atau Keep?

#### **Keep (Recommended):**
```
✅ README.md                                  # Tetap
✅ DEPLOYMENT-GUIDE.md                        # Untuk reference
✅ ROADMAP-SIM-KURIKULUM.md                   # Future planning
✅ PRE-DEPLOYMENT-CLEANUP.md                  # This file
```

#### **Opsional Hapus (Jika tidak perlu di repo):**
```
❓ ACADEMIC-YEAR-COMPLETE.md                  # History log
❓ ACTIVITIES-COMPLETE.md                     # History log
❓ ACTIVITY-TYPE-COMPLETE.md                  # History log
❓ AUTHENTICATION-COMPLETE.md                 # History log
❓ BUGFIX-*.md                                # History log
❓ CALENDAR-*.md                              # History log
❓ COLOR-CONSISTENCY-FIX.md                   # History log
❓ VALIDATION-PAGE-COMPLETE.md                # History log
❓ FORMULA-CHANGE-EXAM-DAYS.md                # History log
❓ CALENDAR-ICON-IMPLEMENTATION.md            # Implementation log
❓ CONTEXT-TRANSFER-VERIFICATION.md           # Internal note
```

**Recommendation:** Pindahkan ke folder `docs/` atau hapus jika tidak diperlukan di production.

```bash
# Buat folder docs untuk menyimpan
mkdir docs
mv *-COMPLETE.md docs/
mv BUGFIX-*.md docs/
mv CALENDAR-*.md docs/
mv FORMULA-*.md docs/
mv CONTEXT-*.md docs/
```

---

### **3. Environment Files**

**PASTIKAN .env TIDAK TER-COMMIT!**

```bash
# Cek apakah .env di-track
git status

# Jika .env muncul, JANGAN commit!
# Pastikan .gitignore sudah benar
```

**Update .gitignore:**
```
/.phpunit.cache
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.fleet
/.idea
/.vscode

# Test files
*.test.php
check_*.php
add_*.php
analyze_*.php
```

---

### **4. Temporary/Cache Files**

```bash
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Hapus compiled files
rm -rf bootstrap/cache/*.php

# Node modules (jika ada)
rm -rf node_modules/

# Vendor (akan di-install via composer di server)
# OPSIONAL: Bisa dihapus jika repo size besar
# rm -rf vendor/
```

---

## ✅ FILE YANG HARUS ADA

### **1. .env.example**

Pastikan `.env.example` lengkap dan up-to-date:

```env
APP_NAME="E-KALDIK"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ekaldik
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
CACHE_PREFIX=

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ekaldik.local"
MAIL_FROM_NAME="${APP_NAME}"
```

---

### **2. README.md**

Buat/Update README.md yang informatif:

```markdown
# E-KALDIK - Sistem Kalender Pendidikan

Aplikasi manajemen kalender pendidikan untuk sekolah.

## Features
- Manajemen Tahun Pelajaran & Semester
- Kalender Kegiatan Sekolah
- Perhitungan Hari Efektif
- Export PDF & Excel
- Multi-role Access

## Tech Stack
- Laravel 11
- Livewire 3
- Tailwind CSS
- MySQL

## Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## License
Proprietary - All rights reserved
```

---

### **3. .gitignore**

Pastikan `.gitignore` sudah benar (lihat section 3 di atas).

---

## 🔍 VERIFICATION CHECKLIST

Sebelum push ke GitHub:

- [ ] Semua file test PHP sudah dihapus
- [ ] `.env` TIDAK ter-track di git
- [ ] `.env.example` sudah lengkap dan up-to-date
- [ ] `.gitignore` sudah benar
- [ ] `README.md` informatif
- [ ] Cache Laravel sudah di-clear
- [ ] Tidak ada sensitive data (password, API key) di code
- [ ] Database seeder ready untuk production
- [ ] Migration files final dan tested

---

## 📦 FINAL CHECKLIST SEBELUM PUSH

```bash
# 1. Hapus file test
rm add_*.php check_*.php analyze_*.php

# 2. Organize documentation
mkdir -p docs
mv *-COMPLETE.md docs/ 2>/dev/null
mv BUGFIX-*.md docs/ 2>/dev/null
mv CALENDAR-*.md docs/ 2>/dev/null

# 3. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Check git status
git status

# 5. Review files yang akan di-commit
git diff

# 6. Add all (hati-hati, review dulu!)
git add .

# 7. Commit
git commit -m "feat: E-KALDIK v1.0 - Calendar Management System with Effective Days Calculation"

# 8. Push
git push origin main
```

---

## 🚨 SECURITY CHECK

Sebelum push, pastikan TIDAK ADA:

- [ ] ❌ Hardcoded password
- [ ] ❌ API keys di code
- [ ] ❌ Database credentials di code
- [ ] ❌ .env file
- [ ] ❌ Private keys
- [ ] ❌ Sensitive user data

Gunakan:
```bash
# Search for potential secrets
grep -r "password" --include="*.php" app/
grep -r "api_key" --include="*.php" app/
grep -r "secret" --include="*.php" app/
```

---

## 📋 QUICK COMMAND SUMMARY

```bash
# Clean up
rm add_*.php check_*.php analyze_*.php
mkdir docs && mv *-COMPLETE.md BUGFIX-*.md CALENDAR-*.md docs/
php artisan cache:clear
php artisan config:clear

# Verify
git status
cat .gitignore
cat .env.example

# Commit
git add .
git commit -m "feat: E-KALDIK v1.0 ready for deployment"
git push origin main
```

---

## ✅ POST-CLEANUP

Setelah cleanup:
1. ✅ Repository bersih
2. ✅ Siap di-clone ke server
3. ✅ Tidak ada file sensitive
4. ✅ Documentation organized
5. ✅ Ready for production deployment

---

**Status:** Ready for GitHub Push 🚀  
**Next Step:** Follow DEPLOYMENT-GUIDE.md

**END OF CHECKLIST**

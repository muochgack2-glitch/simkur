# 📝 PRODUCTION DEPLOYMENT NOTES

**Date:** 24 Juni 2026  
**Version:** E-KALDIK v1.0  
**Status:** ✅ Ready for Production

---

## ✅ FINAL CHECKLIST

### **Code & Repository:**
- ✅ All test files cleaned up
- ✅ Documentation organized in `docs/` folder
- ✅ Production seeder created and tested
- ✅ All changes pushed to GitHub
- ✅ Repository: https://github.com/muochgack2-glitch/simkur.git

### **Database Seeder:**
- ✅ ProductionSeeder.php completed
- ✅ Includes username field for all users
- ✅ Smart environment detection
- ✅ Documentation complete

---

## 🚀 PRODUCTION DEPLOYMENT COMMAND SEQUENCE

### **1. Clone Repository**
```bash
cd /www/wwwroot
git clone https://github.com/muochgack2-glitch/simkur.git your-domain.com
cd your-domain.com
```

### **2. Install Dependencies**
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### **3. Environment Setup**
```bash
cp .env.example .env
nano .env
```

**Edit .env:**
```env
APP_NAME="E-KALDIK"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_strong_password
```

### **4. Generate Key & Link Storage**
```bash
php artisan key:generate
php artisan storage:link
mkdir -p public/images
```

### **5. Database Migration & Seeding**
```bash
# IMPORTANT: Fresh install, will create all tables
php artisan migrate --force

# Seed with production data
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

### **6. Set Permissions**
```bash
chown -R www:www /www/wwwroot/your-domain.com
chmod -R 775 storage bootstrap/cache public/images
```

### **7. Optimize for Production**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

### **8. Configure Nginx (aaPanel)**
**Root Directory:** `/www/wwwroot/your-domain.com/public`  
**Rewrite Rules:** Laravel

---

## 🔐 DEFAULT CREDENTIALS

After seeding, login with:

```
Admin:
  Username: admin
  Email: admin@ekaldik.local
  Password: password

Kurikulum:
  Username: kurikulum
  Email: kurikulum@ekaldik.local
  Password: password

Guru:
  Username: guru
  Email: guru@ekaldik.local
  Password: password
```

⚠️ **CRITICAL:** Change ALL passwords immediately after first login!

---

## ⚠️ COMMON ISSUES & SOLUTIONS

### **Issue 1: Duplicate Entry Error (username 'admin')**
**Cause:** Seeder run multiple times on same database  
**Solution:** 
```bash
# Fresh install (will delete all data)
php artisan migrate:fresh --force
php artisan db:seed --force
```

### **Issue 2: Permission Denied**
**Solution:**
```bash
chown -R www:www /www/wwwroot/your-domain.com
chmod -R 775 storage bootstrap/cache public/images
```

### **Issue 3: 500 Internal Server Error**
**Check:**
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Check permissions
ls -la storage/
ls -la bootstrap/cache/

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### **Issue 4: Database Connection Error**
**Check .env:**
```bash
# Verify database credentials
cat .env | grep DB_

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### **Issue 5: Missing PHP Extensions**
**Install via aaPanel:**
- PHP 8.2 → Install Extensions:
  - mbstring ✅
  - xml ✅
  - bcmath ✅
  - zip ✅
  - gd ✅
  - pdo_mysql ✅
  - fileinfo ✅

---

## 📋 POST-DEPLOYMENT CHECKLIST

After deployment, verify:

- [ ] Site accessible via HTTPS
- [ ] Login page loads correctly
- [ ] Can login with default credentials
- [ ] Change all default passwords
- [ ] Update Settings:
  - [ ] School name
  - [ ] School address
  - [ ] Upload logo
  - [ ] Principal name & NIY
- [ ] Test create kegiatan
- [ ] Test calendar view (admin)
- [ ] Test public calendar
- [ ] Test export PDF
- [ ] Test perhitungan hari efektif
- [ ] Test validation page

---

## 🔄 UPDATE DEPLOYMENT (Pull Updates)

When you push updates to GitHub:

```bash
cd /www/wwwroot/your-domain.com

# Pull latest code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run new migrations (if any)
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
chown -R www:www .
chmod -R 775 storage bootstrap/cache public/images
```

---

## 🎯 PRODUCTION SEEDER DATA

### **Users (3)**
- Administrator (admin / admin@ekaldik.local)
- Tim Kurikulum (kurikulum / kurikulum@ekaldik.local)
- Guru Contoh (guru / guru@ekaldik.local)

### **Activity Types (14)**
All with emoji icons:
- LAP, PKL, MPLS, PTS, PAS, PAT, ANBK
- LIBNAS, LIBSEM, RAPAT, KEGIATAN
- UPACARA, TKA, RAPOR

### **Settings (7)**
- school_name, school_address, school_logo
- principal_name, principal_niy
- weekend_days, app_timezone

### **Academic Data**
- Academic Year 2026/2027
- Semester Ganjil (13 Jul - 20 Des 2026)
- Semester Genap (5 Jan - 20 Jun 2027)

---

## 🔒 SECURITY CHECKLIST

- [ ] `APP_DEBUG=false` in .env
- [ ] Strong database password
- [ ] `.env` not in git (checked)
- [ ] SSL/HTTPS enabled
- [ ] Default passwords changed
- [ ] File permissions correct (775 storage, 644 files)
- [ ] Regular backups scheduled
- [ ] Error logs monitored

---

## 📞 SUPPORT CONTACTS

**Repository:** https://github.com/muochgack2-glitch/simkur.git

**Documentation:**
- Deployment Guide: `DEPLOYMENT-GUIDE.md`
- Seeder Docs: `SEEDER-DOCUMENTATION.md`
- Roadmap: `ROADMAP-SIM-KURIKULUM.md`

---

## ✅ DEPLOYMENT SUCCESS CRITERIA

System is successfully deployed when:
1. ✅ Site accessible via domain
2. ✅ HTTPS working
3. ✅ Can login with any default user
4. ✅ Can create new kegiatan
5. ✅ Calendar displays correctly
6. ✅ Public calendar accessible
7. ✅ Export PDF works
8. ✅ Hari efektif calculation works
9. ✅ No errors in Laravel logs
10. ✅ All passwords changed

---

**Prepared by:** Development Team  
**Last Updated:** 24 Juni 2026  
**Version:** 1.0.0  
**Status:** ✅ Production Ready

🚀 **Ready for Deployment to aaPanel!**

---

**END OF DOCUMENT**

# 📚 E-KALDIK - Sistem Kalender Pendidikan

**Version:** 1.0.0  
**Status:** ✅ Production Ready  
**Tech Stack:** Laravel 11 + Livewire 3 + Tailwind CSS  
**Repository:** https://github.com/muochgack2-glitch/simkur.git

---

## 🎯 TENTANG E-KALDIK

E-KALDIK (Elektronik - Kalender Pendidikan) adalah sistem manajemen kalender pendidikan berbasis web yang membantu sekolah dalam:

- 📅 Mengelola kalender akademik tahunan
- 🏫 Merencanakan kegiatan sekolah
- 📊 Menghitung hari efektif pembelajaran
- 📄 Export kalender ke PDF
- 🌐 Publish kalender publik untuk siswa & orang tua
- 📥 Import/Export kegiatan via Excel

---

## ✨ FITUR UTAMA

### **1. Manajemen Tahun Pelajaran & Semester**
- Kelola tahun pelajaran aktif
- Semester ganjil & genap
- Perhitungan otomatis hari efektif

### **2. Kalender Kegiatan**
- Kalender interaktif (FullCalendar.js)
- 14+ jenis kegiatan preset (MPLS, PTS, PAS, PAT, ANBK, dll)
- Custom kegiatan dengan emoji icons
- Drag & drop support
- Multi-view (month/year/list)

### **3. Perhitungan Hari Efektif**
- Otomatis hitung hari efektif per semester
- Exclude weekend (Sabtu/Minggu)
- Exclude hari libur nasional
- Exclude kegiatan ujian (PTS/PAS/PAT)
- Sesuai aturan Permendikbud

### **4. Export & Import**
- Export PDF dengan logo sekolah
- Export Excel untuk backup
- Import Excel untuk bulk insert
- Template Excel tersedia

### **5. Kalender Publik**
- URL publik untuk siswa/orang tua
- Read-only view
- Responsive design
- No login required

### **6. Multi-role Access**
- **Admin:** Full access semua fitur
- **Waka Kurikulum:** Kelola kegiatan & kalender
- **Guru:** View kalender (planning: input kegiatan kelas)

### **7. Activity Log**
- Track semua perubahan
- Who did what when
- Audit trail lengkap

---

## 🚀 QUICK START

### **For Production Deployment (aaPanel/Hosting):**

```bash
# 1. Clone repository
cd /www/wwwroot
git clone https://github.com/muochgack2-glitch/simkur.git
cd simkur

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Setup environment
cp .env.example .env
nano .env  # Edit DB credentials

# 4. Generate key
php artisan key:generate

# 5. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www:www .

# 6. Run migrations & seeder
php artisan migrate --force
php artisan db:seed --force

# 7. Done!
```

**📖 Detailed Guide:** Lihat file `HOSTING-DEPLOYMENT-STEPS.md`

---

### **For Local Development:**

```bash
# 1. Clone & install
git clone https://github.com/muochgack2-glitch/simkur.git
cd simkur
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
# Edit .env dengan DB credentials Anda

# 4. Migrate & seed
php artisan migrate
php artisan db:seed

# 5. Run dev server
php artisan serve

# 6. Access
# http://localhost:8000
```

---

## 🔐 DEFAULT CREDENTIALS

⚠️ **LOGIN MENGGUNAKAN USERNAME, BUKAN EMAIL!**

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

⚠️ **IMPORTANT:** Ganti password setelah first login!

---

## 📋 REQUIREMENTS

### **Server Requirements:**
- PHP >= 8.2
- MySQL >= 5.7 atau MariaDB >= 10.3
- Composer
- Web Server (Apache/Nginx)

### **PHP Extensions:**
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD atau Imagick (untuk PDF)

### **Recommended:**
- Node.js & NPM (untuk development)
- Redis (untuk caching)
- Supervisor (untuk queue workers)

---

## 📚 DOCUMENTATION

### **Core Documentation:**
- 🚀 **[HOSTING-DEPLOYMENT-STEPS.md](./HOSTING-DEPLOYMENT-STEPS.md)** - Complete deployment guide untuk aaPanel
- 🔐 **[SESSION-FIX-GUIDE.md](./SESSION-FIX-GUIDE.md)** - **LOGIN FIX GUIDE** - Solusi lengkap masalah session/login
- 📖 **[DEPLOYMENT-GUIDE.md](./DEPLOYMENT-GUIDE.md)** - General deployment information
- 🗺️ **[ROADMAP-SIM-KURIKULUM.md](./ROADMAP-SIM-KURIKULUM.md)** - Development roadmap (v2.0 planning)
- 📝 **[SEEDER-DOCUMENTATION.md](./SEEDER-DOCUMENTATION.md)** - Database seeder guide
- 🔧 **[FIXING-SUMMARY.md](./FIXING-SUMMARY.md)** - Latest fixes & troubleshooting
- 📊 **[PRODUCTION-DEPLOYMENT-NOTES.md](./PRODUCTION-DEPLOYMENT-NOTES.md)** - Production notes

### **Technical Documentation:**
- `database/seeders/README.md` - Seeder technical docs
- `app/Services/EffectiveDayService.php` - Effective days calculation logic
- `app/Services/ExportPdfService.php` - PDF export logic

---

## 🛠️ TECH STACK

### **Backend:**
- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **Livewire 3** - Full-stack framework untuk Laravel

### **Frontend:**
- **Tailwind CSS** - Utility-first CSS
- **Alpine.js** - Minimal JavaScript framework (via Livewire)
- **FullCalendar.js** - Interactive calendar
- **SweetAlert2** - Beautiful alerts
- **Emoji Picker** - Native emoji support

### **Libraries:**
- **DomPDF** - PDF generation
- **PhpSpreadsheet** - Excel import/export
- **Carbon** - Date manipulation

---

## 📁 PROJECT STRUCTURE

```
E-KALDIK/
├── app/
│   ├── Console/Commands/      # Artisan commands
│   ├── Http/
│   │   ├── Controllers/       # HTTP Controllers
│   │   ├── Middleware/        # Custom middleware
│   ├── Livewire/              # Livewire components
│   │   ├── Activity/          # Kegiatan CRUD
│   │   ├── ActivityType/      # Jenis kegiatan CRUD
│   │   ├── AcademicYear/      # Tahun pelajaran CRUD
│   │   ├── EffectiveDay/      # Hari efektif view
│   │   ├── Dashboard/         # Dashboard
│   │   └── Settings/          # Settings management
│   ├── Models/                # Eloquent models
│   └── Services/              # Business logic services
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/               # Database seeders
│   └── database.sqlite        # SQLite (local dev)
├── resources/
│   ├── views/
│   │   ├── livewire/          # Livewire blade templates
│   │   ├── layouts/           # Layout templates
│   │   └── pdf/               # PDF templates
│   └── css/                   # Stylesheets
├── public/                    # Public assets
├── storage/                   # Logs, cache, uploads
├── .env.example               # Environment template
├── composer.json              # PHP dependencies
└── README.md                  # This file
```

---

## 🔄 DEVELOPMENT WORKFLOW

### **Feature Branch Workflow:**

```bash
# 1. Create feature branch
git checkout -b feature/nama-fitur

# 2. Make changes & commit
git add .
git commit -m "feat: deskripsi fitur"

# 3. Push to GitHub
git push origin feature/nama-fitur

# 4. Create Pull Request di GitHub

# 5. Merge ke main setelah review
```

### **Commit Message Convention:**

```
feat: new feature
fix: bug fix
docs: documentation changes
style: code style changes (formatting)
refactor: code refactoring
test: add tests
chore: maintenance tasks
```

---

## 🧪 TESTING

### **Run Tests:**

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=EffectiveDayTest

# With coverage
php artisan test --coverage
```

### **Manual Testing Checklist:**

- [ ] Login dengan 3 role berbeda
- [ ] Buat kegiatan baru
- [ ] Edit kegiatan existing
- [ ] Delete kegiatan
- [ ] Drag & drop di kalender
- [ ] Export PDF
- [ ] Import Excel
- [ ] View kalender publik
- [ ] Perhitungan hari efektif
- [ ] Change password
- [ ] Update settings

---

## 🐛 TROUBLESHOOTING

### **Common Issues:**

**1. ⚠️ LOGIN TIDAK BERFUNGSI / SESSION TIDAK PERSIST**

**Gejala:** Setelah klik login, form kembali kosong. Tidak redirect ke dashboard.

**Solusi:** Lihat panduan lengkap di **[SESSION-FIX-GUIDE.md](./SESSION-FIX-GUIDE.md)**

Quick fix di hosting:
```bash
cd /www/wwwroot/simkur
chmod +x fix-session-hosting.sh
./fix-session-hosting.sh
```

**Root cause:** Config cache tidak refresh ketika .env diubah.

---

**2. Error 500 - Internal Server Error**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
chmod -R 775 storage bootstrap/cache
```

**3. Database Connection Error**
```bash
# Check .env
cat .env | grep DB_

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

**4. Seeder Error "Data truncated"**
```bash
# Pull latest fixes
git pull origin main
composer dump-autoload

# Re-run seeder
php artisan migrate:fresh --seed --force
```

**5. PDF tidak tampil dengan benar**
```bash
# Check GD/Imagick extension
php -m | grep -i gd

# Clear PDF cache
rm storage/framework/cache/pdf/*
```

**More troubleshooting:** Lihat `FIXING-SUMMARY.md` atau `SESSION-FIX-GUIDE.md`

---

## 🗺️ ROADMAP

### **Version 1.0 (CURRENT)** ✅
- [x] Kalender pendidikan
- [x] Manajemen kegiatan
- [x] Perhitungan hari efektif
- [x] Export PDF
- [x] Import/Export Excel
- [x] Kalender publik
- [x] Multi-role access

### **Version 2.0 (PLANNING)** - SIM KURIKULUM
- [ ] Mata pelajaran
- [ ] Struktur kurikulum
- [ ] Manajemen kelas
- [ ] Jadwal pelajaran
- [ ] RPP (Rencana Pelaksanaan Pembelajaran)
- [ ] Penilaian & rapor
- [ ] Dashboard analytics

**Detailed Roadmap:** `ROADMAP-SIM-KURIKULUM.md`

---

## 🤝 CONTRIBUTING

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'feat: Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📜 LICENSE

This project is proprietary software developed for internal use.

---

## 👥 CREDITS

**Developed by:** AI Assistant + Human Developer  
**Date:** Juni 2026  
**Framework:** Laravel, Livewire  
**UI Design:** Tailwind CSS  

---

## 📞 SUPPORT

### **Need Help?**

1. Check documentation files:
   - `HOSTING-DEPLOYMENT-STEPS.md` untuk deployment
   - `FIXING-SUMMARY.md` untuk troubleshooting
   - `ROADMAP-SIM-KURIKULUM.md` untuk future plans

2. Check GitHub Issues:
   - https://github.com/muochgack2-glitch/simkur/issues

3. Review codebase:
   - Most files are well-commented
   - Check `app/Services/` for business logic
   - Check `database/seeders/README.md` for seeder info

---

## 🎉 ACKNOWLEDGMENTS

Special thanks to:
- Laravel Team for the amazing framework
- Livewire Team for full-stack reactivity
- Tailwind CSS Team for utility-first CSS
- FullCalendar.js for interactive calendar

---

**⭐ Happy Coding! ⭐**

---

**Last Updated:** 24 Juni 2026  
**Version:** 1.0.0  
**Status:** Production Ready ✅

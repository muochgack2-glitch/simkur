# e-KALDIK - Kalender Pendidikan Digital untuk SMK

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**Aplikasi Web untuk Mengelola Kalender Pendidikan Sekolah**

[Documentation](#dokumentasi) • [Installation](#instalasi) • [Features](#fitur-phase-1) • [Screenshots](#screenshots)

</div>

---

## 📋 Deskripsi

**e-KALDIK** adalah aplikasi web profesional untuk mengelola Kalender Pendidikan sekolah selama satu tahun pelajaran secara digital, stabil, dan user-friendly. Dirancang khusus untuk SMK dengan kebutuhan Waka Kurikulum dalam mengelola kegiatan akademik dan non-akademik.

### Target Pengguna:
- **Admin**: Mengelola sistem secara keseluruhan
- **Waka Kurikulum**: Mengelola kalender dan kegiatan akademik
- **Guru**: Melihat informasi kalender (read-only)

---

## ✨ Fitur (Phase 1)

### 1. Authentication & Authorization
- ✅ Login dengan username/password
- ✅ Logout
- ✅ Ganti Password
- ✅ Role-based access control (Admin, Waka Kurikulum, Guru)

### 2. Manajemen Tahun Pelajaran
- ✅ Tambah tahun pelajaran baru
- ✅ Edit tahun pelajaran
- ✅ Aktifkan tahun pelajaran (hanya 1 aktif)
- ✅ Arsipkan tahun pelajaran
- ✅ Auto-generate 2 semester (Ganjil & Genap)

### 3. Master Jenis Kegiatan
9 jenis kegiatan default:
- **MPLS** - Masa Pengenalan Lingkungan Sekolah
- **PTS** - Penilaian Tengah Semester
- **PAS** - Penilaian Akhir Semester
- **PAT** - Penilaian Akhir Tahun
- **ANBK** - Asesmen Nasional Berbasis Komputer
- **Libur Nasional**
- **Libur Semester**
- **Rapat Guru**
- **Kegiatan Sekolah**

### 4. Kalender Pendidikan
- ✅ Tambah/Edit/Hapus kegiatan
- ✅ View Bulanan (calendar grid)
- ✅ View Tahunan (12 bulan overview)
- ✅ View Daftar Agenda (list dengan filter)
- ✅ Color coding per jenis kegiatan
- ✅ Deteksi bentrok jadwal

### 5. Perhitungan Hari Efektif (Otomatis)
- ✅ Hitung hari belajar
- ✅ Hitung hari libur
- ✅ Hitung hari ujian
- ✅ Hitung minggu efektif per semester
- ✅ Auto-recalculate saat ada perubahan kegiatan

### 6. Dashboard
- ✅ Tahun Pelajaran Aktif
- ✅ Jumlah Kegiatan
- ✅ Statistik Hari Efektif
- ✅ Agenda Terdekat (7 hari ke depan)
- ✅ Chart kegiatan per bulan

### 7. Import & Export
- ✅ Import Excel (template provided)
- ✅ Export PDF (Yearly/Monthly/List)
- ✅ Export Excel

---

## 🛠️ Tech Stack

### Backend
- **Laravel 12.62.0** - PHP Framework
- **MySQL 8.0+** - Database
- **PHP 8.2+** - Programming Language

### Frontend
- **Livewire 4.3.1** - Full-stack framework
- **Tailwind CSS 3.x** - CSS Framework
- **Alpine.js** - JavaScript Framework (via Livewire)
- **FullCalendar 6.x** - Interactive Calendar

### Libraries
- **DomPDF** - PDF Generation
- **Laravel Excel** - Excel Import/Export
- **Vite** - Asset Bundler

---

## 📦 Instalasi

### Prerequisites

Pastikan sudah terinstall:
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0
- Web Server (Apache/Nginx) atau PHP built-in server

### Step-by-Step Installation

#### 1. Clone Repository

```bash
git clone https://github.com/your-repo/e-kaldik.git
cd e-kaldik
```

#### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

#### 3. Setup Environment

```bash
# Copy .env.example to .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Configure Database

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ekaldik
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 5. Create Database

```sql
CREATE DATABASE ekaldik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 6. Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

#### 7. Build Assets

```bash
# For development (with hot reload)
npm run dev

# For production
npm run build
```

#### 8. Start Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

---

## 🔑 Default Credentials

Setelah seeding, gunakan credentials berikut untuk login:

### Admin
```
Username: admin
Password: password
```

### Waka Kurikulum
```
Username: waka
Password: password
```

### Guru
```
Username: guru1
Password: password
```

⚠️ **PENTING**: Ganti password default setelah login pertama kali!

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di folder `/docs`:

- **01-analisis-kebutuhan.md** - Analisis kebutuhan sistem lengkap
- **02-erd-database.md** - Entity Relationship Diagram
- **03-struktur-tabel.md** - Detail struktur database
- **04-user-flow.md** - User flow untuk semua role
- **05-struktur-folder.md** - Struktur project Laravel
- **06-roadmap.md** - Roadmap pengembangan 7 sprint
- **07-progress-log.md** - Log progress development

### Setup Guides

- **SETUP-INSTRUCTIONS.md** - Panduan setup lengkap
- **DATABASE-SETUP.md** - Panduan khusus database setup

---

## 🗂️ Struktur Database

### Core Tables (9 tables):

1. **users** - User accounts (admin, waka, guru)
2. **academic_years** - Tahun pelajaran
3. **semesters** - Semester (Ganjil & Genap)
4. **activity_types** - Master jenis kegiatan
5. **activities** - Kegiatan kalender pendidikan
6. **effective_days** - Perhitungan hari efektif
7. **activity_logs** - Audit trail sistem
8. **import_logs** - Log import Excel
9. **settings** - Konfigurasi aplikasi

---

## 🏗️ Struktur Project

```
e-KALDIK/
├── app/
│   ├── Models/              # Eloquent Models (9 models)
│   ├── Livewire/            # Livewire Components
│   ├── Http/
│   │   ├── Controllers/     # Controllers
│   │   ├── Middleware/      # Custom Middleware
│   │   └── Requests/        # Form Requests
│   └── Services/            # Business Logic Services
├── database/
│   ├── migrations/          # 12 migration files
│   └── seeders/             # Data seeders
├── resources/
│   ├── views/               # Blade templates
│   └── css/                 # Tailwind CSS
├── docs/                    # Documentation (7 files)
└── public/                  # Public assets
```

---

## 🚀 Development Progress

**Sprint 1** (Week 1-2): **60% Complete**

- ✅ Development Environment Setup (100%)
- ✅ Database Schema & Migrations (100%)
- ✅ Models & Relationships (100%)
- ✅ Seeders & Initial Data (100%)
- ⏳ Authentication System (0%)

See **docs/07-progress-log.md** for detailed progress.

---

## 🔄 Git Workflow

```bash
# Create feature branch
git checkout -b feature/nama-fitur

# Commit changes
git add .
git commit -m "feat: deskripsi fitur"

# Push to remote
git push origin feature/nama-fitur

# Create Pull Request
```

### Commit Convention

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `style:` - Code style changes
- `refactor:` - Code refactoring
- `test:` - Adding tests
- `chore:` - Maintenance tasks

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter UserTest

# Run tests with coverage
php artisan test --coverage
```

---

## 🐛 Troubleshooting

### Common Issues:

**Database connection error:**
```bash
# Check MySQL is running
# Update .env with correct credentials
php artisan config:cache
```

**Migration error:**
```bash
# Reset database
php artisan migrate:fresh --seed
```

**Assets not loading:**
```bash
# Rebuild assets
npm run build
```

See **SETUP-INSTRUCTIONS.md** for more troubleshooting tips.

---

## 📝 License

This project is licensed under the MIT License.

---

## 👥 Contributors

- **Developer** - [@YourName](https://github.com/yourname)

---

## 📞 Support

Jika ada pertanyaan atau masalah:

- 📧 Email: support@example.com
- 💬 Issues: [GitHub Issues](https://github.com/your-repo/e-kaldik/issues)

---

## 🎯 Roadmap Phase 2

Future features yang akan dikembangkan:

- [ ] Modul PKL (Praktek Kerja Lapangan)
- [ ] Modul UKK (Uji Kompetensi Keahlian)
- [ ] Modul TEFA (Teaching Factory)
- [ ] Jadwal Pelajaran
- [ ] WhatsApp Gateway / Notifikasi
- [ ] AI Features
- [ ] Supervisi KBM
- [ ] Monitoring KBM

---

<div align="center">

**Made with ❤️ for Indonesian Education**

⭐ Star us on GitHub — it motivates us a lot!

</div>

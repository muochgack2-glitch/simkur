# Analisis Navbar dan Menu Berdasarkan Role

## 📋 Ringkasan Struktur Menu Saat Ini

### 🎯 Menu Berdasarkan Role

#### 1️⃣ **ADMIN** (role: `admin`)
**Full Access - Semua Menu:**

**Dashboard:** Dashboard Admin (route: `dashboard.admin`)

**📅 Kalender Akademik:**
- Kalender Kegiatan
- Hari Efektif
- Tahun Pelajaran
- Jenis Kegiatan

**📂 Master Data:**
- 👥 Data Pengguna
- 👨‍🎓 Alumni
- 🏫 Data Kelas
- 📚 Mata Pelajaran
- 📅 Jadwal Mengajar
- 🎓 Manajemen PKL
- 🎓 Kenaikan Kelas
- 📊 Riwayat Kenaikan

**📓 Jurnal Mengajar** (direct link)

**📚 Perangkat Ajar:**
- 📖 Lihat Semua
- ⏳ Approval
- 📊 Monitoring Kelengkapan

**📝 Asesmen:**
- ⚙️ Kelola Asesmen
- 📈 Profil Belajar Siswa

**⚙️ Pengaturan:**
- 🏫 Pengaturan Umum
- ⏰ Jam Mengajar

---

#### 2️⃣ **WAKA KURIKULUM** (role: `waka_kurikulum`)
**Dashboard:** Dashboard Admin (route: `dashboard.admin`)

**📅 Kalender Akademik:**
- Kalender Kegiatan
- Hari Efektif
- Tahun Pelajaran
- Jenis Kegiatan

**📂 Master Data:**
- 📅 Jadwal Mengajar
- 🎓 Manajemen PKL
- 🎓 Kenaikan Kelas
- 📊 Riwayat Kenaikan

**📓 Jurnal Mengajar** (direct link)

**📚 Perangkat Ajar:**
- 📖 Lihat Semua
- ⏳ Approval
- 📊 Monitoring Kelengkapan

**📝 Asesmen:**
- ⚙️ Kelola Asesmen
- 📈 Profil Belajar Siswa

---

#### 3️⃣ **KEPALA SEKOLAH** (role: `kepala_sekolah`)
**Dashboard:** Dashboard Kepsek (route: `dashboard.kepsek`)

**📅 Kalender Akademik:**
- Kalender Kegiatan
- Hari Efektif
- Tahun Pelajaran
- Jenis Kegiatan

**📂 Master Data:**
- 👥 Data Pengguna
- 👨‍🎓 Alumni
- 🏫 Data Kelas
- 📚 Mata Pelajaran
- 📅 Jadwal Mengajar
- 🎓 Manajemen PKL

**📓 Jurnal Mengajar** (direct link)

**📚 Perangkat Ajar:**
- 📖 Lihat Semua
- 📊 Monitoring Kelengkapan

**📝 Asesmen:**
- 📈 Profil Belajar Siswa

---

#### 4️⃣ **GURU** (role: `guru`)
**Dashboard:** Dashboard Guru (route: `dashboard.guru`)

**📅 Kalender Akademik:**
- Kalender Kegiatan
- Hari Efektif
- Tahun Pelajaran
- Jenis Kegiatan

**📓 Jurnal Mengajar** (direct link)

**📚 Perangkat Ajar** (direct link - tanpa submenu)

**📝 Asesmen:**
- 📈 Profil Belajar Siswa

---

#### 5️⃣ **SISWA** (role: `siswa`)
**Dashboard:** Dashboard Siswa (route: `dashboard.siswa`)

**📝 Asesmen:**
- ✍️ Asesmen Saya

---

## 🔍 Detail Permission Methods (User Model)

Berikut adalah method permission yang digunakan untuk kontrol akses:

```php
// Role Checks
isAdmin()               // role === 'admin'
isWakaKurikulum()       // role === 'waka_kurikulum'
isKepalaSekolah()       // role === 'kepala_sekolah'
isGuru()                // role === 'guru'
isSiswa()               // role === 'siswa'

// Permission Checks
canManageActivities()   // admin, waka_kurikulum, kepala_sekolah
canManageUsers()        // admin, kepala_sekolah
canManageAssessments()  // admin, waka_kurikulum
canViewAllStudentProfiles() // admin, waka_kurikulum, kepala_sekolah, guru
```

---

## 📊 Tabel Perbandingan Akses Menu

| Menu Item | Admin | Waka | Kepsek | Guru | Siswa |
|-----------|-------|------|--------|------|-------|
| Dashboard | ✅ (custom) | ✅ (custom) | ✅ (custom) | ✅ (custom) | ✅ (custom) |
| Kalender Akademik | ✅ | ✅ | ✅ | ✅ | ❌ |
| Master Data - Pengguna | ✅ | ❌ | ✅ | ❌ | ❌ |
| Master Data - Alumni | ✅ | ❌ | ✅ | ❌ | ❌ |
| Master Data - Kelas | ✅ | ❌ | ✅ | ❌ | ❌ |
| Master Data - Mapel | ✅ | ❌ | ✅ | ❌ | ❌ |
| Master Data - Jadwal | ✅ | ✅ | ✅ | ❌ | ❌ |
| Master Data - PKL | ✅ | ✅ | ✅ | ❌ | ❌ |
| Master Data - Kenaikan | ✅ | ✅ | ❌ | ❌ | ❌ |
| Jurnal Mengajar | ✅ | ✅ | ✅ | ✅ | ❌ |
| Perangkat Ajar | ✅ (full) | ✅ (full) | ✅ (view+mon) | ✅ (view) | ❌ |
| Asesmen - Kelola | ✅ | ✅ | ❌ | ❌ | ❌ |
| Asesmen - Profil Siswa | ✅ | ✅ | ✅ | ✅ | ❌ |
| Asesmen - Asesmen Saya | ❌ | ❌ | ❌ | ❌ | ✅ |
| Pengaturan | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 💡 Observasi & Catatan

### ✅ Yang Sudah Baik:
1. **Separation by Role** - Setiap role punya dashboard sendiri
2. **Hierarchical Access** - Admin punya akses paling lengkap
3. **Role-Based Visibility** - Menu hanya muncul untuk role yang punya akses
4. **Mobile Responsive** - Ada mobile menu untuk layar kecil

### ⚠️ Potensi Perbaikan:
1. **Waka Kurikulum** tidak bisa akses Master Data Pengguna/Kelas/Mapel
   - Apakah perlu? Karena Waka fokus ke kurikulum
   
2. **Guru** tidak bisa lihat Jadwal Mengajar
   - Padahal mereka perlu tahu jadwal sendiri
   
3. **Kepala Sekolah** tidak bisa kelola Asesmen
   - Apakah perlu akses approval/monitoring asesmen?
   
4. **Perangkat Ajar** untuk Guru
   - Hanya view, tidak ada approval flow
   - Sudah sesuai dengan kebutuhan?

---

## 🎯 PERTANYAAN UNTUK DISKUSI:

### 1. **Struktur Menu Saat Ini**
Apakah struktur menu saat ini sudah sesuai dengan kebutuhan sekolah? Ada yang perlu ditambah/dikurangi?

### 2. **Akses Waka Kurikulum**
Apakah Waka Kurikulum perlu akses ke Master Data (Pengguna/Kelas/Mapel)?

### 3. **Akses Guru ke Jadwal**
Apakah Guru perlu menu untuk melihat jadwal mengajar sendiri?

### 4. **Kepala Sekolah & Asesmen**
Apakah Kepala Sekolah perlu bisa kelola/approve asesmen, atau cukup lihat profil siswa saja?

### 5. **Menu Baru**
Apakah ada menu baru yang perlu ditambahkan untuk role tertentu?

### 6. **Urutan Menu**
Apakah urutan menu saat ini sudah logis dan sesuai dengan workflow sehari-hari?

### 7. **Icon & Labeling**
Apakah emoji icon dan label menu sudah jelas dan mudah dipahami?

### 8. **User Dropdown Menu**
Menu dropdown user (pojok kanan atas) saat ini hanya ada:
- Ganti Password
- Logout

Apakah perlu ditambah:
- Profil Saya?
- Notifikasi?
- Bantuan/Panduan?

---

## 📝 CATATAN IMPLEMENTASI TEKNIS

### Route Protection:
- Semua route sudah dilindungi dengan middleware `auth` dan `check.role`
- Format: `->middleware('check.role:admin,kepala_sekolah')`

### Dashboard Routing:
- Root `/` dan `/dashboard` redirect otomatis ke dashboard sesuai role
- Admin & Waka → `dashboard.admin`
- Kepsek → `dashboard.kepsek`
- Guru → `dashboard.guru`
- Siswa → `dashboard.siswa`

### Conditional Menu Rendering:
Menggunakan kombinasi:
- `@if(auth()->user()->isAdmin())`
- `@if(auth()->user()->canManageUsers())`
- `@if(in_array(auth()->user()->role, ['guru', 'waka_kurikulum']))`

---

**File Terkait:**
- Layout: `resources/views/components/layouts/app.blade.php`
- User Model: `app/Models/User.php`
- Routes: `routes/web.php`
- Middleware: `app/Http/Middleware/CheckRole.php`

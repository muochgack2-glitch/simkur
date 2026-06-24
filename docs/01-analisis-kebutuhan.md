# Analisis Kebutuhan Sistem e-KALDIK

## 1. Overview Sistem

**Nama Aplikasi**: e-KALDIK (Electronic - Kalender Pendidikan)  
**Target Pengguna**: SMK (Sekolah Menengah Kejuruan)  
**Tujuan**: Mengelola Kalender Pendidikan sekolah selama satu tahun pelajaran secara digital, stabil, dan user-friendly.

## 2. Analisis Stakeholder

### 2.1 Admin
**Peran**: Mengelola sistem secara keseluruhan
**Kebutuhan**:
- Mengelola data master (tahun pelajaran, jenis kegiatan)
- Mengelola user dan role
- Akses penuh ke seluruh fitur sistem
- Monitoring aktivitas sistem

### 2.2 Waka Kurikulum
**Peran**: Mengelola kalender pendidikan dan kegiatan akademik
**Kebutuhan**:
- Membuat, mengubah, dan menghapus kegiatan kalender
- Import data kegiatan dari Excel
- Export laporan (PDF, Excel)
- Melihat dashboard dan statistik hari efektif
- Mengelola tahun pelajaran aktif

### 2.3 Guru
**Peran**: Melihat informasi kalender pendidikan
**Kebutuhan**:
- View-only access ke kalender
- Melihat agenda dan kegiatan terdekat
- Export kalender untuk keperluan pribadi
- Notifikasi kegiatan penting

## 3. Kebutuhan Fungsi (Functional Requirements)

### FR-01: Authentication & Authorization
- **FR-01.1**: Sistem harus dapat melakukan login dengan username dan password
- **FR-01.2**: Sistem harus dapat logout
- **FR-01.3**: Sistem harus dapat mengubah password
- **FR-01.4**: Sistem harus mengimplementasikan role-based access control (Admin, Waka Kurikulum, Guru)
- **FR-01.5**: Sistem harus mencatat aktivitas login (log)

### FR-02: Manajemen Tahun Pelajaran
- **FR-02.1**: Sistem dapat menambah tahun pelajaran baru (format: 2024/2025)
- **FR-02.2**: Sistem dapat mengubah tahun pelajaran
- **FR-02.3**: Sistem dapat mengaktifkan satu tahun pelajaran (hanya 1 yang aktif)
- **FR-02.4**: Sistem dapat mengarsipkan tahun pelajaran lama
- **FR-02.5**: Sistem harus mencatat tanggal mulai dan selesai tahun pelajaran

### FR-03: Manajemen Semester
- **FR-03.1**: Sistem mengelola 2 semester (Ganjil dan Genap)
- **FR-03.2**: Setiap semester memiliki periode tanggal mulai dan selesai
- **FR-03.3**: Semester terikat dengan tahun pelajaran

### FR-04: Master Jenis Kegiatan
- **FR-04.1**: Sistem menyediakan jenis kegiatan standar:
  - MPLS (Masa Pengenalan Lingkungan Sekolah)
  - PTS (Penilaian Tengah Semester)
  - PAS (Penilaian Akhir Semester)
  - PAT (Penilaian Akhir Tahun)
  - ANBK (Asesmen Nasional Berbasis Komputer)
  - Libur Nasional
  - Libur Semester
  - Rapat Guru
  - Kegiatan Sekolah
- **FR-04.2**: Admin dapat menambah jenis kegiatan custom
- **FR-04.3**: Setiap jenis kegiatan memiliki warna default
- **FR-04.4**: Setiap jenis kegiatan memiliki kategori (akademik/non-akademik)

### FR-05: Kalender Pendidikan
- **FR-05.1**: Sistem dapat menambah kegiatan dengan field:
  - Nama Kegiatan (required)
  - Jenis Kegiatan (dropdown, required)
  - Tanggal Mulai (required)
  - Tanggal Selesai (required)
  - Semester (Ganjil/Genap, required)
  - Warna (color picker, optional - default dari jenis)
  - Keterangan (textarea, optional)
- **FR-05.2**: Sistem dapat mengubah kegiatan
- **FR-05.3**: Sistem dapat menghapus kegiatan (soft delete)
- **FR-05.4**: Sistem dapat menampilkan view bulanan (calendar grid)
- **FR-05.5**: Sistem dapat menampilkan view tahunan (12 bulan)
- **FR-05.6**: Sistem dapat menampilkan daftar agenda (list view dengan filter)
- **FR-05.7**: Sistem dapat mendeteksi bentrok jadwal dan memberikan warning

### FR-06: Perhitungan Hari Efektif (Otomatis)
- **FR-06.1**: Sistem menghitung hari belajar (exclude weekend dan libur)
- **FR-06.2**: Sistem menghitung hari libur
- **FR-06.3**: Sistem menghitung hari ujian (PTS, PAS, PAT)
- **FR-06.4**: Sistem menghitung minggu efektif per semester
- **FR-06.5**: Sistem menampilkan rekapitulasi hari efektif per semester dan per tahun

### FR-07: Dashboard
- **FR-07.1**: Menampilkan tahun pelajaran aktif
- **FR-07.2**: Menampilkan jumlah kegiatan (total dan per jenis)
- **FR-07.3**: Menampilkan statistik hari efektif
- **FR-07.4**: Menampilkan agenda terdekat (7 hari ke depan)
- **FR-07.5**: Menampilkan grafik kegiatan per bulan
- **FR-07.6**: Quick action buttons untuk fitur utama

### FR-08: Import Excel
- **FR-08.1**: Sistem dapat import data kegiatan dari Excel (.xlsx, .xls)
- **FR-08.2**: Sistem menyediakan template Excel standar
- **FR-08.3**: Sistem melakukan validasi data sebelum import
- **FR-08.4**: Sistem menampilkan preview data sebelum import
- **FR-08.5**: Sistem menampilkan laporan hasil import (sukses/gagal)

### FR-09: Export PDF
- **FR-09.1**: Sistem dapat export kalender tahunan ke PDF
- **FR-09.2**: Sistem dapat export kalender bulanan ke PDF
- **FR-09.3**: Sistem dapat export daftar agenda ke PDF
- **FR-09.4**: PDF memiliki header (logo sekolah, nama sekolah, tahun pelajaran)
- **FR-09.5**: PDF dapat dikustomisasi (landscape/portrait)

### FR-10: Export Excel
- **FR-10.1**: Sistem dapat export daftar kegiatan ke Excel
- **FR-10.2**: Sistem dapat export rekapitulasi hari efektif ke Excel
- **FR-10.3**: Excel memiliki formatting yang rapi (header, border, color)

## 4. Kebutuhan Non-Fungsi (Non-Functional Requirements)

### NFR-01: Performance
- **NFR-01.1**: Response time halaman < 2 detik
- **NFR-01.2**: Sistem dapat menangani minimal 100 concurrent users
- **NFR-01.3**: Import Excel maksimal 1000 row dalam 10 detik

### NFR-02: Usability
- **NFR-02.1**: Sistem menggunakan interface yang intuitif dan user-friendly
- **NFR-02.2**: Sistem responsive (desktop, tablet, mobile)
- **NFR-02.3**: Sistem menggunakan Bahasa Indonesia
- **NFR-02.4**: Sistem menyediakan feedback yang jelas (success/error message)

### NFR-03: Reliability
- **NFR-03.1**: System availability 99% (uptime)
- **NFR-03.2**: Backup database otomatis setiap hari
- **NFR-03.3**: Error handling yang baik (tidak crash)

### NFR-04: Security
- **NFR-04.1**: Password di-hash menggunakan bcrypt
- **NFR-04.2**: Implementasi CSRF protection
- **NFR-04.3**: Validasi input untuk mencegah SQL Injection dan XSS
- **NFR-04.4**: Session timeout setelah 2 jam idle

### NFR-05: Maintainability
- **NFR-05.1**: Kode mengikuti PSR-12 coding standard
- **NFR-05.2**: Database schema tidak berubah di fase berikutnya
- **NFR-05.3**: Dokumentasi code lengkap
- **NFR-05.4**: Modular structure untuk kemudahan pengembangan fase 2

## 5. Business Rules

### BR-01: Tahun Pelajaran
- Hanya boleh ada 1 tahun pelajaran aktif
- Format tahun: YYYY/YYYY (contoh: 2024/2025)
- Tahun pelajaran dimulai bulan Juli dan berakhir bulan Juni tahun berikutnya

### BR-02: Semester
- Semester Ganjil: Juli - Desember
- Semester Genap: Januari - Juni
- Setiap kegiatan harus terikat ke salah satu semester

### BR-03: Kegiatan
- Tanggal selesai harus >= tanggal mulai
- Kegiatan ujian (PTS, PAS, PAT) dihitung sebagai hari ujian, bukan hari belajar
- Kegiatan libur tidak dihitung sebagai hari efektif

### BR-04: Hari Efektif
- Sabtu dan Minggu default tidak dihitung (kecuali ada kegiatan khusus)
- Hari libur nasional tidak dihitung
- 1 minggu efektif = minimal 5 hari belajar

### BR-05: Access Control
- Admin: Full access
- Waka Kurikulum: Create, Read, Update, Delete (CRUD) kalender dan master data
- Guru: Read only

## 6. Constraints & Assumptions

### Constraints
- Menggunakan Laravel 12 (latest stable)
- Menggunakan Livewire 4 untuk interactivity
- Menggunakan MySQL 8.0+
- Tidak ada integrasi dengan sistem eksternal di Phase 1

### Assumptions
- User sudah memiliki akun (dibuat oleh admin)
- Data sekolah (nama, logo) sudah dikonfigurasi
- Server memiliki PHP 8.2+ dan ekstensi yang diperlukan
- Internet connection tersedia untuk CDN (Tailwind, FullCalendar)

## 7. Out of Scope (Phase 1)
- Modul PKL (Praktek Kerja Lapangan)
- Modul UKK (Uji Kompetensi Keahlian)
- Modul TEFA (Teaching Factory)
- Jadwal Pelajaran
- WhatsApp Gateway / Notifikasi
- AI Features
- Supervisi KBM
- Monitoring KBM
- Mobile Apps
- Multi-sekolah (Multi-tenancy)

## 8. Success Criteria

Sistem dianggap berhasil jika:
1. ✅ Semua fitur Phase 1 berjalan tanpa bug critical
2. ✅ Waka Kurikulum dapat membuat kalender pendidikan dalam < 30 menit
3. ✅ Guru dapat dengan mudah melihat jadwal kegiatan
4. ✅ Perhitungan hari efektif akurat 100%
5. ✅ Import/Export berjalan lancar tanpa error
6. ✅ Sistem stabil untuk 1 tahun pelajaran penuh
7. ✅ Database schema mendukung pengembangan Phase 2 tanpa perubahan struktur mayor

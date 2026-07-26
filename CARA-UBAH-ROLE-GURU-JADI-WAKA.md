# Cara Mengubah Role Guru Menjadi Waka Kurikulum

## ✅ FITUR BARU: Edit Role User

Admin sekarang dapat mengubah role user langsung dari halaman Edit User.

---

## 📋 LANGKAH-LANGKAH

### 1. Login sebagai Admin
Login ke sistem dengan akun Admin

### 2. Buka Menu Data Pengguna
- Klik menu **Master Data** (📂)
- Pilih **Data Pengguna** (👥)

### 3. Cari Guru yang Akan Dijadikan Waka
- Gunakan search atau scroll untuk menemukan guru
- Klik tombol **Edit** (✏️) di baris guru tersebut

### 4. Ubah Role
Di halaman Edit User:
- **Dropdown Role** sekarang bisa diubah (hanya untuk Admin)
- Pilih **👔 Waka Kurikulum**
- Field yang relevan akan tetap muncul:
  - NIP/NUPTK
  - Beban Mengajar
  - Jurusan yang Diampu
  - Mata Pelajaran yang Diampu

### 5. Simpan
- Klik tombol **Simpan**
- Sistem akan menampilkan pesan: "User berhasil diperbarui! Role telah diubah."
- Perubahan role akan tercatat di Activity Log

---

## 🔐 HAK AKSES

### Admin
- ✅ Dapat mengubah role ke: Admin, Kepala Sekolah, Waka Kurikulum, Guru, Siswa
- ✅ Dapat melihat dan mengubah semua field

### Kepala Sekolah
- ✅ Dapat mengubah role ke: Waka Kurikulum, Guru, Siswa
- ❌ Tidak dapat mengubah role ke: Admin, Kepala Sekolah

### User Lain
- ❌ Tidak dapat melihat menu Edit User

---

## ⚠️ PENTING

### 1. **Tidak Bisa Edit Akun Sendiri**
Admin tidak dapat mengubah role akun sendiri untuk keamanan. Pesan error:
> "Anda tidak dapat mengedit akun Anda sendiri. Gunakan menu Profile."

### 2. **Data Guru Tetap Tersimpan**
Ketika mengubah guru menjadi Waka Kurikulum:
- ✅ NIP/NUPTK tetap tersimpan
- ✅ Beban mengajar tetap tersimpan
- ✅ Jurusan yang diampu tetap tersimpan
- ✅ Mata pelajaran yang diampu tetap tersimpan

Waka Kurikulum masih bisa mengajar dan memiliki data seperti guru.

### 3. **Log Perubahan**
Setiap perubahan role akan tercatat di Activity Log dengan format:
> "Mengubah data user: [Nama User] (Role diubah dari guru ke waka_kurikulum)"

---

## 🎯 CONTOH PENGGUNAAN

### Skenario 1: Guru Dipromosikan Jadi Waka
**Sebelum:**
- Role: 👨‍🏫 Guru
- NIP: 198012345678901234
- Mengajar: Matematika, Fisika

**Setelah diubah ke Waka Kurikulum:**
- Role: 👔 Waka Kurikulum
- NIP: 198012345678901234 (tetap)
- Mengajar: Matematika, Fisika (tetap)
- Akses tambahan:
  - ✅ Kenaikan Kelas
  - ✅ Approval Perangkat Ajar
  - ✅ Manajemen Assessment
  - ✅ Monitoring Jurnal Semua Guru

### Skenario 2: Waka Kembali Jadi Guru
**Sebelum:**
- Role: 👔 Waka Kurikulum

**Setelah diubah ke Guru:**
- Role: 👨‍🏫 Guru
- Kehilangan akses:
  - ❌ Kenaikan Kelas
  - ❌ Approval Perangkat Ajar
  - ❌ Manajemen Assessment
- Masih punya akses:
  - ✅ Jurnal Mengajar sendiri
  - ✅ Perangkat Ajar sendiri

---

## 📱 TAMPILAN DROPDOWN ROLE

### Untuk Admin:
```
Pilih Role *
┌─────────────────────────┐
│ ⚙️ Admin                │
│ 🎓 Kepala Sekolah        │
│ 👔 Waka Kurikulum        │
│ 👨‍🏫 Guru                 │
│ 👨‍🎓 Siswa                │
└─────────────────────────┘
⚠️ Hati-hati saat mengubah role. 
Pastikan user memiliki akses yang sesuai.
```

### Untuk Kepala Sekolah:
```
Pilih Role *
┌─────────────────────────┐
│ 👔 Waka Kurikulum        │
│ 👨‍🏫 Guru                 │
│ 👨‍🎓 Siswa                │
└─────────────────────────┘
Anda dapat mengubah role ke 
Waka Kurikulum, Guru, atau Siswa
```

---

## 🔧 TROUBLESHOOTING

### Role dropdown tidak muncul?
- Pastikan Anda login sebagai **Admin** atau **Kepala Sekolah**
- Guru/Siswa/Waka tidak bisa melihat dropdown role

### Error "Anda tidak dapat mengedit akun Anda sendiri"?
- Ini normal dan untuk keamanan
- Minta Admin lain untuk mengubah role Anda
- Atau gunakan menu Profile untuk ubah data lain

### Setelah ubah role, user tidak bisa login?
- Logout dan login kembali
- Clear browser cache
- Role akan langsung aktif setelah disimpan

---

## ✅ KESIMPULAN

Sekarang Admin dapat dengan mudah:
1. ✅ Mengubah role Guru menjadi Waka Kurikulum
2. ✅ Mengubah role Waka Kurikulum kembali jadi Guru
3. ✅ Data guru (NIP, mata pelajaran) tetap tersimpan
4. ✅ Perubahan tercatat di Activity Log
5. ✅ Prosesnya aman dengan validasi yang ketat

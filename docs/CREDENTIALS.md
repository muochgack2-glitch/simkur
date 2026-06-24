# 🔑 Default Credentials - e-KALDIK

## Login Credentials

Setelah menjalankan `php artisan db:seed`, gunakan credentials berikut untuk login:

---

### 👤 Admin

**Username:** `admin`  
**Password:** `password`  
**Email:** admin@smk.sch.id  
**Role:** Administrator  

**Akses:**
- Full access ke semua fitur
- Manage users
- Manage settings
- Manage tahun pelajaran
- Manage master data
- Create/Edit/Delete kegiatan
- Import/Export data

---

### 👤 Waka Kurikulum

**Username:** `waka`  
**Password:** `password`  
**Email:** waka@smk.sch.id  
**Role:** Waka Kurikulum  

**Akses:**
- Manage tahun pelajaran
- Manage master data
- Create/Edit/Delete kegiatan
- Import/Export data
- View dashboard
- Manage hari efektif

---

### 👤 Guru 1

**Username:** `guru1`  
**Password:** `password`  
**Email:** siti@smk.sch.id  
**Role:** Guru  
**Nama:** Siti Nurhaliza  

**Akses:**
- View-only access
- Lihat kalender
- Lihat dashboard
- Export untuk pribadi

---

### 👤 Guru 2

**Username:** `guru2`  
**Password:** `password`  
**Email:** ahmad@smk.sch.id  
**Role:** Guru  
**Nama:** Ahmad Hidayat  

**Akses:**
- View-only access
- Lihat kalender
- Lihat dashboard
- Export untuk pribadi

---

## 🔒 Security Recommendations

### ⚠️ PENTING - Lakukan Setelah Setup:

1. **Ganti Semua Password Default**
   - Login dengan setiap akun
   - Navigate ke Profile > Change Password
   - Gunakan password yang kuat

2. **Update Admin Email**
   - Ganti email admin dari default ke email sebenarnya
   - Untuk recovery password

3. **Disable Default Users (Optional)**
   - Jika tidak digunakan, disable akun guru1 dan guru2
   - Atau hapus dan buat user baru sesuai kebutuhan

4. **Production Security**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

---

## 🔑 Password Policy (Recommended)

Untuk keamanan, gunakan password yang memenuhi kriteria:

- Minimal 8 karakter
- Kombinasi huruf besar dan kecil
- Mengandung angka
- Mengandung karakter khusus (@, #, $, !, dll)

**Contoh password yang kuat:**
- `Smk2024!@#`
- `WakaKur!2024`
- `Guru$ecure1`

---

## 📋 Testing Accounts

Untuk development dan testing, Anda bisa menggunakan default credentials di atas.

**Untuk Production:**
1. Buat user baru dengan data sebenarnya
2. Hapus atau disable default users
3. Ganti semua default passwords

---

## 🔄 Reset Password

Jika lupa password:

1. **Via Artisan Tinker:**
   ```bash
   php artisan tinker
   ```
   
   ```php
   $user = \App\Models\User::where('username', 'admin')->first();
   $user->password = Hash::make('newpassword');
   $user->save();
   exit
   ```

2. **Via Seeder:**
   ```bash
   # Reset semua ke default
   php artisan db:seed --class=UserSeeder --force
   ```

---

## 👥 User Management

### Menambah User Baru (via Tinker):

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Nama Lengkap',
    'username' => 'username',
    'email' => 'email@smk.sch.id',
    'password' => Hash::make('password'),
    'role' => 'guru', // admin, waka_kurikulum, guru
    'is_active' => true,
]);
exit
```

### Mengubah Role User:

```php
$user = \App\Models\User::where('username', 'guru1')->first();
$user->role = 'waka_kurikulum';
$user->save();
```

### Menonaktifkan User:

```php
$user = \App\Models\User::where('username', 'guru1')->first();
$user->is_active = false;
$user->save();
```

---

## 📊 Database Status Check

Untuk memastikan users sudah di-seed:

```bash
php artisan tinker
```

```php
// Check total users
\App\Models\User::count(); // Should return 4

// List all users
\App\Models\User::select('username', 'role', 'is_active')->get();

// Check admin exists
\App\Models\User::where('role', 'admin')->exists(); // Should return true
```

---

## ⚡ Quick Login Test

After setup, test login dengan:

1. Browse to: http://localhost:8000
2. Try login dengan `admin` / `password`
3. Verify dashboard loads
4. Logout
5. Try login dengan role lainnya

---

## 🛡️ Security Checklist

- [ ] All default passwords changed
- [ ] Admin email updated
- [ ] Unused default users disabled
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] Strong passwords enforced
- [ ] Regular password rotation policy
- [ ] Session timeout configured (120 minutes default)

---

**Remember: Security is everyone's responsibility! 🔒**

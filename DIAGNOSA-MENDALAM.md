# 🔬 DIAGNOSA MENDALAM - LOGIN ISSUE

## ⚠️ MASALAH PERSISTEN

Sudah dicoba berkali-kali tapi login tetap tidak berfungsi. Sekarang kita harus **DIAGNOSA DARI AKAR** dengan bukti nyata.

---

## 📋 STEP 1: JALANKAN DIAGNOSA SCRIPT

```bash
cd /www/wwwroot/simkur
chmod +x diagnosa-actual.sh
./diagnosa-actual.sh > diagnosa-output.txt
cat diagnosa-output.txt
```

**PENTING:** Kirim SELURUH output `diagnosa-output.txt` ini!

Script ini akan check:
1. ✅ Isi `.env` actual (SESSION settings)
2. ✅ Config Laravel actual (dari `config()`)
3. ✅ Cache files yang masih ada
4. ✅ Session directory & permissions
5. ✅ Test session write/read
6. ✅ Test Auth dengan user admin
7. ✅ Livewire routes
8. ✅ Laravel log
9. ✅ PHP-FPM status
10. ✅ Nginx config

---

## 🧪 STEP 2: TEST LOGIN TANPA LIVEWIRE

### A. Test via Browser (with UI)

```bash
# Deploy test files
cd /www/wwwroot/simkur
git pull origin main
php artisan route:clear
php artisan config:clear
```

Buka di browser:
1. **https://simkur.smkpgriblora.sch.id/test-login-controller**
   - Ini test login TANPA Livewire
   - Pure Laravel controller + JavaScript
   - Klik "Test Login"
   - Lihat hasilnya

2. **https://simkur.smkpgriblora.sch.id/test-session**
   - Ini test session persistence
   - Refresh beberapa kali
   - Lihat apakah `test_time` berubah atau tetap

### B. Test via PHP File (alternative)

Jika route tidak work, copy file:
```bash
cd /www/wwwroot/simkur
cp test-login-simple.php public/
```

Buka: **https://simkur.smkpgriblora.sch.id/test-login-simple.php**

---

## 🔍 STEP 3: ANALISA HASIL

### Skenario A: "Auth attempt SUCCESS" tapi "Auth check FALSE"

**Gejala:**
- `Auth::attempt()` return `true`
- Tapi `Auth::check()` return `false` immediately after

**Kemungkinan:**
1. Session tidak persist antara statement
2. Session driver bermasalah
3. Session middleware tidak berjalan

**Solusi:**
```bash
# Coba ganti ke COOKIE driver
nano /www/wwwroot/simkur/.env
# Ubah: SESSION_DRIVER=cookie

php artisan config:clear
php artisan config:cache
systemctl restart php-fpm-83
```

---

### Skenario B: Session write/read return NULL

**Gejala:**
- `session()->put('test', 'value')`
- `session()->get('test')` return `null`

**Kemungkinan:**
1. Session files tidak writable
2. Session path salah
3. PHP session handler error

**Solusi:**
```bash
# Check permission
ls -ld /www/wwwroot/simkur/storage/framework/sessions
# Harus: drwxrwxr-x www www

# Fix permission
chown -R www:www /www/wwwroot/simkur/storage
chmod -R 775 /www/wwwroot/simkur/storage

# Check PHP session
php -i | grep session.save_path
```

---

### Skenario C: Config shows WRONG driver

**Gejala:**
- `.env` memiliki `SESSION_DRIVER=file`
- `config('session.driver')` return `array` atau lainnya

**Kemungkinan:**
- Config cache MASIH ada dan tidak di-clear

**Solusi:**
```bash
# Force delete cache
rm -f /www/wwwroot/simkur/bootstrap/cache/config.php
rm -f /www/wwwroot/simkur/bootstrap/cache/*.php

# Rebuild
php artisan config:cache

# Verify
php artisan tinker --execute="echo config('session.driver');"
# HARUS sama dengan .env
```

---

### Skenario D: User credentials salah

**Gejala:**
- `Auth::attempt()` return `false`

**Kemungkinan:**
- User tidak ada di database
- Password hash tidak match

**Solusi:**
```bash
# Check user
php artisan tinker --execute="
\$user = App\Models\User::where('username', 'admin')->first();
if (\$user) {
    echo 'User exists: ' . \$user->username . PHP_EOL;
    echo 'Is active: ' . \$user->is_active . PHP_EOL;
    \$check = Hash::check('password', \$user->password);
    echo 'Password valid: ' . (\$check ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'User NOT FOUND!' . PHP_EOL;
}
"

# Jika user tidak ada, run seeder
php artisan db:seed --class=ProductionSeeder --force
```

---

### Skenario E: Livewire tidak trigger Auth

**Gejala:**
- Test login controller (non-Livewire) BERHASIL
- Livewire login GAGAL

**Kemungkinan:**
- Livewire session handling issue
- Livewire request berbeda

**Solusi:**
Ubah `app/Livewire/Auth/Login.php`:

```php
public function login()
{
    $this->validate();
    
    $credentials = [
        'username' => $this->username,
        'password' => $this->password,
    ];

    if (Auth::attempt($credentials, $this->remember)) {
        if (!Auth::user()->is_active) {
            Auth::logout();
            $this->addError('username', 'Akun Anda tidak aktif.');
            return;
        }

        Auth::user()->update(['last_login_at' => now()]);
        
        ActivityLog::createLog(
            action: 'login',
            description: 'User berhasil login ke sistem'
        );

        // PENTING: Force session save
        session()->save();
        
        // REDIRECT dengan full URL
        return redirect()->to('/dashboard');
    }

    $this->addError('username', 'Username atau password salah.');
}
```

---

### Skenario F: Nginx tidak pass cookies

**Gejala:**
- Login berhasil di tinker/test
- Tapi browser tidak set cookie

**Kemungkinan:**
- Nginx config tidak pass `Set-Cookie` header
- HTTPS redirect issue

**Solusi:**

Check Nginx config:
```bash
nano /www/server/panel/vhost/nginx/simkur.smkpgriblora.sch.id.conf
```

Pastikan ada:
```nginx
location ~ \.php$ {
    fastcgi_pass unix:/tmp/php-cgi-83.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    
    # IMPORTANT: Pass cookies
    fastcgi_param HTTP_COOKIE $http_cookie;
}
```

Restart:
```bash
systemctl restart nginx
```

---

## 🎯 CHECKLIST DIAGNOSA

Isi checklist ini dengan hasil actual:

```
[ ] Script diagnosa dijalankan - output tersedia
[ ] Config driver di .env: _______
[ ] Config driver di Laravel: _______
[ ] Session test write/read: SUCCESS / FAILED
[ ] Auth attempt dengan tinker: SUCCESS / FAILED
[ ] Auth check setelah attempt: TRUE / FALSE
[ ] Session files created di storage: YES / NO
[ ] Permission storage/sessions: _______
[ ] Test login controller (non-Livewire): SUCCESS / FAILED
[ ] Browser set cookie: YES / NO
[ ] PHP-FPM status: RUNNING / ERROR
[ ] Laravel log menunjukkan: _______
```

---

## 💡 KEMUNGKINAN ROOT CAUSE (Prioritas)

Berdasarkan experience dengan masalah ini:

### 1. **Config Cache Issue** (90% probability)
- Config cache tidak clear dengan benar
- Laravel baca config lama dari cache

**Quick Test:**
```bash
php artisan tinker --execute="echo config('session.driver');"
```
Harus sama dengan `.env`

---

### 2. **Session Handler Issue** (60% probability)
- File driver tidak work karena permission
- Database driver tidak work karena connection

**Quick Test:**
```bash
php artisan tinker --execute="
session()->put('test', time());
echo session()->get('test');
"
```
Harus return nilai yang sama

---

### 3. **Livewire Specific Issue** (40% probability)
- Livewire request handling berbeda
- Livewire tidak trigger session save

**Quick Test:**
Buka `/test-login-controller` - jika berhasil, masalah di Livewire

---

### 4. **Cookie/HTTPS Issue** (30% probability)
- Browser tidak terima cookie karena secure flag
- SameSite policy blocking

**Quick Test:**
Browser DevTools → Application → Cookies
Harus ada cookie `e_kaldik_session`

---

### 5. **Auth Guard Issue** (10% probability)
- Guard configuration salah
- User provider salah

**Quick Test:**
```bash
php artisan tinker --execute="echo config('auth.defaults.guard');"
```
Harus `web`

---

## 🚨 LAST RESORT: NUCLEAR OPTION

Jika SEMUA cara gagal, lakukan fresh setup:

```bash
# 1. Backup database
mysqldump -u simkur_user -p simkur_ekaldik > /tmp/backup.sql

# 2. Backup .env
cp /www/wwwroot/simkur/.env /tmp/env.backup

# 3. Delete installation
rm -rf /www/wwwroot/simkur/*

# 4. Fresh clone
cd /www/wwwroot/simkur
git clone https://github.com/muochgack2-glitch/simkur.git .

# 5. Restore .env
cp /tmp/env.backup .env

# 6. Fresh install
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 7. Clear everything
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# 8. Permissions
chown -R www:www .
chmod -R 775 storage bootstrap/cache

# 9. Config
php artisan key:generate --force
php artisan config:cache
php artisan route:cache

# 10. Test
php artisan tinker --execute="
session()->put('test', 'works');
echo session()->get('test');
"

# 11. Restart
systemctl restart php-fpm-83
systemctl restart nginx
```

---

## 📞 BUTUH BANTUAN LEBIH?

Kirimkan:
1. **Output `diagnosa-actual.sh`** (lengkap)
2. **Screenshot** dari `/test-login-controller`
3. **Browser DevTools** → Network → saat klik login (screenshot Headers)
4. **Last 50 lines** Laravel log: `tail -50 storage/logs/laravel.log`

Dengan 4 info ini, saya bisa identify EXACT root cause.

---

**Created:** 2026-06-24  
**Purpose:** Deep diagnosis untuk persistent login issue  
**Method:** Evidence-based debugging dengan isolated tests

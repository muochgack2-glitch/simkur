# Deployment Instructions - Livewire $commit Fix

## Masalah yang Diperbaiki
Error `MethodNotFoundException` untuk method `$commit` yang muncul ketika menggunakan `wire:model.live` di semua halaman (filter kelas, datepicker, dropdown, dll).

## Solusi
Dibuat `LivewireCommitFixServiceProvider` yang menggunakan Livewire hook untuk mengintercept panggilan method `$commit` sebelum Livewire melakukan pengecekan method existence.

## Cara Deploy ke Production

### 1. Login ke Server
```bash
ssh root@simkur.smkpgriblora.sch.id
# atau login via aaPanel
```

### 2. Masuk ke Direktori Aplikasi
```bash
cd /www/wwwroot/simkur
```

### 3. Backup Database (Opsional tapi Disarankan)
```bash
php artisan db:backup
# atau manual via aaPanel
```

### 4. Pull Update dari Git
```bash
git pull origin main
```

### 5. Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 6. Restart PHP-FPM (Penting!)
Via aaPanel:
- Masuk ke aaPanel
- Klik "App Store" atau "软件商店"
- Cari "PHP 8.3"
- Klik "Settings"
- Klik "Service" tab
- Klik tombol "Restart"

Via Command Line:
```bash
systemctl restart php-fpm-83
# atau
service php-fpm-83 restart
```

### 7. Test Aplikasi
1. Buka https://simkur.smkpgriblora.sch.id
2. Login
3. Test fitur-fitur yang sebelumnya error:
   - Filter kelas di halaman apapun
   - Datepicker di jurnal mengajar
   - Dropdown selection
   - Navigation kalender
   - Search user

## Files yang Diubah
- `app/Providers/LivewireCommitFixServiceProvider.php` (NEW)
- `app/Livewire/BaseComponent.php` (NEW)
- `bootstrap/providers.php` (UPDATED)

## Troubleshooting

### Jika masih error setelah deploy:

1. **Cek apakah file sudah ter-pull:**
   ```bash
   ls -lah app/Providers/LivewireCommitFixServiceProvider.php
   ls -lah app/Livewire/BaseComponent.php
   ```

2. **Cek apakah service provider terdaftar:**
   ```bash
   php artisan about | grep -i provider
   ```

3. **Clear OPcache (jika pakai OPcache):**
   ```bash
   php artisan config:clear
   # Atau via PHP code:
   php -r "opcache_reset();"
   # Atau restart PHP-FPM (cara paling aman)
   ```

4. **Cek log error:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

5. **Pastikan permissions correct:**
   ```bash
   chown -R www:www /www/wwwroot/simkur
   chmod -R 755 /www/wwwroot/simkur
   chmod -R 775 /www/wwwroot/simkur/storage
   chmod -R 775 /www/wwwroot/simkur/bootstrap/cache
   ```

### Jika masih tetap error:

1. **Test di mode maintenance:**
   ```bash
   php artisan down
   # Deploy steps...
   php artisan up
   ```

2. **Rebuild autoload:**
   ```bash
   composer dump-autoload --optimize
   ```

3. **Check Livewire version:**
   ```bash
   composer show livewire/livewire
   ```
   Should be: `v3.4.x` or `v3.5.x` (not 3.8.x or 4.x)

## Rollback (jika diperlukan)

Jika ada masalah setelah deploy:

```bash
cd /www/wwwroot/simkur
git reset --hard HEAD~1
php artisan config:clear
php artisan cache:clear
php artisan view:clear
systemctl restart php-fpm-83
```

## Penjelasan Teknis

### Kenapa error ini terjadi?
- Livewire JavaScript (semua versi 3.x) mengirim request method `$commit` ke backend
- PHP component tidak memiliki method `$commit`
- Livewire melakukan pengecekan method existence dan throw `MethodNotFoundException`

### Kenapa __call() tidak bekerja?
- Livewire menggunakan `Utils::getPublicMethodsDefinedBySubClass()` untuk mendapatkan daftar method
- Magic method `__call()` tidak termasuk dalam daftar tersebut
- Exception di-throw SEBELUM `__call()` bisa dieksekusi

### Bagaimana solusi ini bekerja?
- Livewire memiliki hook system yang trigger sebelum method check
- Hook `'call'` di-trigger dengan parameter `$returnEarly` callback
- Service provider kita listen hook ini dan call `$returnEarly(null)` untuk method `$commit`
- Ini membuat Livewire skip method check dan return null langsung

### Kenapa tidak modify vendor code?
- Modifying vendor code akan hilang ketika `composer update`
- Service provider approach lebih maintainable
- Bisa di-version control
- Tidak melanggar best practices

## Contact
Jika ada masalah saat deploy, hubungi developer atau cek dokumentasi Livewire:
https://livewire.laravel.com/docs/hooks

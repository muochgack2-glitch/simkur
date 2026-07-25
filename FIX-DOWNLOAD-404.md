# Fix 404 Download Issue - Teaching Materials

## 🔍 Diagnosis

Error 404 saat download file teaching material bisa disebabkan oleh:

1. **Storage link tidak ada** - Symlink `public/storage` tidak terbuat
2. **File tidak ada di storage** - File belum ter-upload dengan benar
3. **Permission issue** - PHP tidak bisa akses file
4. **Route tidak aktif** - Cache route bermasalah

---

## 🛠️ Troubleshooting Steps

### Step 1: Run Diagnostic Script

```bash
cd /www/wwwroot/simkur
php debug-download.php
```

Script ini akan mengecek:
- Apakah ada teaching materials di database
- Apakah file-nya ada di storage
- Status storage symlink
- Permissions directory

### Step 2: Fix Storage Link

Jika symlink tidak ada atau broken:

```bash
# Remove old link (if exists)
rm -f public/storage

# Create new symlink
php artisan storage:link

# Verify
ls -la public/storage
```

### Step 3: Fix Permissions

```bash
# Set correct permissions
chmod -R 775 storage/
chmod -R 775 storage/app/public/
chmod -R 775 bootstrap/cache/

# Set ownership (adjust www-data to your web user)
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### Step 4: Clear All Cache

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Step 5: Test Upload

Jika masalah persist, test upload file baru:

1. Login sebagai guru
2. Buat perangkat ajar baru
3. Upload file kecil (< 1MB)
4. Cek apakah file masuk ke `storage/app/public/teaching-materials/`

```bash
# Check uploaded files
ls -lah storage/app/public/teaching-materials/
```

---

## 🎯 Quick Fix (All-in-One)

```bash
cd /www/wwwroot/simkur

# 1. Recreate storage link
rm -f public/storage
php artisan storage:link

# 2. Fix permissions
chmod -R 775 storage/
chown -R www-data:www-data storage/

# 3. Clear cache
php artisan optimize:clear

# 4. Restart PHP-FPM
systemctl restart php8.2-fpm  # Adjust version if needed

# 5. Test
php debug-download.php
```

---

## 📝 Common Issues & Solutions

### Issue 1: "File not found in storage"
**Cause:** File path di database salah atau file hilang

**Solution:**
```bash
# Check database records
php artisan tinker
>>> $m = App\Models\TeachingMaterial::first();
>>> $m->file_path;
>>> Storage::exists($m->file_path);
```

### Issue 2: "Symlink not working"
**Cause:** Server tidak support symlink atau permission denied

**Solution:**
```bash
# Check if symlink supported
ln -s storage/app/public public/storage

# Or use .htaccess rewrite (alternative)
# Add to public/.htaccess:
# RewriteRule ^storage/(.*)$ ../storage/app/public/$1 [L]
```

### Issue 3: "403 Forbidden"
**Cause:** Access control atau authentication issue

**Solution:**
- Check `canAccessMaterial()` method di User model
- Verify user logged in dan punya akses

### Issue 4: "Route not found"
**Cause:** Route cache outdated

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

---

## 🔗 Testing Downloads

After fixes, test these URLs:

1. **Main file download:**
   ```
   https://simkur.smkpgriblora.sch.id/teaching-materials/{id}/download
   ```

2. **Attachment download:**
   ```
   https://simkur.smkpgriblora.sch.id/teaching-materials/{materialId}/attachments/{attachmentId}/download
   ```

3. **Preview:**
   ```
   https://simkur.smkpgriblora.sch.id/teaching-materials/preview?path={base64_path}
   ```

---

## 📞 Need Help?

Jika masih error setelah semua step:

1. Share output dari `php debug-download.php`
2. Share error dari `storage/logs/laravel.log`
3. Share browser console error (F12 → Console)

---

**Last Updated:** 2026-07-25

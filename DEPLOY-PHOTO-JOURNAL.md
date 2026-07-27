# Deploy: Foto Kegiatan Jurnal Mengajar

## 📋 Ringkasan Fitur
Menambahkan fitur upload foto kegiatan pembelajaran pada Jurnal Mengajar dengan fitur:
- Upload foto dari kamera atau galeri
- Auto-compress & resize foto (max 1024x1024px, ~500KB)
- Optional (tidak wajib)
- Access control: Waka Kurikulum, Admin, Kepala Sekolah, Guru Pemilik
- Bisa hapus foto tanpa menghapus jurnal
- Photo viewer dengan lightbox modal

## 🚀 Deployment Steps

### 1. Pull dari GitHub
```bash
cd /www/wwwroot/simkur.smkpgriblora.sch.id
git pull origin main
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

> **Note**: Package `intervention/image` sudah diinstall di local, akan otomatis terupdate saat `composer install`

### 3. Run Migration
```bash
php artisan migrate --force
```

Migration yang akan dijalankan:
- `2026_07_26_132738_add_activity_photo_to_teaching_journals_table`
  - Menambah kolom `activity_photo` (nullable) ke tabel `teaching_journals`

### 4. Setup Storage Link (PENTING!)
```bash
php artisan storage:link
```

Ini akan membuat symlink dari `public/storage` ke `storage/app/public` agar foto bisa diakses publik.

**Verifikasi:**
```bash
ls -la public/ | grep storage
```
Harus muncul: `storage -> ../storage/app/public`

### 5. Set Permissions (Linux)
```bash
chmod -R 775 storage
chown -R www:www storage
chmod -R 775 public/storage
chown -R www:www public/storage
```

### 6. Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 📁 File Changes

### Modified Files:
1. **app/Livewire/TeachingJournal/Create.php**
   - Added `WithFileUploads` trait
   - Added `$activity_photo` property
   - Added `deletePhoto()` method
   - Added `processPhotoUpload()` method with compression
   - Updated `save()` validation & logic

2. **app/Livewire/TeachingJournal/Edit.php**
   - Added `WithFileUploads` trait
   - Added `$activity_photo` and `$existing_photo` properties
   - Added `deletePhoto()` method
   - Added `processPhotoUpload()` method
   - Updated `update()` validation & logic

3. **app/Livewire/TeachingJournal/Index.php**
   - Added `$showPhotoModal`, `$currentPhotoUrl`, `$currentPhotoJournal` properties
   - Added `viewPhoto()` method with access control
   - Added `closePhotoModal()` method
   - Added `deletePhotoFromModal()` method

4. **resources/views/livewire/teaching-journal/create.blade.php**
   - Added photo upload section with camera/gallery button
   - Added photo preview
   - Added delete button

5. **resources/views/livewire/teaching-journal/edit.blade.php**
   - Added photo upload section
   - Added existing photo display
   - Added replace photo functionality

6. **resources/views/livewire/teaching-journal/index.blade.php**
   - Added "Foto" column in table
   - Added photo icon with access control
   - Added lightbox modal for photo viewing
   - Added download & delete photo from modal

7. **app/Models/TeachingJournal.php**
   - Already updated (previous task)

### New Migration:
- `database/migrations/2026_07_26_132738_add_activity_photo_to_teaching_journals_table.php`

## 🔒 Access Control

Photo dapat dilihat oleh:
- ✅ Admin
- ✅ Waka Kurikulum
- ✅ Kepala Sekolah
- ✅ Guru pemilik jurnal

Photo dapat dihapus oleh:
- ✅ Admin
- ✅ Waka Kurikulum
- ✅ Kepala Sekolah
- ✅ Guru pemilik jurnal

Guru lain **TIDAK BISA** melihat foto jurnal guru lain.

## 📸 Storage Structure

Foto disimpan di:
```
storage/app/public/journal-photos/YYYY/MM/user_{id}_{timestamp}_{hash}.jpg
```

Contoh:
```
storage/app/public/journal-photos/2026/07/user_5_1722001234_abc123def.jpg
```

Public URL:
```
https://simkur.smkpgriblora.sch.id/storage/journal-photos/2026/07/user_5_1722001234_abc123def.jpg
```

## 🎯 Image Processing

- **Format input**: JPG, JPEG, PNG, WEBP
- **Max upload**: 2MB
- **Auto resize**: Max 1024x1024px (maintain aspect ratio)
- **Auto compress**: JPEG quality 75% (~500KB target)
- **Format output**: Always JPEG

## ✅ Testing Checklist

### Local Testing (DONE ✓)
- [x] Migration created
- [x] Migration run successfully
- [x] Storage link created
- [x] No PHP errors in components
- [x] Code follows Laravel/Livewire best practices

### Production Testing (TODO)
- [ ] Migration runs successfully
- [ ] Storage link created properly
- [ ] Upload foto dari mobile (camera)
- [ ] Upload foto dari desktop (gallery)
- [ ] Preview foto before save
- [ ] Delete foto before save
- [ ] Save jurnal dengan foto
- [ ] View foto di list (role check)
- [ ] View foto di modal (lightbox)
- [ ] Download foto
- [ ] Delete foto dari modal
- [ ] Edit jurnal - keep existing photo
- [ ] Edit jurnal - replace photo
- [ ] Edit jurnal - delete photo
- [ ] Permission check (guru lain tidak bisa lihat)

## 🐛 Troubleshooting

### Issue: Foto tidak muncul (404)
**Solution:**
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Issue: Upload error "The file is too large"
**Check php.ini:**
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
```

Restart PHP-FPM:
```bash
systemctl restart php-fpm
```

**Note**: Validation set to max 10MB (10240 KB) to accommodate modern smartphone cameras.

### Issue: Permission denied saat upload
**Fix permissions:**
```bash
chmod -R 775 storage
chown -R www:www storage
```

### Issue: Image tidak ter-compress
**Verify Intervention Image installed:**
```bash
composer show intervention/image
```

Should show: `intervention/image 3.x`

## 📝 Notes

1. **Foto bersifat OPTIONAL** - jurnal tetap bisa disimpan tanpa foto
2. **Satu foto per jurnal** - jika upload baru, foto lama otomatis terhapus
3. **Delete foto tidak menghapus jurnal** - hanya hapus file & set column NULL
4. **Auto-cleanup tidak diimplementasi** - foto tetap tersimpan meski jurnal dihapus (bisa tambahkan event listener jika perlu)
5. **Mobile optimized** - input file dengan `capture="environment"` untuk akses kamera langsung

## 🔄 Rollback Plan

Jika ada masalah serius:

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Revert code
git reset --hard HEAD~1
git push origin main --force

# Clear cache
php artisan optimize:clear
```

## 📊 Database Impact

**Table**: `teaching_journals`
**New Column**: `activity_photo` VARCHAR(255) NULL
**Impact**: Minimal - hanya menambah 1 kolom nullable, tidak ada data loss

## 🎉 Done!

Fitur foto kegiatan jurnal mengajar siap digunakan!

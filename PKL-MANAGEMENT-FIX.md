# FIX: PKL Management - Tombol Pilih Kelas XII

## Masalah
User melaporkan "tidak ada tombol pilih kelas xii" di halaman PKL Management.

## Penyebab
Ada 2 masalah utama:

1. **Filter Academic Year Missing**: 
   - Code sebelumnya: `SchoolClass::orderBy('name')->get()` - mengambil SEMUA kelas dari SEMUA tahun ajaran
   - Seharusnya: hanya kelas dari tahun ajaran aktif saja
   
2. **XII Detection**: 
   - Perlu normalisasi yang lebih baik untuk mendeteksi kelas XII (handle "xii", "Xii", "XII")

## Solusi Implementasi

### 1. Filter by Academic Year (`app/Livewire/TeachingSchedule/PklManagement.php`)
```php
// BEFORE:
$classes = SchoolClass::orderBy('name')->get();

// AFTER:
$classes = SchoolClass::where('academic_year_id', $this->academicYear->id)
    ->orderBy('name')
    ->get();
```

### 2. Improved XII Detection
```php
// Normalize class name for XII detection
$normalizedName = strtoupper(trim($class->name));
$isXii = str_starts_with($normalizedName, 'XII');
```

### 3. Better UI Feedback (`resources/views/livewire/teaching-schedule/pkl-management.blade.php`)
- Menambahkan counter kelas XII di button: "✓ Pilih Semua Kelas XII (3)"
- Debug info di info card: "Total kelas: X | Kelas XII: Y"
- Tambah `wire:key` untuk Livewire reactivity

## Deployment Steps

### Server Production
```bash
cd /www/wwwroot/simkur

# Pull changes
git pull origin main

# No need to rebuild or clear cache (hanya perubahan logic)
```

## Testing Checklist

1. ✅ Login sebagai admin/waka/kepsek
2. ✅ Buka halaman `/teaching-schedule/pkl-management`
3. ✅ Verifikasi info card menampilkan: "Total kelas: X | Kelas XII: Y"
4. ✅ **Jika ada kelas XII**: Button "✓ Pilih Semua Kelas XII (3)" harus muncul
5. ✅ **Jika tidak ada kelas XII**: Pesan warning "⚠️ Tidak ada kelas XII ditemukan" harus muncul
6. ✅ Class cards menampilkan badge "XII" untuk kelas-kelas grade 12
7. ✅ Click button untuk select semua XII classes
8. ✅ Test nonaktifkan dan aktifkan jadwal

## Expected Behavior

### Scenario A: Ada Kelas XII untuk Tahun Ajaran Aktif
- Button "✓ Pilih Semua Kelas XII (3)" muncul
- Kelas XII memiliki badge purple "XII"
- Counter menunjukkan jumlah kelas XII yang ditemukan

### Scenario B: Tidak Ada Kelas XII untuk Tahun Ajaran Aktif
- Pesan warning kuning muncul: "⚠️ Tidak ada kelas XII ditemukan"
- Info menunjukkan total kelas yang ada

## Debugging Jika Masih Bermasalah

### Check Database
```sql
-- Cek tahun ajaran aktif
SELECT id, year, is_active FROM academic_years WHERE is_active = 1;

-- Cek kelas untuk tahun ajaran aktif (ganti 1 dengan id dari query di atas)
SELECT id, name, grade, major, academic_year_id 
FROM classes 
WHERE academic_year_id = 1
ORDER BY name;

-- Harus ada 3 rows: XII AKL, XII BUSANA, XII MPLB
```

### Check Browser Console
- Buka Developer Tools (F12)
- Tab Console
- Cari error JavaScript atau Livewire

### Check Livewire Wire:Key
- Pastikan button memiliki unique wire:key
- Refresh page (Ctrl+F5) untuk clear cache browser

## Files Changed
- `app/Livewire/TeachingSchedule/PklManagement.php`
- `resources/views/livewire/teaching-schedule/pkl-management.blade.php`

## Git Commit
```
932de44 - Fix PKL management: filter classes by academic year and improve XII detection
```

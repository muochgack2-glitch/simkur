# 📚 Cara Import Jadwal Mengajar dari File TXT

## 📝 Overview

Seeder ini akan otomatis mengimport jadwal mengajar dari file `Jadwal_Guru_Terintegrasi_FIX.txt` ke database.

**File sumber**: `Jadwal_Guru_Terintegrasi_FIX.txt` (di root project)

---

## ✨ Fitur Seeder

### ✅ **Otomatis Parse Format TXT**
- Membaca file dengan format:
  ```
  1. Nama Guru
     Senin:
        - Jam ke-1 s/d 2: X AKL - Matematika
     Selasa:
        - Jam ke-3 s/d 4: XI BUSANA - Bahasa Inggris
  ```

### ✅ **Smart Matching**
- **Teacher**: Cari guru berdasarkan nama (fuzzy matching)
- **Class**: Cari kelas berdasarkan nama exact
- **Subject**: Auto-create jika belum ada
- **Time Slots**: Link dengan time_slots table

### ✅ **Duplicate Detection**
- Skip jadwal yang sudah ada (berdasarkan: teacher + class + subject + day + time slots)

### ✅ **Error Handling**
- Transaction: Rollback jika ada error
- Detailed error messages
- Summary report

### ✅ **Auto-Create**
- Subject: Otomatis buat mata pelajaran baru
- Subject Code: Generate otomatis dari nama
- Teacher: Opsional auto-create (bisa dinonaktifkan)

---

## 🚀 Cara Menggunakan

### **Prasyarat:**

1. ✅ File `Jadwal_Guru_Terintegrasi_FIX.txt` ada di root project
2. ✅ Academic Year sudah dibuat dan `is_active = true`
3. ✅ Semester sudah dibuat dan `is_active = true`
4. ✅ School Classes sudah dibuat (X AKL, XI BUSANA, dll)
5. ✅ Time Slots sudah dibuat (slot 1-10)
6. ✅ Teachers (opsional - bisa auto-create)

---

### **Step 1: Pastikan Master Data Lengkap**

```bash
# Check academic year
php artisan tinker
>>> App\Models\AcademicYear::where('is_active', true)->first()

# Check semester
>>> App\Models\Semester::where('is_active', true)->first()

# Check classes
>>> App\Models\SchoolClass::pluck('name')

# Check time slots
>>> App\Models\TimeSlot::count()
```

---

### **Step 2: Jalankan Seeder**

```bash
php artisan db:seed --class=JadwalMapelFromFileSeeder
```

**Output:**
```
🚀 Starting Jadwal Mapel Seeder from TXT file...

📄 Parsed 23 teachers from file

📅 Using: 2026/2027 - Semester ganjil

[1/23] Processing: Drs. Suseno
[2/23] Processing: Budi Siswanto, S.Pd.I.
[3/23] Processing: Dewi Wartini, S.Pd.
...

✅ SEEDING COMPLETED!
   Created: 234 schedules
   Skipped: 12 schedules (duplicates)
   Errors: 3 schedules

⚠️  ERRORS ENCOUNTERED:
   - Drs. Suseno - Senin - XII MPLB: Class not found: XII MPLB
   - ...
```

---

### **Step 3: Verifikasi Hasil**

```bash
# Check total schedules created
php artisan tinker
>>> App\Models\TeachingSchedule::count()

# Check schedules for specific teacher
>>> App\Models\User::where('name', 'LIKE', '%Suseno%')->first()->teachingSchedules->count()

# Check subjects created
>>> App\Models\Subject::orderBy('created_at', 'desc')->take(10)->pluck('name')
```

---

## 🎯 **Format File TXT**

Seeder ini support format berikut:

```
NOMOR. NAMA GURU
   HARI:
      - Jam ke-START s/d END: KELAS - MATA PELAJARAN
      - Jam ke-NUMBER: KELAS - MATA PELAJARAN
```

### **Contoh:**

```
1. Drs. Suseno.
   Senin:
      - Jam ke-5 s/d 6: XII MPLB - PKN
   Selasa:
      - Jam ke-1 s/d 2: XII BUSANA - PKN
      - Jam ke-3 s/d 4: XI BUSANA - PKN

2. Dewi Wartini, S.Pd.
   Senin:
      - Jam ke-1 s/d 2: X BUSANA - Matematika
      - Jam ke-4 s/d 5: X AKL - Matematika
   Rabu:
      - Jam ke-1 s/d 2: X MPLB - Matematika
```

### **Rules:**
- ✅ Nomor + titik + nama guru
- ✅ Hari diakhiri dengan titik dua `:`
- ✅ Entry dimulai dengan `-` dan "Jam ke-"
- ✅ Format: `Jam ke-X s/d Y` atau `Jam ke-X`
- ✅ Kelas dan mapel dipisah dengan `-`

---

## 🔧 **Kustomisasi Seeder**

### **1. Nonaktifkan Auto-Create Teacher**

Edit seeder, comment bagian ini:

```php
// Find or create teacher
private function findOrCreateTeacher(string $teacherName): ?User
{
    $teacher = User::where('name', 'LIKE', "%{$teacherName}%")
        ->whereHas('roles', fn($q) => $q->where('name', 'Guru'))
        ->first();
    
    if ($teacher) {
        return $teacher;
    }

    // COMMENT/REMOVE BAGIAN INI JIKA TIDAK MAU AUTO-CREATE
    /*
    $this->command->warn("Teacher not found, creating: {$teacherName}");
    $teacher = User::create([...]);
    */
    
    return null; // Return null jika tidak ketemu
}
```

### **2. Custom Subject Code Generator**

Edit method `generateSubjectCode()`:

```php
private function generateSubjectCode(string $subjectName): string
{
    // Custom logic here
    // Misalnya: PKN → PKN, Matematika → MAT, dll
    
    $mapping = [
        'PKN' => 'PKN',
        'Matematika' => 'MAT',
        'Bahasa Indonesia' => 'BIND',
        'Bahasa Inggris' => 'BING',
        // ... tambahkan mapping lainnya
    ];
    
    if (isset($mapping[$subjectName])) {
        return $mapping[$subjectName];
    }
    
    // Fallback to auto-generate
    return strtoupper(substr(str_replace(' ', '', $subjectName), 0, 5));
}
```

### **3. Set Default Room**

Edit method `createTeachingSchedule()`, ubah line:

```php
TeachingSchedule::create([
    // ...
    'room' => null, // ← Ubah jadi 'Ruang Kelas' atau dynamic logic
    // ...
]);
```

---

## 📊 **Data yang Dibuat**

### **1. Subjects (Auto-Create)**
Jika mapel belum ada, akan dibuat otomatis:
```
Name: PKN
Code: PKN (auto-generated)
Description: PKN
Is Active: true
```

### **2. Teaching Schedules**
Untuk setiap entry di file:
```
semester_id: Active semester
teacher_id: ID guru yang match
school_class_id: ID kelas yang match
subject_id: ID mapel (auto-create if not exist)
day_of_week: Senin/Selasa/Rabu/Kamis/Jumat/Sabtu
time_slot_id: [1,2] atau [3,4,5] (JSON array)
room: null (opsional)
notes: "Imported from Jadwal_Guru_Terintegrasi_FIX.txt"
```

### **3. Teachers (Opsional Auto-Create)**
Jika teacher belum ada DAN auto-create diaktifkan:
```
name: Nama dari file (cleaned)
username: Generated dari nama
email: username@smkpgriblora.sch.id
password: bcrypt('password')
is_active: true
roles: [Guru]
```

---

## ⚠️ **Troubleshooting**

### **Error: "Class not found: XII MPLB"**

**Penyebab**: Kelas belum dibuat di database

**Solusi**:
```bash
php artisan tinker
>>> App\Models\SchoolClass::create([
    'academic_year_id' => 1,
    'name' => 'XII MPLB',
    'grade' => 'XII',
    'major' => 'Manajemen Perkantoran dan Layanan Bisnis',
    'year_level' => 12,
    'is_active' => true,
]);
```

### **Error: "No time slots found"**

**Penyebab**: Time slots belum dibuat

**Solusi**:
```bash
php artisan db:seed --class=TimeSlotSeeder
```

### **Error: "No active academic year"**

**Penyebab**: Tahun ajaran belum ada atau tidak aktif

**Solusi**:
```bash
php artisan tinker
>>> App\Models\AcademicYear::create([
    'year' => '2026/2027',
    'start_date' => '2026-07-01',
    'end_date' => '2027-06-30',
    'is_active' => true,
]);
```

### **Warning: "Teacher not found, creating: XXX"**

**Bukan error**, ini info bahwa guru belum ada dan sedang dibuat otomatis.

**Jika tidak mau auto-create**: Nonaktifkan di seeder (lihat Kustomisasi)

---

## 📈 **After Import**

### **1. Verify Data**

```bash
# Total schedules
php artisan tinker
>>> App\Models\TeachingSchedule::count()

# Per teacher
>>> User::find(1)->teachingSchedules->count()

# Per class
>>> SchoolClass::find(1)->teachingSchedules->count()

# Per subject
>>> Subject::where('name', 'PKN')->first()->teachingSchedules->count()
```

### **2. Check for Overlaps**

```bash
php artisan schedule:check-overlap
# Jika ada command untuk check overlap
```

### **3. Generate Reports**

Visit di browser:
- `/schedules` - Lihat semua jadwal
- `/schedules/teacher/{id}` - Jadwal per guru
- `/schedules/class/{id}` - Jadwal per kelas

---

## 🎯 **Tips & Best Practices**

### ✅ **DO:**
1. Backup database sebelum run seeder
2. Test di development dulu
3. Verify master data lengkap
4. Check error messages
5. Run verification after import

### ❌ **DON'T:**
1. Run di production tanpa test
2. Run multiple times tanpa clear (akan skip duplicates, tapi waste time)
3. Ignore error messages
4. Skip verification

---

## 🔄 **Re-import / Update**

### **Jika ingin re-import:**

```bash
# Option 1: Delete existing schedules untuk semester ini
php artisan tinker
>>> App\Models\TeachingSchedule::where('semester_id', 1)->delete();

# Option 2: Delete all teaching schedules (HATI-HATI!)
>>> App\Models\TeachingSchedule::truncate();

# Then run seeder again
php artisan db:seed --class=JadwalMapelFromFileSeeder
```

### **Jika ingin update file TXT:**

1. Edit file `Jadwal_Guru_Terintegrasi_FIX.txt`
2. Hapus schedules lama (lihat di atas)
3. Run seeder lagi

---

## 📞 **Support**

Jika ada masalah:
1. Check error message di terminal
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify master data lengkap
4. Check file TXT format correct

---

## ✅ **Checklist Deployment**

```
[ ] File TXT ada di root project
[ ] Academic Year created & active
[ ] Semester created & active
[ ] School Classes created (all: X AKL, XI BUSANA, XII MPLB, dll)
[ ] Time Slots created (slot 1-10)
[ ] Teachers created (or enable auto-create)
[ ] Backup database
[ ] Test di development
[ ] Run seeder
[ ] Verify results
[ ] Check for errors
[ ] Test scheduling views
```

---

**Happy Importing! 📚✨**

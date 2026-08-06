# Analisis Cara Pilihan Jam pada Tambah Jadwal

## 📋 CARA KERJA SAAT INI

### 🎯 Flow Pilihan Jam di Tambah Jadwal

#### 1️⃣ **Pilih Hari** (Required Step)
- Dropdown: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu
- **TRIGGER:** `wire:model.live="day_of_week"`
- **EFEK:** Reset pilihan jam mulai & jam selesai ke kosong

#### 2️⃣ **Pilih Jam Mulai** (Conditional)
- **KONDISI:** Hanya aktif jika hari sudah dipilih
- **OPSI:** Semua time slot untuk hari tersebut (termasuk istirahat)
- **FORMAT:** Display name dari TimeSlot (misal: "JP 1 (07:00-07:45)")
- **TRIGGER:** `wire:model.live="start_time_slot_id"`
- **EFEK:** Reset jam selesai ke kosong, hitung ulang total JP

#### 3️⃣ **Pilih Jam Selesai** (Conditional)
- **KONDISI:** Hanya aktif jika jam mulai sudah dipilih
- **OPSI:** Hanya time slot dengan `order >= start_slot.order`
- **LOGIKA:** Tidak bisa pilih jam yang lebih awal dari jam mulai
- **TRIGGER:** `wire:model.live="end_time_slot_id"`
- **EFEK:** Hitung ulang total JP

#### 4️⃣ **Hitung Total JP** (Automatic)
```php
// Get all slots between start and end (inclusive)
$slots = TimeSlot::active()
    ->where('day_of_week', $this->day_of_week)
    ->where('order', '>=', $startSlot->order)
    ->where('order', '<=', $endSlot->order)
    ->get();

// Count only teaching slots (skip order 1, 5, 10)
$this->totalJP = $slots->filter(function($slot) {
    return $slot->order > 1 && $slot->order != 5 && $slot->order != 10;
})->count();
```

**SKIP OTOMATIS:**
- `order <= 1` → Pre-class / persiapan
- `order == 5` → Istirahat 1
- `order == 10` → Istirahat 2

#### 5️⃣ **Simpan Jadwal**
- **VALIDASI:** Start time <= End time (berdasarkan order)
- **SIMPAN:** Array of time_slot_id (tidak termasuk istirahat)
- **FORMAT DB:** JSON array `[2,3,4,6,7,8,9]` (skip 1, 5, 10)

---

## 🎨 UI/UX EXPERIENCE

### ✅ Yang Sudah Baik:
1. **Progressive Disclosure**
   - Jam Mulai disabled sampai Hari dipilih
   - Jam Selesai disabled sampai Jam Mulai dipilih
   - Tooltip informatif: "💡 Pilih hari terlebih dahulu"

2. **Real-time Feedback**
   - Total JP dihitung otomatis setiap perubahan
   - Info card biru menampilkan: "Total Jam Pelajaran: X JP"
   - Catatan: "Istirahat akan otomatis di-skip"

3. **Smart Filtering**
   - Jam Selesai hanya menampilkan opsi >= Jam Mulai
   - Mencegah input error dari user

4. **Clear Display**
   - Format: "JP 1 (07:00-07:45)"
   - Mudah dibaca dan dipahami

---

## 📊 PERBANDINGAN: Tambah Jadwal vs Copy Jurnal

| Aspek | Tambah Jadwal | Copy Jurnal (Saat Ini) |
|-------|---------------|------------------------|
| **Step 1** | Pilih Hari | Pilih Tanggal Copy |
| **Step 2** | Pilih Jam Mulai | Pilih Jam Mulai |
| **Step 3** | Pilih Jam Selesai | Pilih Jam Selesai |
| **Skip Breaks** | ✅ Otomatis | ✅ Otomatis |
| **Show Total JP** | ✅ Ya | ✅ Ya |
| **Range Selection** | ✅ Start-End | ✅ Start-End |
| **Validation** | Start <= End | Start <= End |
| **UI Pattern** | 3 Dropdowns | 3 Dropdowns |

**KESIMPULAN:** Copy Jurnal sudah mengikuti pola yang sama! ✅

---

## 🔍 DETAIL TEKNIS

### Database Structure (time_slot_id field):
```json
// Format: JSON Array
[2, 3, 4, 6, 7, 8, 9]

// Skipped automatically:
// - 1 (pre-class)
// - 5 (break 1)
// - 10 (break 2)
```

### TimeSlot Model Fields:
```php
- id: integer (primary key)
- day_of_week: string (Senin, Selasa, ...)
- order: integer (1-15)
- name: string (JP 1, JP 2, ...)
- display_name: string (JP 1 (07:00-07:45))
- time_range: string (07:00-07:45)
- start_time: time
- end_time: time
- is_active: boolean
```

### Key Methods:
```php
// Get slots for specific day
TimeSlot::active()
    ->where('day_of_week', $day)
    ->ordered()
    ->get();

// Get end time options (>= start)
TimeSlot::active()
    ->where('day_of_week', $day)
    ->where('order', '>=', $startSlot->order)
    ->ordered()
    ->get();

// Calculate JP (skip breaks)
$slots->filter(function($slot) {
    return $slot->order > 1 
        && $slot->order != 5 
        && $slot->order != 10;
})->count();
```

---

## 🎯 CONTOH SKENARIO

### Skenario 1: Mengajar 2 JP
**INPUT:**
- Hari: Senin
- Jam Mulai: JP 2 (07:45-08:30)
- Jam Selesai: JP 3 (08:30-09:15)

**RESULT:**
- Total JP: **2 JP**
- time_slot_id: `[2, 3]`

---

### Skenario 2: Mengajar Melewati Istirahat
**INPUT:**
- Hari: Senin
- Jam Mulai: JP 3 (08:30-09:15)
- Jam Selesai: JP 6 (10:15-11:00)

**PROSES:**
- Range: JP 3, JP 4, **ISTIRAHAT (JP 5)**, JP 6
- Filter: Skip JP 5 (istirahat)

**RESULT:**
- Total JP: **3 JP** (JP 3, 4, 6)
- time_slot_id: `[3, 4, 6]`
- **CATATAN:** JP 5 otomatis di-skip!

---

### Skenario 3: Mengajar Seharian (2 Istirahat)
**INPUT:**
- Hari: Senin
- Jam Mulai: JP 2 (07:45-08:30)
- Jam Selesai: JP 12 (13:15-14:00)

**PROSES:**
- Range: JP 2, 3, 4, **ISTIRAHAT (5)**, 6, 7, 8, 9, **ISTIRAHAT (10)**, 11, 12
- Filter: Skip JP 5 dan 10

**RESULT:**
- Total JP: **10 JP**
- time_slot_id: `[2, 3, 4, 6, 7, 8, 9, 11, 12]`

---

## 💡 KELEBIHAN SISTEM SAAT INI

### ✅ Advantages:
1. **User-Friendly**
   - Progressive disclosure mencegah kebingungan
   - Tooltip membantu user memahami flow

2. **Error Prevention**
   - Tidak bisa pilih jam selesai < jam mulai
   - Dropdown disabled sampai prerequisite terpenuhi

3. **Smart Calculation**
   - Istirahat otomatis di-skip
   - Total JP langsung terlihat

4. **Flexible**
   - Bisa mengajar 1 JP atau seharian
   - Bisa melewati istirahat tanpa manual skip

5. **Consistent**
   - Pola sama digunakan di Tambah Jadwal dan Copy Jurnal
   - Easy to maintain

---

## ⚠️ POTENSI ISSUE / EDGE CASES

### 1. **Jika Jam Mulai = Jam Selesai**
**SKENARIO:** User pilih JP 2 untuk mulai DAN selesai
**RESULT:** Total JP = 1 JP ✅
**STATUS:** Valid use case

### 2. **Jika Time Slot Tidak Lengkap**
**SKENARIO:** Ada hari tanpa time slot di database
**RESULT:** Dropdown kosong + message "Tidak ada jam tersedia"
**STATUS:** Handled ✅

### 3. **Jika Time Slot Non-Sequential**
**SKENARIO:** Order ada gap (1, 2, 3, 5, 6 → missing 4)
**RESULT:** Tetap bisa pilih, tapi JP count bisa tidak akurat
**STATUS:** ⚠️ Perlu data time_slots konsisten

### 4. **Overlap Detection**
**SKENARIO:** Guru/Kelas sudah ada jadwal di JP yang sama
**STATUS:** Tidak ada validasi di form ❌
**RECOMMENDATION:** Perlu tambah validasi overlap

---

## 🎯 PERTANYAAN DISKUSI:

### 1. **Validasi Overlap**
Apakah perlu tambah validasi untuk mencegah:
- Guru mengajar 2 kelas di jam yang sama?
- Kelas memiliki 2 mapel di jam yang sama?

### 2. **Display Format**
Apakah format "JP 1 (07:00-07:45)" sudah cukup jelas?
Atau perlu format lain seperti "07:00-07:45 (JP 1)"?

### 3. **Break Time Display**
Saat ini istirahat masih muncul di dropdown (bisa dipilih), tapi di-skip saat save.
Alternatif:
- Tetap tampilkan tapi disabled
- Hide dari dropdown sama sekali
- Tampilkan dengan label berbeda (misal: "ISTIRAHAT" dengan warna berbeda)

### 4. **Bulk Schedule Creation**
Apakah perlu fitur untuk membuat jadwal yang sama untuk beberapa hari sekaligus?
Misal: Matematika setiap Senin & Rabu JP 2-3

### 5. **Template Jadwal**
Apakah perlu fitur template untuk jadwal yang berulang?
Misal: "Simpan sebagai template" untuk digunakan kembali

### 6. **Quick Edit**
Apakah perlu fitur quick edit jam tanpa buka modal?
Misal: Click pada jam di tabel → dropdown inline

---

## 📝 CATATAN PENTING

### ⚡ Performance:
- Query time_slots setiap kali modal dibuka
- Sudah optimal dengan `ordered()` scope
- No N+1 query issues

### 🔒 Security:
- Validation di server-side (rules method)
- Protect against SQL injection (Eloquent)
- CSRF protection (Livewire)

### 🧪 Testing Scenarios:
- ✅ Pilih jam normal (tanpa istirahat)
- ✅ Pilih jam melewati istirahat
- ✅ Pilih jam seharian (2 istirahat)
- ✅ Edit jadwal existing
- ❌ Overlap detection (belum ada)

---

**File Terkait:**
- Component: `app/Livewire/TeachingSchedule/Index.php`
- View: `resources/views/livewire/teaching-schedule/index.blade.php`
- Model: `app/Models/TimeSlot.php`
- Model: `app/Models/TeachingSchedule.php`

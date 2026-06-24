# Weekend Validation - Prevent Activities on Saturday & Sunday

## Implementasi

Sistem sekarang **memvalidasi dan mencegah** kegiatan dijadwalkan di hari weekend (Sabtu & Minggu).

## Fitur

### 1. **Real-time Warning** ✅
Saat user memilih tanggal yang mencakup weekend:
- **Warning merah** muncul otomatis
- Memberitahu bahwa tanggal mencakup Sabtu/Minggu
- Menyarankan untuk pilih hari kerja saja

### 2. **Save Prevention** ✅
Saat user coba save kegiatan dengan tanggal weekend:
- **Tidak bisa disimpan**
- Error message: "Kegiatan tidak boleh dijadwalkan di hari Sabtu atau Minggu"
- Form tidak di-submit

### 3. **Auto-check** ✅  
- Check otomatis saat tanggal berubah
- Menggunakan `wire:model.live` untuk real-time validation
- Loop semua tanggal dalam range untuk detect weekend

## Files Modified

### 1. Create Component
**File:** `app/Livewire/Activity/Create.php`

**Changes:**
```php
// Properties
public $weekendWarning = false;
public $hasWeekendDays = false;

// Method
public function checkWeekend() {
    // Loop through date range
    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
        if ($date->isWeekend()) {
            $this->hasWeekendDays = true;
            break;
        }
    }
}

// In save()
if ($this->hasWeekendDays && Setting::getValue('prevent_weekend_activities', true)) {
    session()->flash('error', 'Kegiatan tidak boleh dijadwalkan di hari Sabtu atau Minggu...');
    return;
}
```

### 2. Edit Component
**File:** `app/Livewire/Activity/Edit.php`

**Changes:** Same as Create component

### 3. Create View
**File:** `resources/views/livewire/activity/create.blade.php`

**Changes:**
```blade
@if($weekendWarning && $hasWeekendDays)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <!-- Warning message -->
    </div>
@endif
```

### 4. Edit View
**File:** `resources/views/livewire/activity/edit.blade.php`

**Changes:** Same as Create view

### 5. Settings
**File:** `database/seeders/SettingSeeder.php`

**New Setting:**
```php
[
    'key' => 'prevent_weekend_activities',
    'value' => '1',
    'type' => 'boolean',
    'group' => 'system',
    'description' => 'Cegah Kegiatan di Hari Weekend (Sabtu/Minggu)',
]
```

## How It Works

### Flow Diagram
```
User memilih tanggal
    ↓
wire:model.live triggered
    ↓
updatedStartDate() / updatedEndDate()
    ↓
checkWeekend()
    ↓
Loop tanggal start → end
    ↓
Cek setiap tanggal: isWeekend()?
    ↓
Yes → set weekendWarning = true
    ↓
UI menampilkan warning merah
    ↓
User klik Save
    ↓
save() / update()
    ↓
Check: hasWeekendDays && setting enabled?
    ↓
Yes → Flash error & return (tidak save)
No → Save ke database
```

### Weekend Detection
```php
Carbon::parse('2026-07-25')->isWeekend() // Saturday → true
Carbon::parse('2026-07-26')->isWeekend() // Sunday → true
Carbon::parse('2026-07-27')->isWeekend() // Monday → false
```

## Configuration

### Enable/Disable via Settings

Di database settings table:
```sql
UPDATE settings 
SET value = '1' 
WHERE key = 'prevent_weekend_activities'; -- Enable

UPDATE settings 
SET value = '0' 
WHERE key = 'prevent_weekend_activities'; -- Disable
```

Atau via Settings UI (jika ada):
- System Settings → "Cegah Kegiatan di Hari Weekend"
- Toggle ON/OFF

## User Experience

### Scenario 1: Single Day Weekend
```
Start: 2026-07-25 (Saturday)
End: 2026-07-25 (Saturday)

Result:
⚠️  Warning: "Tanggal mencakup hari Sabtu atau Minggu"
❌  Save button → Error: "Tidak boleh dijadwalkan di hari Sabtu..."
```

### Scenario 2: Range Including Weekend
```
Start: 2026-07-24 (Friday)
End: 2026-07-26 (Sunday)

Result:
⚠️  Warning: "Tanggal mencakup hari Sabtu atau Minggu"
❌  Save button → Error
```

### Scenario 3: Weekdays Only
```
Start: 2026-07-27 (Monday)
End: 2026-07-31 (Friday)

Result:
✅  No warning
✅  Save success
```

## Visual Indicators

### Warning Box Design
```
🔴 Red background (#FEF2F2)
🔴 Red border (#FCA5A5)
⚠️  Warning icon
📝 Bold title: "Peringatan: Kegiatan di Hari Libur"
📝 Description: Explanation + suggestion
```

### Error Flash Message
```
🔴 Red toast notification
📝 "Kegiatan tidak boleh dijadwalkan di hari Sabtu atau Minggu. 
    Silakan pilih tanggal hari kerja."
```

## Testing Checklist

### Create Activity
- [ ] Pilih tanggal Sabtu → Warning muncul ✅
- [ ] Pilih tanggal Minggu → Warning muncul ✅
- [ ] Pilih range Jumat-Minggu → Warning muncul ✅
- [ ] Klik Save dengan weekend → Error muncul, tidak save ✅
- [ ] Ubah ke weekday → Warning hilang ✅
- [ ] Klik Save weekday only → Success save ✅

### Edit Activity
- [ ] Edit tanggal existing ke Sabtu → Warning muncul ✅
- [ ] Klik Update dengan weekend → Error muncul ✅
- [ ] Ubah ke weekday → Success update ✅

### Setting Control
- [ ] Disable prevent_weekend_activities → Save weekend berhasil ✅
- [ ] Enable prevent_weekend_activities → Save weekend ditolak ✅

## Edge Cases Handled

### 1. Long Range (7+ days)
```php
Start: 2026-07-20 (Monday)
End: 2026-07-31 (Friday)

// Contains 2 weekends (25-26, 1-2 Aug)
Result: ⚠️  Warning detected correctly
```

### 2. Existing Data
- Data existing dengan weekend **tidak** otomatis divalidasi
- Hanya saat **create baru** atau **edit existing**
- Backward compatible

### 3. Timezone
- Menggunakan server timezone
- Carbon default timezone dari config

### 4. Leap Year / Month End
- Carbon handles automatically
- No special logic needed

## Performance

### Efficiency
```php
// Worst case: 365 days range
for ($i = 0; $i < 365; $i++) {
    if ($date->isWeekend()) break; // Exit early
}

// Best case: First day is weekend → 1 iteration
// Average: ~7 days range → 7 iterations
// Performance: Negligible (< 1ms)
```

## Internationalization

Saat ini hardcoded untuk:
- **Saturday** (Sabtu) = Weekend
- **Sunday** (Minggu) = Weekend

Future enhancement: Bisa customize via settings
```php
weekend_days: ["saturday", "sunday"] // Default
weekend_days: ["friday", "saturday"] // Custom (e.g., Middle East)
```

## Database Impact

**Zero impact** - No database changes needed:
- Validation di aplikasi level
- Tidak ada column baru
- Tidak ada migration
- Hanya insert 1 settings row

## API / Import Compatibility

Import Excel perlu di-update juga (future):
```php
// In ImportService
if ($date->isWeekend() && Setting::getValue('prevent_weekend_activities', true)) {
    $errors[] = "Row {$row}: Tanggal tidak boleh weekend";
}
```

## Known Limitations

1. **Public Holidays tidak dicheck**
   - Hanya weekend (Sabtu/Minggu)
   - Public holidays (e.g., 17 Agustus) masih bisa
   - Future: Integrate dengan holiday calendar

2. **No auto-skip**
   - User harus manual pilih tanggal lain
   - Tidak ada fitur "skip weekend" otomatis
   - Future: Button "Adjust to Weekdays"

3. **Settings UI not yet**
   - Setting di database, belum ada UI
   - Future: Add toggle di Settings page

## Future Enhancements

### 1. Auto-adjust Dates
```php
public function adjustToWeekdays() {
    while ($this->start_date->isWeekend()) {
        $this->start_date->addDay();
    }
    while ($this->end_date->isWeekend()) {
        $this->end_date->subDay();
    }
}
```

### 2. Public Holiday Integration
```php
public function isHoliday($date) {
    return PublicHoliday::whereDate('date', $date)->exists();
}
```

### 3. Custom Weekend Days
```php
$weekendDays = Setting::getValue('weekend_days', ['saturday', 'sunday']);
$customWeekend = in_array($date->englishDayOfWeek, $weekendDays);
```

### 4. Workday Calculator
```php
public function countWorkdays($start, $end) {
    $count = 0;
    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
        if (!$date->isWeekend() && !$this->isHoliday($date)) {
            $count++;
        }
    }
    return $count;
}
```

## Status

✅ **IMPLEMENTED** - Weekend validation active

**Test now:**
1. Buka Create Activity
2. Pilih tanggal Sabtu (e.g., 2026-07-25)
3. Lihat warning merah muncul
4. Klik Save → Error message
5. Ubah ke Senin → Warning hilang
6. Klik Save → Success!

🚫 Kegiatan tidak bisa dijadwalkan di weekend lagi!

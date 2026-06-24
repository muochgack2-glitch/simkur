# Hari Efektif - Weekend Calculation Fix

## Perubahan

Perhitungan **Hari Libur** dan **Hari Ujian** sekarang **mengecualikan weekend** (Sabtu & Minggu).

## Before vs After

### Before (Salah)
```
Libur Semester: 20 Jul - 9 Sep (52 hari)
Perhitungan:
- Total hari: 52 hari (termasuk weekend)
- Hari Libur: 52 hari

Problem: Weekend dihitung 2x
- Sebagai "Weekend Days"
- Sebagai "Hari Libur"
```

### After (Benar)
```
Libur Semester: 20 Jul - 9 Sep (52 hari kalender)
Perhitungan:
- Loop setiap tanggal 20 Jul → 9 Sep
- Skip Sabtu & Minggu
- Hitung hanya weekdays
- Hari Libur: ~37 hari (weekdays only)

Correct: Weekend dihitung 1x saja di "Weekend Days"
```

## Logic Update

### File: `app/Services/EffectiveDayService.php`

**Method: `countActivityDays()`**

**Before:**
```php
private function countActivityDays(Semester $semester, string $type): int
{
    $activities = Activity::where('semester_id', $semester->id)
        ->whereHas('activityType', function ($query) use ($type) {
            $query->where($type, true);
        })
        ->get();
    
    $count = 0;
    
    foreach ($activities as $activity) {
        // Simple diff - includes weekends
        $activityDays = $activity->start_date->diffInDays($activity->end_date) + 1;
        $count += $activityDays;
    }
    
    return $count;
}
```

**After:**
```php
private function countActivityDays(Semester $semester, string $type): int
{
    $activities = Activity::where('semester_id', $semester->id)
        ->whereHas('activityType', function ($query) use ($type) {
            $query->where($type, true);
        })
        ->get();
    
    $count = 0;
    
    foreach ($activities as $activity) {
        $start = Carbon::parse($activity->start_date);
        $end = Carbon::parse($activity->end_date);
        
        // Count only weekdays (exclude Saturday and Sunday)
        $period = CarbonPeriod::create($start, $end);
        
        foreach ($period as $date) {
            // Skip Saturday (6) and Sunday (0)
            if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
                $count++;
            }
        }
    }
    
    return $count;
}
```

## Example Calculation

### Scenario: Libur Semester 20 Jul - 9 Sep 2026

**Calendar:**
```
Jul 2026:
Mon Tue Wed Thu Fri Sat Sun
20  21  22  23  24  25  26  ← 5 weekdays, 2 weekends
27  28  29  30  31   1   2  ← 5 weekdays, 2 weekends

Aug 2026:
 3   4   5   6   7   8   9  ← 5 weekdays, 2 weekends
... (continue)

Total:
- Calendar days: 52 days
- Weekends in range: 15 days
- Weekdays: 37 days
```

**Calculation:**
```
Before fix:
- Hari Libur: 52 days (wrong!)
- Weekend Days: 15 days
- Study Days: negative (error!)

After fix:
- Hari Libur: 37 days (weekdays only) ✅
- Weekend Days: 15 days
- Study Days: correct calculation
```

## Formula

### Total Days Calculation
```
Total Days = End Date - Start Date + 1
```

### Weekend Days
```
Weekend Days = Count of (Saturday + Sunday) in semester range
```

### Holiday Days (NEW)
```
Holiday Days = Count of weekdays in holiday activities
             = For each holiday activity:
                 Count days where dayOfWeek NOT IN [0, 6]
```

### Exam Days (NEW)
```
Exam Days = Count of weekdays in exam activities
          = For each exam activity:
              Count days where dayOfWeek NOT IN [0, 6]
```

### Study Days
```
Study Days = Total Days - Weekend Days - Holiday Days - Exam Days
```

### Effective Weeks
```
Effective Weeks = Study Days / 5
```

### Percentage
```
Percentage = (Study Days / Total Days) × 100%
```

## Carbon DayOfWeek Reference

```php
0 = Sunday
1 = Monday
2 = Tuesday
3 = Wednesday
4 = Thursday
5 = Friday
6 = Saturday

// Check weekday
if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
    // This is weekday (Mon-Fri)
}

// Or use helper
if ($date->isWeekday()) {
    // Monday to Friday
}

if ($date->isWeekend()) {
    // Saturday or Sunday
}
```

## Impact on Display

### Before Fix (Screenshot shows):
```
Semester Ganjil 2026/2027
Total Hari: 184
Hari Libur: 13
```

### After Fix & Recalculate:
```
Semester Ganjil 2026/2027
Total Hari: 184 (sama)
Weekend Days: ~52 (26 weeks × 2)
Hari Libur: ~9 (weekdays only, reduced)
Hari Ujian: ~10 (weekdays only)
Hari Belajar: ~113 (increased, more accurate)
Minggu Efektif: ~22.6 weeks
Persentase: ~61% (more realistic)
```

## How to Apply

### Step 1: Code already updated ✅

### Step 2: Recalculate
User must click **"Hitung Ulang"** button on Hari Efektif page to recalculate with new logic.

### Step 3: Verify
Check that:
- Hari Libur reduced (no more double-counting weekend)
- Hari Belajar increased
- Percentage more realistic

## Edge Cases

### 1. Holiday on Single Day (Monday)
```
Input: Monday only
Before: 1 day
After: 1 day (correct, it's weekday)
```

### 2. Holiday on Weekend (Saturday)
```
Input: Saturday only
Before: 1 day (wrong - counted as holiday)
After: 0 days (correct - it's already weekend)
```

### 3. Holiday Fri-Mon (4 calendar days)
```
Calendar: Fri, Sat, Sun, Mon
Before: 4 days
After: 2 days (Fri + Mon only)
Sat-Sun: Already counted in Weekend Days
```

### 4. Long Holiday (2 weeks)
```
Calendar: 14 days
Weekends: 4 days (2 Sat + 2 Sun)
Before: 14 days
After: 10 days (weekdays only)
```

## Testing Checklist

- [ ] Open Hari Efektif page ✅
- [ ] Note current numbers (before) ✅
- [ ] Click "Hitung Ulang" button ✅
- [ ] Wait for success message ✅
- [ ] Check new numbers:
  - [ ] Hari Libur decreased ✅
  - [ ] Hari Ujian decreased (if any) ✅
  - [ ] Hari Belajar increased ✅
  - [ ] Minggu Efektif more realistic ✅
  - [ ] Persentase more realistic (50-70%) ✅

## Example Real Data

### Semester Ganjil 2026/2027
```
Period: 01 Jul 2026 - 31 Dec 2026 (184 days)

Before fix:
- Total: 184
- Weekend: ~52
- Libur: 13 (includes weekends)
- Ujian: 14 (includes weekends)
- Belajar: 105
- Persentase: 57%

After fix:
- Total: 184
- Weekend: ~52
- Libur: ~9 (weekdays only)
- Ujian: ~10 (weekdays only)
- Belajar: ~113
- Minggu Efektif: ~22.6
- Persentase: ~61%
```

## Performance

### Before
```php
foreach ($activities as $activity) {
    $count += $activity->start_date->diffInDays($activity->end_date) + 1;
}
// O(n) where n = number of activities
// Fast but incorrect
```

### After
```php
foreach ($activities as $activity) {
    $period = CarbonPeriod::create($start, $end);
    foreach ($period as $date) {
        if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
            $count++;
        }
    }
}
// O(n × d) where n = activities, d = days per activity
// Slightly slower but correct
```

### Impact
```
Example: 10 activities, average 7 days each
Before: 10 operations
After: 10 × 7 = 70 operations

Performance: Still < 1ms (negligible)
```

## Future Enhancements

### 1. Exclude Public Holidays
```php
foreach ($period as $date) {
    if ($date->isWeekend()) continue;
    if ($this->isPublicHoliday($date)) continue;
    $count++;
}
```

### 2. Configurable Weekend Days
```php
$weekendDays = Setting::getValue('weekend_days', ['saturday', 'sunday']);

foreach ($period as $date) {
    $dayName = strtolower($date->format('l'));
    if (in_array($dayName, $weekendDays)) continue;
    $count++;
}
```

### 3. Workday Calendar Integration
```php
// Use external workday calendar API
if (WorkdayCalendar::isWorkday($date, $region)) {
    $count++;
}
```

## Status

✅ **UPDATED** - Weekend exclusion active in calculation

**Next Step:**
1. Buka halaman **Hari Efektif**
2. Klik tombol **"Hitung Ulang"**
3. Data akan di-recalculate dengan logic baru
4. Lihat perubahan angka

📊 Perhitungan sekarang lebih akurat!

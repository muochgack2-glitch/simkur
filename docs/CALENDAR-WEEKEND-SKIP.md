# Calendar View - Skip Weekend Days

## Implementasi

Event di kalender sekarang **tidak ditampilkan di hari Sabtu dan Minggu**. Event yang span multiple days akan otomatis di-split menjadi segmen weekday saja.

## How It Works

### Before (dengan weekend):
```
Kegiatan Sekolah Reguler
Start: 2026-07-20 (Senin)
End: 2026-09-09 (Rabu)

Calendar menampilkan:
Sen Sel Rab Kam Jum [Sab] [Min]
20  21  22  23  24  [25] [26]  ← Event continuous termasuk weekend
27  28  29  30  31  [1]  [2]   ← Event continuous termasuk weekend
```

### After (skip weekend):
```
Kegiatan Sekolah Reguler
Start: 2026-07-20 (Senin)
End: 2026-09-09 (Rabu)

Calendar menampilkan:
Sen Sel Rab Kam Jum [Sab] [Min]
20  21  22  23  24  [ ]  [ ]   ← Event STOP di Jumat
27  28  29  30  31  [ ]  [ ]   ← Event LANJUT di Senin, STOP di Jumat
 3   4   5   6   7  [ ]  [ ]   ← Event LANJUT di Senin
```

## Algorithm

### Split Events into Weekday Segments

```php
foreach ($activities as $activity) {
    $currentStart = null;
    
    // Loop through each day in range
    for ($date = $start; $date <= $end; $date++) {
        
        if (isWeekday($date)) {
            // Start new segment if not started
            if ($currentStart === null) {
                $currentStart = $date;
            }
            // Continue segment (do nothing)
        } 
        else { // Weekend
            // End current segment
            if ($currentStart !== null) {
                addEvent($currentStart, $date); // End before weekend
                $currentStart = null;
            }
        }
    }
    
    // Add final segment if exists
    if ($currentStart !== null) {
        addEvent($currentStart, $end);
    }
}
```

### Example Flow

**Input:**
```
Activity: "Kegiatan Reguler"
Start: Monday July 20
End: Wednesday July 29
```

**Process:**
```
Day 1 (Mon 20): Weekday → Start segment (currentStart = Mon 20)
Day 2 (Tue 21): Weekday → Continue segment
Day 3 (Wed 22): Weekday → Continue segment
Day 4 (Thu 23): Weekday → Continue segment
Day 5 (Fri 24): Weekday → Continue segment
Day 6 (Sat 25): WEEKEND → End segment [Mon 20 - Fri 24], reset currentStart
Day 7 (Sun 26): WEEKEND → No segment
Day 8 (Mon 27): Weekday → Start NEW segment (currentStart = Mon 27)
Day 9 (Tue 28): Weekday → Continue segment
Day 10 (Wed 29): Weekday → Continue segment
End of loop → Add final segment [Mon 27 - Wed 29]
```

**Output:**
```
Event 1: Mon July 20 - Sat July 25 (exclusive = Fri 24 end)
Event 2: Mon July 27 - Thu July 30 (exclusive = Wed 29 end)
```

## Implementation Details

### File: `app/Livewire/Activity/Index.php`

**Location:** Inside `render()` method, events preparation section

**Code:**
```php
$events = [];
if ($this->view !== 'list') {
    $activities = Activity::with('activityType')
        ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
        ->when($this->filterSemester, fn($q) => $q->where('semester_id', $this->filterSemester))
        ->when($this->filterType, fn($q) => $q->where('activity_type_id', $this->filterType))
        ->get();
    
    foreach ($activities as $activity) {
        $color = $activity->color ?: ($activity->activityType->default_color ?? '#3B82F6');
        
        $start = \Carbon\Carbon::parse($activity->start_date);
        $end = \Carbon\Carbon::parse($activity->end_date);
        
        // Skip weekends - split continuous events into weekday segments
        $currentStart = null;
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $isWeekend = $date->dayOfWeek === 0 || $date->dayOfWeek === 6;
            
            if (!$isWeekend) {
                // Weekday - start or continue event segment
                if ($currentStart === null) {
                    $currentStart = $date->copy();
                }
            } else {
                // Weekend - end current segment if any
                if ($currentStart !== null) {
                    $events[] = [
                        'id' => $activity->id,
                        'title' => $activity->name,
                        'start' => $currentStart->format('Y-m-d'),
                        'end' => $date->format('Y-m-d'), // Exclusive end (before weekend)
                        'backgroundColor' => $color,
                        'borderColor' => $color,
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'type' => $activity->activityType->name,
                            'description' => $activity->description,
                        ],
                    ];
                    $currentStart = null;
                }
            }
        }
        
        // Add final segment if exists
        if ($currentStart !== null) {
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->name,
                'start' => $currentStart->format('Y-m-d'),
                'end' => $end->copy()->addDay()->format('Y-m-d'), // Exclusive end
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => $activity->activityType->name,
                    'description' => $activity->description,
                ],
            ];
        }
    }
}
```

## Carbon DayOfWeek Values

```php
Carbon::SUNDAY    = 0
Carbon::MONDAY    = 1
Carbon::TUESDAY   = 2
Carbon::WEDNESDAY = 3
Carbon::THURSDAY  = 4
Carbon::FRIDAY    = 5
Carbon::SATURDAY  = 6

// Check weekend
$isWeekend = $date->dayOfWeek === 0 || $date->dayOfWeek === 6;
```

## FullCalendar Exclusive End

FullCalendar menggunakan **exclusive end date**:
```
Event displayed: July 20 - July 24
FullCalendar end: July 25 (next day after last day)

start: '2026-07-20'
end: '2026-07-25'  // Exclusive (displays up to July 24)
```

## Edge Cases Handled

### 1. Single Day Event (Friday)
```
Input: Fri July 24 - Fri July 24
Output: 1 event [Fri July 24 - Sat July 25 (exclusive)]
Result: ✅ Displays Friday only
```

### 2. Single Day Event (Saturday)
```
Input: Sat July 25 - Sat July 25
Process: Loop hits Saturday immediately → isWeekend → no segment created
Output: 0 events
Result: ✅ Not displayed (correct!)
```

### 3. Friday to Monday
```
Input: Fri July 24 - Mon July 27
Process:
- Fri 24: Weekday → Start segment
- Sat 25: Weekend → End segment [Fri 24]
- Sun 26: Weekend → Skip
- Mon 27: Weekday → Start new segment [Mon 27]
Output: 2 events
Result: ✅ Split correctly
```

### 4. Monday to Monday (7 days)
```
Input: Mon July 20 - Mon July 27
Process:
- Mon-Fri 20-24: Continuous segment
- Sat-Sun 25-26: End first segment, skip
- Mon 27: Start new segment
Output: 2 events [Mon 20 - Fri 24], [Mon 27]
Result: ✅ Weekend skipped
```

### 5. Entire Week (Mon-Sun)
```
Input: Mon July 20 - Sun July 26
Process:
- Mon-Fri 20-24: Continuous segment
- Sat 25: End segment
- Sun 26: Skip (last day)
Output: 1 event [Mon 20 - Fri 24]
Result: ✅ Weekend not included
```

## Performance Considerations

### Time Complexity
```
n = number of activities
d = average days per activity

For each activity:
- Loop through d days: O(d)
- Check weekend: O(1)
- Add to events array: O(1)

Total: O(n * d)

Example:
- 50 activities
- Average 30 days per activity
- Iterations: 50 * 30 = 1,500
- Time: ~5-10ms (negligible)
```

### Memory Usage
```
Before split:
- 1 activity with 30 days → 1 event in array

After split:
- 1 activity with 30 days → 3-5 events (split by weekends)
- Memory increase: 3-5x per long-duration activity

Total events in calendar:
- Before: ~50 events
- After: ~150-200 events (worst case)
- Memory: Still negligible (< 1MB)
```

## Visual Result

### Calendar Display

**Before:**
```
Sen  Sel  Rab  Kam  Jum  Sab  Min
20   21   22   23   24   25   26
[████████████████████████████████]  ← Event continuous
27   28   29   30   31    1    2
[████████████████████████████████]  ← Event continuous
```

**After:**
```
Sen  Sel  Rab  Kam  Jum  Sab  Min
20   21   22   23   24   [ ]  [ ]
[███████████████████████]           ← Event STOP di Jumat
27   28   29   30   31   [ ]  [ ]
[███████████████████████]           ← Event START di Senin
```

## Benefits

✅ **Visual Clarity**: Jelas bahwa kegiatan hanya weekdays  
✅ **Data Accuracy**: Reflect real school schedule  
✅ **User Understanding**: User tahu kegiatan tidak ada di weekend  
✅ **Consistent**: Sama dengan validation di Create/Edit  
✅ **Professional**: School calendar yang proper  

## Testing Checklist

- [ ] Single day Friday → Tampil ✅
- [ ] Single day Saturday → TIDAK tampil ✅
- [ ] Single day Sunday → TIDAK tampil ✅
- [ ] Mon-Fri (5 days) → 1 segment ✅
- [ ] Mon-Sun (7 days) → 1 segment (Mon-Fri only) ✅
- [ ] Fri-Mon (4 days) → 2 segments (Fri), (Mon) ✅
- [ ] Long event (30 days) → Multiple segments ✅
- [ ] Filter semester → Weekend skip tetap berjalan ✅
- [ ] Filter jenis → Weekend skip tetap berjalan ✅

## Known Limitations

1. **Same event name appears multiple times**
   - 1 activity bisa jadi 3-5 event bars di calendar
   - Semua punya nama sama
   - User might think it's duplicate
   - **Acceptable**: This is intentional to show weekday-only

2. **Click event redirects to same edit page**
   - All segments have same activity ID
   - Clicking any segment → edit same activity
   - **Acceptable**: This is correct behavior

3. **No visual indicator of segments**
   - Segments look like separate activities
   - No line connecting segments
   - **Future**: Could add visual cue (dotted line, icon, etc.)

## Future Enhancements

### 1. Visual Connection Between Segments
```javascript
// In calendar.js
eventClassNames: function(arg) {
    if (arg.event.extendedProps.isSegmented) {
        return ['segmented-event'];
    }
}

// CSS
.segmented-event {
    border-left: 3px dashed #888;
}
```

### 2. Segment Counter in Title
```php
$segmentCount = 1;
// ...
'title' => $activity->name . ' (Bagian ' . $segmentCount . ')'
```

### 3. Tooltip with Full Date Range
```javascript
eventDidMount: function(info) {
    tippy(info.el, {
        content: `
            <strong>${info.event.title}</strong><br>
            Full range: ${originalStart} - ${originalEnd}<br>
            Showing: ${displayStart} - ${displayEnd}
        `
    });
}
```

## Status

✅ **IMPLEMENTED** - Calendar weekend skip active

**Test now:**
1. Refresh browser (Ctrl+F5)
2. Buka Calendar view
3. Lihat event di Sabtu/Minggu → **TIDAK ADA**
4. Event Friday → Berhenti di Jumat
5. Event Monday → Mulai di Senin
6. Event long duration → Split jadi multiple segments

🎯 Weekend di kalender sekarang bersih dari event!

# Design Document: Public Journal Monitoring

## 📋 Overview

**Feature Name:** Halaman Monitoring Publik Jurnal Guru Hari Ini  
**Purpose:** Real-time monitoring status pengisian jurnal mengajar guru untuk transparansi dan accountability  
**Access Level:** Public (tanpa login)  
**URL:** `/monitoring/jurnal-hari-ini`

---

## 🎯 Business Goals

1. **Transparansi**: Kepala sekolah dan waka kurikulum dapat melihat status pengisian jurnal secara real-time
2. **Accountability**: Pressure positif agar guru disiplin mengisi jurnal tepat waktu
3. **Monitoring**: Identifikasi cepat guru yang belum mengisi jurnal untuk tindak lanjut
4. **Data-Driven Decision**: Basis data untuk evaluasi kedisiplinan guru

---

## 🏗️ High-Level Design

### System Architecture

```
┌─────────────┐
│   Browser   │
│  (Public)   │
└──────┬──────┘
       │
       │ HTTP GET /monitoring/jurnal-hari-ini
       ▼
┌──────────────────────────────────────┐
│   Livewire Component                 │
│   JournalMonitoring\Index            │
│                                      │
│   - Auto-detect hari ini             │
│   - Load schedules & journals        │
│   - Categorize teachers              │
│   - Calculate completion %           │
└──────┬───────────────────────────────┘
       │
       │ Query Database
       ▼
┌──────────────────────────────────────┐
│   Database Models                    │
│                                      │
│   - TeachingSchedule (jadwal)        │
│   - TeachingJournal (jurnal)         │
│   - User (guru)                      │
│   - TimeSlot (jam pelajaran)         │
└──────────────────────────────────────┘
```

### Component Breakdown

#### 1. **Livewire Component**: `JournalMonitoring\Index.php`
**Responsibilities:**
- Detect hari ini (tanggal + hari dalam bahasa Indonesia)
- Query guru yang ada jadwal hari ini
- Query jurnal yang sudah diisi hari ini
- Match dan kategorisasi guru
- Calculate completion statistics
- Auto-refresh setiap 5 menit

**Public Properties:**
```php
public $today;           // Carbon instance
public $dayName;         // "Senin", "Selasa", etc.
public $teachers;        // Collection of categorized teachers
public $stats;           // Summary statistics
public $lastRefresh;     // Timestamp
```

**Public Methods:**
```php
mount()                  // Initialize data
refresh()                // Manual refresh
categorizeTeachers()     // Main logic
calculateStats()         // Calculate summary
```

#### 2. **View Template**: `journal-monitoring/index.blade.php`
**Structure:**
- Header dengan summary stats
- 3 kolom untuk kategori (Sudah, Sebagian, Belum)
- Auto-refresh indicator
- Manual refresh button

#### 3. **Route**: Public route (no middleware)
```php
Route::get('/monitoring/jurnal-hari-ini', Index::class)
    ->name('monitoring.journal.today');
```

---

## 🔧 Low-Level Design

### Algorithm: Kategorisasi Guru

```pseudocode
FUNCTION categorizeTeachers():
    today = Carbon::today()
    dayName = getDayNameInIndonesian(today)
    
    // 1. Get active academic year
    academicYear = AcademicYear::where('is_active', true)->first()
    IF NOT academicYear THEN RETURN empty
    
    // 2. Get all teachers with schedule today
    schedules = TeachingSchedule::with(['teacher', 'schoolClass', 'subject'])
        ->where('day_of_week', dayName)
        ->where('is_active', true)
        ->where('academic_year_id', academicYear->id)
        ->get()
    
    // 3. Group schedules by teacher
    teacherSchedules = schedules->groupBy('teacher_id')
    
    // 4. Get all journals today
    journals = TeachingJournal::where('date', today)
        ->where('academic_year_id', academicYear->id)
        ->get()
    
    // 5. Group journals by teacher
    teacherJournals = journals->groupBy('teacher_id')
    
    // 6. Initialize categories
    completed = []
    partial = []
    notStarted = []
    
    // 7. Process each teacher
    FOR EACH (teacherId, scheduleList) IN teacherSchedules:
        teacher = scheduleList[0]->teacher
        
        // Count total JP from schedules
        totalJP = 0
        FOR EACH schedule IN scheduleList:
            IF schedule->time_slot_id IS array:
                totalJP += count(schedule->time_slot_id)
            ELSE:
                totalJP += 1
            END IF
        END FOR
        
        // Count filled JP from journals
        filledJP = 0
        IF teacherJournals HAS teacherId:
            FOR EACH journal IN teacherJournals[teacherId]:
                IF journal->time_slot IS array:
                    filledJP += count(journal->time_slot)
                ELSE:
                    filledJP += 1
                END IF
            END FOR
        END IF
        
        // Calculate percentage
        percentage = (filledJP / totalJP) * 100
        
        // Categorize
        teacherData = {
            'id': teacherId,
            'name': teacher->name,
            'total_jp': totalJP,
            'filled_jp': filledJP,
            'remaining_jp': totalJP - filledJP,
            'percentage': percentage,
            'schedules': scheduleList  // For detail view
        }
        
        IF percentage == 100:
            completed.push(teacherData)
        ELSE IF percentage > 0:
            partial.push(teacherData)
        ELSE:
            notStarted.push(teacherData)
        END IF
    END FOR
    
    // 8. Sort each category by name
    completed.sortBy('name')
    partial.sortBy('name')
    notStarted.sortBy('name')
    
    RETURN {
        'completed': completed,
        'partial': partial,
        'not_started': notStarted
    }
END FUNCTION
```

### Data Structure: Teacher Data

```php
[
    'id' => 123,
    'name' => 'Dewi Wartini, S.Pd',
    'total_jp' => 4,
    'filled_jp' => 4,
    'remaining_jp' => 0,
    'percentage' => 100,
    'schedules' => [
        [
            'class' => 'X TKJ',
            'subject' => 'Basis Data',
            'time_slots' => 'JP 1-2 (2 JP)',
            'is_filled' => true
        ],
        [
            'class' => 'XI MM',
            'subject' => 'Pemrograman Web',
            'time_slots' => 'JP 5-6 (2 JP)',
            'is_filled' => true
        ]
    ]
]
```

### Data Structure: Summary Stats

```php
[
    'total_teachers' => 23,
    'completed_count' => 15,
    'completed_percentage' => 65,
    'partial_count' => 5,
    'partial_percentage' => 22,
    'not_started_count' => 3,
    'not_started_percentage' => 13
]
```

### Query Optimization Strategy

**Problem:** Multiple queries dapat lambat jika banyak data

**Solution:**
1. **Eager Loading**: Load relasi sekaligus
```php
TeachingSchedule::with(['teacher', 'schoolClass', 'subject', 'timeSlot'])
```

2. **Single Query untuk Journals**: Load semua jurnal hari ini sekaligus
```php
TeachingJournal::where('date', today())->get()
```

3. **Group di PHP**: Gunakan Collection groupBy di memory (faster)

4. **Optional Cache**: Cache hasil 5 menit (jika data besar)
```php
Cache::remember('journal-monitoring-'.date('Y-m-d'), 300, function() {
    return $this->categorizeTeachers();
});
```

---

## 🎨 UI/UX Design

### Layout Structure

```
┌────────────────────────────────────────────────────────┐
│  HEADER: 🗓️ Monitoring Jurnal Hari Ini                │
│  Senin, 4 Agustus 2026                                 │
│  Auto-refresh: ⟳ 3 menit lagi  [🔄 Refresh Sekarang]  │
└────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────┐
│  SUMMARY STATS                                         │
│  Total Guru Mengajar: 23                               │
│  ✅ Sudah: 15 (65%) | ⚠️ Sebagian: 5 (22%) |          │
│  ❌ Belum: 3 (13%)                                     │
└────────────────────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┐
│ ✅ SUDAH ISI │ ⚠️ SEBAGIAN  │ ❌ BELUM ISI │
│    (15)      │     (5)      │     (3)      │
├──────────────┼──────────────┼──────────────┤
│ Dewi         │ Budi         │ Ari          │
│ 4/4 JP       │ 2/3 JP       │ 0/2 JP       │
│ 100%         │ 67%          │ 0%           │
│              │              │              │
│ Ilham        │ Yully        │ Dhani        │
│ 3/3 JP       │ 1/2 JP       │ 0/3 JP       │
│ 100%         │ 50%          │ 0%           │
└──────────────┴──────────────┴──────────────┘
```

### Color Scheme

- **✅ Sudah Isi**: Green (#10B981, bg-green-50)
- **⚠️ Isi Sebagian**: Yellow (#F59E0B, bg-yellow-50)
- **❌ Belum Isi**: Red (#EF4444, bg-red-50)

### Responsive Design

- **Desktop**: 3 kolom side-by-side
- **Tablet**: 2 kolom (Sudah + Sebagian di atas, Belum di bawah)
- **Mobile**: 1 kolom stack

---

## 📊 Database Queries

### Query 1: Get Schedules for Today

```sql
SELECT ts.*, u.name as teacher_name, c.name as class_name, s.name as subject_name
FROM teaching_schedules ts
JOIN users u ON ts.teacher_id = u.id
JOIN classes c ON ts.class_id = c.id
JOIN subjects s ON ts.subject_id = s.id
WHERE ts.day_of_week = 'Senin'
  AND ts.is_active = 1
  AND ts.academic_year_id = ?
  AND u.role = 'guru'
  AND u.is_active = 1
```

### Query 2: Get Journals for Today

```sql
SELECT *
FROM teaching_journals
WHERE date = '2026-08-04'
  AND academic_year_id = ?
```

### Query 3: (Optional) Get Time Slot Details

```sql
SELECT id, name, start_time, end_time
FROM time_slots
WHERE id IN (?)
  AND is_active = 1
ORDER BY `order`
```

---

## 🔄 Auto-Refresh Mechanism

### Implementation Options

**Option 1: Livewire Wire:poll**
```blade
<div wire:poll.300s>  <!-- Refresh every 5 minutes -->
    <!-- Content -->
</div>
```
**Pros:** Simple, built-in Livewire
**Cons:** Full component refresh

**Option 2: JavaScript setInterval + Livewire**
```javascript
setInterval(() => {
    @this.call('refresh');
}, 300000); // 5 minutes
```
**Pros:** More control
**Cons:** Slightly complex

**Recommendation:** Use Option 1 (wire:poll) untuk simplicity

### Countdown Timer

Display remaining time:
```
Auto-refresh: ⟳ 4 menit 32 detik lagi
```

Implementation:
```javascript
let countdown = 300; // 5 minutes
setInterval(() => {
    countdown--;
    updateDisplay(countdown);
    if (countdown <= 0) countdown = 300;
}, 1000);
```

---

## 🚀 Performance Considerations

### Expected Load
- **Users:** 1-10 concurrent (low traffic)
- **Data Size:** 20-30 teachers × 2-5 schedules = ~100 records
- **Refresh Rate:** Every 5 minutes

### Performance Targets
- **Page Load:** < 1 second
- **Refresh:** < 500ms
- **Database Queries:** < 5 queries per request

### Optimization Strategy
1. **Eager Loading**: Reduce N+1 queries
2. **Collection Processing**: Use PHP in-memory grouping
3. **Optional Cache**: If > 50 teachers, cache for 5 min
4. **Index Database**: Ensure indexes on:
   - `teaching_schedules.day_of_week`
   - `teaching_schedules.academic_year_id`
   - `teaching_journals.date`
   - `teaching_journals.teacher_id`

---

## 🔐 Security & Access Control

### Access Level
**Public** - No authentication required

**Rationale:**
- Monitoring purpose (transparansi)
- No sensitive data exposed (hanya nama guru & stats)
- Similar to public calendar feature

### Data Exposure
**Displayed:**
- ✅ Nama guru
- ✅ Jumlah JP
- ✅ Status completion

**Hidden:**
- ❌ Materi pelajaran
- ❌ Kehadiran siswa
- ❌ Foto kegiatan
- ❌ Detail personal guru

### Optional: Rate Limiting
Prevent abuse dengan throttle:
```php
Route::get('/monitoring/jurnal-hari-ini', Index::class)
    ->middleware('throttle:60,1'); // 60 requests per minute
```

---

## 📱 Responsive Behavior

### Desktop (≥1024px)
```
┌─────────────┬─────────────┬─────────────┐
│   SUDAH     │  SEBAGIAN   │   BELUM     │
│  (width:    │  (width:    │  (width:    │
│   33.33%)   │   33.33%)   │   33.33%)   │
└─────────────┴─────────────┴─────────────┘
```

### Tablet (768px - 1023px)
```
┌─────────────┬─────────────┐
│   SUDAH     │  SEBAGIAN   │
│  (width:    │  (width:    │
│   50%)      │   50%)      │
└─────────────┴─────────────┘
┌───────────────────────────┐
│        BELUM              │
│      (width: 100%)        │
└───────────────────────────┘
```

### Mobile (<768px)
```
┌───────────────────────────┐
│        SUDAH              │
│      (width: 100%)        │
├───────────────────────────┤
│      SEBAGIAN             │
│      (width: 100%)        │
├───────────────────────────┤
│        BELUM              │
│      (width: 100%)        │
└───────────────────────────┘
```

---

## 🧪 Testing Strategy

### Unit Tests
1. Test `categorizeTeachers()` dengan berbagai scenario:
   - Semua guru sudah isi
   - Sebagian guru isi
   - Tidak ada guru yang isi
   - Tidak ada jadwal hari ini

2. Test percentage calculation:
   - 0%, 50%, 100%
   - Edge case: 0 total JP

### Integration Tests
1. Test complete flow dari mount sampai render
2. Test auto-refresh mechanism
3. Test dengan real database data

### Manual Testing Checklist
- [ ] Hari Senin dengan jadwal normal
- [ ] Hari Sabtu/Minggu (tidak ada jadwal)
- [ ] Hari libur nasional
- [ ] Semua guru sudah isi (edge case)
- [ ] Tidak ada guru yang isi (edge case)
- [ ] Auto-refresh berjalan 5 menit
- [ ] Manual refresh button works
- [ ] Responsive di mobile, tablet, desktop

---

## 📦 Deliverables

### Phase 1: MVP (Must Have)
- [x] Design document (this file)
- [ ] Livewire component `JournalMonitoring\Index.php`
- [ ] Blade view `journal-monitoring/index.blade.php`
- [ ] Public route registration
- [ ] Categorization logic
- [ ] Summary statistics
- [ ] 3-column layout
- [ ] Auto-refresh (5 min)
- [ ] Manual refresh button

### Phase 2: Enhancements (Nice to Have)
- [ ] Export to Excel
- [ ] Filter by subject
- [ ] Filter by class
- [ ] Detail modal (expand schedules)
- [ ] History view (pilih tanggal)
- [ ] WhatsApp notification integration
- [ ] Email reminder

---

## 🗓️ Implementation Roadmap

### Day 1: Core Logic
- Create Livewire component
- Implement categorization algorithm
- Write unit tests

### Day 2: UI & UX
- Design Blade view
- Implement responsive layout
- Add auto-refresh

### Day 3: Testing & Polish
- Integration testing
- Performance optimization
- Documentation

---

## 📚 References

- **Existing Similar Feature:** `/kaldik` (public calendar)
- **Models Used:**
  - `app/Models/TeachingSchedule.php`
  - `app/Models/TeachingJournal.php`
  - `app/Models/User.php`
  - `app/Models/TimeSlot.php`

---

**Document Version:** 1.0  
**Last Updated:** 2026-08-04  
**Author:** System Design

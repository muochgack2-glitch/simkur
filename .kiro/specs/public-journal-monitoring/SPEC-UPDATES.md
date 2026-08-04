# Spec Updates: Public Journal Monitoring

**Updated:** 2026-08-04  
**Version:** 1.1

---

## 🔄 Changes from Original Spec

### 1. **Kategorisasi: 3 → 2 Categories**

**Before:**
- ✅ Sudah Isi Lengkap (100%)
- ⚠️ Isi Sebagian (1-99%)
- ❌ Belum Isi (0%)

**After:**
- ❌ **Belum Isi** (0% completion)
- ✅ **Sudah Isi** (1-100% completion, termasuk partial)

**Rationale:**
- Simplifikasi monitoring
- Fokus: Siapa yang belum isi SAMA SEKALI
- Guru yang sudah mulai isi (walau partial) = progress positif

---

### 2. **Card Detail: Tambah Info Jadwal per JP**

**Before:**
- Hanya show total JP (X/Y JP)
- Tidak ada detail per schedule

**After:**
- Show detail per schedule dengan status:
  - ✓ JP 1-2: X AKL - Matematika (Sudah diisi)
  - ✗ JP 5-6: XII BUSANA - Matematika (Belum diisi)

**Benefit:**
- Lebih informatif
- Langsung tahu JP mana yang belum diisi
- Easy follow-up

---

### 3. **Sorting: Alphabetical (A-Z)**

**Requirement:**
- Sort guru by name (A-Z) dalam tiap kategori
- Consistent ordering untuk mudah cari

**Implementation:**
```php
$teachers->sortBy('name')
```

---

### 4. **No Max Limit Display**

**Before:**
- Might have pagination or limit

**After:**
- Tampilkan SEMUA guru tanpa batasan
- No pagination needed (max ~20 guru per day)
- Single page scroll

**Rationale:**
- Data tidak banyak (12-20 guru per hari)
- Better UX: Lihat semua sekaligus
- No need click "Load More"

---

### 5. **Card Size: Extra Compact**

**Specifications:**
- Very small cards untuk muat banyak
- Grid: 6 kolom di desktop lebar (≥1280px)
- Responsive: 
  - XL: 6 cols
  - LG: 4 cols
  - MD: 3 cols
  - SM: 2 cols
  - XS: 1 col

**Layout Goal:**
- Muat 12 guru dalam 2 rows (desktop lebar)
- Minimal scrolling

---

## 🚀 New Features (Phase 2)

### 1. **History View**

**Description:** Lihat monitoring hari sebelumnya

**UI:**
```
📅 [◀ Prev] [Pilih Tanggal: Senin, 4 Agustus 2026] [Next ▶]
```

**Features:**
- Date picker untuk pilih tanggal
- Navigation buttons (prev/next day)
- Load historical data
- Disable future dates

**Phase:** Phase 2 (Future enhancement)

---

### 2. **Calendar Integration**

**Description:** Deteksi hari libur dari master calendar

**Data Source:** `activities` table
- Filter: `type` = 'libur'
- Check: `start_date` <= today <= `end_date`

**Display:**
- Banner: "🎉 Hari Libur: [Nama Libur]"
- Message: "Tidak ada jadwal mengajar hari ini"
- Empty state dengan icon

**Logic:**
```php
$holiday = Activity::where('type', 'libur')
    ->whereDate('start_date', '<=', today())
    ->whereDate('end_date', '>=', today())
    ->first();

if ($holiday) {
    return view('no-schedule', [
        'message' => "Libur: {$holiday->title}"
    ]);
}
```

**Weekend Detection:**
```php
$dayName = Carbon::today()->locale('id')->dayName; // 'Sabtu', 'Minggu'

if (in_array($dayName, ['Sabtu', 'Minggu'])) {
    return view('no-schedule', [
        'message' => "Hari {$dayName} - Tidak ada jadwal"
    ]);
}
```

**Phase:** Phase 2 (Can be added to Phase 1 if quick)

---

## 📋 Implementation Priorities

### Phase 1 (MVP) - Must Have
1. ✅ 2 kategori (Belum/Sudah)
2. ✅ Detail jadwal per JP dengan status icon
3. ✅ Alphabetical sorting
4. ✅ No max limit
5. ✅ Extra compact cards
6. ✅ Class overview cards in header (without progress bars)
7. ✅ Auto-refresh 5 min
8. ✅ Manual refresh button
9. ✅ Public access
10. ✅ Responsive layout

**UI Mockups:** ✅ Complete (UI-WITH-CLASS-CARDS.html finalized)

### Phase 2 - Nice to Have
1. ⏳ History view (date picker)
2. ⏳ Calendar integration (libur detection)
3. ⏳ Export to Excel
4. ⏳ Filter by subject/class
5. ⏳ Detail modal per guru

---

## 🎨 Updated UI Mockup

### Main Mockup Files:
1. **`UI-MOCKUP-FINAL.html`** - Guru cards only (12 teachers from Monday)
2. **`UI-WITH-CLASS-CARDS.html`** ✅ **COMPLETE** - Added class overview cards in header

### Latest: UI-WITH-CLASS-CARDS.html (FINALIZED)

**Features:**
- **Section 1:** Class cards overview (6 classes in header)
  - Compact format: Colored header + status counters + JP list
  - NO progress bars (removed as requested)
  - Differentiated colors per class for quick identification
- **Section 2:** Teacher cards - Belum Isi (12 teachers)
- **Section 3:** Teacher cards - Sudah Isi (empty state)
- Click-to-detail modal for class schedules

**Card Formats:**

*Class Cards (Header):*
```
┌─────────────────────┐
│ [Colored] X AKL     │ ← Header dengan warna
├─────────────────────┤
│ ✓ 3  ✗ 2           │ ← Status counters
│ JP 1-2, 3-5,       │ ← JP list
│ 6-7, 8, 9          │
└─────────────────────┘
```

*Teacher Cards:*
- Same as UI-MOCKUP-FINAL.html
- Extra compact design
- Detail per JP dengan status icon

**Grid:**
- Desktop wide (XL): 6 columns
- Desktop (LG): 4 columns
- Tablet (MD): 3 columns
- Mobile (SM): 2 columns
- Mobile small: 1 column

---

## ✅ Ready for Implementation

All requirements clarified and documented. Ready to proceed with:
1. Livewire component creation
2. Blade view implementation
3. Route registration
4. Testing

**Est. Implementation Time:** 2-3 days for Phase 1 MVP

---

**Questions?** Proceed to implementation or need more clarification?

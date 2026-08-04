# Class Card Update V2 - Detail Mapel & JP

**Date:** 2026-08-04  
**Change Request:** "di card kelas, detailnya nama mapel dan jp"

---

## ✅ Changes Applied

### Before (V1): Counter + JP List Only
```
┌─────────────────────┐
│ [Blue] X AKL        │
├─────────────────────┤
│ ✓ 3  ✗ 2           │
│ JP 1-2, 3-5, 6-7    │
└─────────────────────┘
```

### After (V2): Detail per Mapel + Status
```
┌─────────────────────────┐
│ [Blue] X AKL            │
├─────────────────────────┤
│ ✓ Matematika • JP 1-2   │
│ ✗ Prog AKL • JP 3-5     │
│ ✓ Informatika • JP 6-7  │
├─────────────────────────┤
│ ✓ 2  ✗ 1               │
└─────────────────────────┘
```

---

## 📋 All 6 Class Cards Updated

### 1. X AKL (Blue)
- ✓ Matematika • JP 1-2
- ✗ Prog AKL • JP 3-5
- ✓ Informatika • JP 6-7
- **Summary:** ✓ 2 ✗ 1

### 2. X BUSANA (Purple)
- ✓ Matematika • JP 1-2
- ✓ B. Indonesia • JP 3-6
- ✗ Sejarah • JP 7-8
- ✗ BK • JP 9
- **Summary:** ✓ 2 ✗ 2

### 3. X MPLB (Green)
- ✗ Ke-PGRI-an • JP 1
- ✗ Prog MPLB • JP 2-5
- ✗ PIPAS • JP 6-9
- **Summary:** ✓ 0 ✗ 3

### 4. XI AKL (Indigo)
- ✓ Akuntansi Lembaga • JP 1-2
- ✓ Matematika • JP 3-5
- ✗ Akuntansi Keuangan • JP 6-9
- **Summary:** ✓ 2 ✗ 1

### 5. XI BUSANA (Pink)
- ✓ B. Indonesia • JP 1-3
- ✓ Gambar Teknis • JP 4-9
- **Summary:** ✓ 2 ✗ 0

### 6. XI MPLB (Teal)
- ✓ KIK • JP 1-5
- ✗ Ekonomi Bisnis • JP 6-9
- **Summary:** ✓ 1 ✗ 1

---

## 🎨 HTML Structure

```html
<div class="bg-white rounded-lg shadow border-t-4 border-blue-500">
    <!-- Colored Header -->
    <div class="bg-blue-500 px-3 py-2">
        <h3 class="font-bold text-white text-sm">X AKL</h3>
    </div>
    
    <!-- Subject List -->
    <div class="p-2 space-y-1.5">
        <!-- Each Subject -->
        <div class="text-xs">
            <div class="flex items-start gap-1">
                <span class="text-green-600">✓</span>
                <div class="flex-1">
                    <span class="text-gray-700 font-medium">Matematika</span>
                    <span class="text-gray-500"> • JP 1-2</span>
                </div>
            </div>
        </div>
        
        <!-- Summary Counter -->
        <div class="text-xs text-gray-400 pt-1 border-t">
            <span class="text-green-600 font-bold">✓ 2</span>
            <span class="text-red-600 font-bold ml-2">✗ 1</span>
        </div>
    </div>
</div>
```

---

## 💡 Key Improvements

### 1. **More Informative**
- User langsung tahu mapel mana yang belum diisi
- Tidak perlu klik modal untuk detail

### 2. **Better Visual Hierarchy**
```
Header (Colored)
  ├─ Subject 1 (✓/✗)
  ├─ Subject 2 (✓/✗)
  ├─ Subject 3 (✓/✗)
  └─ Summary (Footer)
```

### 3. **Consistent Format**
- All subjects: `[icon] [name] • [JP range]`
- Icon first untuk quick scanning
- JP di samping dengan separator bullet

### 4. **Space Efficient**
- `space-y-1.5` untuk spacing optimal
- `text-xs` untuk semua text
- `pt-1 border-t` untuk visual separator

---

## 🔄 Data Structure for Implementation

```php
// Per class aggregation needed
$classSchedules = [
    'class_name' => 'X AKL',
    'color' => 'blue',
    'subjects' => [
        [
            'name' => 'Matematika',
            'jp_range' => 'JP 1-2',
            'is_filled' => true,
            'teacher' => 'Dewi Wartini, S.Pd'
        ],
        [
            'name' => 'Dasar Prog Keahlian AKL',
            'jp_range' => 'JP 3-5',
            'is_filled' => false,
            'teacher' => 'Ari Yunitasari, S.Pd'
        ],
        // ...
    ],
    'filled_count' => 2,
    'not_filled_count' => 1
];
```

---

## ✅ Status

**UI Mockup:** ✅ Complete  
**File:** `UI-WITH-CLASS-CARDS.html`  
**All 6 cards updated:** ✅ Yes  
**Ready for implementation:** ✅ Yes

---

**Next:** Implement Livewire component with this class card structure

# 📊 Dashboard Analytics for Perangkat Ajar - v1.3.0

## 🎯 Overview

Menambahkan **statistik dan analytics untuk Perangkat Ajar** di semua dashboard (Admin/Waka, Kepsek, Guru) untuk monitoring dan evaluasi kelengkapan materi pembelajaran.

---

## ✨ Features Added

### 1. Admin/Waka Dashboard (Index.php)

**New Statistics Cards:**
- 📊 **Total Perangkat Ajar** - All materials
- ✅ **Approved Materials** - Materials yang sudah disetujui
- ⏳ **Pending Approval** - Materials menunggu persetujuan
- ❌ **Rejected Materials** - Materials yang ditolak
- 📝 **Draft Materials** - Materials yang masih draft
- 📥 **Total Downloads** - Total download semua materials
- 👁️ **Total Views** - Total views semua materials

**Analytics:**
- 📈 **Upload Trend Chart** (6 months) - Grafik approved materials per bulan
- 🏆 **Top 5 Contributors** - Guru paling produktif (by approved materials)
- 📂 **Top 10 Category Coverage** - Kategori dengan material terbanyak

**Methods Added:**
```php
private function loadMaterialStats($activeYear)
private function prepareMaterialChartData()
```

---

### 2. Kepsek Dashboard (KepsekIndex.php)

**New Statistics:**
- 📊 **Total Materials** (all status)
- ✅ **Approved Count**
- ⏳ **Pending Count**
- ❌ **Rejected Count**
- 📊 **Category Coverage %** - Berapa persen dari 20 kategori sudah terisi
  - Formula: `(distinct approved categories / 20) * 100`

**Dimension Coverage (8 Dimensi P5):**
- 🙏 Beriman & Bertakwa
- 🌏 Berkebinekaan Global
- 🤝 Gotong Royong
- 💪 Mandiri
- 🧠 Bernalar Kritis
- 🎨 Kreatif
- 🔢 Numerasi
- 📚 Literasi

**Monthly Trend Chart** (6 months) - Approved materials per bulan

**Method Added:**
```php
private function getMaterialStats($activeYear)
```

---

### 3. Guru Dashboard (GuruIndex.php)

**My Materials Statistics:**
- 📊 **Total Materials** (all my materials)
- 📝 **My Draft** - Materials yang masih draft
- ⏳ **My Pending** - Materials menunggu approval
- ✅ **My Approved** - Materials yang disetujui
- ❌ **My Rejected** - Materials yang ditolak

**Performance Metrics:**
- 📥 **My Total Downloads** - Total download materials saya
- 👁️ **My Total Views** - Total views materials saya
- ⭐ **My Most Downloaded Material** - Material dengan download terbanyak (title + count)

**Analytics:**
- 📈 **My Upload Trend** (6 months) - Chart upload materials per bulan (all status)
- 📂 **My Category Coverage** (Top 5) - Kategori yang saya sudah buat (approved only)

**Methods Added:**
```php
private function loadMyMaterialStats($teacherId)
private function prepareMyMaterialChartData($teacherId)
```

---

## 🔧 Technical Implementation

### Database Queries Optimization

All queries use proper indexing:
- `status` column - filtered frequently
- `academic_year_id` - scoped by year
- `created_by` - for guru's own materials
- `created_at` - for monthly aggregations

### Data Aggregation

**Sum Aggregations:**
```php
SUM(download_count) as total_downloads
SUM(view_count) as total_views
```

**Count Aggregations:**
```php
count(*) as total
count(DISTINCT category) as distinct_categories
```

**Grouping:**
```php
->groupBy('category')
->groupBy('created_by')
```

### Chart Data Format

All charts use consistent format:
```php
[
    'labels' => ['Jan', 'Feb', 'Mar', ...], // Month names (localized ID)
    'data' => [5, 8, 12, ...], // Counts
]
```

---

## 📂 Files Modified

### Backend (Livewire Components):
1. ✅ `app/Livewire/Dashboard/Index.php` - Admin/Waka dashboard
   - Added 11 public properties for material stats
   - Added `loadMaterialStats()` method
   - Added `prepareMaterialChartData()` method
   - Import `TeachingMaterial` model

2. ✅ `app/Livewire/Dashboard/GuruIndex.php` - Guru dashboard
   - Added 10 public properties for my material stats
   - Added `loadMyMaterialStats()` method
   - Added `prepareMyMaterialChartData()` method
   - Import `TeachingMaterial` model

3. ✅ `app/Livewire/Dashboard/KepsekIndex.php` - Kepsek dashboard
   - Added `getMaterialStats()` method
   - Pass `$materialStats` to view
   - Import `TeachingMaterial` model

### Frontend (Blade Views):
❌ **NOT YET IMPLEMENTED** - Views need to be updated to display the stats

---

## 📊 Statistics Examples

### Admin/Waka Dashboard Stats:
```php
$this->totalMaterials = 50
$this->materialsApproved = 35
$this->materialsPending = 10
$this->materialsRejected = 3
$this->materialsDraft = 2
$this->totalDownloads = 450
$this->totalViews = 1250

$this->categoryCoverage = [
    ['category' => 'modul_ajar', 'label' => 'Modul Ajar', 'count' => 12],
    ['category' => 'atp', 'label' => 'Alur Tujuan Pembelajaran (ATP)', 'count' => 8],
    ...
]

$this->topContributors = [
    ['teacher_id' => 5, 'material_count' => 15, 'creator' => User{name: 'Pak Budi'}],
    ...
]

$this->materialChartData = [
    'labels' => ['Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan'],
    'data' => [3, 5, 7, 8, 6, 10]
]
```

### Guru Dashboard Stats:
```php
$this->myMaterialsTotal = 12
$this->myMaterialsDraft = 2
$this->myMaterialsPending = 1
$this->myMaterialsApproved = 8
$this->myMaterialsRejected = 1
$this->myTotalDownloads = 85
$this->myTotalViews = 320

$this->myMostDownloaded = TeachingMaterial{
    title: 'Modul Ajar Matematika Kelas X',
    download_count: 45
}

$this->myCategoryCoverage = [
    ['category' => 'modul_ajar', 'label' => 'Modul Ajar', 'count' => 5],
    ['category' => 'lkpd', 'label' => 'LKPD', 'count' => 3],
]
```

### Kepsek Dashboard Stats:
```php
$materialStats = [
    'total' => 50,
    'approved' => 35,
    'pending' => 10,
    'rejected' => 5,
    'category_coverage_percent' => 75.0, // 15 out of 20 categories filled
    'dimensions' => [
        'beriman' => 20,
        'kebinekaan' => 15,
        'gotong_royong' => 18,
        'mandiri' => 22,
        'bernalar_kritis' => 19,
        'kreatif' => 16,
        'numerasi' => 14,
        'literasi' => 25,
    ],
    'monthly_chart' => [
        'labels' => ['Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan'],
        'data' => [5, 7, 8, 9, 6, 10]
    ]
]
```

---

## 🎨 UI/UX Design (To Be Implemented)

### Card Design:
```
┌─────────────────────────────┐
│  📊 Total Perangkat Ajar    │
│                             │
│         50                  │
│  ───────────────────────    │
│  ✅ 35  ⏳ 10  ❌ 3  📝 2   │
└─────────────────────────────┘
```

### Chart Design:
- **Upload Trend**: Line chart dengan gradient fill
- **Category Coverage**: Horizontal bar chart
- **Dimension Coverage**: Radar chart (8 sisi untuk 8 dimensi)
- **Top Contributors**: Leaderboard dengan avatar

### Color Scheme:
- Approved: Green (#10b981)
- Pending: Yellow (#f59e0b)
- Rejected: Red (#ef4444)
- Draft: Gray (#6b7280)
- Downloads: Blue (#3b82f6)
- Views: Purple (#8b5cf6)

---

## ✅ What's Working (Backend Only)

- ✅ Data aggregation methods implemented
- ✅ Chart data preparation methods implemented
- ✅ Proper query scoping by academic year
- ✅ Efficient database queries dengan grouping & aggregations
- ✅ All statistics calculated correctly
- ✅ Error handling untuk empty data (null coalescing)

---

## ❌ What's Not Yet Implemented (Frontend)

- ❌ **View Templates** - Blade views belum diupdate
- ❌ **Chart Rendering** - Chart.js integration belum ditambahkan
- ❌ **Statistics Cards** - Card components belum dirender
- ❌ **Responsive Design** - Mobile layout belum diatur
- ❌ **Export Features** - Export charts to image/PDF
- ❌ **Drill-down Links** - Click category untuk filter materials

---

## 🚀 Next Steps (Pending)

### 1. Update Blade Views
- [ ] `resources/views/livewire/dashboard/index.blade.php` - Admin/Waka view
- [ ] `resources/views/livewire/dashboard/guru-index.blade.php` - Guru view
- [ ] `resources/views/livewire/dashboard/kepsek-index.blade.php` - Kepsek view

### 2. Add Chart.js Integration
- [ ] Install Chart.js (or use existing)
- [ ] Create chart rendering components
- [ ] Add Alpine.js for interactivity

### 3. Design Statistics Cards
- [ ] Create reusable card component
- [ ] Add icons (Heroicons or Font Awesome)
- [ ] Responsive grid layout

### 4. Additional Features
- [ ] Click-through to filtered material list
- [ ] Export charts as PNG/PDF
- [ ] Print-friendly dashboard
- [ ] Real-time updates (Livewire polling)

---

## 📝 Usage Example (After Frontend Implementation)

### Admin Accessing Dashboard:
1. Login sebagai Admin/Waka
2. Dashboard otomatis load dengan statistik Perangkat Ajar
3. Melihat total materials: 50 (35 approved, 10 pending, 3 rejected, 2 draft)
4. Melihat top contributor: Pak Budi dengan 15 materials
5. Melihat upload trend: Naik dari 3 menjadi 10 per bulan
6. Klik kategori "Modul Ajar" → Redirect ke material list dengan filter

### Guru Accessing Dashboard:
1. Login sebagai Guru
2. Dashboard menampilkan "My Materials": 12 total (8 approved, 1 pending, 2 draft, 1 rejected)
3. Melihat most downloaded: "Modul Ajar Matematika X" dengan 45 downloads
4. Melihat upload trend pribadi: Konsisten 2-3 materials per bulan
5. Motivasi untuk upload lebih banyak berdasarkan stats

### Kepsek Accessing Dashboard:
1. Login sebagai Kepsek
2. Dashboard monitoring: 35 materials approved dari total 50
3. Category coverage: 75% (15 dari 20 kategori sudah terisi)
4. Dimension coverage: Literasi paling banyak (25), Numerasi kurang (14)
5. Insight untuk fokus pada kategori & dimensi yang kurang

---

## 🔍 Performance Considerations

### Query Optimization:
- Use `when()` instead of `if()` for conditional queries
- Use `select()` for specific columns only
- Use `first()` instead of `get()->first()`
- Eager loading dengan `with()` untuk relationships

### Caching (Future):
```php
Cache::remember('dashboard_material_stats_' . $activeYear->id, 3600, function() {
    return $this->loadMaterialStats($activeYear);
});
```

### Pagination:
- Top contributors: Limit 5
- Category coverage: Limit 10
- Recent materials: Limit 5

---

## 🐛 Known Issues

None (backend only, no UI issues yet)

---

## 📞 Support

**Version**: 1.3.0  
**Module**: Dashboard Analytics - Perangkat Ajar  
**Status**: ✅ Backend Complete | ❌ Frontend Pending  
**Date**: 2026-07-25

**Related Files:**
- `app/Livewire/Dashboard/Index.php`
- `app/Livewire/Dashboard/GuruIndex.php`
- `app/Livewire/Dashboard/KepsekIndex.php`
- `app/Models/TeachingMaterial.php`

---

**Last Updated**: 2026-07-25  
**System**: SIMKUR SMK PGRI Blora  
**Feature**: Dashboard Analytics for Teaching Materials

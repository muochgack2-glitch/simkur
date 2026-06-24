# PDF Preview Modal - Complete Implementation

## ✅ Fitur yang Sudah Diimplementasikan

### 1. **Modal Preview dengan HTML**
- Modal full-screen dengan preview HTML (bukan PDF langsung)
- Paper size simulation (A4 / Letter)
- Orientation support (Portrait / Landscape)
- Scrollable preview untuk dokumen panjang

### 2. **Paper Format Selector**
User bisa pilih format kertas sebelum download:
- **A4**: 210 x 297 mm (standar internasional)
- **Letter**: 8.5 x 11 inch (standar US)

### 3. **Print Functionality**
- Tombol Print untuk print langsung dari browser
- Print-specific CSS untuk hasil optimal
- Hanya print area preview (tanpa button/modal)

### 4. **Export Types**
Tiga jenis export dengan preview:

#### a) **Kalender Tahunan** (Yearly)
- Grid 12 bulan dalam 2 kolom
- Weekend days dengan warna merah muda
- Legend kegiatan per bulan
- Orientation: Portrait

#### b) **Kalender Bulanan** (Monthly)
- Kalender besar 1 bulan
- Daftar kegiatan lengkap di bawah
- Weekend days dengan warna merah muda
- Orientation: Landscape

#### c) **Daftar Kegiatan** (List)
- Tabel lengkap semua kegiatan
- Filter berdasarkan semester/jenis
- Export ke PDF table format
- Orientation: Portrait

---

## 🎨 User Flow

```
1. User klik tombol "Export" (dropdown)
   ↓
2. Pilih salah satu:
   - Preview Kalender Tahunan
   - Preview Kalender Bulanan  
   - Preview Daftar Kegiatan
   ↓
3. Modal terbuka dengan preview HTML
   - Tampilan mirip PDF asli
   - Bisa scroll lihat semua halaman
   - Pilih paper size (A4/Letter)
   ↓
4. User punya 3 opsi:
   - [Tutup] → Close modal
   - [Print] → Print langsung dari browser
   - [Download PDF] → Generate & download PDF
```

---

## 📁 File Structure

```
app/
├── Livewire/
│   └── Activity/
│       └── PreviewExport.php         ← Modal component logic

resources/views/
├── livewire/
│   └── activity/
│       ├── index.blade.php           ← Export dropdown button
│       └── preview-export.blade.php  ← Modal UI
└── pdf/
    └── preview/
        ├── calendar-yearly.blade.php  ← HTML preview yearly
        ├── calendar-monthly.blade.php ← HTML preview monthly
        └── activity-list.blade.php    ← HTML preview list
```

---

## 🔧 Technical Implementation

### Component: `PreviewExport.php`

**Properties:**
```php
public $showModal = false;
public $exportType = 'yearly'; // yearly, monthly, list
public $year;
public $month;
public $paperSize = 'a4'; // a4, letter
public $orientation = 'portrait'; // portrait, landscape
public $previewData = [];
```

**Methods:**
- `openPreview($type, $params)` - Open modal dengan tipe tertentu
- `loadPreviewData()` - Load data untuk preview
- `downloadPdf()` - Redirect ke PDF download dengan params
- `generateMonthCalendar()` - Generate calendar grid (sama dengan ExportPdfService)

### Alpine.js Integration

**Dropdown Toggle:**
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Export</button>
    <div x-show="open" @click.away="open = false">
        <!-- Dropdown menu -->
    </div>
</div>
```

**Event Dispatch:**
```javascript
@click="$dispatch('openPreview', { 
    type: 'yearly' 
})"
```

### Livewire Listeners

```php
protected $listeners = ['openPreview'];
```

---

## 🎨 UI/UX Features

### 1. **Modal Header**
- Title dinamis berdasarkan export type
- Paper size & orientation indicator
- Close button (X)

### 2. **Modal Body**
- Background abu-abu (simulasi meja)
- Paper putih di tengah dengan shadow
- Ukuran paper sesuai pilihan (A4/Letter)
- Scrollable untuk dokumen panjang

### 3. **Paper Size Selector**
```html
<select wire:model.live="paperSize">
    <option value="a4">A4 (210 x 297 mm)</option>
    <option value="letter">Letter (8.5 x 11 in)</option>
</select>
```

### 4. **Action Buttons**
- **Tutup** (gray) - Close modal
- **Print** (dark gray) - Print functionality
- **Download PDF** (blue, primary) - Download action

---

## 📄 Preview Templates

### Yearly Preview (`calendar-yearly.blade.php`)
```html
- Header dengan school name & tahun pelajaran
- Grid 2 kolom x 6 baris = 12 bulan
- Calendar table per bulan (Sen-Min)
- Weekend cells: bg-red-50
- Activity bars (colored)
- Legend kegiatan di bawah setiap bulan
```

### Monthly Preview (`calendar-monthly.blade.php`)
```html
- Header dengan school name & bulan
- Full calendar table (7 kolom)
- Weekend cells: bg-red-50
- Activity tags dengan warna
- Daftar lengkap kegiatan di bawah
```

### List Preview (`activity-list.blade.php`)
```html
- Header dengan school name
- Table format
  - No | Start | End | Name | Type | Semester
- Zebra striping (alternate row colors)
- Summary total kegiatan
```

---

## 🖨️ Print Functionality

### CSS for Print
```css
@media print {
    body * {
        visibility: hidden;
    }
    .print-preview, .print-preview * {
        visibility: visible;
    }
    .print-preview {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        box-shadow: none !important;
    }
}
```

**How it works:**
1. User klik tombol "Print"
2. Browser print dialog muncul
3. Hanya konten `.print-preview` yang di-print
4. Button, modal, dll disembunyikan

---

## 📊 Weekend Integration

Preview templates menggunakan `$day['isWeekend']` dari service:

```php
// ExportPdfService & PreviewExport
$weekendDays = Setting::getValue('weekend_days', ['saturday', 'sunday']);
$dayName = strtolower($current->format('l'));
$isWeekend = in_array($dayName, $weekendDays);
```

**Visual Indicator:**
- Weekend days: `bg-red-50` (light red)
- Weekdays: `bg-white`
- Other month: `bg-gray-50`

---

## 🚀 Usage Examples

### Open Preview from Button
```html
<button @click="$dispatch('openPreview', { 
    type: 'yearly' 
})">
    Preview Kalender Tahunan
</button>
```

### Open with Parameters
```html
<button @click="$dispatch('openPreview', { 
    type: 'monthly', 
    params: { year: 2026, month: 6 } 
})">
    Preview Juni 2026
</button>
```

### Download PDF with Format
```php
public function downloadPdf()
{
    return redirect()->route('activities.export', [
        'type' => $this->exportType,
        'format' => $this->paperSize,
        'year' => $this->year,
        'month' => $this->month,
    ]);
}
```

---

## ✨ Benefits

### For Users:
1. **No Wasted Paper** - Preview sebelum print/download
2. **Choose Format** - A4 atau Letter sesuai printer
3. **Quick Check** - Lihat konten tanpa generate PDF
4. **Print Option** - Print langsung dari browser
5. **Stay in Context** - Modal, tidak perlu buka tab baru

### For Developers:
1. **Reusable Component** - Satu component untuk 3 jenis export
2. **HTML Preview** - Lebih cepat dari PDF generation
3. **Easy Maintain** - Template terpisah per export type
4. **Consistent Logic** - generateMonthCalendar() dipakai di PDF & Preview

---

## 🔄 Next Steps (Optional Enhancements)

- [ ] Add zoom controls in modal
- [ ] Add page navigation for multi-page documents
- [ ] Cache preview data untuk performa
- [ ] Add "Share via Email" button
- [ ] Custom paper sizes (legal, A3, etc.)
- [ ] Dark mode support untuk preview
- [ ] Keyboard shortcuts (ESC close, Ctrl+P print)

---

## 📝 Testing Checklist

- [ ] Dropdown Export berfungsi
- [ ] Modal terbuka dengan benar
- [ ] Preview yearly tampil dengan 12 bulan
- [ ] Preview monthly tampil dengan calendar + list
- [ ] Preview list tampil dengan table
- [ ] Weekend days berwarna merah muda
- [ ] Paper size selector mengubah ukuran preview
- [ ] Print button membuka print dialog
- [ ] Download PDF button generate PDF
- [ ] Close button menutup modal
- [ ] Click backdrop menutup modal
- [ ] Responsive di mobile (optional)

---

**Status**: ✅ **COMPLETE**  
**Date**: 2026-06-23  
**Features**: Modal Preview, HTML Preview, Paper Format Selector, Print Button, Weekend Integration

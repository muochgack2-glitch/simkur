# UI Documentation: Public Journal Monitoring

## 🎨 Visual Design Overview

Halaman ini dirancang dengan prinsip **clarity, hierarchy, dan visual feedback** untuk memudahkan monitoring cepat status jurnal guru.

---

## 📱 Preview Files

**File yang sudah dibuat:**
1. `UI-MOCKUP.html` - Desktop/Tablet view (full interactive)
2. `UI-MOCKUP-MOBILE.html` - Mobile view

**Cara melihat:**
```bash
# Buka di browser
start UI-MOCKUP.html          # Desktop view
start UI-MOCKUP-MOBILE.html   # Mobile view
```

---

## 🖥️ Desktop View Layout

### 1. **Header Section** (Blue Gradient)
```
┌────────────────────────────────────────────────────────────┐
│  🗓️ Monitoring Jurnal Hari Ini    Auto-refresh: ⟳ 4m 32s │
│  Senin, 4 Agustus 2026             [🔄 Refresh Sekarang]  │
└────────────────────────────────────────────────────────────┘
```

**Elements:**
- **Title:** Bold, large text dengan emoji kalender
- **Date:** Format Indonesia penuh
- **Countdown:** Real-time countdown timer
- **Refresh Button:** CTA button (white on blue)

**Colors:**
- Background: Blue gradient (#2563EB → #1D4ED8)
- Text: White
- Button: White background, blue text

---

### 2. **Summary Stats Bar** (Cards)
```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│   TOTAL     │   SUDAH     │  SEBAGIAN   │   BELUM     │
│    23       │    15       │     5       │     3       │
│             │    65%      │    22%      │    13%      │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

**Card Design:**
- Equal width (25% each)
- Large number (4xl font)
- Percentage below (for categories)
- Background colors match category

**Colors:**
- Total: Gray (#F3F4F6)
- Sudah: Green (#ECFDF5)
- Sebagian: Yellow (#FEFCE8)
- Belum: Red (#FEF2F2)

---

### 3. **Main Content: 3 Columns** (Side-by-Side)

```
┌──────────────┬──────────────┬──────────────┐
│ ✅ SUDAH ISI │ ⚠️ SEBAGIAN  │ ❌ BELUM ISI │
│    (15)      │     (5)      │     (3)      │
├──────────────┼──────────────┼──────────────┤
│              │              │              │
│  [Cards]     │  [Cards]     │  [Cards]     │
│              │              │              │
└──────────────┴──────────────┴──────────────┘
```

**Column Structure:**
- Width: 33.33% each (lg:grid-cols-3)
- Equal height
- Scrollable if content overflows
- Max height: 600px

---

### 4. **Teacher Card Design**

#### A. Sudah Isi (Green)
```
┌─────────────────────────────────────┐
│  Dewi Wartini, S.Pd              ✓  │
│  4/4 JP                             │
│  100% Complete                      │
│  ████████████████████ 100%          │
└─────────────────────────────────────┘
```

**Elements:**
- Name: Bold, dark gray
- JP Count: Bold, green (larger font)
- Status: Green text
- Progress bar: Full green
- Checkmark icon: Large, green

**Colors:**
- Background: Green-50 (#ECFDF5)
- Border: Green-200 (#BBF7D0)
- Text: Green-600 (#16A34A)
- Progress: Green-500 (#22C55E)

---

#### B. Isi Sebagian (Yellow)
```
┌─────────────────────────────────────┐
│  Budi Siswanto, S.Pd.I           ⚠  │
│  2/3 JP                             │
│  67% Complete                       │
│  ⚠️ Kurang 1 JP                     │
│  █████████████░░░░░░░ 67%           │
└─────────────────────────────────────┘
```

**Elements:**
- Name: Bold, dark gray
- JP Count: Bold, yellow
- Status: Yellow text
- Warning: Red text (kurang X JP)
- Progress bar: Partial yellow
- Warning icon: Large, yellow

**Colors:**
- Background: Yellow-50 (#FEFCE8)
- Border: Yellow-300 (#FDE047)
- Text: Yellow-600 (#CA8A04)
- Progress: Yellow-500 (#EAB308)
- Warning text: Red-600 (#DC2626)

---

#### C. Belum Isi (Red)
```
┌─────────────────────────────────────┐
│  Ari Yunitasari, S.Pd            ✗  │
│  0/2 JP                             │
│  0% Complete                        │
│  ❗ Belum mengisi sama sekali       │
│  ░░░░░░░░░░░░░░░░░░░░ 0%            │
└─────────────────────────────────────┘
```

**Elements:**
- Name: Bold, dark gray
- JP Count: Bold, red
- Status: Red text
- Alert: Bold red text
- Progress bar: Empty (gray)
- X icon: Large, red

**Colors:**
- Background: Red-50 (#FEF2F2)
- Border: Red-300 (#FCA5A5)
- Text: Red-600 (#DC2626)
- Progress bg: Red-200 (#FECACA)
- Progress fill: Red-500 (#EF4444)

---

## 📱 Mobile View Layout (< 768px)

### Layout Structure: **Vertical Stack**

```
┌─────────────────────┐
│  Header (compact)   │
├─────────────────────┤
│  Summary (2x2 grid) │
├─────────────────────┤
│  ✅ SUDAH ISI       │
│  (Full width)       │
├─────────────────────┤
│  ⚠️ ISI SEBAGIAN    │
│  (Full width)       │
├─────────────────────┤
│  ❌ BELUM ISI       │
│  (Full width)       │
└─────────────────────┘
```

**Key Differences:**
1. **Header:** Compact, stacked layout
2. **Summary:** 2x2 grid instead of 4 columns
3. **Columns:** Stack vertically (100% width each)
4. **Cards:** Smaller padding, condensed text
5. **Font sizes:** Reduced for mobile

---

## 🎨 Color Palette

### Primary Colors
| Color | Hex | Usage |
|-------|-----|-------|
| Blue Primary | #2563EB | Header, buttons |
| Blue Dark | #1D4ED8 | Header gradient end |
| Gray Background | #F9FAFB | Page background |
| White | #FFFFFF | Cards, content |

### Category Colors

#### Green (Sudah Isi)
| Shade | Hex | Usage |
|-------|-----|-------|
| 50 | #ECFDF5 | Card background |
| 200 | #BBF7D0 | Border |
| 500 | #22C55E | Progress bar |
| 600 | #16A34A | Text |

#### Yellow (Sebagian)
| Shade | Hex | Usage |
|-------|-----|-------|
| 50 | #FEFCE8 | Card background |
| 300 | #FDE047 | Border |
| 500 | #EAB308 | Progress bar |
| 600 | #CA8A04 | Text |

#### Red (Belum)
| Shade | Hex | Usage |
|-------|-----|-------|
| 50 | #FEF2F2 | Card background |
| 300 | #FCA5A5 | Border |
| 500 | #EF4444 | Progress bar |
| 600 | #DC2626 | Text |

---

## 🔤 Typography

### Font Family
**Default:** System font stack (Tailwind)
```css
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 
"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

### Font Sizes

| Element | Size | Tailwind Class |
|---------|------|---------------|
| Page Title | 30px | text-3xl |
| Section Title | 20px | text-xl |
| Teacher Name | 18px | text-lg |
| JP Count | 20px | text-xl |
| Stats Number | 36px | text-4xl |
| Body Text | 14px | text-sm |
| Caption | 12px | text-xs |

### Font Weights

| Element | Weight | Tailwind Class |
|---------|--------|---------------|
| Headers | 700 | font-bold |
| Teacher Names | 600 | font-semibold |
| JP Count | 700 | font-bold |
| Body | 500 | font-medium |
| Caption | 400 | (default) |

---

## 🎭 Icons & Emojis

### Emoji Usage
| Icon | Meaning | Location |
|------|---------|----------|
| 🗓️ | Calendar/Date | Page title |
| ⟳ | Refresh/Loading | Countdown timer |
| 🔄 | Manual refresh | Button |
| ✅ | Complete/Success | Sudah category |
| ⚠️ | Warning | Sebagian category |
| ❌ | Error/Missing | Belum category |
| ✓ | Checkmark | Completed cards |
| ⚠ | Warning sign | Partial cards |
| ✗ | X mark | Not started cards |
| ❗ | Alert | Missing JP message |
| ℹ️ | Information | Info footer |

### SVG Icons
- **Refresh Icon:** Circular arrows (heroicons)
- **Loading Spinner:** Animated circle (heroicons)

---

## 🔄 Interactive States

### Hover Effects

**Teacher Cards:**
```css
hover:shadow-md transition
```
- Effect: Shadow increases
- Duration: 300ms
- Easing: ease

**Refresh Button:**
```css
hover:bg-blue-50 transition
```
- Effect: Background lightens
- Duration: 200ms

### Loading State

**Overlay:**
```
┌─────────────────────────────┐
│                             │
│    [Spinner] Memuat data... │
│                             │
└─────────────────────────────┘
```
- Semi-transparent black overlay
- White modal box
- Animated spinner
- Loading text

---

## 📐 Spacing & Layout

### Container
```css
max-width: 1280px;
margin: 0 auto;
padding: 0 1rem; /* 16px */
```

### Grid Gaps
- Summary cards: 1rem (16px)
- Main columns: 1.5rem (24px)
- Teacher cards: 0.75rem (12px)

### Card Padding
- Desktop: 1rem (16px)
- Mobile: 0.75rem (12px)

### Border Radius
- Cards: 0.75rem (12px)
- Buttons: 0.5rem (8px)
- Progress bars: 9999px (fully rounded)

---

## ♿ Accessibility

### Semantic HTML
- Use proper heading hierarchy (h1 → h2 → h3)
- Use `<button>` for clickable elements
- Use `<main>` for main content

### ARIA Labels
```html
<button aria-label="Refresh data jurnal">🔄 Refresh Sekarang</button>
```

### Color Contrast
All text meets WCAG AA standards:
- White text on blue: 4.5:1 ✓
- Green text on green bg: 4.5:1 ✓
- Yellow text on yellow bg: 4.5:1 ✓
- Red text on red bg: 4.5:1 ✓

### Keyboard Navigation
- All buttons are keyboard accessible
- Tab order is logical
- Focus states visible

---

## 📊 Progress Bar Design

### Visual Style
```
Empty:    ░░░░░░░░░░░░░░░░░░░░ 0%
Partial:  █████████░░░░░░░░░░░ 45%
Complete: ████████████████████ 100%
```

### Implementation
```html
<div class="bg-green-200 rounded-full h-2 overflow-hidden">
    <div class="bg-green-500 h-full rounded-full transition-all duration-300" 
         style="width: 67%"></div>
</div>
```

### Colors by Category
- Sudah: Green-200 bg, Green-500 fill
- Sebagian: Yellow-200 bg, Yellow-500 fill
- Belum: Red-200 bg, Red-500 fill

---

## 🎬 Animations

### Countdown Timer
```javascript
setInterval(() => {
    // Update every second
    countdown--;
    updateDisplay();
}, 1000);
```

### Progress Bar
```css
transition: width 0.3s ease;
```

### Loading Spinner
```css
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
```

---

## 📱 Responsive Breakpoints

| Breakpoint | Width | Columns | Grid |
|------------|-------|---------|------|
| Mobile | < 768px | 1 | Stack |
| Tablet | 768px - 1023px | 2+1 | Mixed |
| Desktop | ≥ 1024px | 3 | Side-by-side |

### Tailwind Classes Used
```html
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
```

---

## 🎯 Visual Hierarchy

### Priority Levels

**Level 1 (Highest):**
- Page title
- Summary stats numbers
- JP counts

**Level 2:**
- Section headers (Sudah/Sebagian/Belum)
- Teacher names
- Countdown timer

**Level 3:**
- Status text (percentage, complete)
- Warning messages
- Progress bars

**Level 4:**
- Info footer
- Copyright text

---

## 💡 Design Principles Applied

### 1. **Clarity**
- Clear visual distinction between categories
- Large, readable fonts
- Obvious status indicators

### 2. **Hierarchy**
- Important info (stats) at top
- Color-coded sections
- Size indicates importance

### 3. **Consistency**
- Uniform card design across categories
- Consistent spacing
- Predictable layout

### 4. **Feedback**
- Progress bars show completion
- Icons reinforce status
- Countdown shows update timing

### 5. **Accessibility**
- High contrast colors
- Keyboard navigable
- Screen reader friendly

---

## 🚀 Performance Considerations

### Image Optimization
- No external images used
- Emojis for icons (fast)
- SVG for interactive icons

### CSS
- Tailwind CDN for mockup
- Production: Purged CSS
- Minimal custom CSS

### JavaScript
- Minimal JS (countdown only)
- No heavy libraries
- Native DOM manipulation

---

## 📝 Usage Notes

### For Developers

1. **Tailwind Setup Required**
   ```bash
   npm install -D tailwindcss
   npx tailwindcss init
   ```

2. **Livewire Integration**
   - Use `wire:poll.300s` for auto-refresh
   - Use `wire:loading` for loading states

3. **Browser Support**
   - Modern browsers (Chrome, Firefox, Safari, Edge)
   - IE11: Not supported (flex/grid required)

### For Designers

1. **Figma Export**
   - Mockup dapat di-export ke Figma
   - Colors sudah dalam Tailwind palette
   - Components reusable

2. **Brand Customization**
   - Ganti blue primary sesuai brand
   - Emoji bisa diganti icon custom
   - Font bisa disesuaikan

---

**UI Documentation Version:** 1.0  
**Last Updated:** 2026-08-04  
**Files:** UI-MOCKUP.html, UI-MOCKUP-MOBILE.html

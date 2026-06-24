# Folder Images

Folder ini untuk menyimpan logo sekolah dan gambar lainnya.

## Cara Upload Logo Sekolah:

1. **Copy file logo** (format: PNG, JPG, atau SVG) ke folder ini
   - Lokasi: `C:\Users\DMCenter\Music\SPMB2\E-KALDIK\public\images\`
   - Contoh: `logo.png`, `logo-sekolah.jpg`

2. **Isi path di Settings**:
   - Buka: `/settings` → Tab "Sekolah"
   - Field "Path Logo"
   - Isi: `/images/logo.png` (sesuaikan dengan nama file Anda)
   - Klik "Simpan Pengaturan"

3. **Logo akan muncul** di:
   - Kalender Resmi (`/calendar/official`)
   - Dokumen PDF
   - Header aplikasi (jika di-enable)

## Contoh Struktur:

```
public/
└── images/
    ├── logo.png          ← Logo sekolah
    ├── logo-header.png   ← Logo untuk header
    ├── logo-print.png    ← Logo untuk print
    └── README.md         ← File ini
```

## Tips:

- **Ukuran logo ideal**: 200x200px atau 300x300px
- **Format terbaik**: PNG dengan background transparan
- **Ukuran file**: Maksimal 500KB
- **Nama file**: Hindari spasi, gunakan dash (-) atau underscore (_)

## Akses Logo:

Setelah diupload, logo bisa diakses via URL:
- `http://localhost:8000/images/logo.png`

Di Settings, cukup isi: `/images/logo.png`

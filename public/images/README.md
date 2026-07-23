# Logo Folder

## Cara Mengganti Logo

1. Siapkan file logo sekolah dengan format:
   - **PNG** (recommended, dengan background transparan)
   - **JPG** (untuk logo dengan background)
   - **SVG** (untuk logo vector)

2. Rename file logo menjadi `logo.png` (atau `logo.jpg` / `logo.svg`)

3. Upload/copy file ke folder ini: `public/images/`

4. Logo akan otomatis muncul di:
   - Halaman login
   - Navigasi header (kiri atas)

## Prioritas Logo

Sistem akan mencari logo dengan urutan:
1. `logo.png` (prioritas utama)
2. `logo.jpg` (jika .png tidak ada)
3. `logo.svg` (jika .jpg tidak ada)
4. SVG default (jika tidak ada logo sama sekali)

## Rekomendasi

- Ukuran: 512x512 pixels atau 1024x1024 pixels
- Format: PNG dengan background transparan
- File size: < 500KB untuk performa optimal

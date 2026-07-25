# 📚 Modul Perangkat Ajar - SIMKUR SMK PGRI Blora

## ✅ STATUS: READY FOR MIGRATION

Modul Perangkat Ajar telah dibuat lengkap dengan struktur database, models, Livewire components, dan views.

---

## 📋 Fitur yang Telah Diimplementasi

### 1. **Database Structure**
- ✅ Tabel `teaching_materials` - Menyimpan data perangkat ajar
- ✅ Tabel `teaching_material_shares` - Menyimpan data sharing perangkat ajar
- ✅ Tabel `teaching_material_comments` - Menyimpan komentar pada perangkat ajar
- ✅ Relasi lengkap: Subject, AcademicYear, Creator, Approver, Comments, Shares

### 2. **Models**
- ✅ `TeachingMaterial.php` - Model utama dengan relasi lengkap
- ✅ `TeachingMaterialShare.php` - Model untuk sharing
- ✅ `TeachingMaterialComment.php` - Model untuk komentar
- ✅ Helper methods: `getCategoryLabelAttribute()`, `getStatusLabelAttribute()`, `getSelectedDimensionsAttribute()`, dll.
- ✅ Scopes: `approved()`, `pendingApproval()`, `byCategory()`, `public()`

### 3. **Livewire Components**
- ✅ `TeachingMaterial/Index.php` - Daftar perangkat ajar dengan filter & search
- ✅ `TeachingMaterial/Create.php` - Form upload perangkat ajar baru
- ✅ `TeachingMaterial/Edit.php` - Form edit perangkat ajar
- ✅ `TeachingMaterial/Show.php` - Detail perangkat ajar dengan komentar

### 4. **Views**
- ✅ `teaching-material/index.blade.php` - List view dengan filter & grouped by category
- ✅ `teaching-material/create.blade.php` - Upload form lengkap dengan file/link upload
- ✅ `teaching-material/edit.blade.php` - Edit form (hanya untuk status draft)
- ✅ `teaching-material/show.blade.php` - Detail view dengan file preview & comments

### 5. **Routes & Navigation**
- ✅ Routes: `/teaching-materials`, `/teaching-materials/create`, `/teaching-materials/{id}`, `/teaching-materials/{id}/edit`
- ✅ Menu: "📚 Perangkat Ajar" (visible for: guru, waka, kepsek, admin)
- ✅ Authorization: Middleware `check.role:admin,waka_kurikulum,kepala_sekolah,guru`

### 6. **Features**
- ✅ 12 kategori perangkat ajar:
  - **Perencanaan:** ATP, CP, Modul Ajar (lengkap dengan LKPD, Asesmen, Rubrik)
  - **Media & Bahan Ajar:** Buku Teks, Video Pembelajaran, Presentasi/Infografis, Bahan Bacaan
  - **Asesmen Mandiri:** Bank Soal, Rubrik Penilaian Umum
  - **Kokurikuler SMK:** Job Sheet, Teaching Factory, PKL
- ✅ Upload file (PDF, DOCX, PPTX, XLSX, JPG, PNG, MP4) atau link eksternal (YouTube, Google Drive, dll)
- ✅ 8 Dimensi Profil Lulusan (Kurikulum Merdeka 2025/2026)
- ✅ Approval workflow (draft → pending_approval → approved/rejected)
- ✅ Filter by: Category, Subject, Grade, Status, Academic Year, 8 Dimensi
- ✅ Search by: Title, Description, Tags
- ✅ Komentar system
- ✅ Download & view tracking
- ✅ Authorization: Guru hanya bisa edit/hapus dokumen sendiri (draft only)

---

## 🚀 Cara Install

### **Step 1: Run Migrations**

```bash
php artisan migrate
```

File migration yang akan dijalankan:
- `2026_07_24_100000_create_teaching_materials_table.php`
- `2026_07_24_100001_create_teaching_material_shares_table.php`
- `2026_07_24_100002_create_teaching_material_comments_table.php`

### **Step 2: Clear Caches**

```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### **Step 3: Create Storage Symlink (jika belum)**

```bash
php artisan storage:link
```

### **Step 4: Set Permissions untuk Storage**

```bash
# Windows (PowerShell as Admin)
icacls storage\app\teaching-materials /grant Everyone:F /T

# Linux/Mac
chmod -R 775 storage/app/teaching-materials
chown -R www-data:www-data storage/app/teaching-materials
```

### **Step 5: Verify Routes**

```bash
php artisan route:list --name=teaching-materials
```

Expected output:
```
teaching-materials.index   GET    teaching-materials
teaching-materials.create  GET    teaching-materials/create
teaching-materials.show    GET    teaching-materials/{id}
teaching-materials.edit    GET    teaching-materials/{id}/edit
```

---

## 🧪 Testing Instructions

### **Test Scenario 1: Login sebagai Guru**

```
Username: suseno  (atau guru lain)
Password: password
Role: Guru
```

**Expected:**
- Menu "📚 Perangkat Ajar" visible in navbar
- Can access `/teaching-materials`

### **Test Scenario 2: Upload Perangkat Ajar Baru**

1. Click "📚 Perangkat Ajar" menu
2. Click "Upload Perangkat Ajar" button
3. Fill form:
   - **Judul:** "Modul Ajar: Sistem Persamaan Linear Pertemuan 1"
   - **Kategori:** Modul Ajar
   - **Deskripsi:** "Modul Ajar lengkap dengan LKPD, Asesmen, dan Rubrik"
   - **Mata Pelajaran:** Matematika
   - **Kelas:** X
   - **Fase:** E
   - **Semester:** 2
   - **Tahun Ajaran:** 2025/2026
   - **Upload Type:** Upload File
   - **File:** Upload PDF file (contoh: modul_ajar_sample.pdf)
   - **8 Dimensi:** Check "Bernalar Kritis", "Kreatif", "Numerasi"
   - **Tags:** "aljabar, diferensiasi"
4. Click "💾 Simpan sebagai Draft" atau "📤 Submit untuk Approval"

**Expected:**
- Success message: "Perangkat ajar berhasil disimpan!" atau "Perangkat ajar berhasil disubmit untuk approval!"
- Redirect to index
- New material appears in list

### **Test Scenario 3: View Perangkat Ajar**

1. Navigate to "📚 Perangkat Ajar"
2. Click "👁️ Lihat" on any material

**Expected:**
- Detail page shows:
  - Status badge (Draft/Pending/Approved/Rejected)
  - Metadata (Category, Subject, Grade, Fase, Semester)
  - 8 Dimensi badges
  - File/Link dengan download button
  - Comments section
  - View & download count

### **Test Scenario 4: Edit Perangkat Ajar (Draft Only)**

1. Click "✏️ Edit" on material with status "Draft"
2. Modify title or description
3. Click "💾 Update"

**Expected:**
- Success message
- Changes saved
- Can only edit if status = draft

### **Test Scenario 5: Filter & Search**

1. Use filter by Category → "Modul Ajar"
2. Use filter by Subject → "Matematika"
3. Use filter by Grade → "X"
4. Use filter by 8 Dimensi → Check "Bernalar Kritis"
5. Use search → Type "Sistem Persamaan"

**Expected:**
- Results filtered correctly
- Pagination works
- Materials grouped by category

### **Test Scenario 6: Comments**

1. Open detail page of any material
2. Scroll to "Komentar" section
3. Type comment: "Bagus sekali modulnya!"
4. Click "💬 Kirim Komentar"

**Expected:**
- Comment appears in list
- Shows username and timestamp

---

## 📊 Database Schema

### **teaching_materials**
```
- id
- title
- description
- category (12 kategori)
- subject_id (FK)
- academic_year_id (FK)
- grade (X, XI, XII)
- phase (E, F)
- semester (1, 2)
- file_type (pdf, docx, pptx, xlsx, jpg, png, mp4, link)
- file_path
- file_size
- external_link
- dimension_1_beriman (boolean)
- dimension_2_kebinekaan (boolean)
- dimension_3_gotong_royong (boolean)
- dimension_4_mandiri (boolean)
- dimension_5_bernalar_kritis (boolean)
- dimension_6_kreatif (boolean)
- dimension_7_numerasi (boolean)
- dimension_8_literasi (boolean)
- tags (JSON)
- target_class_ids (JSON)
- is_public (boolean)
- download_count
- view_count
- status (draft, pending_approval, approved, rejected)
- approval_notes
- approved_by (FK)
- approved_at
- created_by (FK)
- updated_by (FK)
- timestamps
```

### **teaching_material_shares**
```
- id
- teaching_material_id (FK)
- shared_with_user_id (FK)
- shared_with_class_id (FK)
- can_edit (boolean)
- can_download (boolean)
- timestamps
```

### **teaching_material_comments**
```
- id
- teaching_material_id (FK)
- user_id (FK)
- comment
- timestamps
```

---

## 🔐 Authorization Rules

1. **Access Module:**
   - Admin ✅
   - Waka Kurikulum ✅
   - Kepala Sekolah ✅
   - Guru ✅
   - Siswa ❌

2. **View Materials:**
   - Admin ✅ (all materials)
   - Waka Kurikulum ✅ (all materials)
   - Kepala Sekolah ✅ (approved materials + own materials)
   - Guru ✅ (approved materials + own materials)

3. **Upload Material:**
   - All authorized roles ✅

4. **Edit/Delete Material:**
   - Admin ✅ (only draft)
   - Guru ✅ (only own materials with status draft)
   - Others ❌

5. **Approve/Reject:**
   - **BELUM DIIMPLEMENTASI** (akan dibuat di fase berikutnya)
   - Waka Kurikulum (planned)
   - Admin (planned)

---

## 📂 File Storage

Upload files disimpan di:
```
storage/app/teaching-materials/
├── atp/
├── cp/
├── modul-ajar/
├── buku-teks/
├── video-pembelajaran/
├── presentasi-infografis/
├── bahan-bacaan/
├── bank-soal/
├── rubrik-penilaian/
├── job-sheet/
├── teaching-factory/
└── pkl/
```

---

## 🎯 FASE 1 Scope (MVP) - COMPLETE ✅

**What's Included:**
- ✅ 12 kategori perangkat ajar (sesuai Kurikulum Merdeka 2025/2026)
- ✅ Upload file atau link eksternal
- ✅ 8 Dimensi Profil Lulusan (pengganti P5)
- ✅ Draft & Submit for approval workflow
- ✅ Filter & search (category, subject, grade, status, dimensi, tags)
- ✅ Komentar system
- ✅ Download & view tracking
- ✅ Authorization (guru hanya edit/delete draft sendiri)

**What's NOT Included (Future FASE 2):**
- ❌ Approval UI untuk Waka Kurikulum (tinggal buat component `Approval.php`)
- ❌ Dashboard analytics (statistics & coverage)
- ❌ Sharing mechanism (share to specific users/classes)
- ❌ Bulk operations (bulk approve, bulk delete)
- ❌ Export to Excel/PDF
- ❌ File preview (PDF viewer di browser)
- ❌ Notifikasi (email/in-app notification)

---

## 📂 Files Created

### **Migrations:**
- `database/migrations/2026_07_24_100000_create_teaching_materials_table.php`
- `database/migrations/2026_07_24_100001_create_teaching_material_shares_table.php`
- `database/migrations/2026_07_24_100002_create_teaching_material_comments_table.php`

### **Models:**
- `app/Models/TeachingMaterial.php`
- `app/Models/TeachingMaterialShare.php`
- `app/Models/TeachingMaterialComment.php`

### **Livewire Components:**
- `app/Livewire/TeachingMaterial/Index.php`
- `app/Livewire/TeachingMaterial/Create.php`
- `app/Livewire/TeachingMaterial/Edit.php`
- `app/Livewire/TeachingMaterial/Show.php`

### **Views:**
- `resources/views/livewire/teaching-material/index.blade.php`
- `resources/views/livewire/teaching-material/create.blade.php`
- `resources/views/livewire/teaching-material/edit.blade.php`
- `resources/views/livewire/teaching-material/show.blade.php`

### **Routes:**
- Updated `routes/web.php` (added teaching-materials routes)

### **Navigation:**
- Updated `resources/views/components/layouts/app.blade.php` (added menu "📚 Perangkat Ajar")

---

## ✨ Next Steps

1. **Run Migration:**
   ```bash
   php artisan migrate
   ```

2. **Test Login & Navigation:**
   - Login sebagai guru
   - Cek apakah menu "📚 Perangkat Ajar" muncul

3. **Test Upload:**
   - Upload sample perangkat ajar
   - Test file upload & link eksternal

4. **Test Workflow:**
   - Save as draft
   - Submit for approval
   - View detail
   - Edit draft
   - Add comment

5. **FASE 2 (Future):**
   - Buat component `Approval.php` untuk Waka Kurikulum
   - Buat dashboard analytics
   - Implement sharing mechanism
   - Add file preview untuk PDF
   - Add export functionality

---

## 🐛 Known Limitations (By Design - FASE 1)

1. No approval UI yet (status manually via DB)
2. No dashboard analytics
3. No sharing UI
4. No file preview (PDF viewer)
5. No export functionality
6. No notification system
7. File size limit: 100MB (configurable)

---

**Last Updated:** 2026-07-24  
**Status:** ✅ READY FOR MIGRATION & TESTING  
**Version:** FASE 1 MVP  
**Branding:** SIMKUR SMK PGRI Blora

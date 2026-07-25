# 📎 Panduan Lengkap: Sistem Lampiran Perangkat Ajar

**SIMKUR SMK PGRI Blora** | Version 1.2.0 | 2026-07-25

---

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Jenis Lampiran](#jenis-lampiran)
3. [User Flow](#user-flow)
4. [Upload File vs Link](#upload-file-vs-link)
5. [Fitur Utama](#fitur-utama)
6. [Permission & Authorization](#permission--authorization)
7. [Technical Details](#technical-details)
8. [Troubleshooting](#troubleshooting)

---

## 📚 Overview

Sistem Lampiran memungkinkan guru untuk melengkapi setiap Perangkat Ajar dengan **multiple attachments** (lampiran pendukung). Ini sangat penting untuk membuat perangkat ajar yang **komprehensif dan lengkap** sesuai standar Kurikulum Merdeka.

### 🎯 Tujuan
- ✅ Melengkapi perangkat ajar dengan file pendukung (LKPD, PPT, Video, Rubrik, dll)
- ✅ Mempermudah sharing materi pembelajaran antar guru
- ✅ Centralized repository untuk semua dokumen pembelajaran
- ✅ Tracking download & popularity per lampiran

### ✨ Keunggulan
- 📁 **Upload fleksibel**: File lokal ATAU link eksternal (YouTube, Google Drive, dll)
- 🎨 **9 jenis lampiran** yang specific untuk kebutuhan pembelajaran
- 📦 **Bulk download**: Download semua lampiran sekaligus dalam format ZIP
- 🎯 **Primary marking**: Tandai lampiran utama yang paling penting
- 🔐 **Permission-based**: Role & ownership-based access control
- 📊 **Tracking**: Monitor download count per lampiran

---

## 📂 Jenis Lampiran

Sistem mendukung **9 jenis lampiran** yang spesifik untuk pembelajaran:

| Icon | Jenis | Deskripsi | Contoh Use Case |
|------|-------|-----------|----------------|
| 📄 | **Dokumen Utama** | File utama tambahan | Document penjelasan lengkap, Handout |
| 📝 | **LKPD** | Lembar Kerja Peserta Didik | Worksheet untuk latihan siswa |
| 📊 | **Presentasi/Slide** | PowerPoint atau slide | Slide untuk mengajar di kelas |
| 🎬 | **Video Pembelajaran** | Video tutorial/penjelasan | Link YouTube, video rekaman guru |
| 📋 | **Instrumen Asesmen** | Soal ujian, kuis, tugas | Soal UTS, kuis harian, project assignment |
| 📏 | **Rubrik Penilaian** | Kriteria penilaian | Rubrik untuk project, rubrik penilaian sikap |
| 🔑 | **Kunci Jawaban** | Jawaban untuk soal/tugas | Answer key untuk LKPD, soal ujian |
| 📚 | **Bahan Bacaan** | Materi bacaan tambahan | Artikel, modul bacaan, e-book |
| 📎 | **Lainnya** | File pendukung lainnya | Apapun yang tidak masuk kategori di atas |

### 📝 Rekomendasi Kelengkapan

Untuk membuat perangkat ajar yang **LENGKAP**, disarankan memiliki:

#### **MINIMAL (untuk Modul Ajar):**
- ✅ **Dokumen Utama**: Modul Ajar lengkap (PDF)
- ✅ **LKPD**: Worksheet untuk siswa
- ✅ **Rubrik Penilaian**: Kriteria penilaian

#### **OPTIMAL (untuk Modul Ajar):**
- ✅ **Dokumen Utama**: Modul Ajar lengkap (PDF)
- ✅ **LKPD**: Worksheet untuk siswa
- ✅ **Presentasi/Slide**: PPT untuk mengajar
- ✅ **Video Pembelajaran**: Video penjelasan materi
- ✅ **Instrumen Asesmen**: Soal evaluasi
- ✅ **Rubrik Penilaian**: Kriteria penilaian
- ✅ **Kunci Jawaban**: Answer key untuk LKPD & Asesmen
- ✅ **Bahan Bacaan**: Materi bacaan tambahan (optional)

#### **Untuk Kategori Lain:**
- **CP/ATP/KKTP/PROTA/PROSEM**: Biasanya cukup 1 dokumen utama
- **Bank Soal**: Dokumen soal + Kunci Jawaban + Rubrik
- **Job Sheet**: Dokumen job sheet + Video tutorial (optional)
- **Teaching Factory**: Dokumen SOP + Video proses (optional)

---

## 🚀 User Flow

### **Opsi C: Guided Flow (RECOMMENDED)**

Alur yang **user-friendly** dengan panduan visual:

```
┌─────────────────────────────────────────┐
│ 1. CREATE PERANGKAT AJAR                │
│    - Upload file utama atau link        │
│    - Isi metadata lengkap               │
│    - Submit (Draft/Approval)            │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│ 2. REDIRECT KE DETAIL PAGE              │
│    ✅ Flash message sukses              │
│    💡 Tip: "Tambahkan lampiran..."      │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│ 3. VISUAL HIGHLIGHTS                    │
│    🔵 Section lampiran ter-highlight    │
│    ⚡ Button "Tambah" bounce animation  │
│    📝 Blue info box dengan panduan      │
│    📍 Auto-scroll smooth ke section     │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│ 4. TAMBAH LAMPIRAN                      │
│    - Klik "Tambah Lampiran" button      │
│    - Modal terbuka                      │
│    - Pilih jenis lampiran               │
│    - Upload file ATAU paste link        │
│    - (Optional) Tandai sebagai primary  │
│    - Submit → Lampiran tersimpan        │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│ 5. ULANGI UNTUK LAMPIRAN LAIN           │
│    - Tambah LKPD, PPT, Video, dll       │
│    - Tidak ada limit jumlah lampiran    │
└─────────────────────────────────────────┘
```

### **Keuntungan Opsi C:**
- ✅ User tidak overwhelmed di form create
- ✅ Focus satu task at a time (create dulu, lampiran kemudian)
- ✅ Visual guidance dengan highlight & info box
- ✅ Auto-scroll mengarahkan user ke section yang tepat
- ✅ Flash message memberikan context & motivation

---

## 📁 Upload File vs Link

### **Upload File (Lokal)**

**Kapan digunakan:**
- ✅ File sudah ada di komputer (PDF, DOCX, PPTX, dll)
- ✅ File size < 100MB
- ✅ Ingin hosting internal (tidak depend on external service)

**Format didukung:**
- 📄 **Dokumen**: PDF, DOCX
- 📊 **Presentasi**: PPTX
- 📈 **Spreadsheet**: XLSX
- 🖼️ **Gambar**: JPG, JPEG, PNG
- 🎬 **Video**: MP4 (max 100MB - untuk video kecil saja)

**Storage:**
- Disimpan di `storage/app/teaching-materials/{category}/`
- File name: `{timestamp}_{slug}.{ext}`
- Protected access (harus login & permission check)

**Contoh:**
```
Input:  modul_ajar_kimia.pdf (2.5 MB)
Stored: storage/app/teaching-materials/modul_ajar/1721890234_modul-ajar-kimia.pdf
```

---

### **Link Eksternal**

**Kapan digunakan:**
- ✅ File > 100MB (video besar, e-book besar)
- ✅ Sudah ada di Google Drive / YouTube / Cloud storage
- ✅ Ingin share file yang frequently updated

**Platform didukung:**
- 🎬 **YouTube**: Video pembelajaran, tutorial
- ☁️ **Google Drive**: Dokumen, video, file besar
- 📝 **Google Docs/Sheets/Slides**: Dokumen kolaboratif
- 📦 **Dropbox, OneDrive**: Cloud storage lainnya
- 🌐 **Website**: Link ke website eksternal

**Contoh:**
```
✅ https://www.youtube.com/watch?v=xxxxx (Video pembelajaran)
✅ https://drive.google.com/file/d/xxxxx/view (Google Drive)
✅ https://docs.google.com/document/d/xxxxx (Google Docs)
✅ https://www.dropbox.com/s/xxxxx/file.pdf (Dropbox)
```

**Keuntungan:**
- ✅ No storage limit (hosting di platform external)
- ✅ Better untuk video besar (YouTube streaming)
- ✅ File bisa diupdate di platform external tanpa re-upload
- ✅ Collaboration-friendly (Google Docs, dll)

**Catatan:**
- ⚠️ Pastikan link **publicly accessible** atau setting permission yang tepat
- ⚠️ Link bisa broken jika file dihapus dari platform external
- ⚠️ YouTube: Pastikan video tidak di-private

---

## ⚙️ Fitur Utama

### 1. **Primary Marking**

Tandai lampiran yang paling penting sebagai **Primary**:

```
📄 Modul_Ajar_Lengkap.pdf [PRIMARY] ← File utama
📝 LKPD_Materi_1.docx
📊 Slide_Presentasi.pptx
🎬 Video_Penjelasan.mp4
```

**Kegunaan:**
- Membantu user menemukan file utama dengan cepat
- Ditampilkan pertama di list (sorted by primary)
- Visual badge "Primary" dengan blue background
- Hanya 1 file yang bisa di-mark sebagai primary (best practice)

**Cara:**
- ✅ Centang checkbox "Tandai sebagai lampiran utama" saat upload
- ✅ Bisa diubah sewaktu-waktu (upload lagi dengan primary=true)

---

### 2. **Bulk Download (ZIP)**

Download **semua lampiran sekaligus** dalam format ZIP:

```
┌─────────────────────────────────────────┐
│ Download Semua (ZIP)  [Total: 15.3 MB]  │
└─────────────────────────────────────────┘
         ▼
┌─────────────────────────────────────────┐
│ Modul_Ajar_Kimia.zip                    │
│ ├── Modul_Ajar_Lengkap.pdf              │
│ ├── LKPD_Materi_1.docx                  │
│ ├── Slide_Presentasi.pptx               │
│ ├── Rubrik_Penilaian.xlsx               │
│ └── Kunci_Jawaban.pdf                   │
└─────────────────────────────────────────┘
```

**Keuntungan:**
- ✅ One-click download semua file
- ✅ Organized dalam ZIP archive
- ✅ Include semua file lokal (exclude external links)
- ✅ Otomatis naming: `{title}_lampiran.zip`

**Catatan:**
- External links tidak diinclude dalam ZIP (hanya file lokal)
- Jika ada external links, akan ditampilkan separate dengan button "Buka Link"

---

### 3. **Download Tracking**

Setiap lampiran memiliki **download counter**:

```
📄 Modul_Ajar_Lengkap.pdf
   • ⬇️ 45 downloads

📝 LKPD_Materi_1.docx
   • ⬇️ 32 downloads
```

**Kegunaan:**
- 📊 Monitoring popularity lampiran
- 📈 Analytics untuk dashboard (future feature)
- 🎯 Identifikasi lampiran yang paling useful

**Auto Increment:**
- Setiap kali user download individual file → counter++
- Setiap kali user download all (ZIP) → all counters++
- Setiap kali user "Buka Link" (external) → counter++

---

### 4. **File Size Tracking**

Display file size untuk setiap lampiran:

```
📄 Modul_Ajar_Lengkap.pdf • 2.5 MB • ⬇️ 45 downloads
📝 LKPD_Materi_1.docx • 456 KB • ⬇️ 32 downloads
📊 Slide_Presentasi.pptx • 8.2 MB • ⬇️ 28 downloads
🎬 Video (YouTube) • ⬇️ 15 opens
```

**Total File Size:**
```
Download Semua (ZIP)  [Total: 11.2 MB]
```

**Formatted:**
- Bytes → Human readable (KB, MB, GB)
- External links tidak ada file size (null)
- Total hanya hitung file lokal

---

### 5. **Delete Individual Attachment**

Hapus lampiran yang tidak diperlukan:

```
[🗑️ Hapus]  ← Button untuk delete
     ▼
┌─────────────────────────────────────────┐
│ Hapus lampiran 'LKPD_Materi_1.docx'?    │
│                                         │
│ [Batal]  [Ya, Hapus]                    │
└─────────────────────────────────────────┘
```

**Permission:**
- ✅ Admin/Waka: Bisa hapus semua lampiran
- ✅ Owner: Bisa hapus lampiran milik sendiri (hanya untuk draft)
- ❌ Others: Tidak bisa hapus

**Cascade:**
- File dihapus dari storage
- Record dihapus dari database
- No soft delete (permanent delete)

---

## 🔐 Permission & Authorization

### **Role-Based Access**

| Role | Create | View | Download | Edit/Delete |
|------|--------|------|----------|-------------|
| **Admin** | ✅ | ✅ All | ✅ All | ✅ All |
| **Waka Kurikulum** | ✅ | ✅ All | ✅ All | ✅ All |
| **Kepsek** | ❌ | ✅ Approved + Own | ✅ Approved + Own | ❌ |
| **Guru** | ✅ | ✅ Approved + Own | ✅ Approved + Own | ✅ Own Draft Only |

### **Ownership Logic**

```php
// Admin & Waka: Full access to all attachments
if (auth()->user()->hasRole(['admin', 'waka_kurikulum'])) {
    return true;
}

// Owner: Can manage own materials (draft only)
if ($material->created_by === auth()->id() && $material->status === 'draft') {
    return true;
}

// Others: View & download only (approved materials)
if ($material->status === 'approved') {
    return true; // can view & download, but not manage
}

return false;
```

### **Material Status Impact**

| Status | Owner Can Manage | Others Can Access |
|--------|------------------|-------------------|
| **draft** | ✅ Yes | ❌ No |
| **pending_approval** | ❌ No (locked) | ❌ No |
| **approved** | ❌ No (locked) | ✅ Yes |
| **rejected** | ✅ Yes (can revise) | ❌ No |

**Reasoning:**
- Draft: Owner bebas edit/manage sebelum submit
- Pending: Locked untuk mencegah perubahan saat review
- Approved: Locked untuk menjaga integrity (sudah disetujui)
- Rejected: Owner bisa revise & re-submit

---

## 🔧 Technical Details

### **Database Schema**

```sql
CREATE TABLE teaching_material_attachments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Foreign Keys
    teaching_material_id BIGINT NOT NULL,
    uploaded_by BIGINT NOT NULL,
    
    -- File Info
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NULL,          -- Nullable untuk links
    file_type VARCHAR(50) NOT NULL,       -- pdf, docx, pptx, link, etc
    file_size BIGINT NULL,                -- Null untuk links
    external_link VARCHAR(500) NULL,      -- Untuk external links
    
    -- Metadata
    attachment_type ENUM(...) DEFAULT 'other',
    is_primary BOOLEAN DEFAULT FALSE,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    
    -- Tracking
    download_count INT DEFAULT 0,
    
    -- Timestamps
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Indexes
    INDEX idx_material (teaching_material_id),
    INDEX idx_type (attachment_type),
    INDEX idx_primary (is_primary),
    
    -- Foreign Keys
    FOREIGN KEY (teaching_material_id) 
        REFERENCES teaching_materials(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) 
        REFERENCES users(id) ON DELETE CASCADE
);
```

### **Model Relationships**

```php
// TeachingMaterial.php
public function attachments()
{
    return $this->hasMany(TeachingMaterialAttachment::class);
}

public function getPrimaryAttachment()
{
    return $this->attachments()->where('is_primary', true)->first();
}

// TeachingMaterialAttachment.php
public function material()
{
    return $this->belongsTo(TeachingMaterial::class, 'teaching_material_id');
}

public function uploader()
{
    return $this->belongsTo(User::class, 'uploaded_by');
}
```

### **File Storage Structure**

```
storage/app/
└── teaching-materials/
    ├── cp/
    ├── atp/
    ├── modul_ajar/
    │   ├── 1721890234_modul-ajar-kimia.pdf
    │   └── 1721890345_lkpd-materi-1.docx
    ├── bank_soal/
    └── ...
```

### **Routes**

```php
// Individual download
GET /teaching-materials/{materialId}/attachments/{attachmentId}/download
    → TeachingMaterialController@downloadAttachment

// Bulk download (ZIP)
GET /teaching-materials/{id}/attachments/download-all
    → TeachingMaterialController@downloadAllAttachments
```

### **Validation Rules**

```php
// File Upload
'attachmentFile' => 'required|file|max:102400|mimes:pdf,docx,pptx,xlsx,jpg,jpeg,png,mp4'

// External Link
'attachmentLink' => 'required|url|max:500'

// Common
'attachmentType' => 'required|in:main,lkpd,presentation,video,assessment,rubric,answer_key,reading_material,other'
'attachmentDescription' => 'nullable|string|max:1000'
'isPrimary' => 'boolean'
```

---

## 🛠️ Troubleshooting

### **Problem: File upload gagal (500 error)**

**Possible Causes:**
1. File size > 100MB
2. File type tidak didukung
3. Storage permission issue

**Solutions:**
```bash
# 1. Check storage permission
php artisan storage:link
chmod -R 775 storage/

# 2. Check upload_max_filesize & post_max_size di php.ini
upload_max_filesize = 100M
post_max_size = 100M

# 3. Check file type validation
# Pastikan extension ada di validation rules
```

---

### **Problem: Link YouTube tidak bisa dibuka**

**Possible Causes:**
1. Video di-private atau unlisted
2. Link format salah

**Solutions:**
```
✅ Correct: https://www.youtube.com/watch?v=dQw4w9WgXcQ
❌ Wrong: https://youtu.be/dQw4w9WgXcQ (short link - work tapi better use full)
❌ Wrong: youtube.com/watch?v=dQw4w9WgXcQ (missing https://)

# Make sure video is PUBLIC or UNLISTED (not PRIVATE)
```

---

### **Problem: Download ZIP gagal / corrupt**

**Possible Causes:**
1. One or more files missing dari storage
2. Memory limit issue (too many/large files)

**Solutions:**
```bash
# 1. Check if files exist
php artisan tinker
>>> $material = \App\Models\TeachingMaterial::find(1);
>>> foreach($material->attachments as $att) {
...     if(!$att->isLink() && !Storage::exists($att->file_path)) {
...         echo "Missing: {$att->file_path}\n";
...     }
... }

# 2. Increase memory limit di php.ini
memory_limit = 512M

# 3. Check PHP zip extension installed
php -m | grep zip
```

---

### **Problem: Auto-scroll tidak bekerja**

**Possible Causes:**
1. JavaScript error di browser
2. Element ID tidak match

**Solutions:**
```javascript
// 1. Check browser console for errors
// 2. Verify element ID
document.getElementById('attachments-section') !== null

// 3. Try manual scroll
document.getElementById('attachments-section').scrollIntoView({ 
    behavior: 'smooth', 
    block: 'center' 
});
```

---

### **Problem: Permission denied saat delete/manage**

**Possible Causes:**
1. Material status bukan draft
2. Bukan owner atau admin/waka

**Solutions:**
```php
// Check permission
$canManage = (
    auth()->user()->hasRole(['admin', 'waka_kurikulum']) ||
    ($material->created_by === auth()->id() && $material->status === 'draft')
);

// If material is pending/approved, only admin/waka can manage
// If rejected, owner can manage untuk revise
```

---

## 📚 Best Practices

### **DO's ✅**

1. **Upload file lokal untuk file kecil** (< 10MB)
   - Faster download
   - No dependency on external service
   
2. **Use external links untuk video besar**
   - YouTube untuk video pembelajaran
   - Google Drive untuk file > 100MB

3. **Mark file utama sebagai Primary**
   - Membantu user menemukan file penting
   - Only 1 primary per material

4. **Tambahkan description untuk setiap lampiran**
   - Membantu user understand isi file
   - Especially important untuk file dengan nama cryptic

5. **Organize by category**
   - LKPD untuk worksheet
   - Presentation untuk PPT
   - Assessment untuk soal

6. **Use meaningful file names**
   - ✅ LKPD_Materi_Asam_Basa.docx
   - ❌ document1.docx

---

### **DON'Ts ❌**

1. ❌ **Upload file > 100MB via file upload**
   - Use external link instead (Google Drive, YouTube)

2. ❌ **Mark multiple files sebagai Primary**
   - Hanya 1 file yang primary (best practice)

3. ❌ **Upload file duplikat**
   - Check dulu apakah file sudah ada
   - Delete old version sebelum upload new

4. ❌ **Use private links untuk external**
   - Pastikan link publicly accessible
   - Atau setting permission "Anyone with link can view"

5. ❌ **Upload file dengan virus**
   - Scan file dengan antivirus dulu
   - System tidak auto-scan virus

6. ❌ **Delete lampiran setelah approved**
   - Material approved itu locked
   - Only admin/waka yang bisa manage

---

## 🎓 Tutorial Video

**Coming Soon:**
- 📹 Tutorial: Upload Perangkat Ajar dengan Lampiran Lengkap
- 📹 Tutorial: Download & Organize Materials
- 📹 Tutorial: Best Practices Lampiran untuk Akreditasi

---

## 📞 Support

**Questions?**
- 📧 Email: support@smkpgriblora.sch.id
- 💬 WhatsApp: +62 xxx xxxx xxxx
- 📍 Location: SMK PGRI Blora

**Documentation:**
- 📚 [Main README](PERANGKAT_AJAR_README.md)
- 📝 [Quick Start Guide](PERANGKAT_AJAR_QUICK_START.md)
- 📋 [Changelog](PERANGKAT_AJAR_CHANGELOG.md)

---

**Last Updated:** 2026-07-25  
**Version:** 1.2.0  
**Status:** ✅ PRODUCTION READY  
**Branding:** SIMKUR SMK PGRI Blora

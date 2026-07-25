# ✅ Testing Checklist - Modul Perangkat Ajar

**SIMKUR SMK PGRI Blora**  
**Version:** 1.0.2  
**Date:** 25 Juli 2026

---

## 🎯 Testing Instructions

Gunakan checklist ini untuk memverifikasi semua fitur bekerja dengan baik.

---

## 1️⃣ SETUP & PREPARATION

### Database Setup
- [ ] Migration berhasil dijalankan: `php artisan migrate`
- [ ] Seeder berhasil dijalankan: `php artisan db:seed --class=TeachingMaterialSeeder`
- [ ] Cache cleared: `php artisan view:clear`, `php artisan route:clear`, `php artisan config:clear`

### Test Users
Pastikan ada user dengan role:
- [ ] Admin
- [ ] Waka Kurikulum
- [ ] Kepala Sekolah
- [ ] Guru (minimal 2 guru berbeda)

---

## 2️⃣ NAVIGATION TESTING

### Desktop Menu
- [ ] **Admin:** Menu "📚 Perangkat Ajar" tampil sebagai dropdown
  - [ ] Submenu "📖 Lihat Semua" ada
  - [ ] Submenu "⏳ Approval" ada
- [ ] **Waka:** Menu "📚 Perangkat Ajar" tampil sebagai dropdown
  - [ ] Submenu "📖 Lihat Semua" ada
  - [ ] Submenu "⏳ Approval" ada
- [ ] **Guru:** Menu "📚 Perangkat Ajar" tampil sebagai link langsung (bukan dropdown)
- [ ] **Kepsek:** Menu "📚 Perangkat Ajar" tampil sebagai link langsung (bukan dropdown)

### Mobile Menu
- [ ] **Admin:** Menu "📚 Perangkat Ajar" tampil dengan submenu
  - [ ] Submenu "📖 Lihat Semua" ada
  - [ ] Submenu "⏳ Approval" ada
- [ ] **Waka:** Menu "📚 Perangkat Ajar" tampil dengan submenu
- [ ] **Guru:** Menu "📚 Perangkat Ajar" tampil tanpa submenu
- [ ] **Kepsek:** Menu "📚 Perangkat Ajar" tampil tanpa submenu

---

## 3️⃣ INDEX PAGE TESTING

### Access Control
- [ ] Admin dapat akses `/teaching-materials`
- [ ] Waka dapat akses `/teaching-materials`
- [ ] Kepsek dapat akses `/teaching-materials`
- [ ] Guru dapat akses `/teaching-materials`

### Display
- [ ] Halaman tampil tanpa error
- [ ] Materials tampil grouped by category
- [ ] Badge status tampil (draft, pending, approved, rejected)
- [ ] Tombol "Upload Perangkat Ajar" tampil untuk admin/waka/guru

### Filter
- [ ] Filter by Category (12 kategori)
- [ ] Filter by Subject (dropdown mata pelajaran)
- [ ] Filter by Grade (10, 11, 12, Semua Kelas)
- [ ] Filter by Status (draft, pending, approved, rejected)
- [ ] Filter by Academic Year (dropdown tahun ajaran)
- [ ] Filter by Dimensions (8 dimensi)
- [ ] Filter reset button works

### Search
- [ ] Search by title works
- [ ] Search by description works
- [ ] Search by tags works
- [ ] Search kombinasi dengan filter works

---

## 4️⃣ CREATE MATERIAL TESTING

### Access Control
- [ ] Admin dapat akses `/teaching-materials/create`
- [ ] Waka dapat akses `/teaching-materials/create`
- [ ] Guru dapat akses `/teaching-materials/create`
- [ ] Kepsek TIDAK dapat akses (403 Forbidden)

### Form Validation
- [ ] Title required
- [ ] Category required
- [ ] Type (file/link) required
- [ ] Subject required
- [ ] Grade required
- [ ] Academic Year required
- [ ] Description optional
- [ ] File required jika type = file
- [ ] Link required jika type = link
- [ ] File max 100MB
- [ ] File types: PDF, DOCX, PPTX, XLSX, JPG, PNG, MP4

### Upload File
- [ ] Upload PDF berhasil
- [ ] Upload DOCX berhasil
- [ ] Upload PPTX berhasil
- [ ] Upload JPG berhasil
- [ ] File disimpan di `storage/app/teaching_materials/`
- [ ] File path tersimpan di database

### Upload Link
- [ ] Link YouTube berhasil
- [ ] Link Google Drive berhasil
- [ ] Link eksternal berhasil
- [ ] Link tersimpan di database

### Dimensions Selection
- [ ] Bisa pilih multiple dimensions
- [ ] Dimensions tersimpan di database (JSON)

### Tags
- [ ] Tags bisa diinput (comma separated)
- [ ] Tags tersimpan di database (JSON)

### Submit
- [ ] Submit berhasil
- [ ] Redirect ke index page
- [ ] Flash message success tampil
- [ ] Material masuk status "draft"

---

## 5️⃣ EDIT MATERIAL TESTING

### Access Control
- [ ] Admin dapat edit SEMUA materials
- [ ] Waka dapat edit SEMUA materials
- [ ] Guru dapat edit HANYA draft sendiri
- [ ] Guru TIDAK dapat edit material orang lain
- [ ] Guru TIDAK dapat edit material yang sudah approved
- [ ] Kepsek TIDAK dapat edit

### Form
- [ ] Data ter-load dengan benar
- [ ] Semua field editable (kecuali status)
- [ ] File bisa diganti
- [ ] Link bisa diganti
- [ ] Update berhasil
- [ ] Flash message success tampil

---

## 6️⃣ DELETE MATERIAL TESTING

### Access Control
- [ ] Admin dapat delete SEMUA materials
- [ ] Waka dapat delete SEMUA materials
- [ ] Guru dapat delete HANYA draft sendiri
- [ ] Guru TIDAK dapat delete material yang sudah approved
- [ ] Kepsek TIDAK dapat delete

### Delete
- [ ] Confirm dialog tampil
- [ ] Delete berhasil
- [ ] File dihapus dari storage (jika type = file)
- [ ] Record dihapus dari database
- [ ] Flash message success tampil

---

## 7️⃣ SHOW DETAIL TESTING

### Access Control
- [ ] Admin dapat lihat SEMUA materials
- [ ] Waka dapat lihat SEMUA materials
- [ ] Kepsek dapat lihat approved materials
- [ ] Guru dapat lihat:
  - [ ] Own materials (semua status)
  - [ ] Approved public materials

### Display
- [ ] Title, description tampil
- [ ] Category, subject, grade tampil
- [ ] Academic year tampil
- [ ] Created by tampil
- [ ] Status badge tampil
- [ ] 8 Dimensions tampil
- [ ] Tags tampil
- [ ] View count tampil
- [ ] Download count tampil

### Actions
- [ ] Tombol "Edit" tampil jika user boleh edit
- [ ] Tombol "Delete" tampil jika user boleh delete
- [ ] Tombol "Download" tampil jika type = file
- [ ] Link "Buka Link" tampil jika type = link

### View Counter
- [ ] View count bertambah setiap kali halaman detail dibuka
- [ ] View count disimpan di database

---

## 8️⃣ DOWNLOAD TESTING

### Access Control
- [ ] Admin dapat download SEMUA files
- [ ] Waka dapat download SEMUA files
- [ ] Kepsek dapat download APPROVED files
- [ ] Guru dapat download:
  - [ ] Own files (semua status)
  - [ ] Approved public files
- [ ] Permission denied jika tidak boleh akses (403)

### Download
- [ ] File berhasil didownload
- [ ] Filename sesuai dengan original
- [ ] Download count bertambah
- [ ] Download count disimpan di database

### Error Handling
- [ ] Error 404 jika file tidak ada
- [ ] Error 403 jika tidak ada permission
- [ ] Error message jelas

---

## 9️⃣ APPROVAL WORKFLOW TESTING

### Submit for Approval (Guru)
- [ ] Guru create material → status "draft"
- [ ] Guru klik "Submit untuk Approval"
- [ ] Status berubah jadi "pending_approval"
- [ ] Flash message success tampil
- [ ] Material muncul di halaman approval

### Approval Page Access
- [ ] Admin dapat akses `/teaching-materials/approval`
- [ ] Waka dapat akses `/teaching-materials/approval`
- [ ] Guru TIDAK dapat akses (403 Forbidden)
- [ ] Kepsek TIDAK dapat akses (403 Forbidden)

### Approval Page Display
- [ ] List materials dengan status "pending_approval"
- [ ] Diurutkan dari yang paling lama menunggu (created_at ASC)
- [ ] Filter & search works
- [ ] Tombol "✅ Setujui" dan "❌ Tolak" tampil

### Approve Material
- [ ] Klik "✅ Setujui"
- [ ] Modal confirm tampil
- [ ] Approve berhasil
- [ ] Status berubah jadi "approved"
- [ ] Approved by tersimpan (user ID)
- [ ] Approved at tersimpan (timestamp)
- [ ] Flash message success tampil
- [ ] Material hilang dari list approval

### Reject Material
- [ ] Klik "❌ Tolak"
- [ ] Modal catatan revisi tampil
- [ ] Catatan required
- [ ] Reject berhasil
- [ ] Status berubah jadi "rejected"
- [ ] Approved by tersimpan (user ID)
- [ ] Approved at tersimpan (timestamp)
- [ ] Approval notes tersimpan
- [ ] Flash message success tampil
- [ ] Material hilang dari list approval

### After Rejection
- [ ] Guru dapat lihat catatan revisi
- [ ] Guru dapat edit material yang rejected
- [ ] Guru dapat submit ulang untuk approval

---

## 🔟 COMMENT SYSTEM TESTING

### Add Comment
- [ ] Authenticated user dapat add comment
- [ ] Form comment tampil di detail page
- [ ] Comment berhasil disimpan
- [ ] Comment tampil dengan author & timestamp
- [ ] Flash message success tampil

### Display Comments
- [ ] Semua comments tampil di detail page
- [ ] Diurutkan dari terbaru ke terlama
- [ ] Author name tampil
- [ ] Timestamp tampil (readable format)

---

## 1️⃣1️⃣ AUTHORIZATION TESTING

### Permission Helper
- [ ] `User::canAccessMaterial()` works correctly
- [ ] Admin/Waka: return true untuk semua materials
- [ ] Owner: return true untuk own materials
- [ ] Public approved: return true untuk semua user
- [ ] Others: return false

### Middleware
- [ ] `/teaching-materials/*` requires auth
- [ ] `/teaching-materials/approval` requires admin/waka only
- [ ] Unauthorized access → 403 Forbidden
- [ ] Unauthenticated access → redirect to login

---

## 1️⃣2️⃣ RESPONSIVE TESTING

### Desktop (> 768px)
- [ ] Layout tampil dengan benar
- [ ] Dropdown menu works
- [ ] Tables responsive
- [ ] Modals centered

### Mobile (< 768px)
- [ ] Layout tampil dengan benar
- [ ] Mobile menu toggle works
- [ ] Submenu collapse/expand works
- [ ] Tables scrollable horizontal
- [ ] Modals full-screen atau centered

---

## 1️⃣3️⃣ ERROR HANDLING TESTING

### File Upload Errors
- [ ] File terlalu besar (> 100MB) → error message
- [ ] File type tidak diizinkan → error message
- [ ] Upload gagal → error message

### Permission Errors
- [ ] Access denied → 403 Forbidden
- [ ] Clear error message
- [ ] Redirect ke halaman sebelumnya

### Not Found Errors
- [ ] Material not found → 404 Not Found
- [ ] File not found → 404 Not Found
- [ ] Clear error message

### Validation Errors
- [ ] Required fields empty → validation error
- [ ] Invalid input → validation error
- [ ] Error message tampil per field

---

## 1️⃣4️⃣ PERFORMANCE TESTING

### Page Load
- [ ] Index page load < 2 detik
- [ ] Detail page load < 1 detik
- [ ] Create/Edit form load < 1 detik

### Database Queries
- [ ] Eager loading relationships (subject, academicYear, creator)
- [ ] No N+1 query problem
- [ ] Pagination works (15 items per page)

### File Download
- [ ] File download mulai segera setelah klik
- [ ] No timeout untuk file besar

---

## 1️⃣5️⃣ INTEGRATION TESTING

### Database Relationships
- [ ] Material → Subject relationship works
- [ ] Material → Academic Year relationship works
- [ ] Material → User (creator) relationship works
- [ ] Material → Comments relationship works

### File Storage
- [ ] Files stored in correct directory
- [ ] File paths correct in database
- [ ] Files deleted when material deleted

### Session & Auth
- [ ] Login persists across pages
- [ ] Permission checks consistent
- [ ] Logout works

---

## ✅ FINAL VERIFICATION

### Documentation
- [ ] README complete & accurate
- [ ] CHANGELOG up-to-date (v1.0.2)
- [ ] QUICK START guide clear
- [ ] COMPLETION REPORT comprehensive

### Code Quality
- [ ] No console errors
- [ ] No PHP errors
- [ ] No broken links
- [ ] No dead code

### User Experience
- [ ] Flash messages clear & helpful
- [ ] Loading states implemented
- [ ] Empty states implemented
- [ ] Error messages user-friendly

---

## 📊 Test Results Summary

```
Total Tests: ~150+
Passed: _____ / _____
Failed: _____ / _____
Skipped: _____ / _____

Date Tested: _______________
Tested By: _______________
Environment: _______________
```

---

## 🐛 Bug Report Template

Jika menemukan bug, catat dengan format:

```
**Bug Title:** [Short description]

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Result:**
[What should happen]

**Actual Result:**
[What actually happened]

**Screenshots:**
[If applicable]

**Environment:**
- Browser: 
- Device: 
- User Role: 
- Date: 
```

---

**Last Updated:** 25 Juli 2026  
**Version Tested:** 1.0.2  
**Contact:** DMCenter Team

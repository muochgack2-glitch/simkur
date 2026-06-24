# 🧪 TESTING GUIDE - Target Grades Feature

**Feature:** Filter Kegiatan Berdasarkan Tingkat Kelas  
**Date:** 2026-06-23

---

## 📋 PRE-REQUISITES

✅ All migrations run successfully  
✅ Test data seeded (`TestGradeSeeder`)  
✅ Assets built (`npm run build`)  
✅ Browser cache cleared

---

## 👥 TEST ACCOUNTS

| Username | Password | Role | Grade | Purpose |
|----------|----------|------|-------|---------|
| `siswa_x` | `password` | siswa | X | Test Kelas X filtering |
| `siswa_xi` | `password` | siswa | XI | Test Kelas XI filtering |
| `siswa_xii` | `password` | siswa | XII | Test Kelas XII filtering |
| (your admin) | - | admin | - | Test admin view (see all) |

---

## 🧪 TEST SCENARIOS

### **TEST 1: Create Activity with Target Grades**

**Steps:**
1. Login as **Admin**
2. Navigate to **Kegiatan** → **Tambah Kegiatan**
3. Fill form:
   - Nama: "Test Kegiatan Kelas X"
   - Jenis: (any)
   - Tanggal: (any valid weekday range)
   - Semester: (select active)
   - **Target Tingkat Kelas:** Check only **Kelas X**
4. Click **Simpan**

**Expected Result:**
- ✅ Activity created successfully
- ✅ Success message shown
- ✅ Redirected to activity list

**Verification:**
- Check database: `SELECT name, target_grades FROM activities WHERE name = 'Test Kegiatan Kelas X'`
- Expected: `target_grades = ["X"]`

---

### **TEST 2: Student View - Kelas X**

**Steps:**
1. **Logout** from admin
2. Login as **siswa_x** (password: `password`)
3. Navigate to **Kegiatan** (Activities page)
4. View **Calendar** (month view)

**Expected Result:**
✅ **SHOULD SEE:**
- MPLS (Kelas X only)
- Libur Semester (Semua Kelas)
- Test Kegiatan Kelas X (if created in Test 1)

❌ **SHOULD NOT SEE:**
- PKL (Kelas XII only)
- Ujian Tengah Semester (Kelas XI & XII)

**Verification:**
- Count events on calendar
- Check badge colors:
  - 🔵 Blue badge = "Kelas X"
  - 🟢 Green badge = "Semua Kelas"

---

### **TEST 3: Student View - Kelas XII**

**Steps:**
1. **Logout** from siswa_x
2. Login as **siswa_xii** (password: `password`)
3. Navigate to **Kegiatan**
4. View **Calendar**

**Expected Result:**
✅ **SHOULD SEE:**
- PKL (Kelas XII only)
- Ujian Tengah Semester (Kelas XI & XII)
- Libur Semester (Semua Kelas)

❌ **SHOULD NOT SEE:**
- MPLS (Kelas X only)
- Test Kegiatan Kelas X

**Verification:**
- Check badge colors:
  - 🟣 Purple badge = "Kelas XII"
  - 🟠 Orange badge = "Kelas XI, XII" (multiple)
  - 🟢 Green badge = "Semua Kelas"

---

### **TEST 4: Admin View - See All Activities**

**Steps:**
1. **Logout** from siswa_xii
2. Login as **Admin**
3. Navigate to **Kegiatan**
4. Switch to **List View** (Daftar)

**Expected Result:**
✅ **SHOULD SEE:**
- ALL activities (no filtering)
- "Target Kelas" column visible
- Badges for each activity showing target grades

**Verification:**
- All activities appear in list
- Each has correct badge in "Target Kelas" column
- Can edit/delete activities

---

### **TEST 5: Edit Activity - Change Target Grades**

**Steps:**
1. Login as **Admin**
2. Navigate to **Kegiatan** → **List View**
3. Click **Edit** on "MPLS" activity
4. Change target grades:
   - Uncheck "Kelas X"
   - Check "Kelas XI" and "Kelas XII"
5. Click **Simpan**

**Expected Result:**
- ✅ Activity updated successfully
- ✅ Success message shown
- ✅ Redirected to activity list

**Verification:**
1. Database check: `SELECT target_grades FROM activities WHERE name = 'MPLS'`
   - Expected: `["XI","XII"]`
2. Login as **siswa_x** → MPLS should **NOT** appear
3. Login as **siswa_xi** → MPLS should **appear**

---

### **TEST 6: Calendar Badge Display**

**Steps:**
1. Login as **Admin**
2. Navigate to **Kegiatan** → **Month View**
3. Observe events on calendar

**Expected Result:**
- ✅ Each event shows small badge below title
- ✅ Badge colors match target grade:
  - 🟢 Green = Semua Kelas
  - 🔵 Blue = Kelas X
  - 🟡 Yellow = Kelas XI
  - 🟣 Purple = Kelas XII
  - 🟠 Orange = Multiple grades

**Verification:**
- Badge text readable
- Colors distinct
- Badge doesn't overlap title
- Works on mobile viewport

---

### **TEST 7: Create Activity - All Grades**

**Steps:**
1. Login as **Admin**
2. Create new activity
3. Check **"Semua Kelas (X, XI, XII)"** checkbox
4. Save

**Expected Result:**
- ✅ All individual grade checkboxes auto-checked
- ✅ Activity saved with `target_grades = null`
- ✅ Visible to all students (all grades)

**Verification:**
- Database: `target_grades IS NULL`
- Login as any student → activity appears
- Badge shows "Semua Kelas" in green

---

### **TEST 8: Validation - No Grade Selected**

**Steps:**
1. Login as **Admin**
2. Create new activity
3. **Uncheck ALL** grade checkboxes (X, XI, XII, All)
4. Try to save

**Expected Result:**
- ❌ Form does NOT submit
- ❌ Error message: "Pilih minimal 1 tingkat kelas"
- ✅ Form remains open with data preserved

---

### **TEST 9: Multiple Grades Selection**

**Steps:**
1. Login as **Admin**
2. Create activity with Kelas XI + XII only
3. Save
4. Login as **siswa_xi** → check calendar
5. Login as **siswa_xii** → check calendar
6. Login as **siswa_x** → check calendar

**Expected Result:**
- ✅ siswa_xi: Activity appears
- ✅ siswa_xii: Activity appears
- ❌ siswa_x: Activity does NOT appear
- Badge shows "Kelas XI, XII" in orange

---

### **TEST 10: List View - Target Kelas Column**

**Steps:**
1. Login as **Admin**
2. Navigate to **Kegiatan** → **List View**
3. Observe "Target Kelas" column

**Expected Result:**
- ✅ Column header visible: "Target Kelas"
- ✅ Each row shows badge with icon
- ✅ Badge colors match calendar
- ✅ Text readable and clear

**Verification:**
- Icon (users) displays correctly
- Badge styling consistent
- Column width appropriate
- Mobile responsive

---

## 🐛 TROUBLESHOOTING

### **Issue: Badge not showing on calendar**
**Solution:**
```bash
npm run build
php artisan optimize:clear
# Clear browser cache (Ctrl+Shift+R)
```

### **Issue: Student sees all activities (filter not working)**
**Check:**
1. User has `role = 'siswa'`: `SELECT role, grade FROM users WHERE username = 'siswa_x'`
2. User has `grade` set: Should be 'X', 'XI', or 'XII'
3. Activity has `target_grades`: `SELECT name, target_grades FROM activities`

**Fix if grade is NULL:**
```sql
UPDATE users SET grade = 'X' WHERE username = 'siswa_x';
```

### **Issue: "Semua Kelas" activities not showing**
**Check:**
- `target_grades` should be `NULL` (not empty array `[]`)
- Query: `SELECT name, target_grades FROM activities WHERE target_grades IS NULL`

**Fix:**
```sql
UPDATE activities SET target_grades = NULL WHERE name = 'Libur Semester';
```

### **Issue: Error "Role siswa not valid"**
**Solution:**
- Migration `add_siswa_role_to_users_table` might not have run
```bash
php artisan migrate
```

---

## ✅ SUCCESS CRITERIA

All tests pass if:
- [x] Students only see activities for their grade + "Semua Kelas"
- [x] Admin/Guru see all activities without filter
- [x] Badges display correctly with right colors
- [x] Create/Edit forms work for all grade combinations
- [x] Validation prevents saving without grade selection
- [x] Database stores target_grades correctly (array or NULL)
- [x] List view shows "Target Kelas" column
- [x] Multiple grades selection works (e.g., XI + XII)

---

## 📊 TEST RESULTS CHECKLIST

| Test | Status | Notes |
|------|--------|-------|
| TEST 1: Create with Target Grades | ⬜ | |
| TEST 2: Student View Kelas X | ⬜ | |
| TEST 3: Student View Kelas XII | ⬜ | |
| TEST 4: Admin View All | ⬜ | |
| TEST 5: Edit Target Grades | ⬜ | |
| TEST 6: Calendar Badge Display | ⬜ | |
| TEST 7: Create All Grades | ⬜ | |
| TEST 8: Validation No Grade | ⬜ | |
| TEST 9: Multiple Grades | ⬜ | |
| TEST 10: List View Column | ⬜ | |

**Legend:** ✅ Pass | ❌ Fail | ⬜ Not Tested

---

## 📸 SCREENSHOTS TO VERIFY

1. **Create Form** - Target Grades section with checkboxes
2. **Calendar View (Admin)** - All activities with colored badges
3. **Calendar View (Siswa X)** - Filtered activities (only X + All)
4. **Calendar View (Siswa XII)** - Filtered activities (only XII + All)
5. **List View** - "Target Kelas" column with badges
6. **Edit Form** - Pre-filled checkboxes matching current data

---

**Ready to test!** 🚀  
Start with TEST 1 and proceed sequentially.
